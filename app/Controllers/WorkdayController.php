<?php
// =================================================================================
// Controlador: WorkdayController
// =================================================================================

namespace App\Controllers;

use App\Models\WorkdayModel;
use App\Models\UsersModel;
use App\Models\CompanyModel;

class WorkdayController extends BaseController
{
    // instanciar modelos
    protected $workdayModel;
    protected $userModel;
    protected $companyModel;

    // =================================================================================
    // Constructor - Inicialización del controlador
    // =================================================================================
    public function __construct()
    {
        $this->workdayModel = new WorkdayModel();
        $this->userModel = new UsersModel();
        $this->companyModel = new CompanyModel();
    }

    // =================================================================================
    // Página principal del control de jornadas (Reloj de control)
    // =================================================================================
    public function index()
    {
        // Obtener ID del usuario autenticado (autenticación ya confirmada por filtro de rutas)
        $userId = session()->get('user_id');

        // Preparar datos para la vista
        $data = [
            'title' => 'Control de Jornada Laboral',
        ];

        // Revisar si la jornada excede las horas máximas y cerrarla automáticamente
        $openWorkday = $this->getOpenWorkdayType($userId);
        if ($openWorkday && $openWorkday['event_type'] === 'start') {
            $this->autoCloseWorkday($userId, $openWorkday['workday_date']);
        }
        
        $currentEventType = $this->getOpenWorkdayType($userId)['event_type'];
        
        // Calcular tiempo transcurrido exacto restando pausas
        $data['elapsed_seconds'] = 0;
        if (in_array($currentEventType, ['start', 'pause', 'resume'])) {
            helper('workday');
            $events = $this->workdayModel->where('user_id', $userId)
                ->where('workday_date', $openWorkday['workday_date'] ?? date('Y-m-d'))
                ->orderBy('event_time', 'ASC')
                ->findAll();
                
            $workdayData = calculate_workday_data($openWorkday['workday_date'] ?? date('Y-m-d'), $events);
            if ($workdayData) {
                $data['elapsed_seconds'] = round($workdayData['total_hours'] * 3600);
            }
        }

        // si la jornada esta abierta mostramos pausa y cierre
        if ($currentEventType === 'start') {
            echo view('template/header', $data);
            echo view('workdays/active', $data);
            echo view('template/footer');
        }
        // si la jornada esta en pausa mostramos reanudar y cierre
        elseif ($currentEventType === 'pause') {
            echo view('template/header', $data);
            echo view('workdays/resume', $data);
            echo view('template/footer');
        }
        // si la jornada esta reanudada mostramos pausa y cierre
        elseif ($currentEventType === 'resume') {
            echo view('template/header', $data);
            echo view('workdays/active', $data);
            echo view('template/footer');
        }
        // si la jornada esta cerrada mostramos iniciar
        elseif ($currentEventType === 'stop') {
            echo view('template/header', $data);
            echo view('workdays/start', $data);
            echo view('template/footer');
        }
    }

    // =================================================================================
    // Iniciar jornada laboral (Registrar entrada)
    // =================================================================================
    public function start()
    {
        // Definir reglas de validación para los datos del formulario
        $rules = [
            'latitud' => 'permit_empty|trim',   // Coordenada GPS opcional
            'longitud' => 'permit_empty|trim',  // Coordenada GPS opcional
        ];

        // Validar datos del formulario
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener usuario autenticado (autenticación ya confirmada por filtro de rutas)
        $userId = session()->get('user_id');

        // Obtener fecha y hora actual
        $currentDateTime = date('Y-m-d H:i:s');
        $currentDate = date('Y-m-d');

        // Obtener coordenadas GPS del formulario (si están disponibles)
        $latitude = $this->request->getPost('latitud');
        $longitude = $this->request->getPost('longitud');

        // Verificar que no hay una jornada ya abierta con el metodo auxiliar getOpenWorkdayType
        $openWorkday = $this->getOpenWorkdayType($userId);

        // comprobamos que no duplique la jornada el mismo dia
        if (!$openWorkday || !isset($openWorkday['workday_date'])) {
            // No hay jornada abierta, puedes proceder
        } elseif ($openWorkday['workday_date'] === $currentDate) {
            return redirect()->back()->with('errors', ['Ya finalizaste tu jornada de hoy']);
        }

        // Comprobar que no se inicie una jornada si ya hay una abierta (operador null coalescing)
        if (($openWorkday['event_type'] ?? 'stop') !== 'stop') {
            return redirect()->back()->with('errors', ['Ya tienes una jornada laboral activa.']);
        }

        // Obtener horas diarias trabajadas y máximas del usuario
        $user = $this->userModel->find($userId);
        $dailyHours = $user['daily_hours'] ?? 8;
        $maxDailyHours = $user['max_daily_hours'] ?? 12;

        // Crear registro de inicio de jornada
        $data = [
            'user_id' => $userId,
            'workday_date' => $currentDate,      // Fecha de la jornada (siempre la fecha del 'in')
            'event_type' => 'start',                // Tipo de evento: entrada
            'event_time' => $currentDateTime,    // Hora exacta del evento
            'latitude' => $latitude,             // Coordenada GPS (opcional)
            'longitude' => $longitude,           // Coordenada GPS (opcional)
            'daily_hours' => $dailyHours,        // Horas diarias trabajadas (se traen de users) 
            'max_daily_hours' => $maxDailyHours, // Horas máximas diarias trabajadas (se traen de users)
        ];

        // Insertar registro en la base de datos
        if ($this->workdayModel->insert($data)) {
            return redirect()->back()->with('success', 'Jornada iniciada correctamente.');
        } else {
            return redirect()->back()->with('errors', ['Error al iniciar la jornada.']);
        }
    }

    // =================================================================================
    // Pausar jornada laboral (Registrar pausa)
    // =================================================================================
    public function pause()
    {
        // Definir reglas de validación para los datos del formulario
        $rules = [
            'latitud' => 'permit_empty|trim',   // Coordenada GPS opcional
            'longitud' => 'permit_empty|trim',  // Coordenada GPS opcional
        ];

        // Validar datos del formulario
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener usuario autenticado
        $userId = session()->get('user_id');

        // Obtener jornada abierta para usar su workday_date
        $openWorkday = $this->getOpenWorkdayType($userId);

        // Obtener fecha y hora actual
        $currentDateTime = date('Y-m-d H:i:s');

        // Obtener coordenadas GPS del formulario
        $latitude = $this->request->getPost('latitud');
        $longitude = $this->request->getPost('longitud');

        // Verificar que hay una jornada abierta
        $openWorkday = $this->getOpenWorkdayType($userId);

        // Verificar que la jornada está activa para poder pausar
        if (!in_array($openWorkday['event_type'], ['start', 'resume'])) {
            return redirect()->back()->with('errors', ['No tienes una jornada activa para pausar.']);
        }

        // Obtener horas diarias trabajadas y máximas del usuario
        $user = $this->userModel->find($userId);
        $dailyHours = $user['daily_hours'] ?? 8;
        $maxDailyHours = $user['max_daily_hours'] ?? 12;


        // Crear registro de pausa
        $data = [
            'user_id' => $userId,
            'workday_date' => $openWorkday['workday_date'],
            'event_type' => 'pause',
            'event_time' => $currentDateTime,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'daily_hours' => $dailyHours,
            'max_daily_hours' => $maxDailyHours,
        ];

        // Insertar registro
        if ($this->workdayModel->insert($data)) {
            return redirect()->back()->with('success', 'Jornada pausada correctamente.');
        } else {
            return redirect()->back()->with('errors', ['Error al pausar la jornada.']);
        }
    }

    // =================================================================================
    // Reanudar jornada laboral (Registrar fin de pausa)
    // =================================================================================
    public function resume()
    {
        // Definir reglas de validación
        $rules = [
            'latitud' => 'permit_empty|trim',
            'longitud' => 'permit_empty|trim',
        ];

        // Validar datos
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener usuario autenticado
        $userId = session()->get('user_id');

        // Obtener jornada abierta para usar su workday_date
        $openWorkday = $this->getOpenWorkdayType($userId);

        // Obtener fecha y hora actual
        $currentDateTime = date('Y-m-d H:i:s');

        // Obtener coordenadas GPS
        $latitude = $this->request->getPost('latitud');
        $longitude = $this->request->getPost('longitud');

        // Verificar que está en pausa
        $openWorkday = $this->getOpenWorkdayType($userId);

        if ($openWorkday['event_type'] !== 'pause') {
            return redirect()->back()->with('errors', ['No tienes una jornada en pausa para reanudar.']);
        }

        // Obtener horas diarias trabajadas y máximas del usuario
        $user = $this->userModel->find($userId);
        $dailyHours = $user['daily_hours'] ?? 8;
        $maxDailyHours = $user['max_daily_hours'] ?? 12;

        // Crear registro de reanudación
        $data = [
            'user_id' => $userId,
            'workday_date' => $openWorkday['workday_date'],
            'event_type' => 'resume',
            'event_time' => $currentDateTime,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'daily_hours' => $dailyHours,
            'max_daily_hours' => $maxDailyHours,
        ];

        // Insertar registro
        if ($this->workdayModel->insert($data)) {
            return redirect()->back()->with('success', 'Jornada reanudada correctamente.');
        } else {
            return redirect()->back()->with('errors', ['Error al reanudar la jornada.']);
        }
    }

    // =================================================================================
    // Finalizar jornada laboral (Registrar salida)
    // =================================================================================
    public function end()
    {
        // Definir reglas de validación
        $rules = [
            'latitud' => 'permit_empty|trim',
            'longitud' => 'permit_empty|trim',
        ];

        // Validar datos
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener usuario autenticado
        $userId = session()->get('user_id');

        // Obtener jornada abierta para usar su workday_date
        $openWorkday = $this->getOpenWorkdayType($userId);

        // Obtener fecha y hora actual
        $currentDateTime = date('Y-m-d H:i:s');

        // Obtener coordenadas GPS
        $latitude = $this->request->getPost('latitud');
        $longitude = $this->request->getPost('longitud');

        // Verificar que hay una jornada abierta o en pausa
        $openWorkday = $this->getOpenWorkdayType($userId);

        // Verificar que la jornada está activa (iniciada o reanudada) para poder finalizar
        if (!in_array($openWorkday['event_type'], ['start', 'pause', 'resume'])) {
            return redirect()->back()->with('errors', ['No tienes una jornada activa para finalizar.']);
        }
        
        // Obtener horas diarias trabajadas y máximas del usuario
        $user = $this->userModel->find($userId);
        $dailyHours = $user['daily_hours'] ?? 8;
        $maxDailyHours = $user['max_daily_hours'] ?? 12;


        // Crear registro de salida
        $data = [
            'user_id' => $userId,
            'workday_date' => $openWorkday['workday_date'],
            'event_type' => 'stop',
            'event_time' => $currentDateTime,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'daily_hours' => $dailyHours,
            'max_daily_hours' => $maxDailyHours,
        ];

        // Insertar registro
        if ($this->workdayModel->insert($data)) {
            return redirect()->back()->with('success', 'Jornada finalizada correctamente.');
        } else {
            return redirect()->back()->with('errors', ['Error al finalizar la jornada.']);
        }
    }

    // =================================================================================
    // Visualización de registros personales de jornadas
    // =================================================================================
    public function myRecords()
    {
        // Obtener usuario autenticado
        $userId = session()->get('user_id');

        // Obtener filtros de la URL (con valores por defecto)
        $date_from = $this->request->getGet('date_from') ?? date('Y-m-01'); // Primer día del mes actual
        $date_to = $this->request->getGet('date_to') ?? date('Y-m-t');       // Último día del mes actual
        $status = $this->request->getGet('status');                          // Filtro de estado (opcional)

        // Obtener todos los eventos del usuario en el rango de fechas
        // IMPORTANTE: Los eventos se ordenan por fecha y hora para cálculo correcto
        $allEvents = $this->workdayModel
            ->where('user_id', $userId)
            ->where('workday_date >=', $date_from)
            ->where('workday_date <=', $date_to)
            ->orderBy('workday_date', 'DESC')    // Fechas más recientes primero
            ->orderBy('event_time', 'ASC')       // Eventos cronológicos dentro de cada fecha
            ->findAll();

        // Agrupar eventos por fecha de jornada
        // Cada grupo representa una jornada completa (entrada, pausas, salida)
        $groupedEvents = [];
        foreach ($allEvents as $event) {
            $groupedEvents[$event['workday_date']][] = $event;
        }

        // Procesar cada jornada agrupada y calcular datos
        $workdays = [];
        foreach ($groupedEvents as $date => $events) {
            // Obtener daily_hours específico de esta jornada desde el evento 'in'
            $inEvent = array_filter($events, function($event) {
                return $event['event_type'] === 'start';
            });
            $inEvent = reset($inEvent); // Obtener el primer (y único) evento 'in'
            $userDailyHours = $inEvent ? ($inEvent['daily_hours'] ?? null) : null;

            // Calcular datos de la jornada usando el método helper
            $workday = calculate_workday_data($date, $events, $userDailyHours);
            if ($workday) {
                // Agregar daily_hours y información del usuario para PDFs
                $workday['daily_hours'] = $userDailyHours;
                $workdays[] = $workday;
            }
        }

        // Aplicar filtro de estado después de calcular todas las jornadas
        if ($status) {
            $workdays = array_filter($workdays, function ($workday) use ($status) {
                return $workday['status'] === $status;
            });
        }

        // Reindexar array después del filtro
        $workdays = array_values($workdays);

        // Configurar paginación
        $perPage = 15;  // Registros por página
        $currentPage = $this->request->getGet('page') ?? 1;
        $total = count($workdays);
        $offset = ($currentPage - 1) * $perPage;
        $workdays = array_slice($workdays, $offset, $perPage);

        // Crear enlaces de paginación
        $pager = service('pager');
        $pager->setPath('workdays/my-records', 'default');
        $pagerLinks = $pager->makeLinks($currentPage, $perPage, $total, 'default_full');

        // Preparar datos para la vista
        $data = [
            'title' => 'Mis Registros de Jornadas',
            'workdays' => $workdays,        // Jornadas procesadas y paginadas
            'date_from' => $date_from,      // Filtro de fecha inicial
            'date_to' => $date_to,          // Filtro de fecha final
            'status' => $status,            // Filtro de estado
            'pager' => $pagerLinks          // Enlaces de paginación
        ];

        // Renderizar vista de registros personales
        echo view('template/header', $data);
        echo view('workdays/my_records', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Gestión de jornadas (Vista para administradores)
    // =================================================================================
    public function manage()
    {
        // Obtener filtros de la URL (con valores por defecto)
        $user_id = $this->request->getGet('user_id');
        $date_from = $this->request->getGet('date_from') ?? date('Y-m-01'); // Primer día del mes actual
        $date_to = $this->request->getGet('date_to') ?? date('Y-m-t');       // Último día del mes actual
        $status = $this->request->getGet('status');                          // Filtro de estado (opcional)


        // Obtener todos los eventos en el rango de fechas
        $query = $this->workdayModel
            ->where('workday_date >=', $date_from)
            ->where('workday_date <=', $date_to)
            ->orderBy('workday_date', 'DESC')    // Fechas más recientes primero
            ->orderBy('event_time', 'ASC');       // Eventos cronológicos dentro de cada fecha

        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        $allEvents = $query->findAll();

        // Agrupar eventos por user_id y workday_date
        $groupedEvents = [];
        foreach ($allEvents as $event) {
            $groupedEvents[$event['user_id']][$event['workday_date']][] = $event;
        }

        // Obtener lista de usuarios para el filtro
        $users = $this->userModel->findAll();

        // Procesar cada jornada agrupada y calcular datos
        $workdays = [];
        foreach ($groupedEvents as $uid => $dates) {
            $user = $this->userModel->find($uid);
            foreach ($dates as $date => $events) {
                // Obtener daily_hours específico de esta jornada desde el evento 'in'
                $inEvent = array_filter($events, function($event) {
                    return $event['event_type'] === 'start';
                });
                $inEvent = reset($inEvent); // Obtener el primer (y único) evento 'in'
                $userDailyHours = $inEvent ? ($inEvent['daily_hours'] ?? null) : null;

                // Calcular datos de la jornada usando el método helper
                $workday = calculate_workday_data($date, $events, $userDailyHours);
                if ($workday) {
                    $workday['user_id'] = $uid;
                    $workday['daily_hours'] = $userDailyHours;
                    $workday['user_name'] = $user['name'] ?? '';
                    $workday['user_identification'] = $user['identification'] ?? '';
                    $workdays[] = $workday;
                }
            }
        }

        // Aplicar filtro de estado después de calcular todas las jornadas
        if ($status) {
            $workdays = array_filter($workdays, function ($workday) use ($status) {
                return $workday['status'] === $status;
            });
        }

        // Reindexar array después del filtro
        $workdays = array_values($workdays);

        // Configurar paginación
        $perPage = 15;  // Registros por página
        $currentPage = $this->request->getGet('page') ?? 1;
        $total = count($workdays);
        $offset = ($currentPage - 1) * $perPage;
        $workdays = array_slice($workdays, $offset, $perPage);

        // Crear enlaces de paginación
        $pager = service('pager');
        $pager->setPath('workdays/manage', 'default');
        $pagerLinks = $pager->makeLinks($currentPage, $perPage, $total, 'default_full');

        // Preparar datos para la vista
        $data = [
            'title' => 'Gestión de Jornadas',
            'workdays' => $workdays,        // Jornadas procesadas y paginadas
            'users' => $users,              // Lista de usuarios para el filtro
            'user_id' => $user_id,          // Filtro de usuario
            'date_from' => $date_from,      // Filtro de fecha inicial
            'date_to' => $date_to,          // Filtro de fecha final
            'status' => $status,            // Filtro de estado
            'pager' => $pagerLinks          // Enlaces de paginación
        ];

        // Renderizar vista de gestión
        echo view('template/header', $data);
        echo view('workdays/manage', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Ver detalles de una jornada específica
    // =================================================================================
    public function view($date)
    {
        // Obtener usuario (desde query para admin, o session para usuario normal)
        $userId = $this->request->getGet('user_id') ?? session()->get('user_id');

        // Verificar si es vista de otro usuario (admin view)
        $isAdminView = $this->request->getGet('user_id') !== null && $this->request->getGet('user_id') != session()->get('user_id');

        // Verificar permisos: si es vista de otro usuario, debe tener permiso manage_workday o ser admin (user_role == 1)
        if ($isAdminView && !has_permission('workdays.manage')) {
            return redirect()->back()->with('errors', ['No tienes permisos para ver jornadas de otros usuarios.']);
        }

        // Obtener eventos de la jornada ORDENADOS CRONOLÓGICAMENTE (ASC) para calcular pausas correctamente
        $events = $this->workdayModel->where('user_id', $userId)
            ->where('workday_date', $date)
            ->orderBy('event_time', 'ASC')
            ->findAll();

        if (empty($events)) {
            return redirect()->back()->with('errors', ['No se encontraron eventos para la jornada seleccionada.']);
        }

        // Obtener daily_hours específico de esta jornada desde el evento 'in'
        $inEvent = array_filter($events, function($event) {
            return $event['event_type'] === 'start';
        });
        $inEvent = reset($inEvent); // Obtener el primer (y único) evento 'in'
        $userDailyHours = $inEvent ? ($inEvent['daily_hours'] ?? null) : null;

        // Calcular datos de la jornada
        $workday = calculate_workday_data($date, $events, $userDailyHours);

        // Preparar datos para la vista
        // Invertir el orden para mostrar en la vista (más reciente primero)
        $eventsForDisplay = array_reverse($events);
        $data = [
            'title'          => 'Detalles de Jornada',
            'workday_date'   => $date,
            'events'         => $eventsForDisplay,
            'worked_hours'   => $workday['total_hours'] ?? 0,
            'overtime_hours' => $workday['overtime_hours'] ?? 0,
            'break_hours'    => $workday['break_time'] ?? 0,
            'is_admin_view'  => $isAdminView
        ];

        // Renderizar vista
        echo view('template/header', $data);
        echo view('workdays/view', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Exportar registros personales a PDF
    // =================================================================================
    public function exportMyPdf()
    {
        // Obtener usuario autenticado
        $userId = session()->get('user_id');

        // Obtener filtros de la URL (mismos que en myRecords)
        $date_from = $this->request->getGet('date_from') ?? date('Y-m-01');
        $date_to = $this->request->getGet('date_to') ?? date('Y-m-t');
        $status = $this->request->getGet('status');

        // Obtener todos los eventos del usuario (sin paginación para PDF completo)
        $allEvents = $this->workdayModel
            ->where('user_id', $userId)
            ->where('workday_date >=', $date_from)
            ->where('workday_date <=', $date_to)
            ->orderBy('workday_date', 'DESC')
            ->orderBy('event_time', 'ASC')
            ->findAll();

        // Agrupar eventos por fecha y procesar jornadas
        $groupedEvents = [];
        foreach ($allEvents as $event) {
            $groupedEvents[$event['workday_date']][] = $event;
        }

        // Procesar cada jornada
        $workdays = [];
        foreach ($groupedEvents as $date => $events) {
            // Obtener daily_hours específico de esta jornada desde el evento 'in'
            $inEvent = array_filter($events, function($event) {
                return $event['event_type'] === 'start';
            });
            $inEvent = reset($inEvent); // Obtener el primer (y único) evento 'in'
            $userDailyHours = $inEvent ? ($inEvent['daily_hours'] ?? null) : null;

            $workday = calculate_workday_data($date, $events, $userDailyHours);
            if ($workday) {
                $workday['daily_hours'] = $userDailyHours;
                $workdays[] = $workday;
            }
        }

        // Aplicar filtro de estado
        if ($status) {
            $workdays = array_filter($workdays, function ($workday) use ($status) {
                return $workday['status'] === $status;
            });
        }

        // Reindexar array
        $workdays = array_values($workdays);

        // Configurar DomPDF para generación de PDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);  // Permitir recursos remotos si es necesario

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');     // Formato A4 horizontal

        // Generar HTML del PDF usando método helper
        $html = $this->generateWorkdayPdfHtml($workdays, $date_from, $date_to, $status);

        // Cargar HTML en DomPDF
        $dompdf->loadHtml($html);
        $dompdf->render();

        // Descargar PDF usando el response de CodeIgniter
        $filename = 'mis_jornadas_' . date('Y-m-d') . '.pdf';
        $pdfContent = $dompdf->output();
        return $this->response->download($filename, $pdfContent);
    }

    // =================================================================================
    // Exportar jornadas gestionadas a PDF
    // =================================================================================
    public function exportPdf()
    {
        // Obtener filtros de la URL
        $user_id = $this->request->getGet('user_id');
        $date_from = $this->request->getGet('date_from') ?? date('Y-m-01');
        $date_to = $this->request->getGet('date_to') ?? date('Y-m-t');
        $status = $this->request->getGet('status');

        // Obtener todos los eventos en el rango de fechas
        $query = $this->workdayModel
            ->where('workday_date >=', $date_from)
            ->where('workday_date <=', $date_to)
            ->orderBy('workday_date', 'DESC')
            ->orderBy('event_time', 'ASC');

        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        $allEvents = $query->findAll();

        // Agrupar por user_id y workday_date
        $groupedEvents = [];
        foreach ($allEvents as $event) {
            $groupedEvents[$event['user_id']][$event['workday_date']][] = $event;
        }

        // Procesar cada jornada
        $workdays = [];
        foreach ($groupedEvents as $uid => $dates) {
            $user = $this->userModel->find($uid);
            foreach ($dates as $date => $events) {
                // Obtener daily_hours específico de esta jornada desde el evento 'in'
                $inEvent = array_filter($events, function($event) {
                    return $event['event_type'] === 'start';
                });
                $inEvent = reset($inEvent); // Obtener el primer (y único) evento 'in'
                $userDailyHours = $inEvent ? ($inEvent['daily_hours'] ?? null) : null;

                $workday = calculate_workday_data($date, $events, $userDailyHours);
                if ($workday) {
                    $workday['user_id'] = $uid;
                    $workday['daily_hours'] = $userDailyHours;
                    $workday['user_name'] = $user['name'] ?? '';
                    $workday['user_identification'] = $user['identification'] ?? '';
                    $workdays[] = $workday;
                }
            }
        }

        // Aplicar filtro de status
        if ($status) {
            $workdays = array_filter($workdays, function ($workday) use ($status) {
                return $workday['status'] === $status;
            });
        }
        $workdays = array_values($workdays);

        // Configurar DomPDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');

        // Generar HTML
        $html = $this->generateManagePdfHtml($workdays, $date_from, $date_to, $status, $user_id);

        $dompdf->loadHtml($html);
        $dompdf->render();

        // Descargar PDF usando el response de CodeIgniter
        $filename = 'jornadas_gestionadas_' . date('Y-m-d') . '.pdf';
        $pdfContent = $dompdf->output();
        return $this->response->download($filename, $pdfContent);
    }


    // =================================================================================
    // Generar HTML para el PDF de jornadas laborales
    // =================================================================================
    private function generateWorkdayPdfHtml($workdays, $date_from, $date_to, $status)
    {
        // Obtener información del usuario
        $userId = session()->get('user_id');
        $user = model('App\Models\UsersModel')->find($userId);

        // Obtener información de la empresa
        $company = $this->companyModel->getCompany();
        $companyName = $company ? $company['name'] : 'OtGest';

        // Calcular totales
        $totalHours       = 0;
        $totalOvertime    = 0;
        $totalBreak       = 0;
        $completedCount   = 0;
        $inProgressCount  = 0;

        foreach ($workdays as $workday) {
            $totalHours   += $workday['total_hours'];
            $totalOvertime += $workday['overtime_hours'] ?? 0;
            $totalBreak    += $workday['break_time'] ?? 0;
            if ($workday['status'] === 'completed') {
                $completedCount++;
            } else {
                $inProgressCount++;
            }
        }

        // Estilos CSS embebidos para el PDF
        $html = '<style>
            body { font-family: Arial, sans-serif; margin: 15px; font-size: 10px; }
            h1 { color: #333; text-align: center; margin-bottom: 20px; font-size: 14px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 9px; }
            th { border: 1px solid #ddd; padding: 4px; text-align: center; background-color: #f5f5f5; font-weight: bold; font-size: 9px; }
            td { border: 1px solid #ddd; padding: 4px; text-align: left; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .header-info { margin-bottom: 15px; font-size: 9px; }
            .header-left { float: left; width: 60%; }
            .header-right { float: right; width: 35%; text-align: right; }
            .clear { clear: both; }
            .total-row { background-color: #fff3cd; font-weight: bold; }
            .status-completed { background-color: #d4edda; color: #155724; }
            .status-in_progress { background-color: #fff3cd; color: #856404; }
        </style>';

        // Cabecera con información general
        $statusText = [
            'completed' => 'Completadas',
            'in_progress' => 'En Progreso',
            '' => 'Todas'
        ];

        $html .= '
        <div class="header-info">
            <div class="header-left">
                <strong>' . esc($companyName) . '</strong><br>
                <strong>Reporte de Mis Jornadas Laborales</strong><br>
                <strong>Período:</strong> ' . date('d/m/Y', strtotime($date_from)) . ' - ' . date('d/m/Y', strtotime($date_to)) . '<br>
                <strong>Estado:</strong> ' . ($statusText[$status] ?? 'Filtrado') . '<br>
                <strong>Registros:</strong> ' . count($workdays) . '<br>
            </div>
            <div class="header-right">
                <strong>Usuario:</strong> ' . esc($user['name']) . '<br>
                <strong>DNI:</strong> ' . esc($user['identification']) . '<br>
            </div>
            <div class="clear"></div>
        </div>';

        // Tabla principal
        $html .= '<table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Horas Totales</th>
                    <th>T. Pausa</th>
                    <th>Horas Extras</th>
                    <th>Jornada</th>
                    <th>Estado</th>
                    <th>Cierre Auto.</th>
                </tr>
            </thead>
            <tbody style="text-align: center;">';

        foreach ($workdays as $workday) {
            $startDisplay = $workday['start_time'] ? esc($workday['start_time']) . '<br><small style="font-size: 8px; color: #666;">' . esc($workday['start_date']) . '</small>' : '-';
            $endDisplay   = $workday['end_time']   ? esc($workday['end_time'])   . '<br><small style="font-size: 8px; color: #666;">' . esc($workday['end_date'])   . '</small>' : '-';
            // Convertir horas decimales a formato horas:minutos
            $totalMinutes   = round($workday['total_hours'] * 60);
            $totalHoursFormatted = floor($totalMinutes / 60) . ':' . str_pad($totalMinutes % 60, 2, '0', STR_PAD_LEFT);
            $breakMinutes   = round(($workday['break_time'] ?? 0) * 60);
            $breakFormatted = floor($breakMinutes / 60) . ':' . str_pad($breakMinutes % 60, 2, '0', STR_PAD_LEFT);
            $overtimeMinutes = round(($workday['overtime_hours'] ?? 0) * 60);
            $overtimeHoursFormatted = floor($overtimeMinutes / 60) . ':' . str_pad($overtimeMinutes % 60, 2, '0', STR_PAD_LEFT);
            // Obtener daily_hours de la jornada (jornada pactada)
            $dailyMinutes = isset($workday['daily_hours']) ? round($workday['daily_hours'] * 60) : 0;
            $dailyHoursFormatted = isset($workday['daily_hours']) ? floor($dailyMinutes / 60) . ':' . str_pad($dailyMinutes % 60, 2, '0', STR_PAD_LEFT) : '-';
            $html .= '<tr>
                <td style="text-align: center;">' . date('d/m/Y', strtotime($workday['date'])) . '</td>
                <td style="text-align: center;">' . $startDisplay . '</td>
                <td style="text-align: center;">' . $endDisplay . '</td>
                <td style="text-align: center;">' . $totalHoursFormatted . '</td>
                <td style="text-align: center;">' . $breakFormatted . '</td>
                <td style="text-align: center;">' . $overtimeHoursFormatted . '</td>
                <td style="text-align: center;">' . $dailyHoursFormatted . '</td>
                <td style="text-align: center;">' . ($workday['status'] === 'completed' ? 'Completada' : 'En Progreso') . '</td>
                <td style="text-align: center;">' . ($workday['autoclose'] ? 'Sí' : 'No') . '</td>
            </tr>';
        }

        // Fila de totales
        $totalMinutesPdf     = round($totalHours * 60);
        $totalHoursFormatted = floor($totalMinutesPdf / 60) . ':' . str_pad($totalMinutesPdf % 60, 2, '0', STR_PAD_LEFT);
        $totalBreakMinsPdf   = round($totalBreak * 60);
        $totalBreakFormatted = floor($totalBreakMinsPdf / 60) . ':' . str_pad($totalBreakMinsPdf % 60, 2, '0', STR_PAD_LEFT);
        $totalOvertimeMinsPdf   = round($totalOvertime * 60);
        $totalOvertimeFormatted = floor($totalOvertimeMinsPdf / 60) . ':' . str_pad($totalOvertimeMinsPdf % 60, 2, '0', STR_PAD_LEFT);
        $html .= '
            <tr class="total-row">
                <td colspan="3" style="text-align: center;"><strong>TOTALES</strong></td>
                <td style="text-align: center;"><strong>' . $totalHoursFormatted . '</strong></td>
                <td style="text-align: center;"><strong>' . $totalBreakFormatted . '</strong></td>
                <td style="text-align: center;"><strong>' . $totalOvertimeFormatted . '</strong></td>
                <td style="text-align: center;"><strong>-</strong></td>
                <td colspan="2" style="text-align: center;"><strong></strong></td>
            </tr>
        </tbody>
        </table>';

        // Pie de página final
        $html .= '
        <div style="margin-top: 20px; font-size: 8px; color: #666; text-align: center;">
            <p><em>Documento generado por ' . session('user_name') . ' el ' . date('d/m/Y H:i:s') . '</em></p>
        </div>';

        return $html;
    }

    // =================================================================================
    // Generar HTML para el PDF de gestión de jornadas
    // =================================================================================
    private function generateManagePdfHtml($workdays, $date_from, $date_to, $status, $user_id = null)
    {
        // Obtener información de la empresa
        $company = $this->companyModel->getCompany();
        $companyName = $company ? $company['name'] : 'OtGest';

        // Calcular totales
        $totalHours      = 0;
        $totalOvertime   = 0;
        $totalBreak      = 0;
        $completedCount  = 0;
        $inProgressCount = 0;

        foreach ($workdays as $workday) {
            $totalHours    += $workday['total_hours'];
            $totalOvertime += $workday['overtime_hours'] ?? 0;
            $totalBreak    += $workday['break_time'] ?? 0;
            if ($workday['status'] === 'completed') {
                $completedCount++;
            } else {
                $inProgressCount++;
            }
        }

        // Obtener información del usuario si se especifica
        $userInfo = '';
        if ($user_id) {
            $user = $this->userModel->find($user_id);
            if ($user) {
                $userInfo = '<div class="header-right">
                    <strong>Usuario:</strong> ' . esc($user['name']) . '<br>
                    <strong>DNI:</strong> ' . esc($user['identification']) . '<br>
                </div>';
            }
        }

        // Determinar si mostrar columna de usuario (solo si no hay filtro de usuario específico)
        $showUserColumn = !$user_id;

        // Estilos CSS
        $html = '<style>
            body { font-family: Arial, sans-serif; margin: 15px; font-size: 10px; }
            h1 { color: #333; text-align: center; margin-bottom: 20px; font-size: 14px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 9px; }
            th { border: 1px solid #ddd; padding: 4px; text-align: center; background-color: #f5f5f5; font-weight: bold; font-size: 9px; }
            td { border: 1px solid #ddd; padding: 4px; text-align: left; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .header-info { margin-bottom: 15px; font-size: 9px; }
            .header-left { float: left; width: 60%; }
            .header-right { float: right; width: 35%; text-align: right; }
            .clear { clear: both; }
            .total-row { background-color: #fff3cd; font-weight: bold; }
            .status-completed { background-color: #d4edda; color: #155724; }
            .status-in_progress { background-color: #fff3cd; color: #856404; }
        </style>';

        // Cabecera
        $statusText = [
            'completed' => 'Completadas',
            'in_progress' => 'En Progreso',
            '' => 'Todas'
        ];

        $html .= '
        <div class="header-info">
            <div class="header-left">
                <strong>' . esc($companyName) . '</strong><br>
                <strong>Reporte de Gesti&oacute;n de Jornadas Laborales</strong><br>
                <strong>Per&iacute;odo:</strong> ' . date('d/m/Y', strtotime($date_from)) . ' - ' . date('d/m/Y', strtotime($date_to)) . '<br>
                <strong>Estado:</strong> ' . ($statusText[$status] ?? 'Filtrado') . '<br>
                <strong>Registros:</strong> ' . count($workdays) . '<br>
            </div>
            ' . $userInfo . '
            <div class="clear"></div>
        </div>';

        // Tabla - encabezado condicional
        $html .= '<table>
            <thead>
                <tr>';
        if ($showUserColumn) {
            $html .= '<th>Usuario</th>';
        }
        $html .= '
                    <th>Fecha</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Horas Totales</th>
                    <th>T. Pausa</th>
                    <th>Horas Extras</th>
                    <th>Jornada</th>
                    <th>Estado</th>
                    <th>Cierre Auto.</th>
                </tr>
            </thead>
            <tbody style="text-align: center;">';

        foreach ($workdays as $workday) {
            $startDisplay = $workday['start_time'] ? esc($workday['start_time']) . '<br><small style="font-size: 8px; color: #666;">' . esc($workday['start_date']) . '</small>' : '-';
            $endDisplay   = $workday['end_time']   ? esc($workday['end_time'])   . '<br><small style="font-size: 8px; color: #666;">' . esc($workday['end_date'])   . '</small>' : '-';
            // Convertir horas decimales a formato horas:minutos
            $totalMinutes        = round($workday['total_hours'] * 60);
            $totalHoursFormatted = floor($totalMinutes / 60) . ':' . str_pad($totalMinutes % 60, 2, '0', STR_PAD_LEFT);
            $breakMinutes        = round(($workday['break_time'] ?? 0) * 60);
            $breakFormatted      = floor($breakMinutes / 60) . ':' . str_pad($breakMinutes % 60, 2, '0', STR_PAD_LEFT);
            $overtimeMinutes        = round(($workday['overtime_hours'] ?? 0) * 60);
            $overtimeHoursFormatted = floor($overtimeMinutes / 60) . ':' . str_pad($overtimeMinutes % 60, 2, '0', STR_PAD_LEFT);
            // Obtener daily_hours de la jornada (jornada pactada)
            $dailyMinutes        = isset($workday['daily_hours']) ? round($workday['daily_hours'] * 60) : 0;
            $dailyHoursFormatted = isset($workday['daily_hours']) ? floor($dailyMinutes / 60) . ':' . str_pad($dailyMinutes % 60, 2, '0', STR_PAD_LEFT) : '-';
            $html .= '<tr>';
            if ($showUserColumn) {
                $html .= '<td style="text-align: center;">' . esc($workday['user_name']) . '<br><small style="font-size: 8px; color: #666;">' . esc($workday['user_identification']) . '</small></td>';
            }
            $html .= '
                <td style="text-align: center;">' . date('d/m/Y', strtotime($workday['date'])) . '</td>
                <td style="text-align: center;">' . $startDisplay . '</td>
                <td style="text-align: center;">' . $endDisplay . '</td>
                <td style="text-align: center;">' . $totalHoursFormatted . '</td>
                <td style="text-align: center;">' . $breakFormatted . '</td>
                <td style="text-align: center;">' . $overtimeHoursFormatted . '</td>
                <td style="text-align: center;">' . $dailyHoursFormatted . '</td>
                <td style="text-align: center;">' . ($workday['status'] === 'completed' ? 'Completada' : 'En Progreso') . '</td>
                <td style="text-align: center;">' . ($workday['autoclose'] ? 'Sí' : 'No') . '</td>
            </tr>';
        }

        // Totales - colspan condicional
        $totalColspan           = $showUserColumn ? 4 : 3;
        $totalMinutesPdf        = round($totalHours * 60);
        $totalHoursFormatted    = floor($totalMinutesPdf / 60) . ':' . str_pad($totalMinutesPdf % 60, 2, '0', STR_PAD_LEFT);
        $totalBreakMinsPdf      = round($totalBreak * 60);
        $totalBreakFormatted    = floor($totalBreakMinsPdf / 60) . ':' . str_pad($totalBreakMinsPdf % 60, 2, '0', STR_PAD_LEFT);
        $totalOvertimeMinsPdf   = round($totalOvertime * 60);
        $totalOvertimeFormatted = floor($totalOvertimeMinsPdf / 60) . ':' . str_pad($totalOvertimeMinsPdf % 60, 2, '0', STR_PAD_LEFT);
        $html .= '
            <tr class="total-row">
                <td colspan="' . $totalColspan . '" style="text-align: center;"><strong>TOTALES</strong></td>
                <td style="text-align: center;"><strong>' . $totalHoursFormatted . '</strong></td>
                <td style="text-align: center;"><strong>' . $totalBreakFormatted . '</strong></td>
                <td style="text-align: center;"><strong>' . $totalOvertimeFormatted . '</strong></td>
                <td style="text-align: center;"><strong>-</strong></td>
                <td colspan="2" style="text-align: center;"><strong></strong></td>
            </tr>
        </tbody>
        </table>';

        // Pie
        $html .= '
        <div style="margin-top: 20px; font-size: 8px; color: #666; text-align: center;">
            <p><em>Documento generado por ' . session('user_name') . ' el ' . date('d/m/Y H:i:s') . '</em></p>
        </div>';

        return $html;
    }


    // =================================================================================
    // Métodos auxiliares
    // =================================================================================

    // =================================================================================
    // Método auxiliar para saber si hay una jornada abierta
    // =================================================================================
    private function getOpenWorkdayType($userId)
    {
        // Buscar el último registro del usuario seleccionando solo campos necesarios
        $lastRecord = $this->workdayModel
            ->select('event_type, workday_date')
            ->where('user_id', $userId)
            ->orderBy('event_time', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        //Si no hay registros, devolver un array con event_type 'stop'
        if (!$lastRecord) {
            return ['event_type' => 'stop'];
        }
        return $lastRecord;
    }

    // =================================================================================
    // Método helper para calcular datos de una jornada específica
    // =================================================================================

    // =================================================================================
    // Método auxiliar para cerrar automáticamente una jornada si excede las horas máximas
    // =================================================================================
    private function autoCloseWorkday($userId, $workdayDate)
    {
        // Verificar si ya existe un registro 'stop' para esta jornada para evitar dobles cierres automáticos
        $existingOut = $this->workdayModel
            ->where('user_id', $userId)
            ->where('workday_date', $workdayDate)
            ->where('event_type', 'stop')
            ->first();

        // Si ya existe un registro de salida, no hacer nada
        if ($existingOut) {
            return false;
        }

        // Obtener las horas máximas diarias del usuario
        $user = $this->userModel->find($userId);
        $maxDailyHours = $user['max_daily_hours'] ?? 8; // Valor por defecto de 8 horas si no está definido

        // Obtener eventos de la jornada específica
        $events = $this->workdayModel
            ->where('user_id', $userId)
            ->where('workday_date', $workdayDate)
            ->orderBy('event_time', 'ASC')
            ->findAll();

        // Calcular horas trabajadas usando el método existente
        $workdayData = calculate_workday_data($workdayDate, $events, $maxDailyHours);

        // Verificar si excede las horas máximas
        if ($workdayData['total_hours'] >= $maxDailyHours) {
            // Obtener horas diarias del usuario para cumplimiento legislativo español
            $dailyHours = $user['daily_hours'] ?? 8;

            // Calcular hora de salida como entrada + horas diarias (cumplimiento legislativo)
            $startDateTime = $workdayDate . ' ' . $workdayData['start_time'] . ':00';
            $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime) + ($dailyHours * 3600) + 59); // +59 segundos para redondear

            // Crear registro de salida automática
            $data = [
                'user_id' => $userId,
                'workday_date' => $workdayDate,
                'event_type' => 'stop',
                'event_time' => $endDateTime,
                'latitude' => null,  // No hay coordenadas para cierre automático
                'longitude' => null,
                'autoclose' => true  // Marcar como cierre automático
            ];

            // Insertar registro
            if ($this->workdayModel->insert($data)) {
                return true; // Éxito
            } else {
                return false; // Error al guardar
            }
        }
        return false; // No excedió las horas
    }

}
