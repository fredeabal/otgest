<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVacationDaysToUsers extends Migration
{
    public function up()
    {
        // Solo añadimos la columna si no existe (para evitar errores en local que ya la tiene)
        if (! $this->db->fieldExists('vacation_days', 'users')) {
            $this->forge->addColumn('users', [
                'vacation_days' => [
                    'type' => 'INT',
                    'null' => true,
                    'default' => 22,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('vacation_days', 'users')) {
            $this->forge->dropColumn('users', 'vacation_days');
        }
    }
}
