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
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="round-45 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                            <i class="ti ti-file-plus fs-24px"></i>
                        </div>
                        <h2 class="fw-bold mb-0 text-primary"><?= $stats['docs_pending_read'] ?? 0 ?></h2>
                    </div>
                    <p class="text-muted fw-bold mb-3 small text-uppercase text-xs letter-spacing-sm" >Documentos Pendientes</p>
                    <div class="pt-3 border-top text-end">
                        <a href="<?= base_url('documents/list') ?>" class="text-primary text-decoration-none fw-bold small transition-hover">
                            Gestionar <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Ausencias Pendientes -->
        <div class="col-md-4">
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="round-45 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                            <i class="ti ti-bell-ringing fs-24px"></i>
                        </div>
                        <h2 class="fw-bold mb-0 text-primary"><?= $stats['absences_pending'] ?? 0 ?></h2>
                    </div>
                    <p class="text-muted fw-bold mb-3 small text-uppercase text-xs letter-spacing-sm" >Ausencias Pendientes</p>
                    <div class="pt-3 border-top text-end">
                        <a href="<?= base_url('absences/manage') ?>" class="text-primary text-decoration-none fw-bold small transition-hover">
                            Gestionar <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Gastos Pendientes -->
        <div class="col-md-4">
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="round-45 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                            <i class="ti ti-receipt fs-24px"></i>
                        </div>
                        <h2 class="fw-bold mb-0 text-primary"><?= $stats['expenses_pending'] ?? 0 ?></h2>
                    </div>
                    <p class="text-muted fw-bold mb-3 small text-uppercase text-xs letter-spacing-sm" >Gastos Pendientes</p>
                    <div class="pt-3 border-top text-end">
                        <a href="<?= base_url('expenses/manage') ?>" class="text-primary text-decoration-none fw-bold small transition-hover">
                            Gestionar <i class="ti ti-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 
    // ---------------------------------------------------------------------------------
    // FILA 2: KPIs DE PRESENCIA (EN VIVO)
    // ---------------------------------------------------------------------------------
    -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-player-play fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-success"><?= $stats['users_active'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm" >Usuarios Activos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-player-pause fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-warning"><?= $stats['users_break'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm" >Usuarios En Pausa</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="ti ti-alarm-off fs-30px"></i>
                    </div>
                    <h2 class="fw-bold mb-1 text-danger"><?= $stats['absences_today'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small text-xs letter-spacing-sm" >Ausencias Hoy</p>
                </div>
            </div>
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


