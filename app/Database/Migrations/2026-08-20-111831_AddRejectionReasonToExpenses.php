<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRejectionReasonToExpenses extends Migration
{
    public function up()
    {
        $this->forge->addColumn('expenses', [
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('expenses', 'rejection_reason');
    }
}
