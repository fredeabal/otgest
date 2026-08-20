<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPermissionsToRoles extends Migration
{
    public function up()
    {
        $this->forge->addColumn('roles', [
            'permissions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        // Insertar plantillas predeterminadas en roles existentes
        $adminPermissions = json_encode([
            'workdays.clockin', 'workdays.records', 'workdays.manage',
            'absences.request', 'absences.list', 'absences.manage',
            'expenses.create', 'expenses.my', 'expenses.manage',
            'documents.received', 'documents.send', 'documents.sent', 'documents.manage',
            'admin.users', 'admin.roles', 'admin.company', 'admin.logs'
        ]);

        $supervisorPermissions = json_encode([
            'workdays.manage',
            'absences.manage',
            'expenses.manage',
            'documents.received', 'documents.send', 'documents.sent', 'documents.manage',
            'admin.users'
        ]);

        $userPermissions = json_encode([
            'workdays.clockin', 'workdays.records',
            'absences.request', 'absences.list',
            'expenses.create', 'expenses.my',
            'documents.received', 'documents.send', 'documents.sent'
        ]);

        $this->db->table('roles')->where('id', 1)->update(['permissions' => $adminPermissions]);
        $this->db->table('roles')->where('id', 2)->update(['permissions' => $supervisorPermissions]);
        $this->db->table('roles')->where('id', 3)->update(['permissions' => $userPermissions]);
    }

    public function down()
    {
        $this->forge->dropColumn('roles', 'permissions');
    }
}
