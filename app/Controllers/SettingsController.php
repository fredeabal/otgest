<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use CodeIgniter\I18n\Time;

class SettingsController extends BaseController
{
    protected $companyModel;

    public function __construct()
    {
        $this->companyModel = new CompanyModel();
    }

    // =================================================================================
    // VISTAS
    // =================================================================================
    public function company()
    {
        if (!has_permission('admin.company')) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        $company = $this->companyModel->getCompany();
        if ($company && $company['updated_by']) {
            $userModel = new \App\Models\UsersModel();
            $updater = $userModel->find($company['updated_by']);
            $company['updated_by_name'] = $updater ? $updater['name'] : 'Usuario desconocido';
        }
        
        $data['company'] = $company;
        $data['title'] = 'Configuración de Empresa';

        echo view('template/header', $data);
        echo view('settings/company', $data);
        echo view('template/footer');
    }

    public function smtp()
    {
        if (!has_permission('admin.company')) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        $data['company'] = $this->companyModel->getCompany();
        $data['title'] = 'Configuración SMTP';

        echo view('template/header', $data);
        echo view('settings/smtp', $data);
        echo view('template/footer');
    }

    public function maintenance()
    {
        if (!has_permission('admin.company')) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        $data['title'] = 'Mantenimiento del Sistema';

        echo view('template/header', $data);
        echo view('settings/maintenance', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // ACCIONES
    // =================================================================================
    public function updateCompany()
    {
        if (!has_permission('admin.company')) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        $rules = [
            'cif' => ['label' => 'CIF', 'rules' => 'required|max_length[20]'],
            'name' => ['label' => 'Nombre', 'rules' => 'required|max_length[255]'],
            'address' => ['label' => 'Dirección', 'rules' => 'permit_empty|max_length[255]'],
            'postal_code' => ['label' => 'Código Postal', 'rules' => 'permit_empty|max_length[10]'],
            'phone' => ['label' => 'Teléfono', 'rules' => 'permit_empty|max_length[20]'],
            'email' => ['label' => 'Email', 'rules' => 'permit_empty|valid_email|max_length[255]'],
            'website' => ['label' => 'Página Web', 'rules' => 'permit_empty|valid_url|max_length[255]']
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $companyData = [
            'cif' => $this->request->getPost('cif'),
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address') ?: null,
            'postal_code' => $this->request->getPost('postal_code') ?: null,
            'phone' => $this->request->getPost('phone') ?: null,
            'email' => $this->request->getPost('email') ?: null,
            'website' => $this->request->getPost('website') ?: null,
            'updated_by' => session()->get('user_id'),
        ];

        $this->companyModel->saveCompany($companyData);

        return redirect()->to('/settings/company')->with('success', 'Datos de la empresa actualizados correctamente.');
    }

    public function updateSmtp()
    {
        if (!has_permission('admin.company')) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        $rules = [
            'smtp_host' => ['label' => 'Host SMTP', 'rules' => 'permit_empty|max_length[255]'],
            'smtp_port' => ['label' => 'Puerto SMTP', 'rules' => 'permit_empty|numeric'],
            'smtp_user' => ['label' => 'Usuario SMTP', 'rules' => 'permit_empty|max_length[255]'],
            'smtp_pass' => ['label' => 'Contraseña SMTP', 'rules' => 'permit_empty|max_length[255]'],
            'smtp_crypto' => ['label' => 'Cifrado SMTP', 'rules' => 'permit_empty|in_list[ssl,tls,none]'],
            'smtp_from_email' => ['label' => 'Email Remitente', 'rules' => 'permit_empty|valid_email|max_length[255]'],
            'smtp_from_name' => ['label' => 'Nombre Remitente', 'rules' => 'permit_empty|max_length[255]']
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $companyData = [
            'smtp_host' => $this->request->getPost('smtp_host') ?: null,
            'smtp_port' => $this->request->getPost('smtp_port') ?: null,
            'smtp_user' => $this->request->getPost('smtp_user') ?: null,
            'smtp_crypto' => $this->request->getPost('smtp_crypto') ?: null,
            'smtp_from_email' => $this->request->getPost('smtp_from_email') ?: null,
            'smtp_from_name' => $this->request->getPost('smtp_from_name') ?: null,
            'updated_by' => session()->get('user_id'),
        ];

        $newSmtpPass = $this->request->getPost('smtp_pass');
        if (!empty($newSmtpPass)) {
            $encrypter = service('encrypter');
            $companyData['smtp_pass'] = base64_encode($encrypter->encrypt($newSmtpPass));
        }

        $this->companyModel->saveCompany($companyData);

        return redirect()->to('/settings/smtp')->with('success', 'Configuración de correo actualizada correctamente.');
    }

    public function testSmtp()
    {
        if (!has_permission('admin.company')) {
            return $this->response->setJSON(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.']);
        }

        helper('email');
        $email = get_configured_email();
        $userEmail = session()->get('user_email');

        if (empty($userEmail)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tu usuario no tiene un correo electrónico configurado para recibir la prueba.']);
        }

        $email->setTo($userEmail);
        $email->setSubject('Prueba de configuración SMTP - OtGest');
        $email->setMessage('Este es un correo de prueba para verificar que la configuración SMTP de OtGest funciona correctamente.');

        if ($email->send()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Correo de prueba enviado correctamente a ' . $userEmail]);
        } else {
            $data = $email->printDebugger(['headers', 'subject', 'body']);
            return $this->response->setJSON(['success' => false, 'message' => 'Error al enviar el correo. Verifica los datos.', 'debug' => $data]);
        }
    }

    public function clearSessions()
    {
        if (!has_permission('admin.company')) { return redirect()->to('/')->with('errors', ['No tienes permisos.']); }
        $sessionPath = WRITEPATH . 'session';
        $currentSessionId = session_id();
        
        if (is_dir($sessionPath)) {
            foreach (glob($sessionPath . '/*') as $file) { 
                if (is_file($file)) {
                    // Skip current session
                    if (strpos(basename($file), $currentSessionId) !== false) {
                        continue;
                    }
                    unlink($file);
                }
            }
        }
        return redirect()->to('/settings/maintenance')->with('success', 'Sesiones inactivas limpiadas correctamente.');
    }

    public function clearCache()
    {
        if (!has_permission('admin.company')) { return redirect()->to('/')->with('errors', ['No tienes permisos.']); }
        $cachePath = WRITEPATH . 'cache';
        if (is_dir($cachePath)) {
            foreach (glob($cachePath . '/*') as $file) { if (is_file($file)) unlink($file); }
        }
        return redirect()->to('/settings/maintenance')->with('success', 'Cache limpiado correctamente.');
    }

    public function clearLogs()
    {
        if (!has_permission('admin.company')) { return redirect()->to('/')->with('errors', ['No tienes permisos.']); }
        $logsPath = WRITEPATH . 'logs';
        if (is_dir($logsPath)) {
            foreach (glob($logsPath . '/*') as $file) { if (is_file($file)) unlink($file); }
        }
        return redirect()->to('/settings/maintenance')->with('success', 'Logs limpiados correctamente.');
    }

    public function clearDebugbar()
    {
        if (!has_permission('admin.company')) { return redirect()->to('/')->with('errors', ['No tienes permisos.']); }
        $debugbarPath = WRITEPATH . 'debugbar';
        if (is_dir($debugbarPath)) {
            foreach (glob($debugbarPath . '/*') as $file) { if (is_file($file)) unlink($file); }
        }
        return redirect()->to('/settings/maintenance')->with('success', 'Debugbar limpiado correctamente.');
    }

    public function clearAll()
    {
        if (!has_permission('admin.company')) { return redirect()->to('/')->with('errors', ['No tienes permisos.']); }
        
        $paths = ['session', 'cache', 'logs', 'debugbar'];
        $currentSessionId = session_id();
        
        foreach ($paths as $path) {
            $fullPath = WRITEPATH . $path;
            if (is_dir($fullPath)) {
                foreach (glob($fullPath . '/*') as $file) {
                    if (is_file($file)) {
                        if ($path === 'session' && strpos(basename($file), $currentSessionId) !== false) {
                            continue;
                        }
                        unlink($file);
                    }
                }
            }
        }
        
        return redirect()->to('/settings/maintenance')->with('success', 'Mantenimiento completo ejecutado correctamente.');
    }

    public function runTests()
    {
        if (!has_permission('admin.company')) { return redirect()->to('/')->with('errors', ['No tienes permisos.']); }
        
        $output = [];
        $return_var = 0;
        // Cambiar al directorio raíz para que PHPUnit encuentre phpunit.xml.dist
        exec('cd ' . ROOTPATH . ' && ./vendor/bin/phpunit 2>&1', $output, $return_var);
        
        $outputStr = implode("\n", $output);
        session()->setFlashdata('test_output', $outputStr);
        
        if ($return_var === 0 || strpos($outputStr, 'OK') !== false) {
            return redirect()->to('/settings/maintenance')->with('success', 'Pruebas unitarias ejecutadas correctamente.');
        } else {
            return redirect()->to('/settings/maintenance')->with('errors', ['Algunas pruebas han fallado. Revisa el registro.']);
        }
    }

    public function downloadDatabase()
    {
        if (!has_permission('admin.company')) { return redirect()->to('/')->with('errors', ['No tienes permisos.']); }
        $dbPath = WRITEPATH . 'database/database.sqlite';
        if (file_exists($dbPath)) {
            return $this->response->download($dbPath, null)->setFileName('otgest_backup_' . date('Y-m-d_H-i') . '.sqlite');
        } else {
            return redirect()->back()->with('errors', ['El archivo de base de datos no se encuentra.']);
        }
    }

    public function restoreDatabase()
    {
        if (!has_permission('admin.company')) { return redirect()->to('/')->with('errors', ['No tienes permisos.']); }
        
        $file = $this->request->getFile('backup_file');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('errors', ['Por favor, selecciona un archivo válido.']);
        }

        $extension = $file->getClientExtension();
        if (!in_array(strtolower($extension), ['db', 'sqlite'])) {
            return redirect()->back()->with('errors', ['El archivo debe tener extensión .db o .sqlite']);
        }
        
        $dbPath = WRITEPATH . 'database/database.sqlite';
        
        try {
            // Reemplazar la base de datos actual con el archivo subido
            $file->move(WRITEPATH . 'database', 'database.sqlite', true);
            
            // Limpiar sesiones para forzar a reconectar y evitar conflictos de IDs o roles
            $this->clearAll();
            
            // Redirigir al login
            return redirect()->to('login')->with('success', 'Base de datos restaurada con éxito. Por favor, inicia sesión nuevamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('errors', ['Hubo un error al restaurar la base de datos: ' . $e->getMessage()]);
        }
    }
}
