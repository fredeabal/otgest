<?php
// =================================================================================
// Controlador: AuthController
// =================================================================================

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\CompanyModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class AuthController extends BaseController
{
    protected $usersModel;
    protected $rolesModel;
    protected $companyModel;

    public function __construct()
    {
        // Instancia de los modelos
        $this->usersModel = new UsersModel();
        $this->rolesModel = new RolesModel();
        $this->companyModel = new CompanyModel();
    }

    // =================================================================================
    // Login de usuario (GET)
    // =================================================================================
    public function login()
    {
        $data['title'] = 'Iniciar sesión';
        // Si el usuario ya está logueado, redirigir siempre a dashboard único
        if(session()->get('isLoggedIn')) {
            return redirect()->to('/user/dashboard');
        }
        return view('auth/login', $data);
    }

    // =================================================================================
    // Login de usuario (POST)
    // =================================================================================
    public function loginPost()
    {
        $throttler = \Config\Services::throttler();
        
        // Permitir 5 intentos por minuto (60 segundos) por cada IP
        // Usamos md5() para evitar caracteres reservados como ':' en direcciones IPv6 (ej: ::1)
        $ipAddress = md5($this->request->getIPAddress());
        if ($throttler->check($ipAddress, 5, MINUTE) === false) {
            return redirect()->back()->with('errors', ['Demasiados intentos de inicio de sesión. Por favor, espera un minuto.']);
        }

        // Procesar el formulario
        $rules = [
            'email'    => [
                'label' => 'correo electrónico',
                'rules' => 'required|valid_email',
            ],
            'password' => [
                'label' => 'contraseña',
                'rules' => 'required|min_length[6]',
            ],
        ];

        // Validar los datos del formulario
        if (! $this->validate($rules)) {
            // Si falla la validación, redirigir manteniendo errores y datos
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }
        
        // Buscar usuario activo por email (lo busca en el modelo UsersModel)
        $user = $this->usersModel->findActiveByEmail($this->request->getPost('email'));
        
        // Si el usuario existe y la contraseña es correcta
        if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
            // Guardar datos mínimos en sesión
            session()->set([
                'user_id' => $user['id'],
                'user_name' => $user['name'],
                'user_email' => $user['email'],
                'user_role' => $user['role_id'],
                'user_role_name' => $user['role_name'],
                'user_avatar' => $user['avatar'],
                'user_theme' => $user['theme'],
                'isLoggedIn' => true,
                'user_permissions' => !empty($user['permissions']) 
                                        ? json_decode($user['permissions'], true) 
                                        : (isset($user['role_permissions']) ? json_decode($user['role_permissions'], true) : []),
            ]);

            // Actualizar último login
            $this->usersModel->update($user['id'], [
                'last_login' => Time::now('Europe/Madrid', 'es_ES'),
                'reset_token' => null,
                'reset_token_expiration' => null
            ]);
            
            // Redirigir siempre a dashboard único
            return redirect()->to('/user/dashboard');
        } else {
            // Usuario o contraseña incorrectos
            return redirect()->back()->with('errors', ['Correo o contraseña incorrectos.']);
        }
    }

    // =================================================================================
    // Logout de usuario
    // =================================================================================
    public function logout()
    {
        // Destruir la sesión
        session()->destroy();
        return redirect()->to('/login');
    }

    // =================================================================================
    // Solicitud de recuperación de contraseña (GET)
    // =================================================================================
    public function forgotPassword()
    {
        $data['title'] = 'Recuperar contraseña';

        return view('auth/forgot_password', $data);
    }

    // =================================================================================
    // Solicitud de recuperación de contraseña (POST)
    // =================================================================================
    public function forgotPasswordPost()
    {
        $rules = [
            'email' => [
                'label' => 'correo electrónico',
                'rules' => 'required|valid_email',
            ],
        ];

        // Validar los datos del formulario
        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        // Buscar usuario activo por email (en UsersModel)
        $user = $this->usersModel->findActiveByEmail($this->request->getPost('email'));
        // Si el usuario existe
        if ($user) {
            // Generar token seguro y guardar expiración
            helper('text');
            $token = random_string('alnum', 64);
            $expiration = Time::now('Europe/Madrid', 'es_ES')->addMinutes(30);
            $this->usersModel->update($user['id'], [
                'reset_token' => $token,
                'reset_token_expiration' => $expiration,
            ]);

            // Enviar correo de recuperación
            helper('email');
            $emailService = get_configured_email();
            $link = site_url('reset-password/' . $token);

            $company = $this->companyModel->getCompany();
            $companyName = $company ? $company['name'] : 'OtGest';

            // get_configured_email() ya configura el 'from' si existe en la BD
            $emailService->setTo($user['email']);
            $emailService->setSubject('Recupera tu contraseña');
            $emailService->setMessage(
                '<div style="font-family: Arial, Helvetica, sans-serif; background: #f7f7f7; padding: 32px 0;">
                    <div style="max-width: 480px; margin: 0 auto; background: #fff; border-radius: 10px; box-shadow: 0 2px 12px rgba(93,135,255,0.08); padding: 32px 24px;">
                        <h2 style="color: #5d87ff; margin-bottom: 16px; text-align: center;">Recupera tu contraseña</h2>
                        <p style="color: #222; margin-bottom: 18px;">Hola, <b>' . esc($user['name']) . '</b>:</p>
                        <p style="color: #444; margin-bottom: 24px;">Has solicitado restablecer tu contraseña. Haz clic en el siguiente botón para continuar:</p>
                        <div style="text-align: center; margin-bottom: 32px;">
                            <a href="' . $link . '" style="display: inline-block; background: #5d87ff; color: #fff; text-decoration: none; padding: 12px 32px; border-radius: 6px; font-weight: bold; font-size: 16px;">Restablecer contraseña</a>
                        </div>
                        <p style="color: #888; font-size: 13px; text-align: center;">Si no solicitaste este cambio, ignora este mensaje.</p>
                        <hr style="border: none; border-top: 1px solid #eee; margin: 32px 0 16px 0;">
                        <p style="color: #aaa; font-size: 12px; text-align: center;">' . esc($companyName) . '</p>
                    </div>
                </div>'
            );
            if (! $emailService->send()) {
                log_message('error', 'No se pudo enviar el correo de recuperación a ' . $user['email']);
            }
        }
        // Siempre mostrar mensaje genérico por seguridad
        return redirect()->back()->with('success', 'Se ha enviado un enlace para restablecer la contraseña.');
    }

    // =================================================================================
    // Restablecimiento de contraseña (GET)
    // =================================================================================
    public function resetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to('/login');
        }
        // Buscar usuario por token
        $user = $this->usersModel->findByResetToken($token);
        
        // Si el usuario no existe o el token ha expirado
        if (!$user || $user['reset_token_expiration'] < Time::now('Europe/Madrid', 'es_ES')->format('Y-m-d H:i:s')) {
            return redirect()->to('/login')->with('errors', ['El enlace no es válido o ha expirado.']);
        }
        return view('auth/reset_password', ['token' => $token]);
    }

    // =================================================================================
    // Restablecimiento de contraseña (POST)
    // =================================================================================
    public function resetPasswordPost($token = null)
    {
        // Si el token no es válido
        if (!$token) {
            return redirect()->to('/login')->with('errors', ['El enlace no es válido o ha expirado.']);
        }
        // Buscar usuario por token
        $user = $this->usersModel->findByResetToken($token);

        // Si el usuario no existe o el token ha expirado
        if (!$user || $user['reset_token_expiration'] < Time::now('Europe/Madrid', 'es_ES')->format('Y-m-d H:i:s')) {
            return redirect()->to('/login')->with('errors', ['El enlace no es válido o ha expirado.']);
        }
        // Validar los datos del formulario
        $rules = [
            'password' => [
                'label' => 'nueva contraseña',
                'rules' => 'required|min_length[8]',
            ],
            'password_confirm' => [
                'label' => 'confirmar contraseña',
                'rules' => 'required|matches[password]',
            ],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }
        // Actualizar contraseña y limpiar token
        $this->usersModel->update($user['id'], [
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_token_expiration' => null,
        ]);
        return redirect()->to('/login')->with('success', 'Contraseña restablecida correctamente.');
    }
} 