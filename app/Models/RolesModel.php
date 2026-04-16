<?php
// =================================================================================
// Modelo: RolesModel
// =================================================================================

namespace App\Models;

use CodeIgniter\Model;

class RolesModel extends Model
{
    // Nombre de la tabla asociada
    protected $table = 'roles';
    // Llave primaria
    protected $primaryKey = 'id';
    // Campos permitidos para inserción/actualización masiva
    protected $allowedFields = [
        'name', 'created_at', 'updated_at', 'updated_by'
    ];
    // Activar timestamps automáticos
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // Soft delete
    protected $useSoftDeletes = false;
} 