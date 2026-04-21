<div class="container">
    <!-- =================================================================================
    // Dashboard de Resumen
    // ================================================================================= -->

    <!-- Card de notificaciones -->
    <?php if (($stats['pending_absences'] ?? 0) > 0 || ($stats['pending_expenses'] ?? 0) > 0): ?>
        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <iconify-icon icon="solar:bell-bold-duotone" class="me-3 text-danger" style="font-size: 3rem;"></iconify-icon>
                            <h6 class="card-title text-danger mb-0">Pendientes de Revisión</h6>
                        </div>
                        <?php if (($stats['pending_absences'] ?? 0) > 0): ?>
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-grow-1">
                                    <p class="card-text mb-0">Tienes <strong><?= $stats['pending_absences'] ?> solicitud(es) de ausencia</strong> pendiente(s) de revisión.</p>
                                </div>
                                <div class="ms-auto">
                                    <a href="<?= base_url('absences/manage') ?>" class="btn btn-danger btn-sm" style="width: 100px;">
                                        Ver ausencias
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (($stats['pending_expenses'] ?? 0) > 0): ?>
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="card-text mb-0">Tienes <strong><?= $stats['pending_expenses'] ?> gasto(s)</strong> pendiente(s) de justificación.</p>
                                </div>
                                <div class="ms-auto">
                                    <a href="<?= base_url('expenses/manage') ?>" class="btn btn-danger btn-sm" style="width: 100px;">
                                        Ver gastos
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Cards Adicionales -->
    <div class="row g-4 mb-4">
        <!-- Jornada de Trabajo -->
        <div class="col-md-4 d-flex align-items-stretch">
            <div class="card h-100 w-100">
                <div class="card-body text-center">
                    <div class="form-group">
                        <iconify-icon icon="solar:stopwatch-bold-duotone" class="mb-3 text-primary" style="font-size: 3rem;"></iconify-icon>
                        <h4 class="card-title mb-1">Jornada de Trabajo</h4>
                        <small>Para modificar la jornada de trabajo haz click aquí.</small>
                        <a href="<?= base_url('workdays') ?>"
                            class="btn d-block w-100 fw-medium btn-primary my-4">
                            Jornada laboral
                        </a>
                        <p class="card-subtitle">
                            <small>
                                <?php if ($current_workday): ?>
                                    <?php if ($current_workday['end_time']): ?>
                                        <iconify-icon icon="solar:check-circle-bold-duotone" class="text-success"></iconify-icon> Jornada finalizada
                                    <?php elseif ($current_workday['autoclose']): ?>
                                        <iconify-icon icon="solar:pause-circle-bold-duotone" class="text-info"></iconify-icon> Jornada cerrada automaticamente
                                    <?php else: ?>
                                        <iconify-icon icon="solar:play-circle-bold-duotone" class="text-warning"></iconify-icon> Jornada activa desde
                                        <?= esc($current_workday['start_time']) ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <iconify-icon icon="solar:pause-circle-bold-duotone" class="text-danger"></iconify-icon> <span>Sin jornada hoy</span>
                                <?php endif; ?>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen Mensual Integrado -->
        <div class="col-md-8">
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <h6 class="fw-bold mb-4 text-muted text-uppercase small text-center" style="letter-spacing: 1px;">Resumen del Mes</h6>
                    <div class="row text-center">
                        <!-- Días -->
                        <div class="col-6 border-end">
                            <div class="round-50 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mx-auto mb-3">
                                <iconify-icon icon="solar:calendar-bold-duotone" width="30"></iconify-icon>
                            </div>
                            <h2 class="fw-bold mb-1 text-primary"><?php echo $stats['my_workdays_month'] ?? 0; ?></h2>
                            <p class="text-muted fw-bold mb-0 small text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">Días Trabajados</p>
                        </div>
                        <!-- Horas -->
                        <div class="col-6">
                            <div class="round-50 rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center mx-auto mb-3">
                                <iconify-icon icon="solar:clock-circle-bold-duotone" width="30"></iconify-icon>
                            </div>
                            <h2 class="fw-bold mb-1 text-info"><?php
                                                    $hours = floor(($stats['my_total_hours_month'] ?? 0) / 60);
                                                    $minutes = floor(($stats['my_total_hours_month'] ?? 0) % 60);
                                                    echo $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                                                    ?></h2>
                            <p class="text-muted fw-bold mb-0 small text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">Horas Trabajadas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row g-4 mb-5">
        <!-- Mis Documentos Enviados -->
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="round-45 rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:plain-bold-duotone" width="24"></iconify-icon>
                        </div>
                        <h2 class="fw-bold mb-0 text-info"><?= $stats['my_sent_documents'] ?? 0 ?></h2>
                    </div>
                    <p class="text-muted fw-bold mb-3 small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Documentos Enviados</p>
                    <div class="pt-3 border-top">
                        <a href="<?= base_url('documents/sent') ?>" class="text-info text-decoration-none fw-bold small transition-hover d-flex align-items-center justify-content-end">
                            Gestionar <iconify-icon icon="solar:alt-arrow-right-linear" class="ms-1"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mis Documentos Recibidos -->
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="round-45 rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:inbox-bold-duotone" width="24"></iconify-icon>
                        </div>
                        <h2 class="fw-bold mb-0 text-warning"><?= $stats['my_received_documents'] ?? 0 ?></h2>
                    </div>
                    <p class="text-muted fw-bold mb-3 small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Documentos Recibidos</p>
                    <div class="pt-3 border-top">
                        <a href="<?= base_url('documents/list') ?>" class="text-warning text-decoration-none fw-bold small transition-hover d-flex align-items-center justify-content-end">
                            Ver bandeja <iconify-icon icon="solar:alt-arrow-right-linear" class="ms-1"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Solicitudes Aceptadas -->
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="round-45 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:like-bold-duotone" width="24"></iconify-icon>
                        </div>
                        <h2 class="fw-bold mb-0 text-success"><?= $stats['my_absences_approved'] ?? 0 ?></h2>
                    </div>
                    <p class="text-muted fw-bold mb-3 small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Solicitudes Aceptadas</p>
                    <div class="pt-3 border-top">
                        <a href="<?= base_url('absences/list?status=approved') ?>" class="text-success text-decoration-none fw-bold small transition-hover d-flex align-items-center justify-content-end">
                            Ver historial <iconify-icon icon="solar:alt-arrow-right-linear" class="ms-1"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Solicitudes Rechazadas -->
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="round-45 rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:dislike-bold-duotone" width="24"></iconify-icon>
                        </div>
                        <h2 class="fw-bold mb-0 text-danger"><?= $stats['my_absences_rejected'] ?? 0 ?></h2>
                    </div>
                    <p class="text-muted fw-bold mb-3 small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Solicitudes Rechazadas</p>
                    <div class="pt-3 border-top">
                        <a href="<?= base_url('absences/list?status=rejected') ?>" class="text-danger text-decoration-none fw-bold small transition-hover d-flex align-items-center justify-content-end">
                            Ver rechazos <iconify-icon icon="solar:alt-arrow-right-linear" class="ms-1"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendario de Actividad -->
    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">Calendario de Actividad</h5>
                </div>
                <div class="card-body">
                    <div id="calendar" style="min-height: 500px;" data-events-url="<?= base_url('dashboard/events') ?>"></div>
                    
                    <!-- Leyenda del Calendario -->
                    <div class="d-flex flex-wrap gap-4 mt-4 pt-3 border-top justify-content-center">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 12px; height: 12px; border-radius: 50%; background-color: #5d87ff; display: inline-block;"></span>
                            <span class="fs-2 fw-semibold text-muted">Jornadas Trabajadas</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 12px; height: 12px; border-radius: 50%; background-color: #13deb9; display: inline-block;"></span>
                            <span class="fs-2 fw-semibold text-muted">Vacaciones</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 12px; height: 12px; border-radius: 50%; background-color: #fa896b; display: inline-block;"></span>
                            <span class="fs-2 fw-semibold text-muted">Bajas / Médicas</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 12px; height: 12px; border-radius: 50%; background-color: #ffae1f; display: inline-block;"></span>
                            <span class="fs-2 fw-semibold text-muted">Otros Permisos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


