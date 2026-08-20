<div class="container">

    <!-- =================================================================================
    // Listado de Documentos Enviados
    // ================================================================================= -->
    <div class="card">
        <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">Documentos Enviados</h5>
        </div>
        <div class="card-body">
            <!-- Mostrar mensajes -->
            <?php if (session()->has('message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('message') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            <?php if (session()->has('warning')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('warning') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            <!-- Fin de mensajes -->
            <!-- Filtros -->
            <form method="get" class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="user_filter" class="form-label fw-semibold text-muted small uppercase">Usuario</label> 
                    <select name="user_id" id="user_filter" class="select2">
                        <option value="">Todos los usuarios</option>
                        <?php $canManageDocs = has_permission('documents.manage'); ?>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= (isset($_GET['user_id']) && $_GET['user_id'] == $user['id']) ? 'selected' : '' ?>>
                                <?php if ($canManageDocs && !empty($user['identification'])): ?>
                                    <?= esc($user['name']) ?> - DNI: <?= esc($user['identification']) ?> (<?= esc($user['email']) ?>)
                                <?php else: ?>
                                    <?= esc($user['name']) ?> (<?= esc($user['email']) ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="daterange" class="form-label fw-semibold text-muted small uppercase">Rango de Fechas</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent"><i class="ti ti-calendar fs-5"></i></span>
                        <input type="text" id="daterange" class="form-control" placeholder="Selecciona el rango de fechas" readonly>
                    </div>
                    <input type="hidden" name="date_from" id="date_from" value="<?= esc($_GET['date_from'] ?? '') ?>">
                    <input type="hidden" name="date_to" id="date_to" value="<?= esc($_GET['date_to'] ?? '') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-icon-lg" title="Filtrar">
                        <i class="ti ti-filter fs-5"></i>
                    </button>
                    <a href="<?= base_url('documents/sent') ?>" class="btn btn-outline-primary btn-icon-lg" title="Limpiar">
                        <i class="ti ti-refresh fs-5"></i>
                    </a>
                </div>
            </form>

            <!-- Buscador de documentos -->
            <div class="mb-3">
                <input type="text" id="documentTableSearch" class="form-control" placeholder="Buscar documento...">
            </div>
            <!-- Fin de buscador de documentos -->

            <span class="text-muted d-block mb-2 d-md-none">Deslice para ver la tabla <i class="ti ti-arrow-right ms-2"></i></span>
            <div class="mb-4 border rounded-1 table-responsive">
                <table class="table text-nowrap mb-0 align-middle table-hover">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th class="">Destinatario</th>
                            <th class="text-center">Fecha de envío</th>
                            <th class="text-center">Fecha de lectura</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No has enviado documentos aún.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                        <tr onclick="window.open('<?= base_url('documents/view/' . $doc['id']) ?>', '_blank')" class="cursor-pointer">
                            <td>
                                <div>
                                    <h6 class="fs-4 fw-semibold mb-0 mb-1"><?= esc($doc['title']) ?></h6>
                                </div>
                            </td>
                            <td class="">
                                <div>
                                    <h6 class="fs-4 fw-semibold mb-0 mb-1"><?= esc($doc['receiver_name']) ?></h6>
                                    <span class="fw-normal text-muted small">
                                        <?php if (has_permission('documents.manage') && !empty($doc['receiver_identification'])): ?>
                                            ID: <?= esc($doc['receiver_identification']) ?> (<?= esc($doc['receiver_email']) ?>)
                                        <?php else: ?>
                                            <?= esc($doc['receiver_email']) ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <h6 class="fw-semibold mb-0 mb-1">
                                    <?= esc(date('d/m/Y', strtotime($doc['sent_at']))) ?>
                                </h6>
                                <span class="fw-normal text-muted small">
                                    <?= esc(date('H:i', strtotime($doc['sent_at']))) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($doc['read_at'])): ?>
                                <h6 class="fw-semibold mb-0 mb-1">
                                    <?= esc(date('d/m/Y', strtotime($doc['read_at']))) ?>
                                </h6>
                                <span class="fw-normal text-muted small">
                                    <?= esc(date('H:i', strtotime($doc['read_at']))) ?>
                                </span>
                                <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning fw-semibold border border-warning fs-2 w-100px-inline"
                                    >Pendiente</span>
                                <?php endif; ?>
                            </td>
                            <!-- <td class="text-center">
                                <?php if (!empty($doc['read_at'])): ?>
                                <span class="badge bg-success-subtle text-success fw-semibold border border-success fs-2 w-100px-inline"
                                    >Entregado</span>
                                <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning fw-semibold border border-warning fs-2 w-100px-inline"
                                    >Pendiente</span>
                                <?php endif; ?>
                            </td> -->
                            <td class="text-center" onclick="event.stopPropagation();">
                                <!-- Acciones como dropdown Modernize/CoreUI -->
                                <div class="dropdown dropstart">
                                    <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ti ti-dots-vertical fs-7"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3 view-doc"
                                                href="#" data-url="<?= base_url('documents/view/' . $doc['id']) ?>"
                                                data-title="<?= esc($doc['title']) ?>">
                                                <i class="ti ti-eye"></i> Ver
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                                href="<?= base_url('documents/download/' . $doc['id']) ?>">
                                                <i class="ti ti-download"></i> Descargar
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3 text-danger delete-doc-swal"
                                                href="#" data-url="<?= base_url('documents/delete/' . $doc['id']) ?>"
                                                data-title="<?= esc($doc['title']) ?>">
                                                <i class="ti ti-trash"></i> Eliminar
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
// Ver documento en nueva ventana
// =================================================================================
document.querySelectorAll('.view-doc').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const url = link.dataset.url;
        const title = link.dataset.title;

        // Abrir en nueva ventana
        window.open(url, '_blank');
    });
});

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
// Confirmación de eliminación con SweetAlert
// =================================================================================
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-doc-swal').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            const title = this.getAttribute('data-title');
            
            Swal.fire({
                title: '¿Eliminar documento?',
                text: `"${title}" se borrará permanentemente.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '<?= csrf_token() ?>';
                    csrf.value = '<?= csrf_hash() ?>';
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
</script>