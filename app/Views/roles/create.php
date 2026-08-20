<div class="container">

    <!-- =================================================================================
// Formulario de Creación de Rol
// ================================================================================= -->

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0 text-primary">Crear rol</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('roles/store') ?>" method="post" autocomplete="off">
                        <?= csrf_field() ?>
                        <!-- =================================================================================
                        // Campo: Nombre del rol
                        // ================================================================================= -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del rol</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>">
                            <?php if (session('errors.name')): ?>
                            <div class="text-danger small mt-1">
                                <?= esc(session('errors.name')) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <!-- =================================================================================
                        // Permisos predeterminados (Plantilla)
                        // ================================================================================= -->
                        <?php $rolePermissions = is_array(old('permissions')) ? old('permissions') : []; ?>
                        <div class="my-4" id="permissions-block">
                            <label class="form-label d-block mb-4 fw-semibold fs-4 text-primary border-bottom pb-2">Permisos de la plantilla</label>
                            
                            <div class="row g-4">
                                <!-- JORNADAS -->
                                <div class="col-12">
                                    <div class="card mb-0 shadow-none border">
                                        <div class="card-header bg-primary-subtle">
                                            <h6 class="mb-0 text-primary d-flex align-items-center gap-2">
                                                <i class="ti ti-clock fs-5"></i> Jornadas
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="workdays_clockin" name="permissions[]" value="workdays.clockin" <?= in_array('workdays.clockin', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="workdays_clockin">Fichar (Iniciar/Pausar)</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="workdays_records" name="permissions[]" value="workdays.records" <?= in_array('workdays.records', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="workdays_records">Mis registros</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="workdays_manage" name="permissions[]" value="workdays.manage" <?= in_array('workdays.manage', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="workdays_manage">Gestión de jornadas</label>
                                                <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- AUSENCIAS -->
                                <div class="col-12">
                                    <div class="card mb-0 shadow-none border">
                                        <div class="card-header bg-primary-subtle">
                                            <h6 class="mb-0 text-primary d-flex align-items-center gap-2">
                                                <i class="ti ti-calendar-event fs-5"></i> Ausencias
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="absences_request" name="permissions[]" value="absences.request" <?= in_array('absences.request', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="absences_request">Solicitar ausencias</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="absences_list" name="permissions[]" value="absences.list" <?= in_array('absences.list', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="absences_list">Ver mi historial</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="absences_manage" name="permissions[]" value="absences.manage" <?= in_array('absences.manage', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="absences_manage">Gestión de ausencias</label>
                                                <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- GASTOS -->
                                <div class="col-12">
                                    <div class="card mb-0 shadow-none border">
                                        <div class="card-header bg-primary-subtle">
                                            <h6 class="mb-0 text-primary d-flex align-items-center gap-2">
                                                <i class="ti ti-receipt fs-5"></i> Gastos
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="expenses_create" name="permissions[]" value="expenses.create" <?= in_array('expenses.create', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="expenses_create">Registrar ticket</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="expenses_my" name="permissions[]" value="expenses.my" <?= in_array('expenses.my', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="expenses_my">Mis gastos</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="expenses_manage" name="permissions[]" value="expenses.manage" <?= in_array('expenses.manage', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="expenses_manage">Gestión de gastos</label>
                                                <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- DOCUMENTOS -->
                                <div class="col-12">
                                    <div class="card mb-0 shadow-none border">
                                        <div class="card-header bg-primary-subtle">
                                            <h6 class="mb-0 text-primary d-flex align-items-center gap-2">
                                                <i class="ti ti-file-text fs-5"></i> Documentos
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="documents_received" name="permissions[]" value="documents.received" <?= in_array('documents.received', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="documents_received">Ver mis recibidos</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="documents_send" name="permissions[]" value="documents.send" <?= in_array('documents.send', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="documents_send">Enviar individual</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="documents_sent" name="permissions[]" value="documents.sent" <?= in_array('documents.sent', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="documents_sent">Ver mis enviados</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="documents_manage" name="permissions[]" value="documents.manage" <?= in_array('documents.manage', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="documents_manage">Envíos masivos</label>
                                                <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ADMINISTRACIÓN -->
                                <div class="col-12">
                                    <div class="card mb-0 shadow-none border border-danger">
                                        <div class="card-header bg-danger-subtle">
                                            <h6 class="mb-0 text-danger d-flex align-items-center gap-2">
                                                <i class="ti ti-shield-lock fs-5"></i> Administración General
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="admin_users" name="permissions[]" value="admin.users" <?= in_array('admin.users', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="admin_users">Gestionar Usuarios</label>
                                                <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="admin_roles" name="permissions[]" value="admin.roles" <?= in_array('admin.roles', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="admin_roles">Gestionar Roles</label>
                                                <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="admin_company" name="permissions[]" value="admin.company" <?= in_array('admin.company', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="admin_company">Ajustes de la empresa</label>
                                                <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="admin_logs" name="permissions[]" value="admin.logs" <?= in_array('admin.logs', $rolePermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="admin_logs">Registro de actividad</label>
                                                <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Botón de enviar
                        // ================================================================================= -->
                        <div class="d-grids pt-5 text-center">
                            <button type="submit" class="btn btn-primary">Registrar</button>
                            <a href="<?= base_url('roles/list') ?>" class="btn btn-dark ms-1">Volver atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>