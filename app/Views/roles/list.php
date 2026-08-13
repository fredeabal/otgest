<div class="container">

<!-- =================================================================================
// Listado de Roles
// ================================================================================= -->
    <div class="card">
        <div class="card-header bg-primary-subtle text-white">
            <h5 class="mb-0 text-primary">Listado de roles</h5>
        </div>
        <div class="card-body">
            <!-- buscador de roles -->
            <div class="mb-3">
                <input type="text" id="roleTableSearch" class="form-control" placeholder="Buscar rol...">
            </div>
            <!-- fin de buscador de roles -->
            <div class="mb-4 border rounded-1">
                <table class="table text-nowrap mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th class="text-center">Fecha de creación</th>
                            <th class=""></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($roles)): ?>
                        <tr>
                            <td colspan="3" class="text-center">No hay roles registrados.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($roles as $role): ?>
                        <tr>
                            <td>
                                <h6 class="fs-4 fw-semibold mb-0 mb-1"><?= esc($role['name']) ?></h6>
                            </td>
                            <td class="text-center">
                                <?= esc(date('d/m/Y', strtotime($role['created_at']))) ?>
                            </td>
                            <td class="text-center">
                                <div class="dropdown dropstart">
                                    <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="ti ti-dots-vertical fs-7"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3"
                                                href="<?= base_url('roles/edit/' . $role['id']) ?>">
                                                <i class="ti ti-pencil"></i> Editar
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-3 delete-role-swal"
                                                href="#" data-url="<?= base_url('roles/delete/' . $role['id']) ?>">
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
        </div>
    </div>
</div>
<script>
// =================================================================================
// Lógica de filtrado de tabla por búsqueda
// =================================================================================
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('roleTableSearch');
    const table = document.querySelector('table');
    const rows = table.querySelectorAll('tbody tr');
    searchInput.addEventListener('input', function() {
        const search = this.value.toLowerCase();
        rows.forEach(row => {
            const rowText = Array.from(row.querySelectorAll('td'))
                .map(td => td.innerText.toLowerCase())
                .join(' ');
            row.style.display = rowText.includes(search) ? '' : 'none';
        });
    });
});
// =================================================================================
// Confirmación de eliminación con SweetAlert
// =================================================================================
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-role-swal').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            Swal.fire({
                title: '¿Seguro que deseas eliminar este rol?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
});
</script>