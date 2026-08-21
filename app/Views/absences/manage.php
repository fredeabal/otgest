<div class="container">
    <!-- =================================================================================
    // Panel de Administración de Solicitudes de Ausencia
    // ================================================================================= -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0 text-primary">Gestión de Solicitudes de Ausencia</h5>
                </div>
                <div class="card-body">

                    <!-- Filtros -->
                    <form method="get" class="mb-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="user_filter" class="form-label fw-semibold text-muted small uppercase">Usuario</label>
                                <select name="user_id" id="user_filter" class="select2">
                                    <option value="">Todos los usuarios</option>
                                    <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>"
                                        <?= (isset($_GET['user_id']) && $_GET['user_id'] == $user['id']) ? 'selected' : '' ?>>
                                        <?= esc($user['name']) ?> (<?= esc($user['identification']) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="type_filter" class="form-label fw-semibold text-muted small uppercase">Tipo</label>
                                <select name="type" id="type_filter" class="form-select">
                                    <option value="">Todos los tipos</option>
                                    <?php foreach ($absenceTypes as $key => $type): ?>
                                    <option value="<?= $key ?>" <?= (isset($_GET['type']) && $_GET['type'] == $key) ? 'selected' : '' ?>>
                                        <?= esc($type) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label fw-semibold text-muted small uppercase">Estado</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="pending" <?= ($current_status ?? '') === 'pending' ? 'selected' : '' ?>>Pendientes</option>
                                    <option value="approved" <?= ($current_status ?? '') === 'approved' ? 'selected' : '' ?>>Aprobadas</option>
                                    <option value="rejected" <?= ($current_status ?? '') === 'rejected' ? 'selected' : '' ?>>Rechazadas</option>
                                    <option value="cancelled" <?= ($current_status ?? '') === 'cancelled' ? 'selected' : '' ?>>Canceladas</option>
                                    <option value="all" <?= empty($current_status) || $current_status === 'all' ? 'selected' : '' ?>>Todas</option>
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
                                <input type="hidden" name="date_from" id="date_from" value="<?= esc($_GET['date_from'] ?? '') ?>">
                                <input type="hidden" name="date_to" id="date_to" value="<?= esc($_GET['date_to'] ?? '') ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary btn-icon-lg" title="Filtrar">
                                    <i class="ti ti-filter fs-5"></i>
                                </button>
                                <a href="<?= base_url('absences/manage') ?>" class="btn btn-outline-primary btn-icon-lg" title="Limpiar">
                                    <i class="ti ti-refresh fs-5"></i>
                                </a>
                                <a href="<?= base_url('absences/export-pdf') . '?' . http_build_query($_GET) ?>" class="btn btn-outline-primary btn-icon-lg" title="Exportar PDF">
                                    <i class="ti ti-file-description fs-5"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Buscador de solicitudes -->
                    <div class="mb-3">
                        <input type="text" id="absenceTableSearch" class="form-control"
                            placeholder="Buscar solicitudes...">
                    </div>
                    <!-- Fin de buscador de solicitudes -->

                    <div class="mb-4 border rounded-1">
                        <table class="table text-nowrap mb-0 align-middle table-hover">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th class="d-none d-md-table-cell">Tipo</th>
                                    <th class="text-center d-none d-md-table-cell">Fecha Inicio</th>
                                    <th class="text-center d-none d-md-table-cell">Fecha Fin</th>
                                    <th class="text-center d-none d-md-table-cell">Estado</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($absences)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay solicitudes de ausencia.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($absences as $absence): ?>
                                <tr onclick="window.location='<?= base_url('absences/view/' . $absence['id']) ?>'" class="cursor-pointer">
                                    <td>
                                        <div>
                                            <h6 class="fs-4 fw-semibold mb-0 d-inline-block text-truncate" style="max-width: 200px;">
                                                <?= esc($absence['user_name']) ?>
                                            </h6>
                                            <span class="fw-normal text-muted small d-block">
                                                ID: <?= esc($absence['user_identification']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <h6 class="fw-semibold mb-0">
                                            <?= $absenceTypes[$absence['type']] ?? $absence['type'] ?>
                                        </h6>
                                    </td>
                                    <td class="text-center d-none d-md-table-cell">
                                        <div>
                                            <h6 class="fw-semibold mb-0 mb-1">
                                                <?= esc(date('d/m/Y', strtotime($absence['start_date']))) ?>
                                            </h6>
                                            <?php if ($absence['start_time']): ?>
                                            <span class="fw-normal text-muted small">
                                                <?= esc(date('H:i', strtotime($absence['start_time']))) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center d-none d-md-table-cell">
                                        <div>
                                            <h6 class="fw-semibold mb-0 mb-1">
                                                <?= esc(date('d/m/Y', strtotime($absence['end_date']))) ?>
                                            </h6>
                                            <?php if ($absence['end_time']): ?>
                                            <span class="fw-normal text-muted small">
                                                <?= esc(date('H:i', strtotime($absence['end_time']))) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center d-none d-md-table-cell">
                                        <?php
                                $statusClass = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'cancelled' => 'secondary'
                                ];
                                ?>
                                        <span
                                            class="badge bg-<?= $statusClass[$absence['status']] ?? 'secondary' ?>-subtle text-<?= $statusClass[$absence['status']] ?? 'secondary' ?> fw-semibold border border-<?= $statusClass[$absence['status']] ?? 'secondary' ?> fs-2 w-100px-inline">
                                            <?= $statusLabels[$absence['status']] ?? $absence['status'] ?>
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
                                                <?php if ($absence['status'] == 'pending'): ?>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-3 approve-absence-swal"
                                                        href="#"
                                                        data-url="<?= base_url('absences/approve/' . $absence['id']) ?>"
                                                        data-method="post">
                                                        <i class="ti ti-circle-check"></i> Aprobar
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-3 reject-absence-swal"
                                                        href="#" data-id="<?= $absence['id'] ?>">
                                                        <i class="ti ti-circle-x"></i> Rechazar
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-3"
                                                        href="<?= base_url('absences/view/' . $absence['id']) ?>">
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
                    <div class="d-flex justify-content-center mt-3">
                        <?= $pager->links() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


