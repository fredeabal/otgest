<!-- =================================================================================
    // Vista: Mis Registros de Jornada
    // ================================================================================= -->
<div class="container">

    <!-- =================================================================================
    // Mis Registros de Jornada
    // ================================================================================= -->
    <div class="card">
        <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">Mis Registros de Jornada</h5>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <form method="get" class="row g-3 mb-4">
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
                <div class="col-md-2">
                    <label for="status" class="form-label">Estado</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>>Completada</option>
                        <option value="in_progress" <?= ($status ?? '') === 'in_progress' ? 'selected' : '' ?>>En progreso</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-icon me-2">
                        <i class="ti ti-filter"></i>
                    </button>
                    <a href="<?= base_url('workdays/my-records') ?>" class="btn btn-outline-primary btn-icon me-2">
                        <i class="ti ti-circle-x"></i>
                    </a>
                    <a href="<?= base_url('workdays/export-my-pdf') . '?' . http_build_query($_GET) ?>" class="btn btn-outline-primary btn-icon">
                        <i class="ti ti-file"></i>
                    </a>
                </div>
            </form>
            <!-- Fin de buscador de jornadas -->

            <div class="mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th class="text-center d-none d-md-table-cell">Inicio</th>
                            <th class="text-center d-none d-md-table-cell">Fin</th>
                            <th class="text-center">Horas totales</th>
                            <th class="text-center d-none d-md-table-cell">Horas extras</th>
                            <th class="text-center d-none d-md-table-cell">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($workdays)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No tienes jornadas registradas en el período seleccionado.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($workdays as $workday): ?>
                        <tr>
                            <!-- Fecha -->
                            <td>
                                <?= esc(date('d/m/Y', strtotime($workday['date']))) ?>
                            </td>
                            <!-- Hora de inicio -->
                            <td class="text-center d-none d-md-table-cell">
                                <?php if ($workday['start_time']): ?>
                                    <?= esc($workday['start_time']) ?><br>
                                    <small class="text-muted"><?= esc($workday['start_date']) ?></small>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <!-- Hora de fin -->
                            <td class="text-center d-none d-md-table-cell">
                                <?php if ($workday['end_time']): ?>
                                    <?= esc($workday['end_time']) ?><br>
                                    <small class="text-muted"><?= esc($workday['end_date']) ?></small>
                                <?php elseif ($workday['autoclose']): ?>
                                    <span class="badge bg-warning-subtle text-warning fw-semibold border border-warning fs-2 w-70px-inline"
                                        >Automático</span><br>
                                    <small class="text-muted"><?= esc($workday['end_date']) ?></small>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <!-- Horas totales -->
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info fw-semibold text-small border border-info fs-2 w-70px-inline"
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
                                <span class="badge bg-warning-subtle text-warning fw-semibold text-small border border-warning fs-2 w-70px-inline"
                                    >
                                    <?php
                                    $overtimeMinutes = round(($workday['overtime_hours'] ?? 0) * 60);
                                    $hours = floor($overtimeMinutes / 60);
                                    $minutes = $overtimeMinutes % 60;
                                    echo $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                                    ?> h
                                </span>
                            </td>

                            <td class="text-center d-none d-md-table-cell">
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
                                <span class="badge bg-<?= $statusClass[$workday['status']] ?? 'secondary' ?>-subtle text-<?= $statusClass[$workday['status']] ?? 'secondary' ?> fw-semibold border border-<?= $statusClass[$workday['status']] ?? 'secondary' ?> fs-2 w-70px-inline">
                                    <?= $statusText[$workday['status']] ?? $workday['status'] ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <!-- Acciones como dropdown Modernize/CoreUI -->
                                <div class="dropdown dropstart">
                                    <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ti ti-dots-vertical fs-7"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                                href="<?= base_url('workdays/view/' . $workday['date']) ?>">
                                                <i class="ti ti-eye"></i> Ver detalles
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