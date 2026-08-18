<!DOCTYPE html>
<html lang="es" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical" data-user-theme="<?= session()->get('user_theme') ?: 'system' ?>">

<head>
    <!-- =================================================================================
    // Script para evitar el parpadeo del tema
    // ================================================================================= -->
    <script>
    (function() {
        var userTheme = '<?= session()->get('user_theme') ?? '' ?>';
        var localTheme = localStorage.getItem('theme');
        // El tema del usuario tiene prioridad absoluta
        var preferredTheme = userTheme ? userTheme : (localTheme || 'system');
        var theme = preferredTheme;
        
        if (theme === 'system') {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        
        document.documentElement.setAttribute('data-bs-theme', theme);
        if (userTheme) {
            document.documentElement.setAttribute('data-user-theme', userTheme);
        }
    })();
    </script>

    <!-- Meta tags requeridos -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- CSRF Token -->
    <meta name="csrf-token-name" content="<?= csrf_token() ?>" />
    <meta name="csrf-token" content="<?= csrf_hash() ?>" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?= base_url() ?>assets/images/logos/favicon.png" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/libs/sweetalert2/dist/sweetalert2.min.css">
    <!-- Datepicker -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/libs/select2/dist/css/select2.min.css">
    <!-- Daterangepicker -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/libs/daterangepicker/daterangepicker.css">
    <!-- Animations -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/libs/animate.css/animate.min.css"/>

    <!-- CSS Principal -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/styles.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/custom.css?v=<?= time() ?>" />
    <!-- solar icons -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    
    <title><?= esc($title) ?></title>

</head>

<body class="link-sidebar">

    <div id="main-wrapper" class="mb-10">
        <!-- =================================================================================
        // Sidebar
        // ================================================================================= -->
        <aside class="left-sidebar with-vertical">
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <!-- Logo -->
                    <a href="<?= base_url() ?>" class="text-nowrap logo-img">
                        <img src="<?= base_url() ?>assets/images/logos/dark-logo.svg" class="logo-dark" alt="Logo-Dark" />
                        <img src="<?= base_url() ?>assets/images/logos/light-logo.svg" class="logo-light" alt="Logo-light" />
                    </a>

                    <a href="javascript:void(0)"
                        class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
                        <iconify-icon icon="solar:close-circle-bold-duotone" class="fs-5"></iconify-icon>
                    </a>
                    
                </div>
                <nav class="sidebar-nav scroll-sidebar" data-simplebar>
                    <ul id="sidebarnav">
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="<?= base_url() ?>" aria-expanded="false">
                                <span><iconify-icon icon="solar:widget-2-bold-duotone" class="fs-5"></iconify-icon></span>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <!-- ============================= -->
                        <!-- Jornadas de Trabajo -->
                        <!-- ============================= -->
                        <?php if (has_permission(['workdays.clockin', 'workdays.records', 'workdays.manage'])): ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <span class="d-flex">
                                    <iconify-icon icon="solar:stopwatch-bold-duotone" class="fs-5"></iconify-icon>
                                </span>
                                <span class="hide-menu">Jornadas</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <?php if (has_permission('workdays.clockin')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('workdays') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Fichar</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if (has_permission('workdays.records')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('workdays/my-records') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Registro</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if (has_permission('workdays.manage')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('workdays/manage') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Gestión</span>
                                        <iconify-icon icon="eos-icons:admin-outlined"></iconify-icon>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <!-- ============================= -->
                        <!-- Documentos -->
                        <!-- ============================= -->
                        <?php if (has_permission(['documents.received', 'documents.sent', 'documents.send', 'documents.manage'])): ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <span class="d-flex">
                                    <iconify-icon icon="solar:document-text-bold-duotone" class="fs-5"></iconify-icon>
                                </span>
                                <span class="hide-menu">Documentos</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <?php if (has_permission('documents.received')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('documents/list') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Recibidos</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if (has_permission('documents.sent')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('documents/sent') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Enviados</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if (has_permission('documents.send')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('documents/send') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Enviar</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if (has_permission('documents.manage')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('documents/bulk-send') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Envío masivo</span>
                                        <iconify-icon icon="eos-icons:admin-outlined"></iconify-icon>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <!-- ============================= -->
                        <!-- Solicitudes -->
                        <!-- ============================= -->
                        <?php if (has_permission(['absences.request', 'absences.list', 'absences.manage'])): ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <span class="d-flex">
                                    <iconify-icon icon="solar:calendar-minimalistic-bold-duotone" class="fs-5"></iconify-icon>
                                </span>
                                <span class="hide-menu">Ausencias</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <?php if (has_permission('absences.request')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('absences/request') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Solicitar</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if (has_permission('absences.list')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('absences/list') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Listar</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if (has_permission('absences.manage')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('absences/manage') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Gestión</span>
                                        <iconify-icon icon="eos-icons:admin-outlined"></iconify-icon>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <!-- ============================= -->
                        <!-- Gastos -->
                        <!-- ============================= -->
                        <?php if (has_permission(['expenses.create', 'expenses.my', 'expenses.manage'])): ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <span class="d-flex">
                                    <iconify-icon icon="solar:bill-list-bold-duotone" class="fs-5"></iconify-icon>
                                </span>
                                <span class="hide-menu">Gastos</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <?php if (has_permission('expenses.create')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('expenses/create') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Registrar</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if (has_permission('expenses.my')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('expenses/my-expenses') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Mis gastos</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php if (has_permission('expenses.manage')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('expenses/manage') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Gestión</span>
                                        <iconify-icon icon="eos-icons:admin-outlined"></iconify-icon>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <!-- ============================= -->
                        <!-- Usuarios -->
                        <!-- ============================= -->
                        <?php if (has_permission('admin.users')): ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <span class="d-flex">
                                    <iconify-icon icon="solar:users-group-rounded-bold-duotone" class="fs-5"></iconify-icon>
                                </span>
                                <span class="hide-menu">Usuarios</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="<?= base_url('users/list') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Listar</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('users/create') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Crear</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <!-- ============================= -->
                        <!-- Roles -->
                        <!-- ============================= -->
                        <?php if (has_permission('admin.roles')): ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <span class="d-flex">
                                    <iconify-icon icon="solar:shield-keyhole-bold-duotone" class="fs-5"></iconify-icon>
                                </span>
                                <span class="hide-menu">Roles</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="<?= base_url('roles/list') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Listar</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('roles/create') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Crear</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <!-- ============================= -->
                        <!-- Configuración -->
                        <!-- ============================= -->
                        <?php if (has_permission('admin.company')): ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                <span class="d-flex">
                                    <iconify-icon icon="solar:settings-bold-duotone" class="fs-5"></iconify-icon>
                                </span>
                                <span class="hide-menu">Configuración</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item">
                                    <a href="<?= base_url('settings/company') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Empresa</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('settings/smtp') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">SMTP Correo</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('settings/maintenance') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Mantenimiento</span>
                                    </a>
                                </li>
                                <?php if (has_permission('admin.logs')): ?>
                                <li class="sidebar-item">
                                    <a href="<?= base_url('logs/list') ?>" class="sidebar-link">
                                        <div class="round-16 d-flex align-items-center justify-content-center">
                                        </div>
                                        <span class="hide-menu">Registro de Actividad</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- =================================================================================
        // Fin Sidebar
        // ================================================================================= -->
        <div class="page-wrapper">
            <!-- =================================================================================
            // Header limpio
            // ================================================================================= -->
            <header class="topbar">
                <div class="with-vertical">
                    <nav class="navbar navbar-expand-lg p-0">
                        <ul class="navbar-nav">
                            <li class="nav-item nav-icon-hover-bg rounded-circle ms-n2">
                                <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                                    <iconify-icon icon="solar:hamburger-menu-line-duotone" class="fs-7"></iconify-icon>
                                </a>
                            </li>
                        </ul>
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                            <li class="nav-item dropdown">
                                <a class="nav-link pe-0" href="javascript:void(0)" id="drop1"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="d-flex align-items-center">
                                        <div class="user-profile-img">
                                            <img src="<?= base_url('users/avatar/' . esc(session('user_avatar'))) ?>"
                                                class="rounded-circle" width="35" height="35" alt="Usuario" />
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="drop1">
                                    <div class="profile-dropdown position-relative" data-simplebar>
                                        <div class="py-3 px-7 pb-0">
                                            <h5 class="mb-0 fs-5 fw-semibold">Perfil de Usuario</h5>
                                        </div>
                                        <div class="d-flex align-items-center py-9 mx-7">
                                            <img src="<?= base_url('users/avatar/' . esc(session('user_avatar'))) ?>"
                                                class="rounded-circle" width="80" height="80" alt="Usuario" />
                                            <div class="ms-3">
                                                <h5 class="mb-1 fs-3"><?= esc(session('user_name')) ?></h5>
                                                <span class="mb-1 d-block text-dark">
                                                    <?php 
                                                        if (session('user_role_name')) {
                                                            echo esc(session('user_role_name'));
                                                        } else {
                                                            $roleId = session('user_role');
                                                            if ($roleId == 1) echo 'Administrador';
                                                            elseif ($roleId == 2) echo 'Supervisor';
                                                            else echo 'Usuario';
                                                        }
                                                    ?>
                                                </span>
                                                <p class="mb-0 d-flex text-dark align-items-center gap-2">
                                                    <iconify-icon icon="solar:letter-bold-duotone"></iconify-icon>
                                                    <?= esc(session('user_email')) ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="message-body">
                                            <a href="<?= base_url('users/profile/' . session('user_id')) ?>"
                                                class="py-8 px-7 mt-8 d-flex align-items-center">
                                                <span
                                                    class="d-flex align-items-center justify-content-center bg-primary-subtle rounded-1 p-6">
                                                    <iconify-icon icon="solar:user-circle-bold-duotone" class="fs-6 text-dark"></iconify-icon>
                                                </span>
                                                <div class="w-75 d-inline-block v-middle ps-3">
                                                    <h6 class="mb-1 bg-hover-primary">Mi Perfil</h6>
                                                    <span class="d-block text-dark">Configuración de la
                                                        cuenta</span>
                                                </div>
                                            </a>
                                            <a href="<?= base_url('logout') ?>"
                                                class="py-8 px-7 d-flex align-items-center">
                                                <span
                                                    class="d-flex align-items-center justify-content-center bg-primary-subtle rounded-1 p-6">
                                                    <iconify-icon icon="solar:logout-bold-duotone" class="fs-6 text-dark"></iconify-icon>
                                                </span>
                                                <div class="w-75 d-inline-block v-middle ps-3">
                                                    <h6 class="mb-1 bg-hover-primary">Cerrar sesión</h6>
                                                    <span class="d-block text-dark">Salir del sistema</span>
                                                </div>
                                            </a>
                                            <a href="javascript:void(0)" 
                                                class="py-8 px-7 d-flex align-items-center"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#helpModal">
                                                <span
                                                    class="d-flex align-items-center justify-content-center bg-primary-subtle rounded-1 p-6">
                                                    <iconify-icon icon="solar:question-circle-bold-duotone" class="fs-6 text-dark"></iconify-icon>
                                                </span>
                                                <div class="w-75 d-inline-block v-middle ps-3">
                                                    <h6 class="mb-1 bg-hover-primary">Ayuda</h6>
                                                    <span class="d-block text-dark">Información de soporte</span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </nav>
                </div>
            </header>
            <div class="pb-12"></div>

            <!-- Modal de Ayuda / Soporte -->
            <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                        <div class="modal-header bg-primary-subtle text-primary border-0 py-4">
                            <h5 class="modal-title text-primary d-flex align-items-center gap-2" id="helpModalLabel">
                                <iconify-icon icon="solar:info-square-bold-duotone" class="fs-6"></iconify-icon>
                                Información del Sistema
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-5">
                            <div class="text-center mb-4">
                                <div class="bg-primary-subtle d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px;">
                                    <iconify-icon icon="solar:user-bold-duotone" class="text-primary" style="font-size: 40px;"></iconify-icon>
                                </div>
                                <h4 class="fw-bold text-dark mb-1">Freddy De Abreu A.</h4>
                                <p class="text-muted fs-3 mb-0">Desarrollo y Soporte Técnico</p>
                            </div>
                            <div class="mt-4 pt-4 border-top border-light-subtle">
                                <div class="text-center mb-4">
                                    <div class="d-flex flex-column align-items-center gap-2 mb-4">
                                        <div class="bg-primary-subtle rounded-circle p-2 d-inline-flex mb-1">
                                            <iconify-icon icon="solar:letter-bold-duotone" class="text-primary fs-5"></iconify-icon>
                                        </div>
                                        <div>
                                            <span class="d-block text-muted fs-2">Correo de contacto</span>
                                            <a href="mailto:freddy@esweb.es" class="fw-bold text-dark fs-3 text-decoration-none">freddy@esweb.es</a>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex flex-column align-items-center gap-2 mb-2">
                                        <div class="bg-primary-subtle rounded-circle p-2 d-inline-flex mb-1">
                                            <iconify-icon icon="solar:code-bold-duotone" class="text-primary fs-5"></iconify-icon>
                                        </div>
                                        <div>
                                            <span class="d-block text-muted fs-2">Versión actual</span>
                                            <span class="fw-bold text-dark fs-3">v1.4.3</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="button" class="btn btn-primary px-5 py-2 rounded-pill fw-bold" data-bs-dismiss="modal">Entendido</button>
                            </div>
                        </div>
                        <div class="modal-footer border-0 py-3 justify-content-center">
                            <span class="fs-2 text-muted">© <?= date('Y') ?> OtGest - Todos los derechos reservados</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- =================================================================================
            // Fin Header
            // ================================================================================= -->