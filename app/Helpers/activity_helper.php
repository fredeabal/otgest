<?php

if (! function_exists('log_activity')) {
    /**
     * Registra una acción del usuario en el sistema.
     *
     * @param string $module El módulo donde ocurre la acción (ej. 'Workdays', 'Users')
     * @param string $action El tipo de acción (ej. 'CREATE', 'UPDATE', 'DELETE')
     * @param string $description Descripción detallada de lo que ocurrió
     * @param int|null $userId ID del usuario (opcional, por defecto toma el de sesión)
     */
    function log_activity($module, $action, $description, $userId = null)
    {
        $request = \Config\Services::request();
        $session = session();
        
        $userId = $userId ?? $session->get('user_id');
        
        $data = [
            'user_id'     => $userId,
            'module'      => $module,
            'action'      => $action,
            'description' => $description,
            'ip_address'  => $request->getIPAddress(),
            'created_at'  => \CodeIgniter\I18n\Time::now('Europe/Madrid', 'es_ES')->toDateTimeString(),
        ];
        
        $model = new \App\Models\ActivityLogModel();
        $model->insert($data);
    }
}
