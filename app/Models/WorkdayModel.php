<?php
// =================================================================================
// Modelo: WorkdayModel
// =================================================================================

namespace App\Models;

use CodeIgniter\Model;

class WorkdayModel extends Model
{
    // Configuración básica del modelo
    protected $table      = 'workday';
    protected $primaryKey = 'id';

    // Campos permitidos para operaciones CRUD
    protected $allowedFields = [
        'user_id',
        'workday_date',
        'event_type',
        'event_time',
        'latitude',
        'longitude',
        'daily_hours',
        'max_daily_hours',
        'autoclose',
        'comments'
    ];

    // Configuración de soft delete
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    // Configuración de timestamps automáticos
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}