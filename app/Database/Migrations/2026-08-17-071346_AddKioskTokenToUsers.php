<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKioskTokenToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'kiosk_token' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'after' => 'reset_token_expiration'
            ],
        ]);

        // Generate tokens for existing users
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $users = $builder->get()->getResult();
        foreach ($users as $user) {
            $token = bin2hex(random_bytes(16));
            $builder->where('id', $user->id)->update(['kiosk_token' => $token]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'kiosk_token');
    }
}
