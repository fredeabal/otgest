<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table            = 'company';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cif', 'name', 'address', 'postal_code', 'phone', 'email', 'website',
        'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_crypto', 'smtp_from_email', 'smtp_from_name'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // =================================================================================
    // Obtener la empresa (solo debería haber una)
    // =================================================================================
    public function getCompany()
    {
        return $this->first();
    }

    // =================================================================================
    // Crear o actualizar la empresa
    // =================================================================================
    public function saveCompany($data)
    {
        $company = $this->getCompany();

        if ($company) {
            // Actualizar
            return $this->update($company['id'], $data);
        } else {
            // Crear
            return $this->insert($data);
        }
    }
}