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
                        <div class="round-45 rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:document-add-bold-duotone" width="24"></iconify-icon>
                        </div>
                        <h2 class="fw-bold mb-0 text-info"><?= $stats['docs_pending_read'] ?? 0 ?></h2>
                    </div>
                    <p class="text-muted fw-bold mb-3 small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Documentos Pendientes</p>
                    <div class="pt-3 border-top text-end">
                        <a href="<?= base_url('documents/list') ?>" class="text-secondary text-decoration-none fw-bold small transition-hover">
                            Gestionar <iconify-icon icon="solar:alt-arrow-right-linear" class="ms-1"></iconify-icon>
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
                        <div class="round-45 rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:bell-bing-bold-duotone" width="24"></iconify-icon>
                        </div>
                        <h2 class="fw-bold mb-0 text-danger"><?= $stats['absences_pending'] ?? 0 ?></h2>
                    </div>
                    <p class="text-muted fw-bold mb-3 small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Ausencias Pendientes</p>
                    <div class="pt-3 border-top text-end">
                        <a href="<?= base_url('absences/manage') ?>" class="text-secondary text-decoration-none fw-bold small transition-hover">
                            Gestionar <iconify-icon icon="solar:alt-arrow-right-linear" class="ms-1"></iconify-icon>
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
                        <div class="round-45 rounded-circle bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center">
                            <iconify-icon icon="solar:bill-list-bold-duotone" width="24"></iconify-icon>
                        </div>
                        <h2 class="fw-bold mb-0 text-secondary"><?= $stats['expenses_pending'] ?? 0 ?></h2>
                    </div>
                    <p class="text-muted fw-bold mb-3 small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Gastos Pendientes</p>
                    <div class="pt-3 border-top text-end">
                        <a href="<?= base_url('expenses/manage') ?>" class="text-secondary text-decoration-none fw-bold small transition-hover">
                            Gestionar <iconify-icon icon="solar:alt-arrow-right-linear" class="ms-1"></iconify-icon>
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
                        <iconify-icon icon="solar:user-hand-up-bold-duotone" width="30"></iconify-icon>
                    </div>
                    <h2 class="fw-bold mb-1 text-success"><?= $stats['users_active'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small" style="font-size: 0.65rem; letter-spacing: 0.5px;">Usuarios Activos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center mx-auto mb-3">
                        <iconify-icon icon="solar:tea-cup-bold-duotone" width="30"></iconify-icon>
                    </div>
                    <h2 class="fw-bold mb-1 text-warning"><?= $stats['users_break'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small" style="font-size: 0.65rem; letter-spacing: 0.5px;">En Pausa</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border shadow-none bg-white">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mx-auto mb-3">
                        <iconify-icon icon="solar:calendar-mark-bold-duotone" width="30"></iconify-icon>
                    </div>
                    <h2 class="fw-bold mb-1 text-primary"><?= $stats['absences_today'] ?? 0 ?></h2>
                    <p class="text-muted fw-bold mb-0 text-uppercase small" style="font-size: 0.65rem; letter-spacing: 0.5px;">Ausencias Hoy</p>
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
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="card-title fw-bold mb-0">Rendimiento vs Ausencias</h5>
                    </div>
                    <div id="dual_performance_chart" style="min-height: 380px;"></div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4 d-flex align-items-center">
                        <iconify-icon icon="solar:history-bold-duotone" class="me-2 text-info"></iconify-icon>
                        Últimos Movimientos
                    </h5>
                    <div class="timeline-widget">
                        <?php if(!empty($stats['activity_timeline'])): ?>
                            <?php foreach($stats['activity_timeline'] as $item): ?>
                                <div class="timeline-item d-flex mb-4">
                                    <div class="timeline-time text-muted fs-2" style="width: 50px;">
                                        <?= date('H:i', strtotime($item['created_at'])) ?>
                                    </div>
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center mx-3">
                                        <span class="timeline-badge border-2 border border-primary rounded-circle"></span>
                                        <span class="timeline-line flex-grow-1 bg-light"></span>
                                    </div>
                                    <div class="timeline-desc">
                                        <h6 class="fw-bold mb-0"><?= esc($item['name']) ?></h6>
                                        <p class="fs-2 mb-0 <?= $item['category'] == 'expense' ? 'text-success' : 'text-primary' ?>">
                                            <?= esc($item['type']) ?>
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

<!-- =================================================================================
// SCRIPTS & ESTILOS GESTIÓN
// ================================================================================= -->
<script src="<?= base_url() ?>assets/libs/apexcharts/dist/apexcharts.min.js"></script>
<link rel="stylesheet" href="<?= base_url() ?>assets/libs/animate.css/animate.min.css"/>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- GRÁFICO DUAL (RENDIMIENTO VS AUSENCIAS) ---
        new ApexCharts(document.querySelector("#dual_performance_chart"), {
            chart: { 
                height: 380, type: 'area', fontFamily: 'Plus Jakarta Sans', foreColor: '#7C8FAC', 
                toolbar: { show: false }, stacked: false
            },
            stroke: { curve: 'smooth', width: [3, 3] },
            fill: { 
                type: 'gradient', 
                gradient: { shadeIntensity: 1, opacityFrom: [0.3, 0.4], opacityTo: [0.05, 0.1], stops: [0, 95, 100] } 
            },
            series: [
                { name: 'Ausencia', type: 'area', data: <?= json_encode($stats['series_absences'] ?? []) ?> },
                { name: 'Asistencia', type: 'area', data: <?= json_encode($stats['series_attendance'] ?? []) ?> }
            ],
            xaxis: { 
                categories: <?= json_encode($stats['chart_labels'] ?? []) ?>, 
                axisBorder: { show: false }, axisTicks: { show: false } 
            },
            markers: { size: [5, 0], strokeWidth: 0, hover: { size: 7 } },
            colors: ['#ff3361', '#5d87ff'],
            grid: { borderColor: 'rgba(255,255,255,0.05)', strokeDashArray: 4 },
            legend: { show: true, position: 'top', horizontalAlign: 'right', labels: { colors: '#7C8FAC' } },
            tooltip: { theme: 'dark' }
        }).render();
    });
</script>

<style>
    .card { border-radius: 12px !important; }
    .round-45 { width: 45px; height: 45px; flex-shrink: 0; }
    .round-50 { width: 50px; height: 50px; flex-shrink: 0; }
    .shadow-none { box-shadow: none !important; }

    /* Timeline */
    .timeline-badge {
        width: 10px; height: 10px;
        background: white; z-index: 1;
        border: 2px solid var(--bs-primary) !important;
    }
    .timeline-line { width: 1px; background-color: #eee !important; }
</style>
