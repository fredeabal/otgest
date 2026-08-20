<!-- =================================================================================
    // Vista: Gestión de Jornadas
    // ================================================================================= -->
<div class="container">

    <!-- =================================================================================
    // Gestión de Jornadas
    // ================================================================================= -->
    <div class="card">
        <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">Gestión de Jornadas</h5>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <form method="get" class="mb-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label for="user_id" class="form-label fw-semibold text-muted small uppercase">Usuario</label>
                        <select name="user_id" id="user_id" class="select2">
                            <option value="">Todos los usuarios</option>
                            <?php foreach ($users as $user): ?>
                            <option value="<?= esc($user['id']) ?>" <?= ($user_id == $user['id']) ? 'selected' : '' ?>>
                                <?= esc($user['name']) ?> (<?= esc($user['identification']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label fw-semibold text-muted small uppercase">Estado</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>>Completada</option>
                            <option value="in_progress" <?= ($status ?? '') === 'in_progress' ? 'selected' : '' ?>>En curso</option>
                            <option value="pause" <?= ($status ?? '') === 'pause' ? 'selected' : '' ?>>En pausa</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="daterange" class="form-label fw-semibold text-muted small uppercase">Rango de Fechas</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="ti ti-calendar fs-5"></i></span>
                            <input type="text" id="daterange" class="form-control" placeholder="Selecciona el rango de fechas" readonly>
                        </div>
                        <input type="hidden" name="date_from" id="date_from" value="<?= esc($date_from) ?>">
                        <input type="hidden" name="date_to" id="date_to" value="<?= esc($date_to) ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary btn-icon-lg" title="Filtrar">
                            <i class="ti ti-filter fs-5"></i>
                        </button>
                        <a href="<?= base_url('workdays/manage') ?>" class="btn btn-outline-primary btn-icon-lg" title="Limpiar">
                            <i class="ti ti-refresh fs-5"></i>
                        </a>
                        <a href="<?= base_url('workdays/export-pdf') . '?' . http_build_query($_GET) ?>" class="btn btn-outline-primary btn-icon-lg" title="Exportar PDF">
                            <i class="ti ti-file fs-5"></i>
                        </a>
                    </div>
                </div>
            </form>

            <span class="text-muted d-block mb-2 d-md-none">Deslice para ver la tabla <i class="ti ti-arrow-right ms-2"></i></span>
            <div class="mb-4 border rounded-1 table-responsive">
                <table class="table text-nowrap mb-0 align-middle table-hover">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th class="text-center d-none d-md-table-cell">Inicio</th>
                            <th class="text-center d-none d-md-table-cell">Fin</th>
                            <th class="text-center">Horas totales</th>
                            <th class="text-center d-none d-md-table-cell">Horas extras</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($workdays)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No se encontraron jornadas con los filtros aplicados.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($workdays as $workday): ?>
                        <tr onclick="window.location='<?= base_url('workdays/view/' . $workday['date'] . '?user_id=' . $workday['user_id']) ?>'" class="cursor-pointer">
                            <td>
                                <div>
                                    <strong><?= esc($workday['user_name']) ?></strong><br>
                                    <small class="text-muted"><?= esc($workday['user_identification']) ?></small>
                                </div>
                            </td>
                            <td>
                                <?= esc(date('d/m/Y', strtotime($workday['date']))) ?>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <?php if ($workday['start_time']): ?>
                                    <?= esc($workday['start_time']) ?><br>
                                    <small class="text-muted"><?= esc($workday['start_date']) ?></small>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <?php if ($workday['end_time']): ?>
                                    <?= esc($workday['end_time']) ?><br>
                                    <small class="text-muted"><?= esc($workday['end_date']) ?></small>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info fw-semibold text-small border border-info fs-2 w-100px-inline"
                                      >
                                    <?php
                                    $totalMinutes = round($workday['total_hours'] * 60);
                                    $hours = floor($totalMinutes / 60);
                                    $minutes = $totalMinutes % 60;
                                    echo $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                                    ?> h
                                </span>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <span class="badge bg-warning-subtle text-warning fw-semibold text-small border border-warning fs-2 w-100px-inline"
                                      >
                                    <?php
                                    $overtimeMinutes = round(($workday['overtime_hours'] ?? 0) * 60);
                                    $hours = floor($overtimeMinutes / 60);
                                    $minutes = $overtimeMinutes % 60;
                                    echo $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                                    ?> h
                                </span>
                            </td>
                            <td class="text-center">
                                <?php
                                $statusClass = [
                                    'completed' => 'success',
                                    'in_progress' => 'warning',
                                    'pause' => 'info'
                                ];
                                $statusText = [
                                    'completed' => 'Completada',
                                    'in_progress' => 'En curso',
                                    'pause' => 'En pausa'
                                ];
                                ?>
                                <span class="badge bg-<?= $statusClass[$workday['status']] ?? 'secondary' ?>-subtle text-<?= $statusClass[$workday['status']] ?? 'secondary' ?> fw-semibold border border-<?= $statusClass[$workday['status']] ?? 'secondary' ?> fs-2 w-100px-inline">
                                    <?= $statusText[$workday['status']] ?? $workday['status'] ?>
                                </span>
                            </td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                <!-- Acciones como dropdown Modernize/CoreUI -->
                                <div class="dropdown dropstart">
                                    <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown"
                                       aria-expanded="false">
                                        <i class="ti ti-dots-vertical fs-7"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                                href="<?= base_url('workdays/view/' . $workday['date'] . '?user_id=' . $workday['user_id']) ?>">
                                                <i class="ti ti-eye"></i> Ver detalles
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                                href="javascript:void(0)" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editWorkdayModal"
                                                onclick="loadEditWorkday('<?= $workday['date'] ?>', <?= $workday['user_id'] ?>, '<?= $workday['start_time'] ?? '' ?>', '<?= $workday['end_time'] ?? '' ?>', '<?= esc(addslashes($workday['user_name'])) ?>')">
                                                <i class="ti ti-edit"></i> Editar
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if (!empty($workdays)): ?>
            <div class="d-flex justify-content-center mt-4">
                <?= $pager ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Edición de Jornada -->
<div class="modal fade" id="editWorkdayModal" tabindex="-1" aria-labelledby="editWorkdayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWorkdayModalLabel">Editar Jornada - <span id="editUserName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('workdays/edit') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" id="editUserId">
                <input type="hidden" name="workday_date" id="editWorkdayDate">
                
                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                        <i class="ti ti-alert-circle fs-5 me-2"></i>
                        <div>
                            Toda modificación quedará registrada en el historial de auditoría por motivos legales.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Hora de Entrada <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="editStartTime" name="start_time" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="end_time" class="form-label">Hora de Salida</label>
                        <input type="time" class="form-control" id="editEndTime" name="end_time">
                        <div class="form-text text-info"><i class="ti ti-info-circle"></i> Si el usuario olvidó fichar la salida, puedes establecerla aquí. Si guardas sin hora de salida, la jornada continuará en curso.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reason" class="form-label">Motivo de la modificación <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="editReason" name="reason" rows="2" required placeholder="Ej: El trabajador olvidó fichar la salida el día de ayer..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadEditWorkday(date, userId, startTime, endTime, userName) {
    document.getElementById('editUserName').innerText = userName;
    document.getElementById('editUserId').value = userId;
    document.getElementById('editWorkdayDate').value = date;
    document.getElementById('editStartTime').value = startTime;
    document.getElementById('editEndTime').value = endTime;
    document.getElementById('editReason').value = ''; // Limpiar siempre el motivo anterior
}
</script>