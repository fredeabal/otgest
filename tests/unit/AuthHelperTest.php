<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;

class AuthHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('auth');
    }

    public function test_admin_tiene_todos_los_permisos()
    {
        $session = Services::session();
        $session->set([
            'isLoggedIn' => true,
            'user_role' => 1,
            'user_permissions' => [],
        ]);

        $this->assertTrue(has_permission('cualquier.permiso'));
        $this->assertTrue(has_permission(['permiso1', 'permiso2']));
    }

    public function test_usuario_sin_permisos()
    {
        $session = Services::session();
        $session->set([
            'isLoggedIn' => true,
            'user_role' => 3,
            'user_permissions' => [],
        ]);

        $this->assertFalse(has_permission('admin.users'));
    }

    public function test_usuario_con_permiso_especifico()
    {
        $session = Services::session();
        $session->set([
            'isLoggedIn' => true,
            'user_role' => 3,
            'user_permissions' => ['workdays.clockin', 'documents.received'],
        ]);

        $this->assertTrue(has_permission('workdays.clockin'));
        $this->assertFalse(has_permission('admin.users'));
        $this->assertTrue(has_permission(['admin.users', 'documents.received']));
    }
}
