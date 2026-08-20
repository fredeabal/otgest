<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">Detalle del Registro de Auditoría</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        
                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Responsable de la Acción:</strong></div>
                                <div class="col-sm-8">
                                    <strong><?= esc($log['user_name'] ?? 'Sistema / Anónimo') ?></strong><br>
                                    <?php if(!empty($log['user_email'])): ?>
                                    <small class="text-muted">Email: <?= esc($log['user_email']) ?></small><br>
                                    <small class="text-muted">Rol: <?= esc(ucfirst($log['user_role'] ?? 'Usuario')) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Fecha y Hora:</strong></div>
                                <div class="col-sm-8">
                                    <?= esc(date('d/m/Y H:i:s', strtotime($log['created_at']))) ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Módulo:</strong></div>
                                <div class="col-sm-8">
                                    <?= esc($log['module']) ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Acción:</strong></div>
                                <div class="col-sm-8">
                                    <?= esc($log['action'] ?? 'SYSTEM') ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Dirección IP:</strong></div>
                                <div class="col-sm-8">
                                    <?= esc($log['ip_address']) ?>
                                </div>
                            </div>
                        </div>

                        <div class="list-group-item px-0">
                            <div class="row">
                                <div class="col-sm-4"><strong>Descripción del Evento:</strong></div>
                                <div class="col-sm-8">
                                    <?= nl2br(esc($log['description'])) ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end bg-white border-top">
                    <a href="<?= base_url('logs/list') ?>" class="btn btn-dark">Volver</a>
                </div>
            </div>
        </div>
    </div>
</div>
