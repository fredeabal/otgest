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
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Fecha desde</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                           value="<?= esc($_GET['date_from'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Fecha hasta</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                           value="<?= esc($_GET['date_to'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Estado</label>
                    <select name="status" id="status" class="form-select">
                        <option value="pending" <?= ($current_status ?? 'pending') === 'pending' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="approved" <?= ($current_status ?? '') === 'approved' ? 'selected' : '' ?>>Aprobado</option>
                        <option value="rejected" <?= ($current_status ?? '') === 'rejected' ? 'selected' : '' ?>>Rechazado</option>
                        <option value="all" <?= ($current_status ?? '') === 'all' ? 'selected' : '' ?>>Todos</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-icon me-2">
                        <iconify-icon icon="solar:filter-bold-duotone"></iconify-icon>
                    </button>
                    <a href="<?= base_url('expenses/my-expenses') ?>" class="btn btn-outline-muted btn-icon me-2">
                        <iconify-icon icon="solar:close-circle-bold-duotone"></iconify-icon>
                    </a>
                    <a href="<?= base_url('expenses/export-my-pdf') . '?' . http_build_query($_GET) ?>" class="btn btn-warning btn-icon">
                        <iconify-icon icon="solar:file-bold-duotone"></iconify-icon>
                    </a>
                </div>
            </form>

            <!-- Buscador de gastos -->
            <div class="mb-3">
                <input type="text" id="expenseTableSearch" class="form-control" placeholder="Buscar gasto...">
            </div>
            <!-- Fin de buscador de gastos -->

            <div class="mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle">
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
                        <tr>
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
                                <span class="<?= $statusClass ?>" style="min-width: 70px; display: inline-block;"><?= $statusText ?></span>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <h6 class="fw-semibold mb-0 mb-1">
                                    <?= esc(date('d/m/Y', strtotime($expense['created_at']))) ?>
                                </h6>
                                <span class="fw-normal text-muted small">
                                    <?= esc(date('H:i', strtotime($expense['created_at']))) ?>
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
                                                href="<?= base_url('expenses/view/' . $expense['id']) ?>">
                                                <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon> Ver detalle
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