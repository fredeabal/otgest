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
                        <i class="ti ti-player-pause mb-3 text-info fs-4rem"></i><br>
                        <h4 class="fw-bold text-info mb-3">Tiempo transcurrido: <span id="active-workday-timer" data-elapsed="<?= esc($elapsed_seconds ?? 0) ?>" data-status="pause">--:--</span></h4>
                        <small>Tu jornada está en pausa. Selecciona una opción para continuar.</small>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
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