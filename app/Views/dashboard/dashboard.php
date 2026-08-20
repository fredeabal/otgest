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
                            <i class="ti ti-bell me-3 text-danger fs-3rem"></i>
                            <h6 class="card-title text-danger mb-0">Pendientes de Revisión</h6>
                        </div>
                        <?php if (($stats['pending_absences'] ?? 0) > 0): ?>
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-grow-1">
                                    <p class="card-text mb-0">Tienes <strong><?= $stats['pending_absences'] ?> solicitud(es) de ausencia</strong> pendiente(s) de revisión.</p>
                                </div>
                                <div class="ms-auto">
                                    <a href="<?= base_url('absences/manage') ?>" class="btn btn-danger btn-sm w-100px" >
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
                                    <a href="<?= base_url('expenses/manage') ?>" class="btn btn-danger btn-sm w-100px" >
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
                        <i class="ti ti-clock mb-3 text-primary fs-3rem"></i>
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
                                        <i class="ti ti-circle-check text-success"></i> Jornada finalizada
                                    <?php elseif ($current_workday['autoclose']): ?>
                                        <i class="ti ti-player-pause text-info"></i> Jornada cerrada automaticamente
                                    <?php else: ?>
                                        <?php if (($current_workday['status'] ?? '') === 'pause'): ?>
                                            <i class="ti ti-player-pause text-info"></i> Jornada en pausa: 
                                            <span class="fw-bold text-info fs-3" id="active-workday-timer" data-elapsed="<?= $current_workday['elapsed_seconds'] ?>" data-status="pause"></span>
                                        <?php else: ?>
                                            <i class="ti ti-player-play text-warning"></i> Jornada activa: 
                                            <span class="fw-bold text-warning fs-3" id="active-workday-timer" data-elapsed="<?= $current_workday['elapsed_seconds'] ?>" data-status="active"></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <i class="ti ti-player-pause text-danger"></i> <span>Sin jornada hoy</span>
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
                                <i class="ti ti-calendar fs-30px"></i>
                            </div>
                            <h2 class="fw-bold mb-1 text-primary"><?php echo $stats['my_workdays_month'] ?? 0; ?></h2>
                            <p class="text-muted fw-bold mb-0 small text-uppercase text-xxs letter-spacing-sm" >Días Trabajados</p>
                        </div>
                        <!-- Horas -->
                        <div class="col-6">
                            <div class="round-50 rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center mx-auto mb-3">
                                <i class="ti ti-clock fs-30px"></i>
                            </div>
                            <h2 class="fw-bold mb-1 text-info"><?php
                                                    $hours = floor(($stats['my_total_hours_month'] ?? 0) / 60);
                                                    $minutes = floor(($stats['my_total_hours_month'] ?? 0) % 60);
                                                    echo $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                                                    ?></h2>
                            <p class="text-muted fw-bold mb-0 small text-uppercase text-xxs letter-spacing-sm" >Horas Trabajadas</p>
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
            <a href="<?= base_url('documents/sent') ?>" class="card h-100 border shadow-none bg-white text-decoration-none transition-hover d-block">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-plane fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-info"><?= $stats['my_sent_documents'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm">Documentos Enviados</p>
                </div>
            </a>
        </div>

        <!-- Mis Documentos Recibidos -->
        <div class="col-md-3 col-sm-6">
            <a href="<?= base_url('documents/list') ?>" class="card h-100 border shadow-none bg-white text-decoration-none transition-hover d-block">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-inbox fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-warning"><?= $stats['my_received_documents'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm">Documentos Recibidos</p>
                </div>
            </a>
        </div>

        <!-- Solicitudes Aceptadas -->
        <div class="col-md-3 col-sm-6">
            <a href="<?= base_url('absences/list?status=approved') ?>" class="card h-100 border shadow-none bg-white text-decoration-none transition-hover d-block">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-thumb-up fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-success"><?= $stats['my_absences_approved'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm">Solicitudes Aceptadas</p>
                </div>
            </a>
        </div>

        <!-- Solicitudes Rechazadas -->
        <div class="col-md-3 col-sm-6">
            <a href="<?= base_url('absences/list?status=rejected') ?>" class="card h-100 border shadow-none bg-white text-decoration-none transition-hover d-block">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-thumb-down fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-danger"><?= $stats['my_absences_rejected'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm">Solicitudes Rechazadas</p>
                </div>
            </a>
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

<!-- Script para el contador de jornada en vivo -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const timerElement = document.getElementById('active-workday-timer');
    if (timerElement) {
        let elapsedSeconds = parseInt(timerElement.getAttribute('data-elapsed'), 10) || 0;
        let isPaused = timerElement.getAttribute('data-status') === 'pause';
        
        function updateTimer() {
            let totalMinutes = Math.floor(elapsedSeconds / 60);
            let hours = Math.floor(totalMinutes / 60);
            let minutes = totalMinutes % 60;
            timerElement.textContent = hours + ':' + minutes.toString().padStart(2, '0');
            if (!isPaused) elapsedSeconds++;
        }
        
        updateTimer();
        setInterval(updateTimer, 1000);
    }
});
</script>


