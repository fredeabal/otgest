<div class="container">

    <!-- =================================================================================
    // Registro de Actividad
    // ================================================================================= -->
    <div class="card">
        <div class="card-header bg-primary-subtle text-white">
            <h5 class="mb-0 text-primary">Registro de Actividad</h5>
        </div>
        <div class="card-body">

            <!-- Filtros -->
            <form action="<?= base_url('logs/list') ?>" method="GET" class="mb-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="user_id" class="form-label fw-semibold text-muted small uppercase">Usuario</label>
                        <select name="user_id" id="user_id" class="select2">
                            <option value="">Todos los usuarios</option>
                            <?php foreach($users as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= isset($_GET['user_id']) && $_GET['user_id'] == $u['id'] ? 'selected' : '' ?>>
                                    <?= esc($u['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="module" class="form-label fw-semibold text-muted small uppercase">Módulo</label>
                        <select name="module" id="module" class="form-select">
                            <option value="">Todos los módulos</option>
                            <?php foreach($modules as $m): ?>
                                <?php if(!empty($m['module'])): ?>
                                <option value="<?= esc($m['module']) ?>" <?= isset($_GET['module']) && $_GET['module'] == $m['module'] ? 'selected' : '' ?>>
                                    <?= esc($m['module']) ?>
                                </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
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
                        <a href="<?= base_url('logs/list') ?>" class="btn btn-outline-primary btn-icon-lg" title="Limpiar">
                            <i class="ti ti-refresh fs-5"></i>
                        </a>
                        <a href="<?= base_url('logs/export-pdf') . '?' . http_build_query($_GET) ?>" class="btn btn-outline-primary btn-icon-lg" title="Exportar PDF">
                            <i class="ti ti-file fs-5"></i>
                        </a>
                    </div>
                </div>
            </form>

            <!-- Buscador -->
            <div class="mb-3">
                <input type="text" id="logTableSearch" class="form-control" placeholder="Buscar en registros...">
            </div>
            <!-- Fin de buscador -->

            <div class="mb-4 border rounded-1 table-responsive">
                <table class="table text-nowrap mb-0 align-middle table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th class="text-center">Módulo</th>
                            <th>Descripción</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay registros que coincidan con los filtros.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <h6 class="fw-semibold mb-0 mb-1"><?= esc(date('d/m/Y', strtotime($log['created_at']))) ?></h6>
                                <span class="fw-normal text-muted small"><?= esc(date('H:i', strtotime($log['created_at']))) ?></span>
                            </td>
                            <td>
                                <h6 class="fs-4 fw-semibold mb-0"><?= esc($log['user_name'] ?? 'Sistema / Anónimo') ?></h6>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary fw-semibold text-small border border-primary fs-2 w-100px-inline">
                                    <?= esc($log['module']) ?>
                                </span>
                            </td>
                            <td style="max-width: 300px;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-normal text-truncate flex-grow-1">
                                        <?= esc($log['description']) ?>
                                    </span>
                                    <?php if(mb_strlen($log['description']) > 40): ?>
                                    <button type="button" class="btn btn-sm btn-light text-primary flex-shrink-0" 
                                            onclick="showLogDetails(this)" 
                                            data-desc="<?= esc($log['description']) ?>"
                                            data-date="<?= esc(date('d/m/Y H:i', strtotime($log['created_at']))) ?>"
                                            data-user="<?= esc($log['user_name'] ?? 'Sistema / Anónimo') ?>"
                                            title="Ver descripción completa">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-muted small">
                                <?= esc($log['ip_address']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if (!empty($logs)): ?>
            <div class="d-flex justify-content-center mt-3">
                <?= $pager->links() ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal Detalles del Log -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <span class="text-muted small uppercase fw-semibold">Fecha y Hora</span>
                    <div id="logModalDate" class="fw-semibold"></div>
                </div>
                <div class="mb-3">
                    <span class="text-muted small uppercase fw-semibold">Usuario</span>
                    <div id="logModalUser" class="fw-semibold"></div>
                </div>
                <div class="mb-0">
                    <span class="text-muted small uppercase fw-semibold">Descripción Completa</span>
                    <div id="logModalDesc" class="p-3 bg-light rounded mt-1" style="white-space: pre-wrap;"></div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function showLogDetails(btn) {
    document.getElementById('logModalDate').innerText = btn.getAttribute('data-date');
    document.getElementById('logModalUser').innerText = btn.getAttribute('data-user');
    document.getElementById('logModalDesc').innerText = btn.getAttribute('data-desc');
    
    var modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
    modal.show();
}
</script>
