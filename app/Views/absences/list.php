<div class="container">
    <!-- =================================================================================
    // Mis Solicitudes de Ausencia
    // ================================================================================= -->
    <div class="card">
        <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">Mis Solicitudes de Ausencia</h5>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <form method="get" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="type_filter" class="form-label fw-semibold text-muted small uppercase">Tipo</label>
                    <select name="type" id="type_filter" class="form-select">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($absenceTypes as $key => $type): ?>
                        <option value="<?= esc($key) ?>" <?= (request()->getGet('type') == $key) ? 'selected' : '' ?>>
                            <?= esc($type) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label fw-semibold text-muted small uppercase">Estado</label>
                    <select name="status" id="status" class="form-select">
                        <option value="pending" <?= ($current_status ?? 'pending') === 'pending' ? 'selected' : '' ?>>Pendientes</option>
                        <option value="approved" <?= ($current_status ?? '') === 'approved' ? 'selected' : '' ?>>Aprobadas</option>
                        <option value="rejected" <?= ($current_status ?? '') === 'rejected' ? 'selected' : '' ?>>Rechazadas</option>
                        <option value="cancelled" <?= ($current_status ?? '') === 'cancelled' ? 'selected' : '' ?>>Canceladas</option>
                        <option value="" <?= ($current_status ?? '') === '' ? 'selected' : '' ?>>Todos</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="daterange" class="form-label fw-semibold text-muted small uppercase">Rango de Fechas</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="ti ti-calendar fs-5"></i></span>
                        <input type="text" id="daterange" class="form-control" placeholder="Selecciona el rango de fechas" readonly>
                    </div>
                    <input type="hidden" name="date_from" id="date_from" value="<?= esc(request()->getGet('date_from')) ?>">
                    <input type="hidden" name="date_to" id="date_to" value="<?= esc(request()->getGet('date_to')) ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-icon-lg" title="Filtrar">
                        <i class="ti ti-filter fs-5"></i>
                    </button>
                    <a href="<?= base_url('absences/list') ?>" class="btn btn-outline-primary btn-icon-lg" title="Limpiar">
                        <i class="ti ti-refresh fs-5"></i>
                    </a>
                    <a href="<?= base_url('absences/export-list-pdf') . '?' . http_build_query($_GET) ?>" class="btn btn-outline-primary btn-icon-lg" title="Exportar PDF">
                        <i class="ti ti-file-download fs-5"></i>
                    </a>
                </div>
            </form>

            <!-- Buscador de solicitudes -->
            <div class="mb-3">
                <input type="text" id="absenceTableSearch" class="form-control" placeholder="Buscar solicitudes...">
            </div>
            <!-- Fin de buscador de solicitudes -->

            <div class="mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle table-hover">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th class="d-none d-md-table-cell text-center">Fecha Inicio</th>
                            <th class="d-none d-md-table-cell text-center">Fecha Fin</th>
                            <th class="text-center d-none d-md-table-cell">Estado</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($absences)): ?>
                        <?php foreach ($absences as $absence): ?>
                        <tr onclick="window.location='<?= base_url('absences/view/' . $absence['id']) ?>'" class="cursor-pointer">
                            <td>
                                <div>
                                    <h6 class="fs-4 fw-semibold mb-0 mb-1">
                                        <?= $absenceTypes[$absence['type']] ?? $absence['type'] ?>
                                    </h6>
                                </div>
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
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                            href="<?= base_url('absences/view/' . $absence['id']) ?>">
                                             <i class="ti ti-eye"></i> Ver detalles
                                         </a>
                                        </li>
                                        <?php if ($absence['status'] == 'pending'): ?>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                                href="<?= base_url('absences/edit/' . $absence['id']) ?>">
                                                <i class="ti ti-pencil"></i> Editar
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3 cancel-absence-swal"
                                                href="#" data-url="<?= base_url('absences/cancel/' . $absence['id']) ?>">
                                                <i class="ti ti-circle-x"></i> Cancelar
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay solicitudes de ausencia.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if (!empty($absences)): ?>
            <div class="d-flex justify-content-center mt-3">
                <?= $pager->links() ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

