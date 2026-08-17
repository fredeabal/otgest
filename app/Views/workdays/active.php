<!-- =================================================================================
Vista: Jornada Activa (Después de Reanudar)
================================================================================= -->

<div class="container">
    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary-subtle">
                    <h5 class="mb-0 text-primary">Jornada activa</h5>
                </div>
                <div class="card-body text-center">
                    <div class="form-group">
                        <!-- Reloj de hora actual -->
                        <div class="mb-4 mt-2">
                            <div class="h1 fw-bolder mb-0" id="current-time-display" style="font-family: monospace; font-size: 3.5rem;">--:--:--</div>
                            <div class="text-muted small text-uppercase fw-semibold mt-1" id="current-date-display">--</div>
                        </div>

                        <?php 
                            $staticBreakSeconds = isset($workdayData) ? floor($workdayData['break_time'] * 3600) : 0;
                            $bHrs = str_pad(floor($staticBreakSeconds / 3600), 2, '0', STR_PAD_LEFT);
                            $bMins = str_pad(floor(($staticBreakSeconds % 3600) / 60), 2, '0', STR_PAD_LEFT);
                            $bSecs = str_pad($staticBreakSeconds % 60, 2, '0', STR_PAD_LEFT);
                            $formattedBreakTime = "$bHrs:$bMins:$bSecs";
                        ?>
                        
                        <!-- Panel de Tiempos (Resumen pequeño) -->
                        <div class="d-flex justify-content-center gap-4 mb-4">
                            <!-- Tiempo Trabajado (Activo) -->
                            <div class="text-start">
                                <p class="text-primary mb-1 small text-uppercase fw-bold">
                                    <span class="spinner-grow spinner-grow-sm align-middle me-1" style="width: 8px; height: 8px;" role="status"></span>
                                    Trabajando
                                </p>
                                <div class="fw-bolder text-primary" id="worked-time-display" style="font-family: monospace; font-size: 1.25rem;">00:00:00</div>
                            </div>
                            
                            <div class="border-start"></div>
                            
                            <!-- Tiempo en Pausa (Estático) -->
                            <div class="text-start">
                                <p class="text-muted mb-1 small text-uppercase fw-semibold"><i class="ti ti-coffee"></i> Pausa hoy</p>
                                <div class="fw-bold text-muted" style="font-family: monospace; font-size: 1.25rem; opacity: 0.7;"><?= $formattedBreakTime ?></div>
                            </div>
                        </div>

                        <p class="text-muted small mb-4">Tu jornada laboral está activa. Selecciona una opción para continuar.</p>
                        <div class="row mt-4">
                            <!-- Formulario para pausar jornada -->
                            <div class="col-md-6">
                                <form action="<?= base_url('workdays/pause') ?>" method="post" id="pause-form" class="mb-3">
                                    <?= csrf_field() ?>

                                    <!-- Campos ocultos para GPS -->
                                    <input type="hidden" name="latitud" id="latitud-pause">
                                    <input type="hidden" name="longitud" id="longitud-pause">

                                    <button type="submit" class="btn d-block w-100 fw-medium btn-warning" id="pause-workday-btn">
                                        <i class="ti ti-player-pause"></i>
                                        Pausar Jornada
                                    </button>
                                </form>
                            </div>
                            <!-- Formulario para finalizar jornada -->
                            <div class="col-md-6">
                                <form action="<?= base_url('workdays/end') ?>" method="post" id="end-form">
                                    <?= csrf_field() ?>

                                    <!-- Campos ocultos para GPS -->
                                    <input type="hidden" name="latitud" id="latitud-end">
                                    <input type="hidden" name="longitud" id="longitud-end">

                                    <button type="submit" class="btn d-block w-100 fw-medium btn-danger" id="end-workday-btn">
                                        <i class="ti ti-player-stop"></i>
                                        Finalizar Jornada
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let seconds = <?= isset($workdayData) ? floor($workdayData['total_hours'] * 3600) : 0 ?>;
    const display = document.getElementById('worked-time-display');

    function updateTime() {
        let hrs = Math.floor(seconds / 3600);
        let mins = Math.floor((seconds % 3600) / 60);
        let secs = seconds % 60;

        let formatted = 
            String(hrs).padStart(2, '0') + ':' + 
            String(mins).padStart(2, '0') + ':' + 
            String(secs).padStart(2, '0');

        display.textContent = formatted;
        seconds++;
    }

    // Reloj de hora actual
    function updateRealTimeClock() {
        const now = new Date();
        const timeDisplay = document.getElementById('current-time-display');
        const dateDisplay = document.getElementById('current-date-display');
        
        if (timeDisplay) timeDisplay.textContent = now.toLocaleTimeString('es-ES', { hour12: false });
        if (dateDisplay) dateDisplay.textContent = now.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }

    // Call once to format initial time properly
    updateTime();
    updateRealTimeClock();
    
    // Update every second
    setInterval(updateTime, 1000);
    setInterval(updateRealTimeClock, 1000);

    // Función para obtener ubicación GPS
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Asignar coordenadas a ambos formularios
                    let latPause = document.getElementById('latitud-pause');
                    let lonPause = document.getElementById('longitud-pause');
                    let latEnd = document.getElementById('latitud-end');
                    let lonEnd = document.getElementById('longitud-end');
                    
                    if(latPause) latPause.value = position.coords.latitude;
                    if(lonPause) lonPause.value = position.coords.longitude;
                    if(latEnd) latEnd.value = position.coords.latitude;
                    if(lonEnd) lonEnd.value = position.coords.longitude;
                },
                function(error) {
                    console.log('Error obteniendo ubicación:', error);
                }
            );
        }
    }

    // Inicializar GPS
    getLocation();
});
</script>
