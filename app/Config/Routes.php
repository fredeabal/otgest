<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');

// =================================================================================
// Rutas de autenticación
// =================================================================================
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::loginPost');
$routes->get('logout', 'AuthController::logout');
$routes->get('forgot-password', 'AuthController::forgotPassword');
$routes->post('forgot-password', 'AuthController::forgotPasswordPost');
$routes->get('reset-password/(:any)', 'AuthController::resetPassword/$1');
$routes->post('reset-password/(:any)', 'AuthController::resetPasswordPost/$1');

// =================================================================================
// Dashboard
// =================================================================================
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('dashboard/events', 'DashboardController::getEvents', ['filter' => 'auth']);
$routes->get('user/dashboard', 'DashboardController::index', ['filter' => 'auth']);

// =================================================================================
// Rutas de gestión de usuarios (solo admin)
// =================================================================================
$routes->get('users/create', 'UsersController::create', ['filter' => 'permission:admin.users']);
$routes->post('users/store', 'UsersController::store', ['filter' => 'permission:admin.users']);
$routes->get('users/list', 'UsersController::list', ['filter' => 'permission:admin.users']);
$routes->get('users/show/(:num)', 'UsersController::show/$1', ['filter' => 'permission:admin.users']);
$routes->get('users/edit/(:num)', 'UsersController::edit/$1', ['filter' => 'permission:admin.users']);
$routes->post('users/update/(:num)', 'UsersController::update/$1', ['filter' => 'permission:admin.users']);
$routes->post('users/delete/(:num)', 'UsersController::delete/$1', ['filter' => 'permission:admin.users']);

// Rutas de perfil
$routes->post('users/update-avatar/(:num)', 'UsersController::updateAvatar/$1', ['filter' => 'auth']);
$routes->get('users/profile/(:num)', 'UsersController::profile/$1', ['filter' => 'auth']);
$routes->post('users/update-profile/(:num)', 'UsersController::updateProfile/$1', ['filter' => 'auth']);

// Rutas de avatar
$routes->get('users/avatar/(:any)', 'UsersController::avatar/$1');
$routes->post('users/delete-avatar/(:num)', 'UsersController::deleteAvatar/$1', ['filter' => 'auth']);

// =================================================================================
// Rutas de gestión de roles (solo admin)
// =================================================================================
$routes->get('roles/create', 'RolesController::create', ['filter' => 'permission:admin.roles']);
$routes->post('roles/store', 'RolesController::store', ['filter' => 'permission:admin.roles']);
$routes->get('roles/list', 'RolesController::list', ['filter' => 'permission:admin.roles']);
$routes->get('roles/edit/(:num)', 'RolesController::edit/$1', ['filter' => 'permission:admin.roles']);
$routes->post('roles/update/(:num)', 'RolesController::update/$1', ['filter' => 'permission:admin.roles']);
$routes->post('roles/delete/(:num)', 'RolesController::delete/$1', ['filter' => 'permission:admin.roles']);

// =================================================================================
// Rutas de gestión de documentos
// =================================================================================
$routes->get('documents/list', 'DocumentsController::list', ['filter' => 'permission:documents.received,documents.manage']);
$routes->get('documents/sent', 'DocumentsController::sent', ['filter' => 'permission:documents.sent,documents.manage']);
$routes->get('documents/send', 'DocumentsController::send', ['filter' => 'permission:documents.send,documents.manage']);
$routes->get('documents/bulk-send', 'DocumentsController::bulkSend', ['filter' => 'permission:documents.manage']);
$routes->post('documents/bulk-store', 'DocumentsController::bulkStore', ['filter' => 'permission:documents.manage']);
$routes->post('documents/store', 'DocumentsController::store', ['filter' => 'permission:documents.send,documents.manage']);
$routes->get('documents/view/(:num)', 'DocumentsController::view/$1', ['filter' => 'permission:documents.received,documents.send,documents.manage']);
$routes->get('documents/download/(:num)', 'DocumentsController::download/$1', ['filter' => 'permission:documents.received,documents.send,documents.manage']);
$routes->post('documents/delete/(:num)', 'DocumentsController::delete/$1', ['filter' => 'permission:documents.sent,documents.manage']);


// =================================================================================
// Rutas de gestión de empresa (solo admin)
// =================================================================================
$routes->group('settings', ['filter' => 'permission:admin.company'], function($routes) {
    $routes->get('company', 'SettingsController::company');
    $routes->post('company/update', 'SettingsController::updateCompany');
    
    $routes->get('smtp', 'SettingsController::smtp');
    $routes->post('smtp/update', 'SettingsController::updateSmtp');
    $routes->post('test-smtp', 'SettingsController::testSmtp');
    
    $routes->get('maintenance', 'SettingsController::maintenance');
    $routes->post('clear-sessions', 'SettingsController::clearSessions');
    $routes->post('clear-cache', 'SettingsController::clearCache');
    $routes->post('clear-logs', 'SettingsController::clearLogs');
    $routes->post('clear-debugbar', 'SettingsController::clearDebugbar');
    $routes->post('clear-all', 'SettingsController::clearAll');

    
    $routes->get('download-db', 'SettingsController::downloadDatabase');
    $routes->post('restore-db', 'SettingsController::restoreDatabase');
});

// =================================================================================
// Registro de Actividad
// =================================================================================
$routes->group('logs', ['filter' => 'auth'], function ($routes) {
    $routes->get('list', 'ActivityLogController::index', ['filter' => 'permission:admin.logs']);
    $routes->get('export-pdf', 'ActivityLogController::exportPdf', ['filter' => 'permission:admin.logs']);
});
// =================================================================================
// Rutas de gestión de solicitudes de ausencia
// =================================================================================
$routes->get('absences/request', 'AbsenceController::request', ['filter' => 'permission:absences.request,absences.manage']);
$routes->post('absences/store', 'AbsenceController::store', ['filter' => 'permission:absences.request,absences.manage']);
$routes->get('absences/list', 'AbsenceController::list', ['filter' => 'permission:absences.list,absences.manage']);
$routes->get('absences/export-list-pdf', 'AbsenceController::exportListPdf', ['filter' => 'permission:absences.list,absences.manage']);
$routes->get('absences/manage', 'AbsenceController::manage', ['filter' => 'permission:absences.manage']);
$routes->get('absences/export-pdf', 'AbsenceController::exportPdf', ['filter' => 'permission:absences.manage']);
$routes->post('absences/approve/(:num)', 'AbsenceController::approve/$1', ['filter' => 'permission:absences.manage']);
$routes->post('absences/reject/(:num)', 'AbsenceController::reject/$1', ['filter' => 'permission:absences.manage']);
$routes->get('absences/edit/(:num)', 'AbsenceController::edit/$1', ['filter' => 'permission:absences.request,absences.list,absences.manage']);
$routes->post('absences/update/(:num)', 'AbsenceController::update/$1', ['filter' => 'permission:absences.request,absences.manage']);
$routes->post('absences/cancel/(:num)', 'AbsenceController::cancel/$1', ['filter' => 'permission:absences.request,absences.list,absences.manage']);
$routes->get('absences/view/(:num)', 'AbsenceController::view/$1', ['filter' => 'permission:absences.request,absences.list,absences.manage']);
$routes->get('absences/export-absence-pdf/(:num)', 'AbsenceController::exportAbsencePdf/$1', ['filter' => 'permission:absences.request,absences.list,absences.manage']);
$routes->get('absences/download/(:num)', 'AbsenceController::download/$1', ['filter' => 'permission:absences.request,absences.list,absences.manage']);



// =================================================================================
// Rutas de control de jornadas laborales
// =================================================================================
$routes->get('workdays', 'WorkdayController::index', ['filter' => 'permission:workdays.clockin,workdays.manage']);
$routes->post('workdays/start', 'WorkdayController::start', ['filter' => 'permission:workdays.clockin,workdays.manage']);
$routes->post('workdays/pause', 'WorkdayController::pause', ['filter' => 'permission:workdays.clockin,workdays.manage']);
$routes->post('workdays/resume', 'WorkdayController::resume', ['filter' => 'permission:workdays.clockin,workdays.manage']);
$routes->get('workdays/my-records', 'WorkdayController::myRecords', ['filter' => 'permission:workdays.records,workdays.manage']);
$routes->get('workdays/export-my-pdf', 'WorkdayController::exportMyPdf', ['filter' => 'permission:workdays.records,workdays.manage']);
$routes->post('workdays/end', 'WorkdayController::end', ['filter' => 'permission:workdays.clockin,workdays.manage']);
$routes->get('workdays/manage', 'WorkdayController::manage', ['filter' => 'permission:workdays.manage']);
$routes->get('workdays/export-pdf', 'WorkdayController::exportPdf', ['filter' => 'permission:workdays.manage']);
$routes->get('workdays/view/(:any)', 'WorkdayController::view/$1', ['filter' => 'permission:workdays.records,workdays.manage']);

// =================================================================================
// Rutas de gestión de justificaciones de gastos
// =================================================================================
$routes->get('expenses/create', 'ExpenseController::create', ['filter' => 'permission:expenses.create,expenses.manage']);
$routes->post('expenses/store', 'ExpenseController::store', ['filter' => 'permission:expenses.create,expenses.manage']);
$routes->get('expenses/my-expenses', 'ExpenseController::my', ['filter' => 'permission:expenses.my,expenses.manage']);
$routes->get('expenses/export-my-pdf', 'ExpenseController::exportMyPdf', ['filter' => 'permission:expenses.my,expenses.manage']);
$routes->get('expenses/manage', 'ExpenseController::manage', ['filter' => 'permission:expenses.manage']);
$routes->get('expenses/export-pending-pdf', 'ExpenseController::exportPendingPdf', ['filter' => 'permission:expenses.manage']);
$routes->post('expenses/approve/(:num)', 'ExpenseController::approve/$1', ['filter' => 'permission:expenses.manage']);
$routes->post('expenses/reject/(:num)', 'ExpenseController::reject/$1', ['filter' => 'permission:expenses.manage']);
$routes->get('expenses/view/(:num)', 'ExpenseController::view/$1', ['filter' => 'permission:expenses.create,expenses.my,expenses.manage']);
$routes->get('expenses/download/(:num)', 'ExpenseController::download/$1', ['filter' => 'permission:expenses.create,expenses.my,expenses.manage']);
$routes->post('expenses/delete/(:num)', 'ExpenseController::delete/$1', ['filter' => 'permission:expenses.create,expenses.my,expenses.manage']);
$routes->get('expenses/receipt/(:num)/(:any)', 'ExpenseController::receipt/$1/$2', ['filter' => 'permission:expenses.create,expenses.my,expenses.manage']);



// =================================================================================
// Rutas del Kiosco de Fichaje
// =================================================================================
$routes->get('kiosk', 'KioskController::index');
$routes->post('kiosk/scan', 'KioskController::scan');
$routes->get('kiosk/action/(:any)', 'KioskController::action/$1');
$routes->post('kiosk/action', 'KioskController::processAction');
