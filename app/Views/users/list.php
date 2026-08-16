<div class="container">

<!-- =================================================================================
// Listado de Usuarios
// ================================================================================= -->
    <div class="card">
        <div class="card-header bg-primary-subtle text-white">
            <h5 class="mb-0 text-primary">Listado de usuarios</h5>
        </div>
        <div class="card-body">
            <!-- buscador de usuarios -->
            <div class="mb-3">
                <input type="text" id="userTableSearch" class="form-control" placeholder="Buscar usuario...">
            </div>
            <!-- fin de buscador de usuarios -->

            <div class="mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th class="d-none d-md-table-cell">Email</th>
                            <th class="text-center d-none d-md-table-cell">Rol</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center d-none d-md-table-cell">Último login</th>
                            <th class=""></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay usuarios registrados.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr onclick="window.location='<?= base_url('users/edit/' . $user['id']) ?>'" class="cursor-pointer">
                            <td>
                                <div>
                                    <h6 class="fs-4 fw-semibold mb-0 mb-1"><?= esc($user['name']) ?></h6>
                                    <span class="fw-normal text-muted small"><?= esc($user['identification']) ?></span>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <?= esc($user['email']) ?>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <span class="badge bg-info-subtle text-info fw-semibold text-small border border-info fs-2 w-100px-inline"
                                    >
                                    <?= esc($user['role_name']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($user['is_active'] == 1): ?>
                                <span class="badge bg-success-subtle text-success fw-semibold border border-success fs-2 w-100px-inline"
                                    >Activo</span>
                                <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger fw-semibold border border-danger fs-2 w-100px-inline"
                                    >Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <h6 class="fw-semibold mb-0 mb-1">
                                    <?= esc(date('d/m/Y', strtotime($user['last_login']))) ?></h6>
                                <span
                                    class="fw-normal text-muted small"><?= esc(date('H:i', strtotime($user['last_login']))) ?></span>
                            </td>

                            <td class="text-center" onclick="event.stopPropagation();">
                                <!-- Acciones como dropdown Modernize/CoreUI -->
                                <div class="dropdown dropstart">
                                    <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ti ti-dots-vertical fs-7"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <?php if ($user['id'] != 1 || session('user_id') == 1): ?>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                                href="<?= base_url('users/edit/' . $user['id']) ?>">
                                                <i class="ti ti-pencil"></i> Editar
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                                href="mailto:<?= esc($user['email']) ?>">
                                                <i class="ti ti-mail"></i> Enviar correo
                                            </a>
                                        </li>
                                        <?php if ($user['id'] != 1): ?>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3 delete-user-swal"
                                                href="#" data-url="<?= base_url('users/delete/' . $user['id']) ?>">
                                                <i class="ti ti-trash"></i> Eliminar
                                            </a>
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
        </div>
    </div>
</div>

<script>
// =================================================================================
// Lógica de filtrado de tabla por búsqueda
// =================================================================================
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('userTableSearch');
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
    document.querySelectorAll('.delete-user-swal').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            Swal.fire({
                title: '¿Seguro que deseas eliminar este usuario?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
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