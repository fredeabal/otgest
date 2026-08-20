<?php
// =================================================================================
// Modelo: ExpenseModel
// =================================================================================

namespace App\Models;

use CodeIgniter\Model;

class ExpenseModel extends Model
{
    // Nombre de la tabla asociada
    protected $table = 'expenses';
    // Llave primaria
    protected $primaryKey = 'id';
    // Campos permitidos para inserción/actualización masiva
    protected $allowedFields = [
        'user_id', 'reason', 'receipt_image', 'status', 'approved_by', 'approved_at', 'amount', 'category', 'expense_date', 'rejection_reason', 'created_at', 'updated_at', 'deleted_at'
    ];
    // Activar timestamps automáticos
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // Soft delete
    protected $useSoftDeletes = true;
    protected $deletedField  = 'deleted_at';

    // =================================================================================
    // Métodos personalizados para gestión de gastos
    // =================================================================================

    // Busca gastos por usuario
    public function findByUser($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }

    // Busca gastos pendientes de aprobación
    public function findPending()
    {
        return $this->where('status', 'pending')->findAll();
    }

    // Busca gastos aprobados
    public function findApproved()
    {
        return $this->where('status', 'approved')->findAll();
    }

    // Busca gastos rechazados
    public function findRejected()
    {
        return $this->where('status', 'rejected')->findAll();
    }

    // Busca gastos por rango de fechas
    public function findByDateRange($startDate, $endDate)
    {
        return $this->where('expense_date >=', $startDate)
                    ->where('expense_date <=', $endDate)
                    ->findAll();
    }

    // Busca gastos por categoría
    public function findByCategory($category)
    {
        return $this->where('category', $category)->findAll();
    }
}
