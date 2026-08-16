<div class="container">

    <!-- =================================================================================
// Formulario de Registro de Usuario
// ================================================================================= -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0 text-primary">Crear usuario</h5>
                </div>
                <div class="card-body">

                    <form action="<?= base_url('users/store') ?>" method="post" autocomplete="off">
                        <?= csrf_field() ?>
                        <!-- =================================================================================
                        // Fila: Identificación y Nombre
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="identification" class="form-label">Número de identificación</label>
                                <input type="text" class="form-control" id="identification" name="identification"
                                    value="<?= old('identification') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?= old('name') ?>">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Fecha de nacimiento y Correo
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="birthdate" class="form-label">Fecha de nacimiento</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="expense_date_display"
                                        value="<?= old('birthdate') ? date('d/m/Y', strtotime(old('birthdate'))) : '' ?>" placeholder="dd/mm/yyyy" autocomplete="off" readonly>
                                    <span class="input-group-text bg-transparent"><i class="ti ti-calendar fs-5"></i></span>
                                </div>
                                <input type="hidden" name="birthdate" id="expense_date" value="<?= old('birthdate') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= old('email') ?>">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Dirección
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="address" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="address" name="address" maxlength="255"
                                    value="<?= old('address') ?>">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Contraseña y Confirmar contraseña
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Mínimo 8 caracteres" autocomplete="new-password">
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirm" class="form-label">Confirmar contraseña</label>
                                <input type="password" class="form-control" id="password_confirm"
                                    name="password_confirm" placeholder="Repite la contraseña"
                                    autocomplete="new-password">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Jornada laboral y Horas máximas
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="daily_hours" class="form-label">Jornada laboral</label>
                                <select class="form-select" id="daily_hours" name="daily_hours">
                                    <option value="">Selecciona horas</option>
                                    <?php for ($i = 1; $i <= 24; $i++): ?>
                                    <option value="<?= $i ?>" <?= old('daily_hours') == $i ? 'selected' : '' ?>>
                                        <?= $i ?> horas</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="max_daily_hours" class="form-label">Horas máximas permitidas</label>
                                <select class="form-select" id="max_daily_hours" name="max_daily_hours">
                                    <option value="">Selecciona horas</option>
                                    <?php for ($i = 1; $i <= 24; $i++): ?>
                                    <option value="<?= $i ?>" <?= old('max_daily_hours') == $i ? 'selected' : '' ?>>
                                        <?= $i ?> horas</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="vacation_days" class="form-label">Vacaciones anuales</label>
                                <input type="number" class="form-control" id="vacation_days" name="vacation_days" min="0" placeholder="Ej. 22" value="<?= old('vacation_days', '22') ?>">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Rol y Estado
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="role_id" class="form-label">Rol</label>
                                <select class="form-select" id="role_id" name="role_id">
                                    <option value="">Selecciona un rol</option>
                                    <?php foreach ($roles as $rol): ?>
                                    <option value="<?= esc($rol['id']) ?>"
                                        <?= old('role_id') == $rol['id'] ? 'selected' : '' ?>><?= esc($rol['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="is_active" class="form-label">Estado</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1" <?= old('is_active', '1') == '1' ? 'selected' : '' ?>>Activo
                                    </option>
                                    <option value="0" <?= old('is_active') == '0' ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Permisos modulares (Granulares)
                        // ================================================================================= -->
                        <?php $userPermissions = is_array(old('permissions')) ? old('permissions') : []; ?>
                        <div class="my-4" id="permissions-block">
                            <label class="form-label d-block mb-4 fw-semibold fs-4 text-primary border-bottom pb-2">Permisos modulares</label>
                            
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
                                                <input class="form-check-input" type="checkbox" id="workdays_clockin" name="permissions[]" value="workdays.clockin" <?= in_array('workdays.clockin', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="workdays_clockin">Fichar (Iniciar/Pausar)</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="workdays_records" name="permissions[]" value="workdays.records" <?= in_array('workdays.records', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="workdays_records">Mis registros</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="workdays_manage" name="permissions[]" value="workdays.manage" <?= in_array('workdays.manage', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="workdays_manage">Gestión de jornadas</label>
                                                <i class="ti ti-user-cog ms-3 text-danger"></i>
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
                                                <input class="form-check-input" type="checkbox" id="absences_request" name="permissions[]" value="absences.request" <?= in_array('absences.request', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="absences_request">Solicitar ausencias</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="absences_list" name="permissions[]" value="absences.list" <?= in_array('absences.list', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="absences_list">Ver mi historial</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="absences_manage" name="permissions[]" value="absences.manage" <?= in_array('absences.manage', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="absences_manage">Gestión de ausencias</label>
                                                <i class="ti ti-user-cog ms-3 text-danger"></i>
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
                                                <input class="form-check-input" type="checkbox" id="expenses_create" name="permissions[]" value="expenses.create" <?= in_array('expenses.create', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="expenses_create">Registrar ticket</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="expenses_my" name="permissions[]" value="expenses.my" <?= in_array('expenses.my', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="expenses_my">Mis gastos</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="expenses_manage" name="permissions[]" value="expenses.manage" <?= in_array('expenses.manage', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="expenses_manage">Gestión de gastos</label>
                                                <i class="ti ti-user-cog ms-3 text-danger"></i>
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
                                                <input class="form-check-input" type="checkbox" id="documents_received" name="permissions[]" value="documents.received" <?= in_array('documents.received', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="documents_received">Ver mis recibidos</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="documents_send" name="permissions[]" value="documents.send" <?= in_array('documents.send', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="documents_send">Enviar individual</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="documents_sent" name="permissions[]" value="documents.sent" <?= in_array('documents.sent', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="documents_sent">Ver mis enviados</label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="documents_manage" name="permissions[]" value="documents.manage" <?= in_array('documents.manage', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="documents_manage">Envíos masivos</label>
                                                <i class="ti ti-user-cog ms-3 text-danger"></i>
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
                                                <input class="form-check-input" type="checkbox" id="admin_users" name="permissions[]" value="admin.users" <?= in_array('admin.users', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="admin_users">Gestionar Usuarios</label>
                                                <i class="ti ti-user-cog ms-3 text-danger"></i>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="admin_roles" name="permissions[]" value="admin.roles" <?= in_array('admin.roles', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="admin_roles">Gestionar Roles</label>
                                                <i class="ti ti-user-cog ms-3 text-danger"></i>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="admin_company" name="permissions[]" value="admin.company" <?= in_array('admin.company', $userPermissions) ? 'checked' : '' ?>>
                                                <label class="form-check-label fs-3" for="admin_company">Ajustes de la empresa</label>
                                                <i class="ti ti-user-cog ms-3 text-danger"></i>
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
                            <a href="<?= base_url('users/list') ?>" class="btn btn-dark ms-1">Volver atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role_id');
    
    // Generar plantillas de permisos desde la base de datos (PHP a JS)
    const allPermissions = {};
    <?php foreach ($roles as $rol): ?>
        allPermissions["<?= esc($rol['id']) ?>"] = <?= !empty($rol['permissions']) ? $rol['permissions'] : '[]' ?>;
    <?php endforeach; ?>

    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            if (this.selectedIndex === 0) return; // No selection
            
            const roleId = this.options[this.selectedIndex].value;
            const preset = allPermissions[roleId] || [];
            
            if (preset.length > 0) {
                // Uncheck all first
                document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                    cb.checked = false;
                });
                
                // Check preset permissions
                preset.forEach(perm => {
                    const cb = document.querySelector(`input[name="permissions[]"][value="${perm}"]`);
                    if (cb) cb.checked = true;
                });
            }
        });
    }
});
</script>