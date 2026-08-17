<!-- =================================================================================
Vista: Reanudar Jornada Laboral
================================================================================= -->

<div class="container">
    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary-subtle">
                    <h5 class="mb-0 text-primary">Jornada en pausa</h5>
                </div>
                <div class="card-body text-center">
                    <div class="form-group">
                        <!-- Reloj de hora actual -->
                        <div class="mb-4 mt-2">
                            <div class="h1 fw-bolder mb-0" id="current-time-display" style="font-family: monospace; font-size: 3.5rem;">--:--:--</div>
                            <div class="text-muted small text-uppercase fw-semibold mt-1" id="current-date-display">--</div>
                        </div>

                        <?php 
                            $staticWorkSeconds = isset($workdayData) ? floor($workdayData['total_hours'] * 3600) : 0;
                            $wHrs = str_pad(floor($staticWorkSeconds / 3600), 2, '0', STR_PAD_LEFT);
                            $wMins = str_pad(floor(($staticWorkSeconds % 3600) / 60), 2, '0', STR_PAD_LEFT);
                            $wSecs = str_pad($staticWorkSeconds % 60, 2, '0', STR_PAD_LEFT);
                            $formattedWorkedTime = "$wHrs:$wMins:$wSecs";
                        ?>
                        
                        <!-- Panel de Tiempos (Resumen pequeño) -->
                        <div class="d-flex justify-content-center gap-4 mb-4">
                            <!-- Tiempo Trabajado (Estático) -->
                            <div class="text-start">
                                <p class="text-muted mb-1 small text-uppercase fw-semibold"><i class="ti ti-briefcase"></i> Trabajado</p>
                                <div class="fw-bold text-muted" style="font-family: monospace; font-size: 1.25rem; opacity: 0.7;"><?= $formattedWorkedTime ?></div>
                            </div>

                            <div class="border-start"></div>

                            <!-- Tiempo en Pausa (Activo) -->
                            <div class="text-start">
                                <p class="text-warning mb-1 small text-uppercase fw-bold">
                                    <span class="spinner-grow spinner-grow-sm align-middle me-1 text-warning" style="width: 8px; height: 8px;" role="status"></span>
                                    En Pausa
                                </p>
                                <div class="fw-bolder text-warning" id="paused-time-display" style="font-family: monospace; font-size: 1.25rem;">00:00:00</div>
                            </div>
                        </div>

                        <p class="text-muted small mb-4">Tu jornada está en pausa. Selecciona una opción para continuar.</p>
                        <div class="row mt-4">
                            <!-- Formulario para reanudar jornada -->
                            <div class="col-md-6">
                                <form action="<?= base_url('workdays/resume') ?>" method="post" id="resume-form" class="mb-4">
                                    <?= csrf_field() ?>

                                    <!-- Campos ocultos para GPS -->
                                    <input type="hidden" name="latitud" id="latitud-resume">
                                    <input type="hidden" name="longitud" id="longitud-resume">

                                    <button type="submit" class="btn d-block w-100 fw-medium btn-primary" id="resume-workday-btn">
                                        <i class="ti ti-player-play"></i>
                                        Reanudar Jornada
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
    let seconds = <?= isset($workdayData) ? floor($workdayData['break_time'] * 3600) : 0 ?>;
    const display = document.getElementById('paused-time-display');

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
                    document.getElementById('latitud-resume').value = position.coords.latitude;
                    document.getElementById('longitud-resume').value = position.coords.longitude;
                    document.getElementById('latitud-end').value = position.coords.latitude;
                    document.getElementById('longitud-end').value = position.coords.longitude;
                },
                function(error) {
                    console.log('Error obteniendo ubicación:', error);
                    // Continuar sin GPS si hay error
                }
            );
        }
    }

    // Inicializar GPS
    getLocation();
});
</script>