<div class="container">

    <!-- =================================================================================
// Formulario de Creación de Rol
// ================================================================================= -->

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0 text-primary">Crear rol</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('roles/store') ?>" method="post" autocomplete="off">
                        <?= csrf_field() ?>
                        <!-- =================================================================================
                        // Campo: Nombre del rol
                        // ================================================================================= -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del rol</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>">
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
                            <button type="submit" class="btn btn-primary">Registrar</button>
                            <a href="<?= base_url('roles/list') ?>" class="btn btn-dark ms-1">Volver atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>