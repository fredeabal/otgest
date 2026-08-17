<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToAbsencesAndDocuments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('absences', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addColumn('documents', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('absences', 'deleted_at');
        $this->forge->dropColumn('documents', 'deleted_at');
    }
}
