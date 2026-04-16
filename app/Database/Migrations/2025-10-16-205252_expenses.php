<?php
// =================================================================================
// Migración: Tabla expenses
// =================================================================================

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Expenses extends Migration
{
    // Crea la tabla expenses con los campos necesarios para el sistema de justificaciones de gastos
    public function up()
    {
        // Definición de la estructura de la tabla expenses
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'reason' => [
                'type'       => 'TEXT',
                'null'       => false,
            ],
            'receipt_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
            ],
            'approved_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'expense_date' => [
                'type' => 'DATE',
                'null' => false,
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('expenses');
    }

    // Elimina la tabla expenses si existe
    public function down()
    {
        $this->forge->dropTable('expenses');
    }
}
