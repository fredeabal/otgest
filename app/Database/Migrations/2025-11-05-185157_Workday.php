<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Workday extends Migration
{
    public function up()
    {
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
            'workday_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'event_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'event_time' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,6',
                'null'       => true,
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,6',
                'null'       => true,
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
            'autoclose' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'comments' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'workday_date', 'event_time']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('workday');
    }

    public function down()
    {
        $this->forge->dropTable('workday');
    }
}
