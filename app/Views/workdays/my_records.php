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
                <div class="col-md-6">
                    <label for="daterange" class="form-label fw-semibold text-muted small uppercase">Rango de Fechas</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="ti ti-calendar fs-5"></i></span>
                        <input type="text" id="daterange" class="form-control" placeholder="Selecciona el rango de fechas" readonly>
                    </div>
                    <input type="hidden" name="date_from" id="date_from" value="<?= esc($date_from) ?>">
                    <input type="hidden" name="date_to" id="date_to" value="<?= esc($date_to) ?>">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label fw-semibold text-muted small uppercase">Estado</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="completed" <?= ($status ?? '') === 'completed' ? 'selected' : '' ?>>Completada</option>
                        <option value="in_progress" <?= ($status ?? '') === 'in_progress' ? 'selected' : '' ?>>En progreso</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-icon-lg" title="Filtrar">
                        <i class="ti ti-filter fs-5"></i>
                    </button>
                    <a href="<?= base_url('workdays/my-records') ?>" class="btn btn-outline-primary btn-icon-lg" title="Limpiar">
                        <i class="ti ti-refresh fs-5"></i>
                    </a>
                    <a href="<?= base_url('workdays/export-my-pdf') . '?' . http_build_query($_GET) ?>" class="btn btn-outline-primary btn-icon-lg" title="Exportar PDF">
                        <i class="ti ti-file fs-5"></i>
                    </a>
                </div>
            </form>
            <!-- Fin de buscador de jornadas -->

            <div class="mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle table-hover">
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
                        <tr onclick="window.location='<?= base_url('workdays/view/' . $workday['date']) ?>'" class="cursor-pointer">
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
                                    <span class="badge bg-warning-subtle text-warning fw-semibold border border-warning fs-2 w-100px-inline"
                                        >Automático</span><br>
                                    <small class="text-muted"><?= esc($workday['end_date']) ?></small>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <!-- Horas totales -->
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