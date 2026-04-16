<?php
// =================================================================================
// Migración: Tabla users
// =================================================================================

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
    // Crea la tabla users con los campos necesarios para el sistema de autenticación y control de acceso
    public function up()
    {
        // Definición de la estructura de la tabla users
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'identification' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'unique'     => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'unique'     => true,
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'birthdate' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'avatar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'permissions' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'reset_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'reset_token_expiration' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'is_active' => [
                'type'    => 'BOOLEAN',
                'default' => true,
            ],
            'daily_hours' => [
                'type' => 'INT',
                'null' => true,
                'check' => 'daily_hours BETWEEN 1 AND 24',
            ],
            'max_daily_hours' => [
                'type' => 'INT',
                'null' => true,
                'check' => 'max_daily_hours BETWEEN 1 AND 12',
            ],
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'deleted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'theme' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'dark',
                'null'       => false,
            ],
            'updated_by' => [
                'type'    => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('users');
    }

    // Elimina la tabla users si existe
    public function down()
    {
        $this->forge->dropTable('users');
    }
} 