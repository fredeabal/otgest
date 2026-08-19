<?php 
/**
 * Dashboard Administrativo "Ultra" - Versión Final Limpia
 * Consolidado para Administradores (Rol 1) y Supervisores (Rol 2)
 */
?>
<div class="container-fluid animate__animated animate__fadeIn">

    <!-- 
    // ---------------------------------------------------------------------------------
    // FILA 1: ALERTAS DE GESTIÓN (PENDIENTES) - PRIORIDAD ALTA
    // ---------------------------------------------------------------------------------
    -->
    <div class="row g-4 mb-4">
        <!-- Docs Pendientes -->
        <div class="col-md-4">
            <a href="<?= base_url('documents/list') ?>" class="card h-100 border shadow-none bg-white text-decoration-none transition-hover d-block">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-file-plus fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-primary"><?= $stats['docs_pending_read'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm">Documentos Pendientes</p>
                </div>
            </a>
        </div>
        <!-- Ausencias Pendientes -->
        <div class="col-md-4">
            <a href="<?= base_url('absences/manage') ?>" class="card h-100 border shadow-none bg-white text-decoration-none transition-hover d-block">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-bell-ringing fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-info"><?= $stats['absences_pending'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm">Ausencias Pendientes</p>
                </div>
            </a>
        </div>
        <!-- Gastos Pendientes -->
        <div class="col-md-4">
            <a href="<?= base_url('expenses/manage') ?>" class="card h-100 border shadow-none bg-white text-decoration-none transition-hover d-block">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-receipt fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-secondary"><?= $stats['expenses_pending'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm">Gastos Pendientes</p>
                </div>
            </a>
        </div>
    </div>

    <!-- 
    // ---------------------------------------------------------------------------------
    // FILA 2: KPIs DE PRESENCIA (EN VIVO)
    // ---------------------------------------------------------------------------------
    -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <a href="<?= base_url('workdays/manage?date_from=' . date('Y-m-d') . '&date_to=' . date('Y-m-d')) ?>" class="card h-100 border shadow-none bg-white text-decoration-none transition-hover d-block">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-player-play fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-success"><?= $stats['users_active'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm" >Usuarios Activos</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= base_url('workdays/manage?date_from=' . date('Y-m-d') . '&date_to=' . date('Y-m-d')) ?>" class="card h-100 border shadow-none bg-white text-decoration-none transition-hover d-block">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-player-pause fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-warning"><?= $stats['users_break'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm" >Usuarios En Pausa</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= base_url('absences/manage?status=approved&date_from=' . date('Y-m-d') . '&date_to=' . date('Y-m-d')) ?>" class="card h-100 border shadow-none bg-white text-decoration-none transition-hover d-block">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-alarm-off fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-danger"><?= $stats['absences_today'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm" >Ausencias Hoy</p>
                </div>
            </a>
        </div>
    </div>

    <!-- 
    // ---------------------------------------------------------------------------------
    // GRÁFICA DUAL Y FEED DE ACTIVIDAD
    // ---------------------------------------------------------------------------------
    -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <i class="ti ti-chart-line me-2 text-primary fs-24px"></i>
                        <h5 class="card-title fw-bold mb-0">Rendimiento vs Ausencias</h5>
                    </div>
                    <div id="dual_performance_chart" 
                         style="min-height: 380px;"
                         data-absences='<?= json_encode($stats['series_absences'] ?? []) ?>'
                         data-attendance='<?= json_encode($stats['series_attendance'] ?? []) ?>'
                         data-labels='<?= json_encode($stats['chart_labels'] ?? []) ?>'>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4 d-flex align-items-center">
                        <i class="ti ti-history me-2 text-primary fs-24px"></i>
                        Últimos Movimientos
                    </h5>
                    <div class="timeline-widget">
                        <?php if(!empty($stats['activity_timeline'])): ?>
                            <?php foreach($stats['activity_timeline'] as $item): ?>
                                <div class="timeline-item d-flex mb-4">
                                    <div class="timeline-time text-muted fs-2">
                                        <?= date('H:i', strtotime($item['created_at'])) ?>
                                    </div>
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center mx-3">
                                        <span class="timeline-badge border-2 border border-primary rounded-circle"></span>
                                        <span class="timeline-line flex-grow-1 bg-light"></span>
                                    </div>
                                    <div class="timeline-desc">
                                        <small class="text-primary fw-bold"><?= esc($item['module']) ?></small>
                                        <h6 class="fw-bold mb-0 fs-2 text-truncate-timeline"><?= esc($item['name']) ?></h6>
                                        <p class="fs-1 mb-0 text-muted">
                                            <?= esc($item['description']) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center py-4">Sin actividad reciente</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


