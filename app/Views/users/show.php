<div class="container">

    <!-- =================================================================================
    // Detalles de Usuario (Solo Lectura)
    // ================================================================================= -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">Detalles de usuario</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12 text-center">
                            <img src="<?= base_url('users/avatar/' . esc($user['avatar'])) ?>" class="rounded-circle shadow-sm"
                                width="120" height="120" alt="Avatar" />
                        </div>
                    </div>

                    <!-- =================================================================================
                    // Fila: Identificación y Nombre
                    // ================================================================================= -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Número de identificación</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($user['identification']) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($user['name']) ?>" readonly>
                        </div>
                    </div>
                    <!-- =================================================================================
                    // Fila: Fecha de nacimiento y Correo
                    // ================================================================================= -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha de nacimiento</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" value="<?= !empty($user['birthdate']) ? date('d/m/Y', strtotime($user['birthdate'])) : '' ?>" readonly>
                                <span class="input-group-text bg-light"><i class="ti ti-calendar fs-5"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control bg-light" value="<?= esc($user['email']) ?>" readonly>
                        </div>
                    </div>
                    <!-- =================================================================================
                    // Fila: Dirección
                    // ================================================================================= -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($user['address']) ?>" readonly>
                        </div>
                    </div>
                    
                    <!-- =================================================================================
                    // Fila: Jornada laboral y Horas máximas
                    // ================================================================================= -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Jornada laboral</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($user['daily_hours'] ?? '') ?> horas" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Horas máximas</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($user['max_daily_hours'] ?? '') ?> horas" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Vacaciones anuales</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($user['vacation_days'] ?? '22') ?> días" readonly>
                        </div>
                    </div>
                    <!-- =================================================================================
                    // Fila: Rol y Estado
                    // ================================================================================= -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Rol</label>
                            <input type="text" class="form-control bg-light" value="<?= esc($user['role_name'] ?? 'Sin rol') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <input type="text" class="form-control bg-light text-<?= $user['is_active'] ? 'success' : 'danger' ?>" value="<?= $user['is_active'] ? 'Activo' : 'Inactivo' ?>" readonly>
                        </div>
                    </div>
                    <!-- =================================================================================
                    // Kiosco / Token NFC
                    // ================================================================================= -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label text-primary"><i class="ti ti-id-badge"></i> Credencial de Kiosco (NFC / QR)</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace text-muted bg-light" id="kiosk_token" value="<?= esc($user['kiosk_token'] ?? '') ?>" readonly>
                                <button class="btn btn-outline-primary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('kiosk_token').value).then(() => { Swal.fire({icon: 'success', title: 'Token copiado', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000}) })"><i class="ti ti-copy"></i> Copiar</button>
                            </div>
                        </div>
                    </div>

                    <!-- =================================================================================
                    // Permisos modulares (Granulares)
                    // ================================================================================= -->
                    <?php
                    // Decodificar los permisos actuales del usuario
                    $userPermissions = isset($user['permissions']) ? json_decode($user['permissions'], true) : [];
                    if (!is_array($userPermissions)) $userPermissions = [];
                    ?>
                    <div class="my-4" id="permissions-block">
                        <label class="form-label d-block mb-4 fw-semibold fs-4 text-primary border-bottom pb-2">Permisos modulares asignados</label>
                        
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
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('workdays.clockin', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Fichar (Iniciar/Pausar)</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('workdays.records', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Mis registros</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('workdays.manage', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Gestión de jornadas</label>
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
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('absences.request', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Solicitar ausencias</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('absences.list', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Ver mi historial</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('absences.manage', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Gestión de ausencias</label>
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
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('expenses.create', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Registrar ticket</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('expenses.my', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Mis gastos</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('expenses.manage', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Gestión de gastos</label>
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
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('documents.received', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Ver mis recibidos</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('documents.manage', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Gestión de documentos</label>
                                            <i class="ti ti-user-cog ms-3 text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- USUARIOS Y AJUSTES -->
                            <div class="col-12">
                                <div class="card mb-0 shadow-none border">
                                    <div class="card-header bg-primary-subtle">
                                        <h6 class="mb-0 text-primary d-flex align-items-center gap-2">
                                            <i class="ti ti-settings fs-5"></i> Administración
                                        </h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('admin.users', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Gestión de usuarios y roles</label>
                                            <i class="ti ti-shield ms-3 text-danger"></i>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('admin.settings', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Ajustes del sistema</label>
                                            <i class="ti ti-shield ms-3 text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grids pt-5 text-center">
                        <a href="<?= base_url('users/edit/' . $user['id']) ?>" class="btn btn-primary">Editar usuario</a>
                        <a href="<?= base_url('users/list') ?>" class="btn btn-dark ms-1">Volver atrás</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
