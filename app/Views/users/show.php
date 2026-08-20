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
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#qrModal"><i class="ti ti-qrcode"></i> Ver QR</button>
                            </div>
                        </div>
                    </div>

                    <!-- =================================================================================
                    // Permisos modulares (Granulares)
                    // ================================================================================= -->
                    <?php
                    // Permisos individuales del usuario; si no tiene, hereda los del rol
                    $userPermissions = isset($user['permissions']) ? json_decode($user['permissions'], true) : null;
                    if (empty($userPermissions)) {
                        $userPermissions = isset($user['role_permissions']) ? json_decode($user['role_permissions'], true) : [];
                    }
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
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('documents.received', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Ver mis recibidos</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('documents.send', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Enviar individual</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('documents.sent', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Ver mis enviados</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('documents.manage', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Envíos masivos</label>
                                            <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- USUARIOS Y AJUSTES -->
                            <div class="col-12">
                                <div class="card mb-0 shadow-none border border-danger">
                                    <div class="card-header bg-danger-subtle">
                                        <h6 class="mb-0 text-danger d-flex align-items-center gap-2">
                                            <i class="ti ti-shield-lock fs-5"></i> Administración General
                                        </h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('admin.users', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Gestionar Usuarios</label>
                                            <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('admin.roles', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Gestionar Roles</label>
                                            <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('admin.company', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Ajustes de la empresa</label>
                                            <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" disabled <?= in_array('admin.logs', $userPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fs-3">Registro de actividad</label>
                                            <i class="ti ti-shield-check fs-4 ms-2 text-danger" title="Requiere permisos de administrador"></i>
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

<!-- Modal QR Kiosco -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-4">
                <h5 class="modal-title mb-4 text-primary">Credencial Kiosco</h5>
                <?php if(!empty($user['kiosk_token'])): ?>
                    <div class="bg-white p-3 rounded-4 d-inline-block shadow-sm border mb-3">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= esc($user['kiosk_token']) ?>" alt="QR Kiosco" class="img-fluid" style="width: 200px; height: 200px;">
                    </div>
                    <p class="mb-0 text-muted small px-3">Imprime este QR para que <?= esc($user['name']) ?> pueda fichar escaneándolo en el terminal.</p>
                <?php else: ?>
                    <p class="text-danger mb-0">Este usuario aún no tiene un token generado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
