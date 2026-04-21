<!-- =================================================================================
    // Vista: Detalles de Jornada
    // ================================================================================= -->
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">Detalles de Jornada</h5>
                </div>

                <!-- Resumen de la jornada -->
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="card border h-100">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <iconify-icon icon="solar:calendar-bold-duotone" class="text-primary" style="font-size: 2rem;"></iconify-icon>
                                    </div>
                                    <h6 class="text-muted mb-1">Fecha</h6>
                                    <h4 class="text-info mb-0"><?php echo date('d/m/Y', strtotime($workday_date)); ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border h-100">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <iconify-icon icon="solar:clock-circle-bold-duotone" class="text-primary" style="font-size: 2rem;"></iconify-icon>
                                    </div>
                                    <h6 class="text-muted mb-1">Horas Trabajadas</h6>
                                    <h4 class="text-primary mb-0"><?php 
                                    $workedMins = round($worked_hours * 60);
                                    echo floor($workedMins / 60) . ':' . str_pad($workedMins % 60, 2, '0', STR_PAD_LEFT); 
                                    ?> h</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border h-100">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <iconify-icon icon="solar:pause-circle-bold-duotone" class="text-warning" style="font-size: 2rem;"></iconify-icon>
                                    </div>
                                    <h6 class="text-muted mb-1">Tiempo de Pausas</h6>
                                    <h4 class="text-warning mb-0"><?php 
                                    $breakMins = round(($break_hours ?? 0) * 60);
                                    echo floor($breakMins / 60) . ':' . str_pad($breakMins % 60, 2, '0', STR_PAD_LEFT); 
                                    ?> h</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border h-100">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <iconify-icon icon="solar:add-circle-bold-duotone" class="text-primary" style="font-size: 2rem;"></iconify-icon>
                                    </div>
                                    <h6 class="text-muted mb-1">Horas Extras</h6>
                                    <h4 class="text-primary mb-0"><?php 
                                    $overtimeMins = round($overtime_hours * 60);
                                    echo floor($overtimeMins / 60) . ':' . str_pad($overtimeMins % 60, 2, '0', STR_PAD_LEFT); 
                                    ?> h
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de eventos -->
                    <div class="list-group mt-4">
                        <?php foreach ($events as $index => $event): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                    <?php
                                    $iconName = '';
                                    switch ($event['event_type']) {
                                        case 'in':
                                            $iconName = 'solar:login-bold-duotone';
                                            $iconClass = 'text-success';
                                            break;
                                        case 'break_start':
                                            $iconName = 'solar:pause-circle-bold-duotone';
                                            $iconClass = 'text-warning';
                                            break;
                                        case 'break_end':
                                            $iconName = 'solar:play-bold-duotone';
                                            $iconClass = 'text-info';
                                            break;
                                        case 'out':
                                            $iconName = 'solar:logout-bold-duotone';
                                            $iconClass = 'text-danger';
                                            break;
                                        default:
                                            $iconName = 'solar:clock-circle-bold-duotone';
                                            $iconClass = 'text-secondary';
                                    }
                                    ?>
                                        <iconify-icon icon="<?= $iconName ?>" class="<?= $iconClass ?> fs-5"></iconify-icon>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">
                                            <?php
                                        $eventNames = [
                                            'in' => 'Entrada al trabajo',
                                            'break_start' => 'Inicio de pausa',
                                            'break_end' => 'Fin de pausa',
                                            'out' => 'Salida del trabajo'
                                        ];
                                        echo $eventNames[$event['event_type']] ?? $event['event_type'];
                                        ?>
                                            <small class="text-muted ms-2">
                                                <?= esc(date('d/m/Y - H:i:s', strtotime($event['event_time']))) ?>
                                            </small>
                                        </h6>
                                        <!-- mostrar dirección usando Nominatim si existen coordenadas GPS -->
                                        <?php if ($event['latitude'] && $event['longitude']): ?>
                                        <p class="text-muted mb-0 small">
                                            <iconify-icon icon="solar:map-point-bold-duotone" class="me-1"></iconify-icon>
                                            Ubicación:
                                            <a href="https://www.google.com/maps/search/?api=1&query=<?= $event['latitude'] ?>,<?= $event['longitude'] ?>"
                                               target="_blank" class="text-decoration-none reverse-geocode" 
                                               data-lat="<?= $event['latitude'] ?>" data-lon="<?= $event['longitude'] ?>">
                                                Cargando dirección...
                                                <iconify-icon icon="solar:link-bold-duotone" class="ms-1 small"></iconify-icon>
                                            </a>
                                        </p>


                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <?php if ($event['event_type'] === 'out' && $event['autoclose']): ?>
                                    <span class="badge bg-warning-subtle text-warning">
                                        <iconify-icon icon="solar:magic-stick-bold-duotone" class="me-1"></iconify-icon>Automático
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-grids py-5 text-center">
                        <?php if (isset($is_admin_view) && $is_admin_view): ?>
                        <a href="<?= base_url('workdays/manage') ?>" class="btn btn-dark ms-1">Volver a Gestión</a>
                        <?php else: ?>
                        <a href="javascript:void(0)" onclick="goBack()" class="btn btn-dark ms-1">Volver atrás</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

