<?php
// =================================================================================
// Migración: Tabla roles
// =================================================================================

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Roles extends Migration
{
    // Crea la tabla roles con los campos necesarios para el sistema de roles
    public function up()
    {
        // Definición de la estructura de la tabla roles
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
                // Nombre del rol: admin, user, etc.
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'updated_by' => [
                'type'    => 'INT',
                'null'    => true,
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('roles');
    }

    // Elimina la tabla roles si existe
    public function down()
    {
        $this->forge->dropTable('roles');
    }
} 