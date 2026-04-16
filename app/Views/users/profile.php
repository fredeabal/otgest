<div class="container">
    <!-- =================================================================================
    // Formulario de Edición de Avatar
    // ================================================================================= -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0">Editar avatar</h5>
                </div>
                <div class="card-body">
                    <!-- avatar del usuario -->
                    <div class="row mb-3">
                        <div class="col-md-12 text-center">
                            <img src="<?= base_url('users/avatar/' . esc($user['avatar'])) ?>" class="rounded-circle"
                                width="120" height="120" alt="Usuario" id="avatarPreview" />
                        </div>
                    </div>

                    <form action="<?= base_url('users/update-avatar/' . $user['id']) ?>" method="post"
                        autocomplete="off" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <!-- =================================================================================
                    // subir avatar
                    // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="avatar" class="form-label">Avatar</label>
                                <input type="file" class="form-control" id="avatar" name="avatar">
                            </div>
                            <small class="text-muted ms-2 mt-1">Solo se permiten archivos de imagen (jpg, png, gif,
                                webp).</small>
                        </div>

                        <!-- =================================================================================
                        // Botón de enviar
                        // ================================================================================= -->
                        <div class="d-grids pt-5 text-center">
                            <button type="submit" class="btn btn-primary">Actualizar avatar</button>
                            <?php if (!empty($user['avatar']) && $user['avatar'] !== 'user-default.png'): ?>
                                <a href="#" data-url="<?= base_url('users/delete-avatar/' . $user['id']) ?>" class="btn btn-danger ms-1 delete-avatar-btn">Eliminar avatar</a>
                            <?php endif; ?>
                            <a href="<?= base_url('users/profile/' . $user['id']) ?>" class="btn btn-dark ms-1">Volver atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- =================================================================================
    // Formulario de Edición de Usuario
    // ================================================================================= -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card mt-4">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0"><?= esc($title ?? 'Perfil de usuario') ?></h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('users/update-profile/' . $user['id']) ?>" method="post"
                        autocomplete="off">
                        <?= csrf_field() ?>
                        <!-- =================================================================================
                        // Fila: Identificación y Nombre
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="identification" class="form-label">Número de identificación</label>
                                <input type="text" class="form-control" id="identification" name="identification"
                                    value="<?= esc($user['identification']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?= old('name', esc($user['name'])) ?>">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Fecha de nacimiento y Correo
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="birthdate" class="form-label">Fecha de nacimiento</label>
                                <input type="date" class="form-control" id="birthdate" name="birthdate"
                                    value="<?= old('birthdate', esc($user['birthdate'])) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= old('email', esc($user['email'])) ?>">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Dirección
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="address" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="address" name="address" maxlength="255"
                                    value="<?= old('address', esc($user['address'])) ?>">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Contraseña y Confirmar contraseña
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Nueva contraseña</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Dejar en blanco para no cambiar">
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirm" class="form-label">Confirmar nueva contraseña</label>
                                <input type="password" class="form-control" id="password_confirm"
                                    name="password_confirm" placeholder="Dejar en blanco para no cambiar">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Tema
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Tema</label>
                                <div class="form-check form-switch py-2">
                                    <input class="form-check-input" type="checkbox" id="themeSwitch" name="theme" value="dark" <?= $user['theme'] == 'dark' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="themeSwitch">
                                        <iconify-icon icon="solar:moon-bold-duotone" class="ms-2"></iconify-icon>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Botón de enviar
                        // ================================================================================= -->
                        <div class="d-grids pt-5 text-center">
                            <button type="submit" class="btn btn-primary">Actualizar perfil</button>
                            <a href="<?= base_url('users/profile') ?>" class="btn btn-dark ms-1">Volver atrás</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =================================================================================
// Script para mostrar el avatar
// ================================================================================= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Previsualización de avatar
    document.getElementById('avatar').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            document.getElementById('avatarPreview').src = URL.createObjectURL(file);
        }
    });

    // Confirmación de eliminación de avatar con SweetAlert2
    document.querySelectorAll('.delete-avatar-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            Swal.fire({
                title: '¿Eliminar avatar?',
                text: 'Volverás a usar la imagen por defecto.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
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