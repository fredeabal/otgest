<div class="container">

    <!-- =================================================================================
    // Gestión de Justificaciones de Gastos
    // ================================================================================= -->
    <div class="card">
        <div class="card-header bg-primary-subtle text-white">
            <h5 class="mb-0 text-primary">Gestión de justificaciones de gastos</h5>
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
                    <div class="col-md-6">
                        <label for="status_filter" class="form-label fw-semibold text-muted small uppercase">Estado</label>
                        <select name="status" id="status_filter" class="form-select">
                            <option value="pending" <?= ($current_status ?? 'pending') === 'pending' ? 'selected' : '' ?>>Pendientes</option>
                            <option value="approved" <?= ($current_status ?? '') === 'approved' ? 'selected' : '' ?>>Aprobados</option>
                            <option value="rejected" <?= ($current_status ?? '') === 'rejected' ? 'selected' : '' ?>>Rechazados</option>
                            <option value="all" <?= ($current_status ?? '') === 'all' ? 'selected' : '' ?>>Todos</option>
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
                        <a href="<?= base_url('expenses/manage') ?>" class="btn btn-outline-primary btn-icon-lg" title="Limpiar">
                            <i class="ti ti-refresh fs-5"></i>
                        </a>
                        <a href="<?= base_url('expenses/export-pending-pdf') . '?' . http_build_query($_GET) ?>"
                            class="btn btn-outline-primary btn-icon-lg" title="Exportar PDF">
                            <i class="ti ti-file-download fs-5"></i>
                        </a>
                    </div>
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
                            <th>Solicitante</th>
                            <th class="d-none d-md-table-cell">Fecha del gasto</th>
                            <th class="d-none d-md-table-cell">Categoría</th>
                            <th class="d-none d-md-table-cell">Importe</th>
                            <th class="text-center d-none d-md-table-cell">Fecha solicitud</th>
                            <th class="text-center d-none d-md-table-cell">Estado</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="7" class="text-center">
                                <?php
                                $statusText = [
                                    'pending' => 'pendientes de aprobación',
                                    'approved' => 'aprobados',
                                    'rejected' => 'rechazados',
                                    'all' => ''
                                ];
                                $message = isset($statusText[$current_status ?? 'pending']) ? 'No hay gastos ' . $statusText[$current_status ?? 'pending'] . '.' : 'No hay gastos.';
                                echo $message;
                                ?>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($expenses as $expense): ?>
                        <tr onclick="window.location='<?= base_url('expenses/view/' . $expense['id']) ?>'" class="cursor-pointer">
                            <td>
                                <div>
                                    <h6 class="fs-4 fw-semibold mb-0 mb-1 d-inline-block text-truncate" style="max-width: 200px;"><?= esc($expense['user_name']) ?></h6>
                                    <span class="fw-normal text-muted small d-block">DNI:
                                        <?= esc($expense['user_identification']) ?></span>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <h6 class="fw-semibold mb-0 mb-1">
                                    <?= esc(date('d/m/Y', strtotime($expense['expense_date']))) ?>
                                </h6>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div>
                                    <h6 class="fs-4 fw-semibold mb-0 mb-1">
                                        <?= esc($expense['category'] ?: '-') ?>
                                    </h6>
                                </div>
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
                            <td class="text-center d-none d-md-table-cell">
                                <h6 class="fw-semibold mb-0 mb-1">
                                    <?= esc(date('d/m/Y', strtotime($expense['created_at']))) ?>
                                </h6>
                                <span class="fw-normal text-muted small">
                                    <?= esc(date('H:i', strtotime($expense['created_at']))) ?>
                                </span>
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
                            <td class="text-center" onclick="event.stopPropagation();">
                                <!-- Acciones como dropdown Modernize/CoreUI -->
                                <div class="dropdown dropstart">
                                    <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ti ti-dots-vertical fs-7"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <?php if ($expense['status'] === 'pending'): ?>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3 approve-expense-swal"
                                                href="#"
                                                data-url="<?= base_url('expenses/approve/' . $expense['id']) ?>"
                                                data-title="aprobar">
                                                <i class="ti ti-circle-check"></i>Aprobar
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3 reject-expense-swal"
                                                href="#" data-url="<?= base_url('expenses/reject/' . $expense['id']) ?>"
                                                data-title="rechazar">
                                                <i class="ti ti-circle-x"></i>Rechazar
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                                href="<?= base_url('expenses/view/' . $expense['id']) ?>">
                                                <i class="ti ti-eye"></i> Ver detalle
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

// =================================================================================
// Confirmación de aprobación/rechazo con SweetAlert
// =================================================================================
document.querySelectorAll('.approve-expense-swal, .reject-expense-swal').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const url = link.dataset.url;
        const action = link.dataset.title;
        const isApprove = link.classList.contains('approve-expense-swal');

        let swalConfig = {
            title: `Confirmar ${action}`,
            text: `¿Estás seguro de ${action} este gasto?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isApprove ? '#28a745' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: isApprove ? 'Sí, aprobar' : 'Sí, rechazar',
            cancelButtonText: 'Cancelar'
        };

        if (!isApprove) {
            swalConfig.input = 'textarea';
            swalConfig.inputPlaceholder = 'Escribe el motivo del rechazo...';
            swalConfig.inputAttributes = {
                'aria-label': 'Motivo del rechazo'
            };
        }

        Swal.fire(swalConfig).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '<?= csrf_token() ?>';
                csrf.value = '<?= csrf_hash() ?>';
                form.appendChild(csrf);

                if (!isApprove && result.value) {
                    const reasonInput = document.createElement('input');
                    reasonInput.type = 'hidden';
                    reasonInput.name = 'rejection_reason';
                    reasonInput.value = result.value;
                    form.appendChild(reasonInput);
                }

                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>