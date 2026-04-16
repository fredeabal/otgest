<div class="container">

    <!-- =================================================================================
    // Formulario de Edición de Empresa
    // ================================================================================= -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white d-flex">
                    <h5 class="mb-0 text-primary">Editar Empresa</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('company/update') ?>" method="post" autocomplete="off">
                        <?= csrf_field() ?>
                        <!-- =================================================================================
                        // Fila: CIF y Nombre
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cif" class="form-label">CIF</label>
                                <input type="text" class="form-control" id="cif" name="cif"
                                    value="<?= old('cif', esc($company['cif'] ?? '')) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nombre de la Empresa</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?= old('name', esc($company['name'] ?? '')) ?>" required>
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Dirección y Código Postal
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="address" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="<?= old('address', esc($company['address'] ?? '')) ?>" maxlength="255">
                            </div>
                            <div class="col-md-4">
                                <label for="postal_code" class="form-label">Código Postal</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code"
                                    value="<?= old('postal_code', esc($company['postal_code'] ?? '')) ?>"
                                    maxlength="10">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Teléfono y Email
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    value="<?= old('phone', esc($company['phone'] ?? '')) ?>" maxlength="20">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= old('email', esc($company['email'] ?? '')) ?>" maxlength="255">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Fila: Página Web
                        // ================================================================================= -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="website" class="form-label">Página Web</label>
                                <input type="url" class="form-control" id="website" name="website"
                                    value="<?= old('website', esc($company['website'] ?? '')) ?>" maxlength="255"
                                    placeholder="https://www.ejemplo.com">
                            </div>
                        </div>
                        <!-- =================================================================================
                        // Botón de enviar
                        // ================================================================================= -->
                        <div class="d-grids pt-5 text-center">
                            <button type="submit" class="btn btn-primary">Actualizar Empresa</button>
                            <a href="javascript:void(0)" onclick="goBack()" class="btn btn-dark ms-1">Volver atrás</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- =================================================================================
            // Configuración de Correo Electrónico (SMTP)
            // ================================================================================= -->
            <div class="card mt-4">
                <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">Configuración de Correo Electrónico (SMTP)</h5>
                    <button type="button" id="btnTestSmtp" class="btn btn-sm btn-primary">
                        <iconify-icon icon="solar:letter-send-bold-duotone" class="align-middle me-1"></iconify-icon>
                        Probar Conexión
                    </button>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('company/update') ?>" method="post" autocomplete="off">
                        <?= csrf_field() ?>
                        <!-- Pass current fields as hidden to avoid losing them if only SMTP is updated (though we use same endpoint) -->
                        <input type="hidden" name="cif" value="<?= esc($company['cif'] ?? '') ?>">
                        <input type="hidden" name="name" value="<?= esc($company['name'] ?? '') ?>">
                        <input type="hidden" name="address" value="<?= esc($company['address'] ?? '') ?>">
                        <input type="hidden" name="postal_code" value="<?= esc($company['postal_code'] ?? '') ?>">
                        <input type="hidden" name="phone" value="<?= esc($company['phone'] ?? '') ?>">
                        <input type="hidden" name="email" value="<?= esc($company['email'] ?? '') ?>">
                        <input type="hidden" name="website" value="<?= esc($company['website'] ?? '') ?>">

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="smtp_host" class="form-label">Servidor SMTP (Host)</label>
                                <input type="text" class="form-control" id="smtp_host" name="smtp_host"
                                    value="<?= old('smtp_host', esc($company['smtp_host'] ?? '')) ?>" 
                                    placeholder="ej: smtp.gmail.com">
                            </div>
                            <div class="col-md-4">
                                <label for="smtp_port" class="form-label">Puerto</label>
                                <input type="number" class="form-control" id="smtp_port" name="smtp_port"
                                    value="<?= old('smtp_port', esc($company['smtp_port'] ?? '')) ?>" 
                                    placeholder="ej: 587">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="smtp_user" class="form-label">Usuario / Email</label>
                                <input type="text" class="form-control" id="smtp_user" name="smtp_user"
                                    value="<?= old('smtp_user', esc($company['smtp_user'] ?? '')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="smtp_pass" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="smtp_pass" name="smtp_pass"
                                    placeholder="<?= !empty($company['smtp_pass']) ? '•••••••• (Dejar en blanco para no cambiar)' : 'Contraseña de la cuenta' ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="smtp_crypto" class="form-label">Cifrado</label>
                                <select class="form-select" id="smtp_crypto" name="smtp_crypto">
                                    <option value="tls" <?= old('smtp_crypto', $company['smtp_crypto'] ?? '') == 'tls' ? 'selected' : '' ?>>TLS (Recomendado)</option>
                                    <option value="ssl" <?= old('smtp_crypto', $company['smtp_crypto'] ?? '') == 'ssl' ? 'selected' : '' ?>>SSL</option>
                                    <option value="none" <?= old('smtp_crypto', $company['smtp_crypto'] ?? '') == 'none' ? 'selected' : '' ?>>Ninguno</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="smtp_from_email" class="form-label">Email Remitente</label>
                                <input type="email" class="form-control" id="smtp_from_email" name="smtp_from_email"
                                    value="<?= old('smtp_from_email', esc($company['smtp_from_email'] ?? '')) ?>"
                                    placeholder="ej: no-reply@empresa.com">
                            </div>
                            <div class="col-md-4">
                                <label for="smtp_from_name" class="form-label">Nombre Remitente</label>
                                <input type="text" class="form-control" id="smtp_from_name" name="smtp_from_name"
                                    value="<?= old('smtp_from_name', esc($company['smtp_from_name'] ?? '')) ?>"
                                    placeholder="ej: Nombre Empresa">
                            </div>
                        </div>

                        <div class="pt-3 text-center">
                            <button type="submit" class="btn btn-primary">Guardar Configuración SMTP</button>
                        </div>
                    </form>
                </div>
            </div>

    <!-- =================================================================================
    // Descarga de Base de Datos SQLite
    // ================================================================================= -->
    <div class="row justify-content-center mt-4 text-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">Descargar base de datos</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3 text-muted">Descarga una copia completa del archivo de base de datos actual para tu respaldo personal de seguridad.</p>
                    <a href="<?= base_url('company/download-db') ?>" class="btn btn-primary d-inline-flex align-items-center">
                        <iconify-icon icon="solar:download-minimalistic-bold" class="me-2 fs-5"></iconify-icon>
                        Descargar Base de Datos
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- =================================================================================
    // Mantenimiento del Sistema
    // ================================================================================= -->
    <div class="row justify-content-center mt-4 text-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">Mantenimiento del Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form action="<?= base_url('company/clear-sessions') ?>" method="post" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-primary me-2">
                                    Limpiar Sesiones
                                </button>
                            </form>
                            <form action="<?= base_url('company/clear-cache') ?>" method="post" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-primary me-2">
                                    Limpiar Cache
                                </button>
                            </form>
                            <form action="<?= base_url('company/clear-logs') ?>" method="post" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-primary me-2">
                                    Limpiar Logs
                                </button>
                            </form>
                            <form action="<?= base_url('company/clear-debugbar') ?>" method="post" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-primary">
                                    Limpiar Debugbar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =================================================================================
    // Información de última modificación
    // ================================================================================= -->
    <?php if (isset($company['updated_at']) && $company['updated_at'] != $company['created_at']): ?>
    <div class="row mb-5 text-center mt-2">
        <div class="col-md-12">
            <small>
                Última modificación el
                <?= esc(date('d/m/Y H:i', strtotime($company['updated_at']))) ?>
                <?php if (isset($company['updated_by_name'])): ?>
                por <?= esc($company['updated_by_name']) ?>
                <?php endif; ?>
            </small>
        </div>
    </div>
    <?php endif; ?>
</div>
<script>
document.getElementById('btnTestSmtp').addEventListener('click', function() {
    const btn = this;
    const originalHtml = btn.innerHTML;
    
    // Deshabilitar botón y mostrar cargando
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Probando...';
    
    fetch('<?= base_url('company/test-smtp') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Conexión Exitosa!',
                text: data.message,
                confirmButtonColor: 'var(--bs-primary)'
            });
        } else {
            let errorMsg = data.message;
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: errorMsg,
                footer: data.debug ? '<div style="max-height: 150px; overflow-y: auto; font-size: 0.8em; text-align: left;">' + data.debug + '</div>' : '',
                confirmButtonColor: 'var(--bs-primary)'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error del Sistema',
            text: 'Ocurrió un error al intentar probar la conexión.',
            confirmButtonColor: 'var(--bs-primary)'
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
});
</script>
