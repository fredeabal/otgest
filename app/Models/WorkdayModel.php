<?php
// =================================================================================
// Modelo: WorkdayModel
// =================================================================================

namespace App\Models;

use CodeIgniter\Model;

class WorkdayModel extends Model
{
    // Configuración básica del modelo
    protected $table      = 'workday';
    protected $primaryKey = 'id';

    // Campos permitidos para operaciones CRUD
    protected $allowedFields = [
        'user_id',
        'workday_date',
        'event_type',
        'event_time',
        'latitude',
        'longitude',
        'daily_hours',
        'max_daily_hours',
        'autoclose',
        'comments'
    ];

    // Configuración de soft delete
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    // Configuración de timestamps automáticos
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // =================================================================================
    // Método auxiliar para cerrar automáticamente una jornada si excede las horas máximas
    // o si es de un día anterior (jornada olvidada)
    // =================================================================================
    public function autoCloseWorkday($userId, $workdayDate)
    {
        // Verificar si ya existe un registro 'stop' para este día
        $existingOut = $this->where('user_id', $userId)
            ->where('workday_date', $workdayDate)
            ->where('event_type', 'stop')
            ->first();

        // Si ya existe un registro de salida, no hacer nada
        if ($existingOut) {
            return false;
        }

        // Obtener el usuario y sus datos de horas
        $userModel = new \App\Models\UsersModel();
        $user = $userModel->find($userId);
        if (!$user) {
            return false;
        }
        $dailyHours = $user['daily_hours'] ?? 8;
        $maxDailyHours = $user['max_daily_hours'] ?? 8;

        // Obtener todos los eventos del día
        $events = $this->where('user_id', $userId)
            ->where('workday_date', $workdayDate)
            ->orderBy('event_time', 'ASC')
            ->findAll();

        if (empty($events)) {
            return false;
        }

        helper('workday');
        $workdayData = calculate_workday_data($workdayDate, $events, $maxDailyHours);

        $isPastDay = (strtotime($workdayDate) < strtotime(date('Y-m-d')));

        // Cerrar si excede las horas máximas o si la jornada es de un día anterior
        if ($workdayData['total_hours'] >= $maxDailyHours || $isPastDay) {
            $startDateTime = $workdayDate . ' ' . $workdayData['start_time'] . ':00';
            $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime) + ($dailyHours * 3600) + 59);

            // Evitar que el cierre automático sea posterior al final del día (23:59:59)
            $dayEnd = strtotime($workdayDate . ' 23:59:59');
            if (strtotime($endDateTime) > $dayEnd) {
                $endDateTime = $workdayDate . ' 23:59:59';
            }

            // Si es ayer o más antiguo, pero por alguna razón $endDateTime es posterior al momento actual del servidor
            // (no debería pasar, pero por seguridad), lo limitamos a la hora actual.
            if (strtotime($endDateTime) > time()) {
                $endDateTime = date('Y-m-d H:i:s');
            }

            $data = [
                'user_id' => $userId,
                'workday_date' => $workdayDate,
                'event_type' => 'stop',
                'event_time' => $endDateTime,
                'latitude' => null,
                'longitude' => null,
                'autoclose' => true
            ];

            $inserted = $this->insert($data);
            
            if ($inserted) {
                $this->sendAutocloseEmail($user, $workdayDate, $endDateTime);
                return true;
            }
            return false;
        }

        return false;
    }

    // =================================================================================
    // Método auxiliar para buscar y cerrar jornadas de días anteriores sin cerrar
    // =================================================================================
    public function autoClosePastWorkdays($userId = null)
    {
        $query = $this->where('workday_date <', date('Y-m-d'))
            ->whereIn('event_type', ['start', 'pause', 'resume']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $pastOpenWorkdays = $query->whereNotIn('workday_date', function($builder) use ($userId) {
                $sub = $builder->select('workday_date')->from('workday')->where('event_type', 'stop');
                if ($userId) {
                    $sub->where('user_id', $userId);
                }
                return $sub;
            })
            ->select('user_id, workday_date')
            ->distinct();
        
        $pastOpenWorkdays = $pastOpenWorkdays->findAll();

        foreach ($pastOpenWorkdays as $workday) {
            $this->autoCloseWorkday($workday['user_id'], $workday['workday_date']);
        }
    }

    // =================================================================================
    // Método auxiliar para enviar email al usuario cuando se cierra su jornada automáticamente
    // =================================================================================
    private function sendAutocloseEmail($user, $workdayDate, $endDateTime)
    {
        helper('email');
        $emailService = get_configured_email();

        $date = date('d/m/Y', strtotime($workdayDate));
        $time = date('H:i', strtotime($endDateTime));

        $content = '
            <p style="margin: 0 0 10px 0; font-size: 16px; color: #5a6a85; -webkit-text-fill-color: #5a6a85;"><strong>Día:</strong> ' . $date . '</p>
            <p style="margin: 0 0 10px 0; font-size: 16px; color: #5a6a85; -webkit-text-fill-color: #5a6a85;"><strong>Hora de cierre:</strong> ' . $time . '</p>
            <p style="margin: 15px 0 0 0; font-size: 13px; color: #8c98a4; -webkit-text-fill-color: #8c98a4;">Si se trata de un error o has olvidado fichar tu salida a tu hora real, por favor, contacta con tu administrador para que ajuste el registro de ese día.</p>
        ';

        $intro = "Hola " . $user['name'] . ",<br>Te informamos de que tu jornada laboral ha superado el límite de horas máximas permitidas y el sistema la ha <strong>cerrado automáticamente</strong>.";

        $emailBody = view('emails/template', [
            'title' => 'Jornada cerrada automáticamente',
            'intro' => $intro,
            'content' => $content,
            'buttonText' => 'Ver mis registros',
            'buttonUrl' => site_url('workdays/my-records')
        ]);

        $emailService->setTo($user['email']);
        $emailService->setSubject($subject);
        $emailService->setMessage($emailBody);

        if (!$emailService->send()) {
            log_message('error', 'No se pudo enviar el correo de auto-cierre de jornada a ' . $user['email']);
        }
    }
}