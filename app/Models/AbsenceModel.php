<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsenceModel extends Model
{
    protected $table            = 'absences';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'type', 'start_date', 'end_date', 'start_time', 'end_time', 'comments', 'status', 'processed_by', 'admin_comments', 'attachment'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // =================================================================================
    // Verificar superposiciones de fechas
    // =================================================================================
    public function checkOverlap($userId, $startDate, $endDate, $excludeId = null)
    {
        $query = $this->where('user_id', $userId)
                      ->where('status !=', 'cancelled')
                      ->where('status !=', 'rejected')
                      ->groupStart()
                          ->where('start_date <=', $endDate)
                          ->where('end_date >=', $startDate)
                      ->groupEnd();

        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }

        return $query->countAllResults() > 0;
    }

    // =================================================================================
    // Tipos de ausencia
    // =================================================================================
    public function getAbsenceTypes()
    {
        return [
            'baja' => 'Baja médica',
            'accidente' => 'Accidente laboral',
            'enfermedad' => 'Enfermedad común',
            'maternidad' => 'Maternidad',
            'paternidad' => 'Paternidad',
            'fallecimiento' => 'Fallecimiento familiar',
            'cuidado' => 'Cuidado familiar',
            'vacaciones' => 'Vacaciones',
            'permiso' => 'Permiso retribuido',
            'festivo' => 'Festivo religioso',
            'formacion' => 'Formación',
            'viaje' => 'Viaje de trabajo',
            'asuntos' => 'Asuntos propios',
            'retraso' => 'Retraso',
            'injustificada' => 'Injustificada',
            'suspension' => 'Suspensión',
            'huelga' => 'Huelga',
            'otros' => 'Otros',
        ];
    }

    // =================================================================================
    // Etiquetas de estado
    // =================================================================================
    public function getStatusLabels()
    {
        return [
            'pending' => 'Pendiente',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            'cancelled' => 'Cancelada',
        ];
    }
}
