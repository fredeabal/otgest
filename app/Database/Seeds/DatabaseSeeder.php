<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

// =================================================================================
// Seeder maestro para encadenar otros seeders
// =================================================================================
class DatabaseSeeder extends Seeder
{
    // Ejecuta los seeders en un orden controlado para respetar dependencias
    public function run()
    {
        // =====================================================================
        // Primero: RolesSeeder porque UsersSeeder depende de roles existentes
        // =====================================================================
        $this->call('RolesSeeder');

        // =====================================================================
        // Segundo: UsersSeeder para crear usuarios con roles válidos
        // =====================================================================
        $this->call('UsersSeeder');

        // =====================================================================
        // Tercero: CompanySeeder para crear datos de la empresa
        // =====================================================================
        $this->call('CompanySeeder');
    }
}


