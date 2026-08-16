<div class="container">

    <!-- =================================================================================
    // Listado de Mis Justificaciones de Gastos
    // ================================================================================= -->
    <div class="card">
        <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">Mis justificaciones de gastos</h5>
        </div>
        <div class="card-body">

            <!-- Filtros -->
            <form method="get" class="row g-3 mb-4">
                <div class="col-md-5">
                    <label for="daterange" class="form-label fw-semibold text-muted small uppercase">Rango de Fechas</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="ti ti-calendar fs-5"></i></span>
                        <input type="text" id="daterange" class="form-control" placeholder="Selecciona el rango de fechas" readonly>
                    </div>
                    <input type="hidden" name="date_from" id="date_from" value="<?= esc($_GET['date_from'] ?? '') ?>">
                    <input type="hidden" name="date_to" id="date_to" value="<?= esc($_GET['date_to'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label fw-semibold text-muted small uppercase">Estado</label>
                    <select name="status" id="status" class="form-select">
                        <option value="pending" <?= ($current_status ?? 'pending') === 'pending' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="approved" <?= ($current_status ?? '') === 'approved' ? 'selected' : '' ?>>Aprobado</option>
                        <option value="rejected" <?= ($current_status ?? '') === 'rejected' ? 'selected' : '' ?>>Rechazado</option>
                        <option value="all" <?= ($current_status ?? '') === 'all' ? 'selected' : '' ?>>Todos</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-icon-lg" title="Filtrar">
                        <i class="ti ti-filter fs-5"></i>
                    </button>
                    <a href="<?= base_url('expenses/my-expenses') ?>" class="btn btn-outline-primary btn-icon-lg" title="Limpiar">
                        <i class="ti ti-refresh fs-5"></i>
                    </a>
                    <a href="<?= base_url('expenses/export-my-pdf') . '?' . http_build_query($_GET) ?>" class="btn btn-outline-primary btn-icon-lg" title="Exportar PDF">
                        <i class="ti ti-file fs-5"></i>
                    </a>
                </div>
            </form>

            <!-- Buscador de gastos -->
            <div class="mb-3">
                <input type="text" id="expenseTableSearch" class="form-control" placeholder="Buscar gasto...">
            </div>
            <!-- Fin de buscador de gastos -->

            <div class="mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle table-hover">
                    <thead>
                        <tr>
                            <th>Fecha del gasto</th>
                            <th class="d-none d-md-table-cell">Importe</th>
                            <th class="d-none d-md-table-cell">Categoría</th>
                            <th class="text-center d-none d-md-table-cell">Estado</th>
                            <th class="text-center d-none d-md-table-cell">Fecha solicitud</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No tienes justificaciones de gastos aún.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($expenses as $expense): ?>
                        <tr onclick="window.location='<?= base_url('expenses/view/' . $expense['id']) ?>'" class="cursor-pointer">
                            <td>
                                <h6 class="fw-semibold mb-0 mb-1">
                                    <?= esc(date('d/m/Y', strtotime($expense['expense_date']))) ?>
                                </h6>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div>
                                    <h6 class="fs-4 fw-semibold mb-0 mb-1">
                                        <?php if ($expense['amount']): ?>
                                            <?= number_format($expense['amount'], 2, ',', '.') ?> €
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </h6>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div>
                                    <h6 class="fs-4 fw-semibold mb-0 mb-1">
                                        <?= esc($expense['category'] ?: '-') ?>
                                    </h6>
                                </div>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                switch ($expense['status']) {
                                    case 'pending':
                                        $statusClass = 'badge bg-warning-subtle text-warning fw-semibold border border-warning text-warning fs-2';
                                        $statusText = 'Pendiente';
                                        break;
                                    case 'approved':
                                        $statusClass = 'badge bg-success-subtle text-success fw-semibold border border-success text-success fs-2';
                                        $statusText = 'Aprobado';
                                        break;
                                    case 'rejected':
                                        $statusClass = 'badge bg-danger-subtle text-danger fw-semibold border border-danger text-danger fs-2';
                                        $statusText = 'Rechazado';
                                        break;
                                }
                                ?>
                                <span class="<?= $statusClass ?> w-100px-inline" ><?= $statusText ?></span>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <h6 class="fw-semibold mb-0 mb-1">
                                    <?= esc(date('d/m/Y', strtotime($expense['created_at']))) ?>
                                </h6>
                                <span class="fw-normal text-muted small">
                                    <?= esc(date('H:i', strtotime($expense['created_at']))) ?>
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
                                                href="<?= base_url('expenses/view/' . $expense['id']) ?>">
                                                <i class="ti ti-eye"></i> Ver detalle
                                            </a>
                                        </li>
                                        <?php if ($expense['receipt_image']): ?>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                                href="<?= base_url('expenses/download/' . $expense['id']) ?>">
                                                <i class="ti ti-download"></i> Descargar
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <?php if ($expense['status'] === 'pending'): ?>
                                        <li>
                                            <form action="<?= base_url('expenses/delete/' . $expense['id']) ?>" method="post" class="d-inline confirm-delete-form" data-confirm="¿Estás seguro de que deseas eliminar esta justificación de gasto?">
                                                <button type="submit" class="dropdown-item d-flex align-items-center gap-3 text-danger">
                                                    <i class="ti ti-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </li>
                                        <?php endif; ?>
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

<script>
// =================================================================================
// Lógica de filtrado de tabla por búsqueda
// =================================================================================
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('expenseTableSearch');
    const table = document.querySelector('table');
    const rows = table.querySelectorAll('tbody tr');

    searchInput.addEventListener('input', function() {
        const search = this.value.toLowerCase();
        rows.forEach(row => {
            // Concatenar el texto de todas las celdas visibles
            const rowText = Array.from(row.querySelectorAll('td'))
                .map(td => td.innerText.toLowerCase())
                .join(' ');
            // Mostrar u ocultar la fila según si coincide
            row.style.display = rowText.includes(search) ? '' : 'none';
        });
    });
});
</script>