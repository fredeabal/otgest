<?php
// =================================================================================
// Seeder: RolesSeeder
// =================================================================================

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class RolesSeeder extends Seeder
{
    // Inserta los roles iniciales en la tabla roles
    public function run()
    {
        // =================================================================================
        // Definir roles base que deben existir siempre
        // =================================================================================
        $data = [
            [
                'name'       => 'Admin',
                'created_at' => Time::now('Europe/Madrid', 'es_ES'),
                'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
            ],
            [
                'name'       => 'Supervisor',
                'created_at' => Time::now('Europe/Madrid', 'es_ES'),
                'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
            ],
            [
                'name'       => 'Usuario',
                'created_at' => Time::now('Europe/Madrid', 'es_ES'),
                'updated_at' => Time::now('Europe/Madrid', 'es_ES'),
            ],
        ];
        
        // =================================================================================
        // Idempotencia: evitar duplicados comprobando qué roles ya existen por nombre
        // =================================================================================
        $names = array_column($data, 'name');
        $existing = $this->db->table('roles')
            ->select('name')
            ->whereIn('name', $names)
            ->get()
            ->getResultArray();

        $existingNames = array_map(function($row) { return $row['name']; }, $existing);

        $toInsert = array_values(array_filter($data, function ($row) use ($existingNames) {
            return !in_array($row['name'], $existingNames, true);
        }));

        // Inserta solo si hay registros faltantes
        if (!empty($toInsert)) {
            $this->db->table('roles')->insertBatch($toInsert);
        }
    }
} 