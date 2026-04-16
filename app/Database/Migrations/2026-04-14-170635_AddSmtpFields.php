<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSmtpFields extends Migration
{
    public function up()
    {
        $fields = [
            'smtp_host' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'website'
            ],
            'smtp_port' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'smtp_host'
            ],
            'smtp_user' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'smtp_port'
            ],
            'smtp_pass' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'smtp_user'
            ],
            'smtp_crypto' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'smtp_pass'
            ],
            'smtp_from_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'smtp_crypto'
            ],
            'smtp_from_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'smtp_from_email'
            ],
        ];

        $this->forge->addColumn('company', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('company', [
            'smtp_host',
            'smtp_port',
            'smtp_user',
            'smtp_pass',
            'smtp_crypto',
            'smtp_from_email',
            'smtp_from_name'
        ]);
    }
}
