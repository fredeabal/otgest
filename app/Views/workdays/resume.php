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
                        <i class="ti ti-player-pause mb-3 text-primary fs-4rem"></i><br>
                        <div class="h3 mb-3 text-primary fw-bold" id="paused-time-display">00:00:00</div>
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