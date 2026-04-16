<?php
// =================================================================================
// Seeder: UsersSeeder
// =================================================================================

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UsersSeeder extends Seeder
{
    // Inserta usuarios iniciales (admin y user) en la tabla users
    public function run()
    {
        // Obtener los IDs de los roles
        $roles = $this->db->table('roles')->get()->getResultArray();

        $roleMap = [];
        foreach ($roles as $role) {
            $roleMap[$role['name']] = $role['id'];
        }

        // Si faltan roles base, salir temprano para evitar errores de FK
        if (!isset($roleMap['Admin']) || !isset($roleMap['Supervisor']) || !isset($roleMap['Usuario'])) {
            return;
        }

        $data = [
            [
                'name'       => 'Administrador',
                'identification' => 'X123456789',
                'email'      => 'admin@demo.com',
                'password'   => password_hash('12345678', PASSWORD_DEFAULT), // Contraseña segura
                'role_id'    => $roleMap['Admin'],
                'is_active'  => true,
                'created_at' => Time::now('Europe/Madrid', 'es_ES'),
                'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
                'avatar'     => 'user-default.png',
                'permissions' => json_encode([
                    'workdays.clockin', 'workdays.records', 'workdays.manage',
                    'absences.request', 'absences.list', 'absences.manage',
                    'expenses.create', 'expenses.my', 'expenses.manage',
                    'documents.received', 'documents.sent', 'documents.send', 'documents.manage',
                    'admin.users', 'admin.roles', 'admin.company'
                ]),
                'address'    => 'Calle Falsa 123',
                'birthdate'  => '1990-01-01',
                'daily_hours' => 8,
                'max_daily_hours' => 12,
                'theme'      => 'dark',
            ],
            [
                'name'       => 'Supervisor',
                'identification' => '987654321X',
                'email'      => 'supervisor@demo.com',
                'password'   => password_hash('12345678', PASSWORD_DEFAULT), // Contraseña segura
                'role_id'    => $roleMap['Supervisor'],
                'is_active'  => true,
                'created_at' => Time::now('Europe/Madrid', 'es_ES'),
                'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
                'avatar'     => 'user-default.png',
                'permissions' => json_encode([
                    'workdays.manage', 'absences.manage', 'expenses.manage',
                    'documents.received', 'documents.sent', 'documents.send', 'documents.manage'
                ]),
                'address'    => 'Calle Falsa 456',
                'birthdate'  => '1985-05-15',
                'daily_hours' => 8,
                'max_daily_hours' => 12,
                'theme'      => 'dark', 
            ],
            [
                'name'       => 'Usuario',
                'identification' => '123456789X',
                'email'      => 'user@demo.com',
                'password'   => password_hash('12345678', PASSWORD_DEFAULT), // Contraseña segura
                'role_id'    => $roleMap['Usuario'],
                'is_active'  => true,
                'created_at' => Time::now('Europe/Madrid', 'es_ES'),
                'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
                'avatar'     => 'user-default.png',
                'permissions' => json_encode([
                    'workdays.clockin', 'workdays.records',
                    'absences.request', 'absences.list',
                    'expenses.create', 'expenses.my',
                    'documents.received', 'documents.sent', 'documents.send'
                ]),
                'address'    => 'Calle Falsa 123',
                'birthdate'  => '1990-01-01',
                'daily_hours' => 8,
                'max_daily_hours' => 12,
                'theme'      => 'dark',
            ],
        ];
        
        // =================================================================================
        // Insertar usuarios uno por uno para manejar duplicados
        // =================================================================================
        foreach ($data as $userData) {
            try {
                $this->db->table('users')->insert($userData);
            } catch (\Exception $e) {
                // Usuario ya existe, continuar
                continue;
            }
        }
    }
}