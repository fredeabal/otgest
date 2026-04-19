<?php
// =================================================================================
// Controlador: UsersController
// =================================================================================

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\RolesModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class UsersController extends BaseController
{
    protected $usersModel;
    protected $rolesModel;

    public function __construct()
    {
        // Instanciar modelos
        $this->usersModel = new UsersModel();
        $this->rolesModel = new RolesModel();
    }

    // =================================================================================
    // Mostrar formulario de registro de usuario
    // =================================================================================
    public function create()
    {
        // Obtener roles para el select
        $data['roles'] = $this->rolesModel->findAll();
        $data['title'] = 'Registrar usuario';
        echo view('template/header', $data);
        echo view('users/create', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Procesar registro de usuario (POST)
    // =================================================================================
    public function store()
    {
        // Reglas de validación
        $rules = [
            'name' => [
                'label' => 'nombre',
                'rules' => 'required|min_length[3]|max_length[255]'
            ],
            'identification' => [
                'label' => 'número de identificación',
                'rules' => 'required|min_length[5]|max_length[30]|is_unique[users.identification]'
            ],
            'email' => [
                'label' => 'correo electrónico',
                'rules' => 'required|valid_email|is_unique[users.email]'
            ],
            'address' => [
                'label' => 'dirección',
                'rules' => 'permit_empty|max_length[255]'
            ],
            'birthdate' => [
                'label' => 'fecha de nacimiento',
                'rules' => 'permit_empty|valid_date'
            ],
            'password' => [
                'label' => 'contraseña',
                'rules' => 'required|min_length[8]'
            ],
            'password_confirm' => [
                'label' => 'confirmar contraseña',
                'rules' => 'required|matches[password]'
            ],
            'role_id' => [
                'label' => 'rol',
                'rules' => 'required|integer|is_not_unique[roles.id]'
            ],
            'permissions' => [
                'label' => 'permisos',
                'rules' => 'permit_empty'
            ],
            'is_active' => [
                'label' => 'estado',
                'rules' => 'required|in_list[0,1]'
            ],
            'daily_hours' => [
                'label' => 'horas diarias',
                'rules' => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[24]'
            ],
            'max_daily_hours' => [
                'label' => 'máximo horas diarias',
                'rules' => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[24]'
            ],
            'vacation_days' => [
                'label' => 'días de vacaciones',
                'rules' => 'permit_empty|integer|greater_than_equal_to[0]'
            ]
        ];

        // Validar datos
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Guardar permisos recibidos
        $permissions = $this->request->getPost('permissions');

        // Guardar usuario
        $this->usersModel->save([
            'avatar' => 'user-default.png',
            'name' => $this->request->getPost('name'),
            'identification' => $this->request->getPost('identification'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
            'birthdate' => $this->request->getPost('birthdate'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id' => $this->request->getPost('role_id'),
            'is_active' => $this->request->getPost('is_active'),
            'daily_hours' => $this->request->getPost('daily_hours'),
            'max_daily_hours' => $this->request->getPost('max_daily_hours'),
            'vacation_days' => $this->request->getPost('vacation_days'),
            'created_at' => Time::now('Europe/Madrid', 'es_ES'),
            'permissions' => $permissions ? json_encode($permissions) : null, // Guardar como JSON
            'updated_by' => session()->get('user_id'),
        ]);

        // Redirigir con mensaje de éxito
        return redirect()->to('/users/list')->with('success', 'Usuario registrado correctamente.');
    }

    // =================================================================================
    // Listado de usuarios
    // =================================================================================
    public function list()
    {
        // Obtener el usuario logueado
        $user = session()->get('user');
        // Obtener todos los usuarios y roles
        $data['users'] = $this->usersModel->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->orderBy('users.created_at', 'ASC')
            ->findAll();
        // Título de la página
        $data['title'] = 'Listado de usuarios';

        echo view('template/header', $data);
        echo view('users/list', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Mostrar formulario de edición de usuario
    // =================================================================================
    public function edit($id)
    {
        // Obtener el usuario logueado
        $user = session()->get('user');
        // Obtener el usuario por ID y unir con la tabla de usuarios para obtener el nombre del último actualizador
        $user = $this->usersModel->select('users.*, updater.name as updated_by_name')
            ->join('users as updater', 'updater.id = users.updated_by', 'left')
            ->find($id);
        // Verificar si el usuario existe
        if (!$user) {
            return redirect()->to('/users/list')->with('errors', ['Usuario no encontrado.']);
        }
        $data['user'] = $user;
        $data['roles'] = $this->rolesModel->findAll();
        $data['title'] = 'Editar usuario';
        echo view('template/header', $data);
        echo view('users/edit', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Procesar edición de usuario (POST)
    // =================================================================================
    public function update($id)
    {
        $user = $this->usersModel->find($id);
        if (!$user) {
            return redirect()->to('/users/list')->with('errors', ['Usuario no encontrado.']);
        }
        $rules = [
            'identification' => [
                'label' => 'número de identificación',
                'rules' => 'required|min_length[5]|max_length[30]|is_unique[users.identification,id,'.$id.']'
            ],
            'name' => [
                'label' => 'nombre',
                'rules' => 'required|min_length[3]|max_length[255]'
            ],
            'role_id' => [
                'label' => 'rol',
                'rules' => 'required|integer|is_not_unique[roles.id]'
            ],
            'is_active' => [
                'label' => 'estado',
                'rules' => 'required|in_list[0,1]'
            ],
            'email' => [
                'label' => 'correo electrónico',
                'rules' => 'required|valid_email|is_unique[users.email,id,'.$id.']'
            ],
            'address' => [
                'label' => 'dirección',
                'rules' => 'permit_empty|max_length[255]'
            ],
            'birthdate' => [
                'label' => 'fecha de nacimiento',
                'rules' => 'permit_empty|valid_date'
            ],
            'daily_hours' => [
                'label' => 'horas diarias',
                'rules' => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[24]'
            ],
            'max_daily_hours' => [
                'label' => 'máximo horas diarias',
                'rules' => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[24]'
            ],
            'vacation_days' => [
                'label' => 'días de vacaciones',
                'rules' => 'permit_empty|integer|greater_than_equal_to[0]'
            ]
        ];
        // Solo validar contraseña si se intenta cambiar
        if ($this->request->getPost('password')) {
            $rules['password'] = [
                'label' => 'contraseña',
                'rules' => 'permit_empty|min_length[8]'
            ];
            $rules['password_confirm'] = [
                'label' => 'confirmar contraseña',
                'rules' => 'permit_empty|matches[password]'
            ];
        }
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        // Guardar permisos recibidos
        $permissions = $this->request->getPost('permissions');
        $data = [
            'name' => $this->request->getPost('name'),
            'role_id' => $this->request->getPost('role_id'),
            'is_active' => $this->request->getPost('is_active'),
            'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
            'permissions' => $permissions ? json_encode($permissions) : null, // Guardar como JSON
            'address' => $this->request->getPost('address'),
            'birthdate' => $this->request->getPost('birthdate'),
            'daily_hours' => $this->request->getPost('daily_hours'),
            'max_daily_hours' => $this->request->getPost('max_daily_hours'),
            'vacation_days' => $this->request->getPost('vacation_days'),
            'email' => $this->request->getPost('email'),
            'updated_by' => session()->get('user_id'),
        ];
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }
        $this->usersModel->update($id, $data);
        return redirect()->to('/users/list')->with('success', 'Usuario actualizado correctamente.');
    }

    // =================================================================================
    // Mostrar formulario de edición de usuario
    // =================================================================================
    public function profile($id)
    {
        // obtener el id de usuario de la sesion
        $sessionId = session()->get('user_id');

        // si el id de la sesion no es el mismo que el id del usuario, no se puede editar
        if ($sessionId != $id) {
            return redirect()->to('/users/list')->with('errors', ['No tienes permisos para editar este usuario.']);
        }

        // obtener el usuario
        $user = $this->usersModel->find($id);
        if (!$user) {
            return redirect()->to('/users/list')->with('errors', ['Usuario no encontrado.']);
        }
        //obtener variablea para pasar a la vista
        $data['user'] = $user;
        $data['roles'] = $this->rolesModel->findAll();
        $data['title'] = 'Editar perfil';

        echo view('template/header', $data);
        echo view('users/profile', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Procesar edición de usuario (POST)
    // =================================================================================
    public function updateProfile($id)
    {
        $user = $this->usersModel->find($id);
        if (!$user) {
            return redirect()->to('/users/list')->with('errors', ['Usuario no encontrado.']);
        }
        $rules = [
            'identification' => [
                'label' => 'número de identificación',
                'rules' => 'required|min_length[5]|max_length[30]|is_unique[users.identification,id,'.$id.']'
            ],
            'name' => [
                'label' => 'nombre',
                'rules' => 'required|min_length[3]|max_length[255]'
            ],
            'email' => [
                'label' => 'correo electrónico',
                'rules' => 'required|valid_email|is_unique[users.email,id,'.$id.']'
            ],
            'address' => [
                'label' => 'dirección',
                'rules' => 'permit_empty|max_length[255]'
            ],
            'birthdate' => [
                'label' => 'fecha de nacimiento',
                'rules' => 'permit_empty|valid_date',
            ]
        ];

        // Solo validar contraseña si se intenta cambiar
        if ($this->request->getPost('password')) {
            $rules['password'] = [
                'label' => 'contraseña',
                'rules' => 'permit_empty|min_length[8]'
            ];
            $rules['password_confirm'] = [
                'label' => 'confirmar contraseña',
                'rules' => 'permit_empty|matches[password]'
            ];
        }
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        // datos a actualizar
        $permissions = $this->request->getPost('permissions');
        $theme = $this->request->getPost('theme') ? 'dark' : 'light';
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
            'birthdate' => $this->request->getPost('birthdate'),
            'theme' => $theme,
            'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
            'permissions' => $permissions ? json_encode($permissions) : null,
        ];
        // si se intenta cambiar la contraseña, se actualiza la contraseña
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }
        // actualizar la sesion
        session()->set('user_name', $data['name']);
        session()->set('user_email', $this->request->getPost('email'));
        session()->set('user_theme', $theme);

        // actualizar el usuario
        $this->usersModel->update($id, $data);
        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }

    // =================================================================================
    // Actualizar avatar de usuario
    // =================================================================================
    public function updateAvatar($id)
    {
        // obtener el usuario
        $user = $this->usersModel->find($id);

        // si el id de la sesion no es el mismo que el id del usuario, no se puede editar
        $sessionId = session()->get('user_id');
        if ($sessionId != $id) {
            return redirect()->back()->with('errors', ['No tienes permisos para editar este usuario.']);
        }

        // obtener el usuario
        if (!$user) {
            return redirect()->back()->with('errors', ['Usuario no encontrado.']);
        }
        // obtener el archivo subido
        $avatar = $this->request->getFile('avatar');

        // Validar que se haya subido un archivo
        if (!$avatar || $avatar->getError() === UPLOAD_ERR_NO_FILE) {
            return redirect()->back()->with('errors', ['No se seleccionó ninguna imagen.']);
        }
        // Validar tipo de archivo (solo imágenes)
        $isImage = $avatar->isValid() && in_array($avatar->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
        // si no es una imagen, se devuelve un error
        if (!$isImage) {
            return redirect()->back()->with('errors', ['Solo se permiten archivos de imagen (jpg, png, gif, webp).']);
        }
        // Validar tamaño (máximo 2MB)
        if ($avatar->getSize() > 2048 * 2048) {
            return redirect()->back()->with('errors', ['La imagen no puede superar 2MB.']);
        }
        // Nombre del archivo: número de identificación + extensión
        $ext = $avatar->getExtension();
        $fileName = $user['identification'] . '.' . $ext;
        $uploadPath = WRITEPATH . 'uploads/profiles/';
        
        // Crear carpeta si no existe
        // if (!is_dir($uploadPath)) {
        //  mkdir($uploadPath, 0777, true);
        // }
        // Eliminar avatar anterior si no es el default
        if (!empty($user['avatar']) && $user['avatar'] !== 'user-default.png') {
            $oldPath = $uploadPath . $user['avatar'];
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        // Mover el archivo
        if (!$avatar->move($uploadPath, $fileName, true)) {
            return redirect()->back()->with('errors', ['No se pudo guardar la imagen.']);
        }

        // actualizar la sesion
        session()->set('user_avatar', $fileName);

        // actualizar el usuario
        $this->usersModel->update($id, ['avatar' => $fileName]);

        return redirect()->back()->with('success', 'Avatar actualizado correctamente.');
    }

    // =================================================================================
    // Servir avatar desde writable
    // =================================================================================
    public function avatar($filename)
    {
        // obtener la ruta del archivo
        $path = WRITEPATH . 'uploads/profiles/' . $filename;
        // si no existe, usar la imagen por defecto
        if (!is_file($path)) {
            $path = WRITEPATH . 'uploads/profiles/user-default.png';
        }
        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }


    // =================================================================================
    // Eliminar avatar y restablecer al default
    // =================================================================================
    public function deleteAvatar($id)
    {
        $currentUserId = session()->get('user_id');

        if ($currentUserId != $id) {
            return redirect()->back()->with('errors', ['No tienes permisos para borrar este avatar.']);
        }

        $user = $this->usersModel->find($id);
        if (!$user) {
            return redirect()->back()->with('errors', ['Usuario no encontrado.']);
        }

        // Borrar archivo físico si no es el default
        if (!empty($user['avatar']) && $user['avatar'] !== 'user-default.png') {
            $path = WRITEPATH . 'uploads/profiles/' . $user['avatar'];
            if (is_file($path)) {
                unlink($path);
            }
        }

        // Actualizar base de datos y sesión
        $this->usersModel->update($id, ['avatar' => 'user-default.png']);
        session()->set('user_avatar', 'user-default.png');

        return redirect()->back()->with('success', 'Avatar eliminado correctamente.');
    }

    // =================================================================================
    // Eliminar usuario
    // =================================================================================
    public function delete($id)
    {
        $user = session()->get('user_id');
        $data = [
            'updated_by' => $user,
            'deleted_at' => Time::now('Europe/Madrid', 'es_ES')
        ];
        // Solo actualiza si hay datos válidos
        if (!empty($data)) {
            $this->usersModel->update($id, $data);
        }
        // Si usas soft deletes, esto es suficiente. Si no, elimina físicamente:
        // $this->usersModel->delete($id);
        return redirect()->to('/users/list')->with('success', 'Usuario eliminado correctamente.');
    }

} 