<div class="container">

    <!-- =================================================================================
    // Listado de Documentos Recibidos
    // ================================================================================= -->
    <div class="card">
        <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">Documentos Recibidos</h5>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <form method="get" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="user_filter" class="form-label">Usuario</label>
                    <select name="user_id" id="user_filter" class="form-select">
                        <option value="">Todos los usuarios</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= (isset($_GET['user_id']) && $_GET['user_id'] == $user['id']) ? 'selected' : '' ?>>
                                <?= esc($user['name']) ?> (<?= esc($user['identification']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
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
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-icon me-2">
                        <iconify-icon icon="solar:filter-bold-duotone"></iconify-icon>
                    </button>
                    <a href="<?= base_url('documents/list') ?>" class="btn btn-outline-muted btn-icon">
                        <iconify-icon icon="solar:close-circle-bold-duotone"></iconify-icon>
                    </a>
                </div>
            </form>

            <!-- Buscador de documentos -->
            <div class="mb-3">
                <input type="text" id="documentTableSearch" class="form-control" placeholder="Buscar documento...">
            </div>
            <!-- Fin de buscador de documentos -->

            <div class="mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th class="d-none d-md-table-cell">Remitente</th>
                            <th class="text-center d-none d-md-table-cell">Fecha de envío</th>
                            <th class="text-center d-none d-md-table-cell">Fecha de lectura</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No has recibido documentos aún.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td>
                                <div>
                                    <h6 class="fs-4 fw-semibold mb-0 mb-1"><?= esc($doc['title']) ?></h6>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div>
                                    <h6 class="fs-4 fw-semibold mb-0 mb-1"><?= esc($doc['sender_name']) ?></h6>
                                    <span class="fw-normal text-muted small">ID: <?= esc($doc['sender_identification']) ?></span>
                                </div>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <h6 class="fw-semibold mb-0 mb-1">
                                    <?= esc(date('d/m/Y', strtotime($doc['sent_at']))) ?>
                                </h6>
                                <span class="fw-normal text-muted small">
                                    <?= esc(date('H:i', strtotime($doc['sent_at']))) ?>
                                </span>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <?php if (!empty($doc['read_at'])): ?>
                                <h6 class="fw-semibold mb-0 mb-1">
                                    <?= esc(date('d/m/Y', strtotime($doc['read_at']))) ?>
                                </h6>
                                <span class="fw-normal text-muted small">
                                    <?= esc(date('H:i', strtotime($doc['read_at']))) ?>
                                </span>
                                <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning fw-semibold border border-warning text-warning fs-2"
                                    style="min-width: 70px; display: inline-block;">Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <!-- Acciones como dropdown Modernize/CoreUI -->
                                <div class="dropdown dropstart">
                                    <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <iconify-icon icon="solar:sort-bold-duotone" class="fs-7"></iconify-icon>                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3 view-doc"
                                                href="#" data-url="<?= base_url('documents/view/' . $doc['id']) ?>"
                                                data-title="<?= esc($doc['title']) ?>"
                                                data-read="<?= !empty($doc['read_at']) ? '1' : '0' ?>">
                                                <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon> Ver
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3 download-doc-swal"
                                                href="#" data-url="<?= base_url('documents/download/' . $doc['id']) ?>"
                                                data-title="<?= esc($doc['title']) ?>"
                                                data-read="<?= !empty($doc['read_at']) ? '1' : '0' ?>">
                                                <iconify-icon icon="solar:download-bold-duotone"></iconify-icon> Descargar
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
    const searchInput = document.getElementById('documentTableSearch');
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
// Ver documento en nueva ventana
// =================================================================================
document.querySelectorAll('.view-doc').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const url = link.dataset.url;
        const title = link.dataset.title;
        const isRead = link.dataset.read === '1';

        if (isRead) {
            // Si ya está leído, abrir directamente
            window.open(url, '_blank');
        } else {
            // Mostrar SweetAlert para confirmar
            Swal.fire({
                title: 'Confirmar visualización',
                text: `¿Deseas ver el documento "${title}"? Al confirmar, se marcará como leído.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, ver documento',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Abrir en nueva ventana
                    window.open(url, '_blank');
                    // Recargar después de un delay para actualizar el estado
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            });
        }
    });
});

// =================================================================================
// Confirmación de descarga con SweetAlert
// =================================================================================
document.querySelectorAll('.download-doc-swal').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const url = link.dataset.url;
        const title = link.dataset.title;
        const isRead = link.dataset.read === '1';

        if (isRead) {
            // Si ya está leído, descarga directamente
            const a = document.createElement('a');
            a.href = url;
            a.download = '';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            return;
        }

        // Mostrar SweetAlert para confirmar
        Swal.fire({
            title: 'Confirmar descarga',
            text: `¿Deseas descargar el documento "${title}"? Al confirmar, se marcará como leído y podrás descargarlo.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, descargar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Forzar descarga
                const a = document.createElement('a');
                a.href = url;
                a.download = '';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                // Recargar la página después de un delay para actualizar el estado
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        });
    });
});
</script>