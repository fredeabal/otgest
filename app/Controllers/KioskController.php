<?php

namespace App\Controllers;

use App\Models\WorkdayModel;
use App\Models\UsersModel;

class KioskController extends BaseController
{
    protected $workdayModel;
    protected $userModel;

    public function __construct()
    {
        $this->workdayModel = new WorkdayModel();
        $this->userModel = new UsersModel();
    }

    // =================================================================================
    // Mostrar pantalla de espera del kiosco
    // =================================================================================
    public function index()
    {
        return view('kiosk/index');
    }

    // =================================================================================
    // Procesar el escaneo de la tarjeta
    // =================================================================================
    public function scan()
    {
        $token = $this->request->getPost('token');
        if (empty($token)) {
            return redirect()->back()->with('error', 'Por favor, escanea tu tarjeta correctamente.');
        }

        $user = $this->userModel->findByKioskToken($token);
        if (!$user) {
            return redirect()->back()->with('error', 'Tarjeta no reconocida o usuario inactivo.');
        }

        $openWorkday = $this->getOpenWorkdayType($user['id']);
        
        $currentDate = date('Y-m-d');
        $currentDateTime = date('Y-m-d H:i:s');
        $latitude = $this->request->getPost('latitud');
        $longitude = $this->request->getPost('longitud');

        // Check for same day duplicate
        if (isset($openWorkday['workday_date']) && $openWorkday['workday_date'] === $currentDate && $openWorkday['event_type'] === 'stop') {
             return redirect()->back()->with('error', "{$user['name']}, ya finalizaste tu jornada de hoy.");
        }

        $eventType = $openWorkday['event_type'] ?? 'stop';

        // Auto-decisión
        if ($eventType === 'stop') {
            // Entrada
            $this->workdayModel->insert([
                'user_id' => $user['id'],
                'workday_date' => $currentDate,
                'event_type' => 'start',
                'event_time' => $currentDateTime,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'comments' => 'Fichaje desde kiosco',
                'daily_hours' => $user['daily_hours'] ?? 8,
                'max_daily_hours' => $user['max_daily_hours'] ?? 12,
            ]);
            return redirect()->back()->with('message', "¡Hola {$user['name']}! Entrada registrada a las " . date('H:i'));
        } elseif ($eventType === 'pause') {
            // Fin de pausa
            $this->workdayModel->insert([
                'user_id' => $user['id'],
                'workday_date' => $openWorkday['workday_date'],
                'event_type' => 'resume',
                'event_time' => $currentDateTime,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'comments' => 'Fichaje desde kiosco',
                'daily_hours' => $user['daily_hours'] ?? 8,
                'max_daily_hours' => $user['max_daily_hours'] ?? 12,
            ]);
            return redirect()->back()->with('message', "¡Hola {$user['name']}! Pausa terminada a las " . date('H:i'));
        } else {
            // Está trabajando ('start' o 'resume'), preguntar qué hacer
            return redirect()->to(base_url("kiosk/action/{$token}"));
        }
    }

    // =================================================================================
    // Mostrar opciones cuando el usuario está trabajando
    // =================================================================================
    public function action($token)
    {
        $user = $this->userModel->findByKioskToken($token);
        if (!$user) {
            return redirect()->to(base_url('kiosk'))->with('error', 'Sesión de kiosco inválida.');
        }
        
        // Comprobar si todavía está trabajando
        $openWorkday = $this->getOpenWorkdayType($user['id']);
        if (!in_array($openWorkday['event_type'], ['start', 'resume'])) {
            return redirect()->to(base_url('kiosk'));
        }

        $data = [
            'user' => $user,
            'token' => $token
        ];

        return view('kiosk/action', $data);
    }

    // =================================================================================
    // Procesar la acción seleccionada
    // =================================================================================
    public function processAction()
    {
        $token = $this->request->getPost('token');
        $action = $this->request->getPost('action'); // 'pause' or 'stop'
        
        $user = $this->userModel->findByKioskToken($token);
        if (!$user) {
            return redirect()->to(base_url('kiosk'))->with('error', 'Error al procesar la acción.');
        }

        $openWorkday = $this->getOpenWorkdayType($user['id']);
        if (!in_array($openWorkday['event_type'], ['start', 'resume'])) {
            return redirect()->to(base_url('kiosk'))->with('error', 'El estado de la jornada ha cambiado.');
        }

        $currentDateTime = date('Y-m-d H:i:s');
        $latitude = $this->request->getPost('latitud');
        $longitude = $this->request->getPost('longitud');
        
        if ($action === 'pause') {
            $this->workdayModel->insert([
                'user_id' => $user['id'],
                'workday_date' => $openWorkday['workday_date'],
                'event_type' => 'pause',
                'event_time' => $currentDateTime,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'comments' => 'Pausa desde kiosco',
                'daily_hours' => $user['daily_hours'] ?? 8,
                'max_daily_hours' => $user['max_daily_hours'] ?? 12,
            ]);
            return redirect()->to(base_url('kiosk'))->with('message', "¡Pausa iniciada, {$user['name']}! Disfruta tu descanso.");
        } elseif ($action === 'stop') {
            $this->workdayModel->insert([
                'user_id' => $user['id'],
                'workday_date' => $openWorkday['workday_date'],
                'event_type' => 'stop',
                'event_time' => $currentDateTime,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'comments' => 'Fin de jornada desde kiosco',
                'daily_hours' => $user['daily_hours'] ?? 8,
                'max_daily_hours' => $user['max_daily_hours'] ?? 12,
            ]);
            return redirect()->to(base_url('kiosk'))->with('message', "¡Jornada finalizada, {$user['name']}! Hasta la próxima.");
        }

        return redirect()->to(base_url('kiosk'));
    }

    // =================================================================================
    // Helper: Obtener estado actual
    // =================================================================================
    private function getOpenWorkdayType($userId)
    {
        $lastRecord = $this->workdayModel
            ->select('event_type, workday_date')
            ->where('user_id', $userId)
            ->orderBy('workday_date', 'DESC')
            ->orderBy('event_time', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        if (!$lastRecord) {
            return ['event_type' => 'stop'];
        }

        // Si la jornada anterior quedó abierta de ayer o antes, para el kiosco cuenta como finalizada
        // (El sistema la autocerrará cuando se listen los registros)
        if ($lastRecord['event_type'] !== 'stop' && $lastRecord['workday_date'] < date('Y-m-d')) {
            return ['event_type' => 'stop'];
        }

        return $lastRecord;
    }
}
