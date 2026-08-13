<div class="container-fluid">


    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card border">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0 text-primary">Configuración SMTP</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Configura los datos del servidor SMTP para que la aplicación pueda enviar notificaciones y correos electrónicos a los usuarios.</p>
                    
                    <form action="<?= base_url('settings/smtp/update') ?>" method="post" autocomplete="off">
                        <?= csrf_field() ?>
                        
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

                        <div class="row mb-4">
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

                        <div class="text-center mt-4 d-flex justify-content-center gap-2">
                            <button type="button" id="btnTestSmtp" class="btn btn-outline-primary px-4">
                                <i class="ti ti-send"></i> <span class="d-none d-md-inline ms-1">Probar Conexión</span>
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-device-floppy"></i> <span class="d-none d-md-inline ms-1">Guardar Cambios</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btnTestSmtp').addEventListener('click', function() {
    const btn = this;
    const originalHtml = btn.innerHTML;
    
    // Deshabilitar botón y mostrar cargando
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <span class="d-none d-md-inline ms-1">Probando...</span>';
    
    fetch('<?= base_url('settings/test-smtp') ?>', {
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
                footer: data.debug ? '<div class="debug-container">' + data.debug + '</div>' : '',
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
