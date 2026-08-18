<?php
// =================================================================================
// Modelo: UsersModel
// =================================================================================

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    // Nombre de la tabla asociada
    protected $table = 'users';
    // Llave primaria
    protected $primaryKey = 'id';
    // Campos permitidos para inserción/actualización masiva
    protected $allowedFields = [
        'name', 'identification', 'email', 'address', 'birthdate', 'password', 'role_id', 'daily_hours', 'max_daily_hours', 'reset_token', 'reset_token_expiration', 'is_active', 'last_login', 'created_at', 'updated_at', 'deleted_at', 'deleted_by', 'avatar', 'permissions', 'theme', 'vacation_days', 'kiosk_token'
    ];
    // Activar timestamps automáticos
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // Soft delete
    protected $useSoftDeletes = true;
    protected $deletedField  = 'deleted_at';

    // =================================================================================
    // Métodos personalizados para autenticación y recuperación
    // =================================================================================

    // Busca un usuario activo por email incluyendo su rol
    public function findActiveByEmail($email)
    {
        // Solo usuarios activos
        return $this->select('users.*, roles.name as role_name')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('users.email', $email)
                    ->where('users.is_active', 1)
                    ->first();
    }

    // Busca un usuario por token de recuperación
    public function findByResetToken($token)
    {
        return $this->where('reset_token', $token)->first();
    }

    // Busca un usuario por token de Kiosco
    public function findByKioskToken($token)
    {
        return $this->where('kiosk_token', $token)->where('is_active', 1)->first();
    }

} 