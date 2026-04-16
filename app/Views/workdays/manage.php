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
            <form method="get" class="row g-3 mb-4">
                <div class="col-md-2">
                    <label for="user_id" class="form-label">Usuario</label>
                    <select name="user_id" id="user_id" class="form-select" required>
                        <?php foreach ($users as $user): ?>
                        <option value="<?= esc($user['id']) ?>" <?= ($user_id == $user['id']) ? 'selected' : '' ?>>
                            <?= esc($user['name']) ?> (<?= esc($user['identification']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Estado</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>>Completada</option>
                        <option value="in_progress" <?= ($status ?? '') === 'in_progress' ? 'selected' : '' ?>>En curso</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Fecha desde</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                           value="<?= esc($date_from) ?>">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Fecha hasta</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                           value="<?= esc($date_to) ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-icon me-2">
                        <iconify-icon icon="solar:filter-bold-duotone"></iconify-icon>
                    </button>
                    <a href="<?= base_url('workdays/manage') ?>" class="btn btn-outline-muted btn-icon me-2">
                        <iconify-icon icon="solar:close-circle-bold-duotone"></iconify-icon>
                    </a>
                    <a href="<?= base_url('workdays/export-pdf') . '?' . http_build_query($_GET) ?>" class="btn btn-warning btn-icon">
                        <iconify-icon icon="solar:file-bold-duotone"></iconify-icon>
                    </a>
                </div>
            </form>

            <span class="text-muted d-block mb-2 d-md-none">Deslice para ver la tabla <iconify-icon icon="solar:alt-arrow-right-bold-duotone" class="ms-2"></iconify-icon></span>
            <div class="mb-4 border rounded-1 table-responsive">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th class="text-center d-none d-md-table-cell">Inicio</th>
                            <th class="text-center d-none d-md-table-cell">Fin</th>
                            <th class="text-center">Horas totales</th>
                            <th class="text-center d-none d-md-table-cell">Horas extras</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($workdays)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No se encontraron jornadas con los filtros aplicados.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($workdays as $workday): ?>
                        <tr>
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
                                <span class="badge bg-info-subtle text-info fw-semibold text-small border border-info fs-2"
                                      style="min-width: 70px; display: inline-block;">
                                    <?php
                                    $totalMinutes = $workday['total_hours'] * 60;
                                    $hours = floor($totalMinutes / 60);
                                    $minutes = floor($totalMinutes % 60);
                                    echo $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                                    ?> h
                                </span>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <span class="badge bg-warning-subtle text-warning fw-semibold text-small border border-warning fs-2"
                                      style="min-width: 70px; display: inline-block;">
                                    <?php
                                    $overtimeMinutes = $workday['overtime_hours'] * 60;
                                    $hours = floor($overtimeMinutes / 60);
                                    $minutes = floor($overtimeMinutes % 60);
                                    echo $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                                    ?> h
                                </span>
                            </td>
                            <td class="text-center">
                                <?php
                                $statusClass = [
                                    'completed' => 'success',
                                    'in_progress' => 'warning'
                                ];
                                $statusText = [
                                    'completed' => 'Completada',
                                    'in_progress' => 'En curso'
                                ];
                                ?>
                                <span class="badge bg-<?= $statusClass[$workday['status']] ?? 'secondary' ?>-subtle text-<?= $statusClass[$workday['status']] ?? 'secondary' ?> fw-semibold border border-<?= $statusClass[$workday['status']] ?? 'secondary' ?> fs-2"
                                    style="min-width: 70px; display: inline-block;">
                                    <?= $statusText[$workday['status']] ?? $workday['status'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <!-- Acciones como dropdown Modernize/CoreUI -->
                                <div class="dropdown dropstart">
                                    <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown"
                                       aria-expanded="false">
                                        <iconify-icon icon="solar:sort-bold-duotone" class="fs-7"></iconify-icon>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                                href="<?= base_url('workdays/view/' . $workday['date'] . '?user_id=' . $workday['user_id']) ?>">
                                                <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon> Ver detalles
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