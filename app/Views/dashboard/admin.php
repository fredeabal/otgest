<?php 
/**
 * Dashboard Administrativo "Ultra" - Versión Final Limpia
 * Consolidado para Administradores (Rol 1) y Supervisores (Rol 2)
 */
?>
<div class="container-fluid animate__animated animate__fadeIn">
    <!-- =================================================================================
    // HEADER
    // ================================================================================= -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-12">
            <h4 class="fw-bold mb-1 mt-2">Panel de Control Operativo</h4>
            <p class="text-muted mb-0">Situación detallada de la empresa al <?= date('d M Y') ?></p>
        </div>
    </div>

    <!-- 
    // ---------------------------------------------------------------------------------
    // FILA 1: KPIs DE PRESENCIA (EN VIVO)
    // ---------------------------------------------------------------------------------
    -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card admin-card border-0 h-100 bg-success-subtle shadow-none">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-3">
                        <iconify-icon icon="solar:user-hand-up-bold-duotone" width="30"></iconify-icon>
                    </div>
                    <h3 class="fw-bold mb-1"><?= $stats['users_active'] ?? 0 ?></h3>
                    <p class="text-success fw-semibold mb-0">Usuarios Activos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card admin-card border-0 h-100 bg-warning-subtle shadow-none">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-warning text-white d-flex align-items-center justify-content-center mx-auto mb-3">
                        <iconify-icon icon="solar:tea-cup-bold-duotone" width="30"></iconify-icon>
                    </div>
                    <h3 class="fw-bold mb-1"><?= $stats['users_break'] ?? 0 ?></h3>
                    <p class="text-warning fw-semibold mb-0">Usuarios en Pausa</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card admin-card border-0 h-100 bg-primary-subtle shadow-none">
                <div class="card-body p-4 text-center">
                    <div class="round-50 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3">
                        <iconify-icon icon="solar:calendar-mark-bold-duotone" width="30"></iconify-icon>
                    </div>
                    <h3 class="fw-bold mb-1"><?= $stats['absences_today'] ?? 0 ?></h3>
                    <p class="text-primary fw-semibold mb-0">Ausencias hoy</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 
    // ---------------------------------------------------------------------------------
    // FILA 2: ALERTAS DE GESTIÓN (PENDIENTES)
    // ---------------------------------------------------------------------------------
    -->
    <div class="row g-4 mb-4">
        <!-- Docs Pendientes -->
        <div class="col-md-4">
            <a href="<?= base_url('documents/list') ?>" class="card admin-card border-0 h-100 bg-info-subtle shadow-none text-decoration-none transition-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="round-45 rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-3">
                        <iconify-icon icon="solar:document-add-bold-duotone" width="24"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0"><?= $stats['docs_pending_read'] ?? 0 ?></h4>
                        <p class="text-info fw-semibold mb-0 small">Docs. pendientes</p>
                    </div>
                </div>
            </a>
        </div>
        <!-- Ausencias Pendientes -->
        <div class="col-md-4">
            <a href="<?= base_url('absences/manage') ?>" class="card admin-card border-0 h-100 bg-danger-subtle shadow-none text-decoration-none transition-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="round-45 rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-3">
                        <iconify-icon icon="solar:bell-bing-bold-duotone" width="24"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0"><?= $stats['absences_pending'] ?? 0 ?></h4>
                        <p class="text-danger fw-semibold mb-0 small">Ausencias pendientes</p>
                    </div>
                </div>
            </a>
        </div>
        <!-- Gastos Pendientes -->
        <div class="col-md-4">
            <a href="<?= base_url('expenses/manage') ?>" class="card admin-card border-0 h-100 bg-secondary-subtle shadow-none text-decoration-none transition-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="round-45 rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3">
                        <iconify-icon icon="solar:bill-list-bold-duotone" width="24"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0"><?= $stats['expenses_pending'] ?? 0 ?></h4>
                        <p class="text-secondary fw-semibold mb-0 small">Gastos pendientes</p>
                    </div>
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
            <div class="card admin-card border-0 mb-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="card-title fw-bold mb-0">Rendimiento vs Ausencias</h5>
                    </div>
                    <div id="dual_performance_chart" style="min-height: 380px;"></div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card admin-card border-0 h-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Últimos Movimientos</h5>
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
/* Sobrescritura de paleta Danger a nivel de Dashboard */
:root, [data-bs-theme="dark"] {
    --bs-danger: #ff3361 !important;
    --bs-danger-rgb: 255, 51, 97 !important;
    --bs-danger-bg-subtle: rgba(255, 51, 97, 0.15) !important;
    --bs-danger-text-emphasis: #ff3361 !important;
}

.bg-danger-subtle {
    background-color: rgba(255, 51, 97, 0.1) !important;
}

.text-danger {
    color: #ff3361 !important;
}

.bg-danger {
    background-color: #ff3361 !important;
}

.admin-card {
    background: #2a3447 !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    border-radius: 20px !important;
}
.transition-hover { transition: transform 0.3s ease; }
.transition-hover:hover { transform: translateY(-5px); }
.round-50 { width: 50px; height: 50px; }
.round-45 { width: 45px; height: 45px; }
.timeline-badge { width: 10px; height: 10px; background: #2a3447; z-index: 1; }
.timeline-line { width: 2px; }
</style>
