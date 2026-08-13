<div class="container-fluid">
    <div class="card border">
        <div class="card-header bg-primary-subtle text-white">
            <h5 class="mb-0 text-primary">Mantenimiento del sistema</h5>
        </div>
        <div class="card-body">

    <!-- =====================================================================
         OPCIONES DE MANTENIMIENTO
         ===================================================================== -->
    <div class="row">



        <!-- Tarjeta de Mantenimiento General -->
        <div class="col-12 mb-4">
            <div class="card border bg-light-primary">
                <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                    <div>
                        <h5 class="card-title fw-bold text-primary mb-1">Mantenimiento Completo</h5>
                        <p class="card-text text-muted mb-0">Ejecuta todas las acciones de limpieza simultáneamente (caché, sesiones inactivas, logs y debugbar).</p>
                    </div>
                    <form action="<?= base_url('settings/clear-all') ?>" method="POST" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            Ejecutar Mantenimiento Completo
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================= SECCIÓN: DATOS Y RESPALDOS ================= -->

        <!-- Respaldos (Backup) -->
        <div class="col-12 mb-4">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-2 rounded bg-light-primary text-primary d-flex align-items-center justify-content-center">
                            <i class="ti ti-database fs-7"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Respaldo y Restauración de Base de Datos</h5>
                    </div>
                    <p class="text-muted mb-4">Descarga una copia de seguridad completa del sistema actual en formato SQLite o sube un archivo de respaldo para restaurarlo. <strong>Atención:</strong> Al restaurar, se destruirán todos los datos configurados en este momento.</p>
                    
                    <div class="row g-4">
                        <!-- Descargar -->
                        <div class="col-md-6">
                            <div class="h-100 p-4 border rounded d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold mb-2">Crear Copia de Seguridad</h6>
                                    <p class="text-muted fs-3 mb-4">Descarga una copia completa de la base de datos actual para guardarla en un lugar seguro.</p>
                                </div>
                                <div>
                                    <a href="<?= base_url('settings/download-db') ?>" class="btn btn-outline-primary px-4 py-2">
                                        <i class="ti ti-download me-1"></i> Descargar Base de Datos
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Restaurar -->
                        <div class="col-md-6">
                            <div class="h-100 p-4 border rounded d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold mb-2">Restaurar Copia de Seguridad</h6>
                                    <p class="text-muted fs-3 mb-4">Sube un archivo de respaldo SQLite anterior para restaurar toda la configuración y los registros del sistema.</p>
                                </div>
                                <div>
                                    <form action="<?= base_url('settings/restore-db') ?>" method="POST" enctype="multipart/form-data" data-confirm="Esta acción es irreversible y reemplazará toda la base de datos actual con la del respaldo. ¿Estás seguro de que quieres continuar?">
                                        <?= csrf_field() ?>
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <input type="file" name="backup_file" id="backup_file" class="d-none" accept=".db,.sqlite">
                                            <label for="backup_file" class="btn btn-primary px-4 mb-0 py-2 cursor-pointer">
                                                <i class="ti ti-upload me-1"></i> Seleccionar Archivo
                                            </label>
                                            <span id="file-name" class="text-muted fs-3">Ningún archivo seleccionado</span>
                                            <button type="submit" class="btn btn-danger px-4 py-2 d-none" id="btn-submit-restore">
                                                Restaurar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= SECCIÓN: PRUEBAS ================= -->
        <!-- Resultados de Tests -->
        <?php if(session()->getFlashdata('test_output')): ?>
        <div class="col-12 mb-4">
            <div class="card border border-primary">
                <div class="card-header bg-primary">
                    <h5 class="mb-0 text-white">Resultados de Pruebas Unitarias (PHPUnit)</h5>
                </div>
                <div class="card-body bg-dark text-light p-3">
                    <pre class="mb-0 text-light text-wrap" style="font-family: monospace; font-size: 0.85rem;"><?= esc(session()->getFlashdata('test_output')) ?></pre>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tests Unitarios -->
        <div class="col-12 mb-4">
            <div class="card border bg-light-primary">
                <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
                    <div>
                        <h5 class="card-title fw-bold text-primary mb-1">Pruebas Unitarias</h5>
                        <p class="card-text text-muted mb-0">Ejecuta las pruebas automatizadas para validar que las funciones críticas del sistema operan correctamente.</p>
                    </div>
                    <form action="<?= base_url('settings/run-tests') ?>" method="POST" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            Ejecutar Tests
                        </button>
                    </form>
                </div>
            </div>
        </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const fileInput = document.getElementById('backup_file');
    const fileNameSpan = document.getElementById('file-name');
    const submitBtn = document.getElementById('btn-submit-restore');
    
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameSpan.textContent = this.files[0].name;
                submitBtn.classList.remove('d-none');
            } else {
                fileNameSpan.textContent = 'Ningún archivo seleccionado';
                submitBtn.classList.add('d-none');
            }
        });
    }
});
</script>
