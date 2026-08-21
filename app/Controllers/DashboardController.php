<?php
// =================================================================================
// Controlador: DashboardController
// =================================================================================
// Controlador principal del escritorio/dashboard del sistema de gestión laboral

namespace App\Controllers;
use App\Models\WorkdayModel;
use App\Models\UsersModel;
use App\Models\DocumentsModel;
use App\Models\AbsenceModel;
use App\Models\ExpenseModel;
use App\Models\ActivityLogModel;

class DashboardController extends BaseController
{
    // Modelos utilizados para acceder a los datos del sistema
    protected $usersModel;       // Gestión de usuarios
    protected $documentsModel;   // Gestión de documentos
    protected $absenceModel;     // Gestión de ausencias
    protected $expenseModel;     // Gestión de gastos
    protected $workdayModel;     // Gestión de jornadas laborales
    protected $activityLogModel; // Registro de actividad del sistema

    // =================================================================================
    // Constructor - Inicialización de modelos
    // =================================================================================
    public function __construct()
    {
        // Inicializar modelos para acceso a datos
        $this->usersModel       = new UsersModel();
        $this->documentsModel   = new DocumentsModel();
        $this->absenceModel     = new AbsenceModel();
        $this->expenseModel     = new ExpenseModel();
        $this->workdayModel     = new WorkdayModel();
        $this->activityLogModel = new ActivityLogModel();
    }

    // =================================================================================
    // Dashboard principal - Escritorio del usuario
    // =================================================================================
    public function index()
    {
        $userId = session()->get('user_id');
        $userRole = session()->get('user_role');
        $isAdmin = ($userRole == 1 || $userRole == 2);
        $permissions = session()->get('user_permissions') ?? [];

        // Auto-cerrar jornadas del pasado no cerradas
        if ($isAdmin) {
            $this->workdayModel->autoClosePastWorkdays();
        } else {
            $this->workdayModel->autoClosePastWorkdays($userId);
        }

        $data['title'] = 'Escritorio';
        $data['stats'] = $this->calculateAllStats($userId, $isAdmin, $permissions);

        // =================================================================================
        // SECCIÓN 4: Datos de jornada laboral actual
        // =================================================================================
        $data['current_workday'] = $this->getCurrentWorkdayData($userId);

        // =================================================================================
        // SECCIÓN 5: Estadísticas mensuales de jornadas
        // =================================================================================
        // Calcular estadísticas del mes actual
        $user = $this->usersModel->find($userId);
        $userDailyHours = $user ? ($user['daily_hours'] ?? null) : null;

        $currentMonthStart = date('Y-m-01');
        $currentMonthEnd = date('Y-m-t');
        $monthlyEvents = $this->workdayModel->where('user_id', $userId)
            ->where('workday_date >=', $currentMonthStart)
            ->where('workday_date <=', $currentMonthEnd)
            ->orderBy('workday_date', 'ASC')
            ->orderBy('event_time', 'ASC')
            ->findAll();

        // Agrupar eventos por fecha
        $groupedEvents = [];
        foreach ($monthlyEvents as $event) {
            $groupedEvents[$event['workday_date']][] = $event;
        }

        // Calcular jornadas completadas y en curso
        $totalHours = 0;
        $workdaysCount = 0;
        foreach ($groupedEvents as $date => $events) {
            $workday = calculate_workday_data($date, $events, $userDailyHours);
            if ($workday && in_array($workday['status'], ['completed', 'in_progress', 'pause'])) {
                $totalHours += $workday['total_hours'];
                $workdaysCount++;
            }
        }

        $data['stats']['my_workdays_month'] = $workdaysCount;
        $data['stats']['my_total_hours_month'] = $totalHours * 60; // Convertir a minutos para la vista

        // =================================================================================
        // Renderizar vista del dashboard
        // =================================================================================
        // Mostrar el dashboard con todas las estadísticas calculadas
        echo view('template/header', $data);
        
        // Cargar vista específica según el rol
        if ($isAdmin) {
            echo view('dashboard/admin', $data);
        } else {
            echo view('dashboard/dashboard', $data);
        }
        
        echo view('template/footer');
    }

    // =================================================================================
    // Método para calcular todas las estadísticas
    // =================================================================================
    // Calcula todas las estadísticas del dashboard en una sola operación optimizada
    private function calculateAllStats($userId, $isAdmin, $permissions)
    {
        $stats = [];

        try {
            // =================================================================================
            // SECCIÓN 1: Optimización de estadísticas de documentos
            // =================================================================================
            // Combinar consultas en una sola consulta agrupada
            $documentsQuery = $this->documentsModel->builder()
                ->select('sender_id, receiver_id, COUNT(*) as count')
                ->where('deleted_at IS NULL')
                ->where("(sender_id = {$userId} OR receiver_id = {$userId})")
                ->groupBy('sender_id, receiver_id')
                ->get();

            // Inicializar contadores
            $stats['my_sent_documents'] = 0;
            $stats['my_received_documents'] = 0;

            if ($documentsQuery->getResult()) {
                foreach ($documentsQuery->getResult() as $row) {
                    if ($row->sender_id == $userId) {
                        $stats['my_sent_documents'] += $row->count;
                    }
                    if ($row->receiver_id == $userId) {
                        $stats['my_received_documents'] += $row->count;
                    }
                }
            }

            // =================================================================================
            // SECCIÓN 2: Optimización de estadísticas de ausencias
            // =================================================================================
            // Consulta agrupada para ausencias por estado
            $absencesQuery = $this->absenceModel->builder()
                ->select('status, COUNT(*) as count')
                ->where('user_id', $userId)
                ->whereIn('status', ['approved', 'rejected'])
                ->groupBy('status')
                ->get();

            $stats['my_absences_approved'] = 0;
            $stats['my_absences_rejected'] = 0;

            if ($absencesQuery->getResult()) {
                foreach ($absencesQuery->getResult() as $row) {
                    if ($row->status === 'approved') {
                        $stats['my_absences_approved'] = $row->count;
                    } elseif ($row->status === 'rejected') {
                        $stats['my_absences_rejected'] = $row->count;
                    }
                }
            }

            // =================================================================================
            // SECCIÓN 3: Estadísticas administrativas optimizadas
            // =================================================================================
            if ($isAdmin) {
                $today = date('Y-m-d');
                
                // --- 1. ESTADOS DE USUARIOS (EN VIVO) ---
                $liveEvents = $this->workdayModel->builder()
                    ->select('event_type, user_id')
                    ->where('workday_date', $today)
                    ->whereIn('id', function($builder) use ($today) {
                        return $builder->selectMax('id')->from('workday')->where('workday_date', $today)->groupBy('user_id');
                    })
                    ->get()->getResultArray();

                $stats['users_active'] = 0;
                $stats['users_break'] = 0;
                foreach ($liveEvents as $event) {
                    // Si el último evento es entrada o retoma, el usuario está activo
                    if ($event['event_type'] == 'start' || $event['event_type'] == 'resume') {
                        $stats['users_active']++;
                    }
                    // Si el último evento es inicio de pausa, el usuario está en pausa
                    if ($event['event_type'] == 'pause') {
                        $stats['users_break']++;
                    }
                }

                // --- 2. GESTIÓN DE AUSENCIAS Y DOCUMENTOS ---
                $stats['absences_today'] = $this->absenceModel->builder()
                    ->where('status', 'approved')
                    ->where('start_date <=', $today)->where('end_date >=', $today)
                    ->countAllResults();

                $stats['docs_pending_read'] = $this->documentsModel->where('read_at', null)->where('receiver_id', $userId)->countAllResults();
                $stats['absences_pending'] = $this->absenceModel->where('status', 'pending')->countAllResults();
                $stats['expenses_pending'] = $this->expenseModel->where('status', 'pending')->countAllResults();

                // --- 3. GRÁFICA DUAL: RENDIMIENTO VS AUSENCIAS (ÚLTIMOS 7 DÍAS) ---
                $labels = [];
                $seriesAttendance = [];
                $seriesAbsences = [];

                $startDate = date('Y-m-d', strtotime('-6 days'));
                $endDate = date('Y-m-d');

                // Optimización 1: Consultar todas las asistencias de los últimos 7 días en una sola query
                $attendanceData = $this->workdayModel->builder()
                    ->select('workday_date, COUNT(*) as count')
                    ->where('event_type', 'start')
                    ->where('workday_date >=', $startDate)
                    ->where('workday_date <=', $endDate)
                    ->groupBy('workday_date')
                    ->get()->getResultArray();
                
                // Mapear los resultados por fecha para acceso rápido
                $attendanceByDate = [];
                foreach ($attendanceData as $row) {
                    $attendanceByDate[$row['workday_date']] = (int) $row['count'];
                }

                // Optimización 2: Consultar todas las ausencias que solapan con la ventana de 7 días
                $absencesData = $this->absenceModel->builder()
                    ->select('start_date, end_date')
                    ->where('status', 'approved')
                    ->where('end_date >=', $startDate)
                    ->where('start_date <=', $endDate)
                    ->get()->getResultArray();

                // Llenar las series recorriendo los 7 días (0 consultas a BD dentro del bucle)
                for ($i = 6; $i >= 0; $i--) {
                    $d = date('Y-m-d', strtotime("-$i days"));
                    $labels[] = date('d M', strtotime($d));
                    
                    // Asistencia Real
                    $seriesAttendance[] = $attendanceByDate[$d] ?? 0;
                    
                    // Ausencias Reales (Contar cuántas ausencias activas solapan con este día)
                    $absencesCount = 0;
                    foreach ($absencesData as $abs) {
                        if ($d >= $abs['start_date'] && $d <= $abs['end_date']) {
                            $absencesCount++;
                        }
                    }
                    $seriesAbsences[] = $absencesCount;
                }

                $stats['chart_labels'] = $labels;
                $stats['series_attendance'] = $seriesAttendance;
                $stats['series_absences'] = $seriesAbsences;

                // --- 4. LISTADO EN VIVO Y TIMELINE ---
                $stats['live_status'] = $this->workdayModel->builder()
                    ->select('users.name, users.avatar, workday.event_type, workday.event_time')
                    ->join('users', 'users.id = workday.user_id')
                    ->where('workday.workday_date', $today)
                    ->whereIn('workday.id', function($builder) use ($today) {
                        return $builder->selectMax('id')->from('workday')->where('workday_date', $today)->groupBy('user_id');
                    })
                    ->limit(5)->get()->getResultArray();

                // Obtener últimos movimientos desde el registro de actividad
                $stats['activity_timeline'] = $this->activityLogModel
                    ->select('activity_logs.description, activity_logs.module, activity_logs.created_at, users.name')
                    ->join('users', 'users.id = activity_logs.user_id', 'left')
                    ->whereIn('activity_logs.module', ['Jornadas', 'Workdays'])
                    ->orderBy('activity_logs.created_at', 'DESC')
                    ->limit(4)
                    ->findAll();
            } else {
                // Estadísticas para usuarios no admin con permisos limitados
                if (has_permission('absences.manage')) {
                    $stats['absences_pending'] = $this->absenceModel->where('status', 'pending')->countAllResults();
                }
                if (has_permission('expenses.manage')) {
                    $stats['expenses_pending'] = $this->expenseModel->where('status', 'pending')->countAllResults();
                }
            }

        } catch (\Exception $e) {
            // Manejar errores de base de datos de manera elegante
            log_message('error', 'Error calculando estadísticas del dashboard: ' . $e->getMessage());
            
            // Establecer valores por defecto en caso de error
            $stats = array_merge($stats, [
                'my_sent_documents' => 0,
                'my_received_documents' => 0,
                'my_absences_approved' => 0,
                'my_absences_rejected' => 0,
                'absences_pending' => 0,
                'expenses_pending' => 0
            ]);
        }

        return $stats;
    }

    // =================================================================================
    // Método auxiliar para obtener datos de jornada laboral actual
    // =================================================================================
    // Obtiene la información de la jornada laboral más reciente del usuario
    // para mostrar el estado en el dashboard
    private function getCurrentWorkdayData($userId)
    {
        // Buscar la jornada laboral más reciente del usuario
        $lastRecord = $this->workdayModel->where('user_id', $userId)
            ->orderBy('event_time', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        // Si no hay registros, retornar null
        if (!$lastRecord) {
            return null;
        }

        // Si el último evento es 'stop', la jornada está finalizada
        if ($lastRecord['event_type'] === 'stop') {
            return [
                'end_time' => $lastRecord['event_time'],
                'autoclose' => $lastRecord['autoclose'] ?? false,
                'start_time' => null
            ];
        }

        // Si el último evento es 'start', 'pause' o 'resume', la jornada está activa
        if (in_array($lastRecord['event_type'], ['start', 'pause', 'resume'])) {
            if ($lastRecord['workday_date'] < date('Y-m-d')) {
                return null;
            }
            $events = $this->workdayModel->where('user_id', $userId)
                ->where('workday_date', $lastRecord['workday_date'])
                ->orderBy('event_time', 'ASC')
                ->findAll();

            $workdayData = calculate_workday_data($lastRecord['workday_date'], $events);

            if ($workdayData) {
                return [
                    'start_time' => $workdayData['start_time'],
                    'start_date' => $workdayData['start_date'],
                    'elapsed_seconds' => round($workdayData['total_hours'] * 3600),
                    'end_time' => null,
                    'autoclose' => false,
                    'status' => $lastRecord['event_type']
                ];
            }
        }

        // Para cualquier otro caso, retornar null
        return null;
    }


    // =================================================================================
    // Endpoint para obtener eventos del calendario (AJAX)
    // =================================================================================
    public function getEvents()
    {
        $userId = session()->get('user_id');
        $events = [];

        // 1. Obtener Días Trabajados (Workdays)
        $workdays = $this->workdayModel->where('user_id', $userId)
            ->select('workday_date')
            ->distinct()
            ->findAll();

        foreach ($workdays as $wd) {
            $events[] = [
                'id' => 'work-' . $wd['workday_date'],
                'title' => 'Jornada',
                'start' => $wd['workday_date'],
                'allDay' => true,
                'color' => '#5d87ff', // Azul Modernize
                'url' => base_url('workdays/view/' . $wd['workday_date']),
                'extendedProps' => ['type' => 'workday']
            ];
        }

        // 2. Obtener Ausencias Aprobadas (Absences)
        $absences = $this->absenceModel->where('user_id', $userId)
            ->where('status', 'approved')
            ->findAll();

        $absTypes = $this->absenceModel->getAbsenceTypes();

        foreach ($absences as $abs) {
            // Determinar color según tipo
            $color = '#ffae1f'; // Warning por defecto (naranja)
            if (in_array($abs['type'], ['baja', 'accidente', 'enfermedad'])) {
                $color = '#fa896b'; // Danger (rojo)
            } elseif ($abs['type'] === 'vacaciones') {
                $color = '#13deb9'; // Success (verde)
            }

            // Para FullCalendar, el end date de un evento de todo el día es exclusivo (no incluido)
            // Sumamos un día a end_date para que se muestre correctamente el rango completo
            $endDate = date('Y-m-d', strtotime($abs['end_date'] . ' +1 day'));

            $events[] = [
                'id' => 'abs-' . $abs['id'],
                'title' => $absTypes[$abs['type']] ?? 'Ausencia',
                'start' => $abs['start_date'],
                'end' => $endDate,
                'allDay' => true,
                'color' => $color,
                'url' => base_url('absences/view/' . $abs['id']),
                'extendedProps' => [
                    'type' => 'absence',
                    'absence_type' => $abs['type']
                ]
            ];
        }

        return $this->response->setJSON($events);
    }
}