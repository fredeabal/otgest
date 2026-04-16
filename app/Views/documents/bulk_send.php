<div class="container">

    <!-- =================================================================================
    // Envío Masivo de Documentos
    // ================================================================================= -->
    <div class="card">
        <div class="card-header bg-primary-subtle text-white">
            <h5 class="mb-0 text-primary">Envío Masivo de Documentos</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Sube múltiples archivos a la vez. Cada archivo debe tener un nombre que comience con la identificación del usuario destinatario seguido de un guion y el nombre del documento.</p>
            <p class="text-muted"><strong>Ejemplo:</strong> X12345678-nomina-julio.pdf</p>

            <form action="<?= base_url('documents/bulk-store') ?>" method="post" enctype="multipart/form-data" id="bulkForm">
                <?= csrf_field() ?>

                <!-- =================================================================================
                // Campo para subir archivos múltiples
                // ================================================================================= -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="documents" class="form-label">Seleccionar archivos</label>
                        <input type="file" class="form-control" id="documents" name="documents[]" multiple>
                        <div class="form-text">Puedes seleccionar varios archivos a la vez. <strong>Nota:</strong> El procesamiento puede tardar varios minutos.</div>
                    </div>
                </div>

                <!-- =================================================================================
                // Botón de envío mejorado
                // ================================================================================= -->
                <div class="d-grids pt-5 text-center">
                    <button type="submit" class="btn btn-primary" id="bulkSubmitBtn">
                        Enviar archivos
                    </button>
                    <a href="<?= base_url('documents/list') ?>" class="btn btn-dark ms-1">Volver atrás</a>
                </div>
            </form>

            <!-- Mostrar mensajes -->
            <?php if (session()->has('message')): ?>
            <div class="alert alert-info mt-3">
                <?= session('message') ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- =================================================================================
// JavaScript para feedback visual del envío bulk
// ================================================================================= -->
<script>
// Corregir: usar evento submit para no impedir el envío del formulario
document.getElementById('bulkForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('bulkSubmitBtn');
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
    submitBtn.disabled = true;
});
</script>

