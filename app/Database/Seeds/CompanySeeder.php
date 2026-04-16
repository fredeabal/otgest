<?php
// =================================================================================
// Seeder: CompanySeeder
// =================================================================================

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class CompanySeeder extends Seeder
{
    // Inserta datos de prueba para la empresa
    public function run()
    {
        $data = [
            'cif' => 'B12345678',
            'name' => 'Empresa Demo S.L.',
            'address' => 'Calle Principal 123',
            'postal_code' => '28001',
            'phone' => '+34 912 345 678',
            'email' => 'info@empresademo.com',
            'website' => 'https://www.empresademo.com',
            'created_at' => Time::now('Europe/Madrid', 'es_ES'),
            'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
        ];

        // =================================================================================
        // Idempotencia: evitar duplicados comprobando por CIF
        // =================================================================================
        $existing = $this->db->table('company')
            ->select('cif')
            ->where('cif', $data['cif'])
            ->get()
            ->getResultArray();

        if (empty($existing)) {
            $this->db->table('company')->insert($data);
        }
    }
}