<div class="container-fluid">

    <!-- =================================================================================
    // Detalle del Log de Actividad
    // ================================================================================= -->
    <div class="card w-100 position-relative overflow-hidden">
        <div class="px-4 py-3 border-bottom bg-primary-subtle d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-semibold mb-0 lh-sm text-primary">
                Detalle del Registro de Auditoría
            </h5>
            <span class="badge bg-primary rounded-3 fw-semibold">
                ID: <?= esc($log['id']) ?>
            </span>
        </div>

        <div class="card-body p-4">
            <div class="row">
                <!-- Información Principal -->
                <div class="col-md-6 mb-4 mb-md-0">
                    <h6 class="fw-semibold mb-3">Información del Evento</h6>
                    <div class="list-group list-group-flush border rounded-3">
                        
                        <div class="list-group-item px-3 py-2">
                            <div class="row align-items-center">
                                <div class="col-sm-4 text-muted small fw-semibold">Fecha y Hora:</div>
                                <div class="col-sm-8 text-dark fw-semibold">
                                    <?= esc(date('d/m/Y H:i:s', strtotime($log['created_at']))) ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-3 py-2">
                            <div class="row align-items-center">
                                <div class="col-sm-4 text-muted small fw-semibold">Módulo:</div>
                                <div class="col-sm-8">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold border border-primary">
                                        <?= esc($log['module']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-3 py-2">
                            <div class="row align-items-center">
                                <div class="col-sm-4 text-muted small fw-semibold">Acción:</div>
                                <div class="col-sm-8">
                                    <span class="badge bg-secondary-subtle text-secondary fw-semibold">
                                        <?= esc($log['action'] ?? 'SYSTEM') ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-3 py-2">
                            <div class="row align-items-center">
                                <div class="col-sm-4 text-muted small fw-semibold">Dirección IP:</div>
                                <div class="col-sm-8 text-dark">
                                    <code><?= esc($log['ip_address']) ?></code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del Usuario -->
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-3">Responsable de la Acción</h6>
                    <div class="list-group list-group-flush border rounded-3">
                        <div class="list-group-item px-3 py-2">
                            <div class="row align-items-center">
                                <div class="col-sm-4 text-muted small fw-semibold">Usuario:</div>
                                <div class="col-sm-8 text-dark fw-semibold">
                                    <?= esc($log['user_name'] ?? 'Sistema / Anónimo') ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php if(!empty($log['user_name'])): ?>
                        <div class="list-group-item px-3 py-2">
                            <div class="row align-items-center">
                                <div class="col-sm-4 text-muted small fw-semibold">Email:</div>
                                <div class="col-sm-8 text-dark">
                                    <?= esc($log['user_email'] ?? 'No disponible') ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-3 py-2">
                            <div class="row align-items-center">
                                <div class="col-sm-4 text-muted small fw-semibold">Rol:</div>
                                <div class="col-sm-8">
                                    <span class="badge bg-success-subtle text-success border border-success fw-semibold">
                                        <?= esc(ucfirst($log['user_role'] ?? 'Usuario')) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Descripción Detallada -->
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="fw-semibold mb-3">Descripción del Evento</h6>
                    <div class="p-3 bg-light rounded-3 border">
                        <p class="mb-0 text-dark" style="white-space: pre-line;">
                            <?= esc($log['description']) ?>
                        </p>
                    </div>
                </div>
            </div>
            
        </div>
        
        <div class="card-footer bg-white border-top text-end p-3">
            <a href="<?= base_url('logs/list') ?>" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i> Volver al listado
            </a>
        </div>
    </div>
</div>
