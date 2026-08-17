<?php
// =================================================================================
// Controlador: RolesController
// =================================================================================

namespace App\Controllers;

use App\Models\RolesModel;
use App\Models\UsersModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class RolesController extends BaseController
{
    protected $rolesModel;
    protected $usersModel;

    public function __construct()
    {
        // Instanciar modelo
        $this->rolesModel = new RolesModel();
        $this->usersModel = new UsersModel();
    }

    // =================================================================================
    // Listado de roles
    // =================================================================================
    public function list()
    {
        //titulo de la pagina
        $data['title'] = 'Listado de roles';
        // Obtener los roles
        $data['roles'] = $this->rolesModel->orderBy('created_at', 'ASC')->findAll();
        // Mostrar la vista 
        echo view('template/header', $data);
        echo view('roles/list', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Mostrar formulario de creación de rol
    // =================================================================================
    public function create()
    {
        //titulo de la pagina
        $data['title'] = 'Crear rol';
        // Mostrar la vista 
        echo view('template/header', $data);
        echo view('roles/create', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Procesar creación de rol (POST)
    // =================================================================================
    public function store()
    {
        // Validar los datos
        $rules = [
            'name' => [
                'label' => 'nombre del rol',
                'rules' => 'required|min_length[3]|max_length[50]|is_unique[roles.name]'
            ]
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        // Guardar array de permisos como JSON
        $permissions = $this->request->getPost('permissions') ?? [];

        // Crear el rol
        $this->rolesModel->save([
            'name' => $this->request->getPost('name'),
            'permissions' => json_encode($permissions),
            'created_at' => Time::now('Europe/Madrid', 'es_ES'),
        ]);
        return redirect()->to('/roles/list')->with('success', 'Rol creado correctamente.');
    }

    // =================================================================================
    // Mostrar formulario de edición de rol
    // =================================================================================
    public function edit($id)
    {
        //titulo de la pagina
        $data['title'] = 'Editar rol';
        // Obtener el rol con el nombre del usuario que lo actualizó
        $role = $this->rolesModel->select('roles.*, updater.name as updated_by_name')
            ->join('users as updater', 'updater.id = roles.updated_by', 'left')
            ->find($id);
        if (!$role) {
            return redirect()->to('/roles/list')->with('errors', ['Rol no encontrado.']);
        }
        // Mostrar la vista
        $data['role'] = $role;
        echo view('template/header', $data);
        echo view('roles/edit', $data);
        echo view('template/footer');
    }

    // =================================================================================
    // Procesar edición de rol (POST)
    // =================================================================================
    public function update($id)
    {
        //titulo de la pagina
        $data['title'] = 'Editar rol';
        // Obtener el rol
        $role = $this->rolesModel->find($id);
        if (!$role) {
            return redirect()->to('/roles/list')->with('errors', ['Rol no encontrado.']);
        }

        // Validar los datos
        $rules = [
            'name' => [
                'label' => 'nombre del rol',
                'rules' => 'required|min_length[3]|max_length[50]|is_unique[roles.name,id,'.$id.']'
            ]
        ];
        // Validar los datos
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        // Guardar array de permisos como JSON
        $permissions = $this->request->getPost('permissions') ?? [];

        // Actualizar el rol
        $this->rolesModel->update($id, [
            'name' => $this->request->getPost('name'),
            'permissions' => json_encode($permissions),
            'updated_by' => session()->get('user_id')
        ]);
        return redirect()->to('/roles/list')->with('success', 'Rol actualizado correctamente.');
    }

    // =================================================================================
    // Eliminar rol
    // =================================================================================
    public function delete($id)
    {
        //titulo de la pagina
        $data['title'] = 'Eliminar rol';
        // Obtener el rol
        $role = $this->rolesModel->find($id);
        if (!$role) {
            return redirect()->to('/roles/list')->with('errors', ['Rol no encontrado.']);
        }
        // si el id del rol es 1 o 2 no se puede eliminar
        if ($id == 1 || $id == 2) {
            return redirect()->to('/roles/list')->with('errors', ['No se puede eliminar este rol.']);
        }

        // Verificar si hay usuarios asignados a este rol (incluyendo los borrados lógicamente)
        $usersCount = $this->usersModel->withDeleted()->where('role_id', $id)->countAllResults();
        if ($usersCount > 0) {
            return redirect()->to('/roles/list')->with('errors', ['No se puede eliminar el rol porque tiene usuarios asignados.']);
        }

        // Eliminar el rol
        $this->rolesModel->delete($id);
        return redirect()->to('/roles/list')->with('success', 'Rol eliminado correctamente.');
    }
} 