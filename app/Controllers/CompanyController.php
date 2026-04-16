<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use CodeIgniter\I18n\Time;

class CompanyController extends BaseController
{
    protected $companyModel;

    public function __construct()
    {
        $this->companyModel = new CompanyModel();
    }

    // =================================================================================
    // Mostrar formulario de edición de empresa (solo admin)
    // =================================================================================
    public function edit()
    {
        // Verificar que sea administrador
        if (session()->get('user_role') != 1) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        $company = $this->companyModel->getCompany();
        if ($company) {
            // Obtener el nombre del usuario que actualizó por última vez
            if ($company['updated_by']) {
                $userModel = new \App\Models\UsersModel();
                $updater = $userModel->find($company['updated_by']);
                $company['updated_by_name'] = $updater ? $updater['name'] : 'Usuario desconocido';
            }
        }
        $data['company'] = $company;
        $data['title'] = 'Editar Empresa';

        echo view('template/header', $data);
        echo view('company/edit', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Actualizar datos de la empresa
    // =================================================================================
    public function update()
    {
        // Verificar que sea administrador
        if (session()->get('user_role') != 1) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        // Reglas de validación
        $rules = [
            'cif' => [
                'label' => 'CIF',
                'rules' => 'required|max_length[20]'
            ],
            'name' => [
                'label' => 'Nombre',
                'rules' => 'required|max_length[255]'
            ],
            'address' => [
                'label' => 'Dirección',
                'rules' => 'permit_empty|max_length[255]'
            ],
            'postal_code' => [
                'label' => 'Código Postal',
                'rules' => 'permit_empty|max_length[10]'
            ],
            'phone' => [
                'label' => 'Teléfono',
                'rules' => 'permit_empty|max_length[20]'
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'permit_empty|valid_email|max_length[255]'
            ],
            'website' => [
                'label' => 'Página Web',
                'rules' => 'permit_empty|valid_url|max_length[255]'
            ],
            'smtp_host' => [
                'label' => 'Host SMTP',
                'rules' => 'permit_empty|max_length[255]'
            ],
            'smtp_port' => [
                'label' => 'Puerto SMTP',
                'rules' => 'permit_empty|numeric'
            ],
            'smtp_user' => [
                'label' => 'Usuario SMTP',
                'rules' => 'permit_empty|max_length[255]'
            ],
            'smtp_pass' => [
                'label' => 'Contraseña SMTP',
                'rules' => 'permit_empty|max_length[255]'
            ],
            'smtp_crypto' => [
                'label' => 'Cifrado SMTP',
                'rules' => 'permit_empty|in_list[ssl,tls,none]'
            ],
            'smtp_from_email' => [
                'label' => 'Email Remitente',
                'rules' => 'permit_empty|valid_email|max_length[255]'
            ],
            'smtp_from_name' => [
                'label' => 'Nombre Remitente',
                'rules' => 'permit_empty|max_length[255]'
            ]
        ];

        // Validar datos
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Preparar datos para guardar
        $companyData = [
            'cif' => $this->request->getPost('cif'),
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address') ?: null,
            'postal_code' => $this->request->getPost('postal_code') ?: null,
            'phone' => $this->request->getPost('phone') ?: null,
            'email' => $this->request->getPost('email') ?: null,
            'website' => $this->request->getPost('website') ?: null,
            'smtp_host' => $this->request->getPost('smtp_host') ?: null,
            'smtp_port' => $this->request->getPost('smtp_port') ?: null,
            'smtp_user' => $this->request->getPost('smtp_user') ?: null,
            'smtp_crypto' => $this->request->getPost('smtp_crypto') ?: null,
            'smtp_from_email' => $this->request->getPost('smtp_from_email') ?: null,
            'smtp_from_name' => $this->request->getPost('smtp_from_name') ?: null,
            'updated_by' => session()->get('user_id'),
        ];

        // Encriptar contraseña solo si se ha proporcionado una nueva
        $newSmtpPass = $this->request->getPost('smtp_pass');
        if (!empty($newSmtpPass)) {
            $encrypter = service('encrypter');
            $companyData['smtp_pass'] = base64_encode($encrypter->encrypt($newSmtpPass));
        }

        // Guardar empresa
        $this->companyModel->saveCompany($companyData);

        return redirect()->to('/company/edit')->with('success', 'Datos de la empresa actualizados correctamente.');
    }

    // =================================================================================
    // Limpiar sesiones
    // =================================================================================
    public function clearSessions()
    {
        // Verificar que sea administrador
        if (session()->get('user_role') != 1) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        // Limpiar archivos de sesión
        $sessionPath = WRITEPATH . 'session';
        if (is_dir($sessionPath)) {
            $files = glob($sessionPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        return redirect()->to('/company/edit')->with('success', 'Sesiones limpiadas correctamente.');
    }

    // =================================================================================
    // Limpiar cache
    // =================================================================================
    public function clearCache()
    {
        // Verificar que sea administrador
        if (session()->get('user_role') != 1) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        // Limpiar archivos de cache
        $cachePath = WRITEPATH . 'cache';
        if (is_dir($cachePath)) {
            $files = glob($cachePath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        return redirect()->to('/company/edit')->with('success', 'Cache limpiado correctamente.');
    }

    // =================================================================================
    // Limpiar logs
    // =================================================================================
    public function clearLogs()
    {
        // Verificar que sea administrador
        if (session()->get('user_role') != 1) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        // Limpiar archivos de logs
        $logsPath = WRITEPATH . 'logs';
        if (is_dir($logsPath)) {
            $files = glob($logsPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        return redirect()->to('/company/edit')->with('success', 'Logs limpiados correctamente.');
    }

    // =================================================================================
    // Limpiar debugbar
    // =================================================================================
    public function clearDebugbar()
    {
        // Verificar que sea administrador
        if (session()->get('user_role') != 1) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        // Limpiar archivos de debugbar
        $debugbarPath = WRITEPATH . 'debugbar';
        if (is_dir($debugbarPath)) {
            $files = glob($debugbarPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        return redirect()->to('/company/edit')->with('success', 'Debugbar limpiado correctamente.');
    }

    // =================================================================================
    // Probar configuración SMTP (AJAX)
    // =================================================================================
    public function testSmtp()
    {
        // Verificar que sea administrador
        if (session()->get('user_role') != 1) {
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
            // Obtenemos el log de depuración
            $data = $email->printDebugger(['headers', 'subject', 'body']);
            return $this->response->setJSON(['success' => false, 'message' => 'Error al enviar el correo. Verifica los datos.', 'debug' => $data]);
        }
    }

    /**
     * Descargar la base de datos SQLite
     */
    public function downloadDatabase()
    {
        // Verificar que sea administrador
        if (session()->get('user_role') != 1) {
            return redirect()->to('/')->with('errors', ['No tienes permisos para acceder a esta sección.']);
        }

        $dbPath = WRITEPATH . 'database/database.sqlite';

        if (file_exists($dbPath)) {
            return $this->response->download($dbPath, null)->setFileName('otgest_backup_' . date('Y-m-d_H-i') . '.sqlite');
        } else {
            return redirect()->back()->with('errors', ['El archivo de base de datos no se encuentra.']);
        }
    }
}