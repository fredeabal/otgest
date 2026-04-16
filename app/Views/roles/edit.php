<div class="container">

<!-- =================================================================================
// Formulario de Edición de Rol
// ================================================================================= -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0 text-primary">Editar rol</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('roles/update/' . $role['id']) ?>" method="post" autocomplete="off">
                        <?= csrf_field() ?>
                        <!-- =================================================================================
                        // Campo: Nombre del rol
                        // ================================================================================= -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del rol</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= old('name', esc($role['name'])) ?>">
                            <?php if (session('errors.name')): ?>
                                <div class="text-danger small mt-1">
                                    <?= esc(session('errors.name')) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- =================================================================================
                        // Botón de enviar
                        // ================================================================================= -->
                        <div class="d-grids pt-5 text-center">
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                            <a href="<?= base_url('roles/list') ?>" class="btn btn-dark ms-1">Volver atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- usuario que lo modifico -->
<?php if ($role['updated_by']): ?>
<div class="container mt-n3 d-none d-md-block">
    <span class="text-muted small">Última actualización por: <?= esc($role['updated_by_name'] ?? 'Usuario desconocido') ?> el <?= esc(date('d/m/Y H:i', strtotime($role['updated_at']))) ?></span>
</div>
<?php endif; ?>
</div>