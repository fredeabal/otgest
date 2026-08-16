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
                        <i class="ti ti-player-play mb-3 text-primary fs-4rem"></i><br>
                        <div class="h3 mb-3 text-primary fw-bold" id="worked-time-display">00:00:00</div>
                        <small>Tu jornada laboral está activa. Selecciona una opción para continuar.</small>
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

    // Call once to format initial time properly
    updateTime();
    
    // Update every second
    setInterval(updateTime, 1000);

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
