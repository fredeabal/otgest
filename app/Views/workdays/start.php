<!-- =================================================================================
Vista: Iniciar Jornada Laboral
================================================================================= -->

<div class="container">
    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary-subtle text-white">
                    <h5 class="mb-0 text-primary">Control de Jornada Laboral</h5>
                </div>
                <div class="card-body text-center">
                    <div class="form-group">
                        <!-- Reloj de hora actual -->
                        <div class="mb-4">
                            <div class="h1 fw-bolder mb-0" id="current-time-display" style="font-family: monospace; font-size: 3rem;">--:--:--</div>
                            <div class="text-muted small text-uppercase fw-semibold" id="current-date-display">--</div>
                        </div>

                        <i class="ti ti-clock mb-3 text-primary fs-4rem"></i><br>
                        <small>Haz clic en el botón para iniciar tu jornada laboral.</small>
                        <form action="<?= base_url('workdays/start') ?>" method="post" id="workday-form" class="my-4">
                            <?= csrf_field() ?>

                            <!-- Campos ocultos para GPS -->
                            <input type="hidden" name="latitud" id="latitud">
                            <input type="hidden" name="longitud" id="longitud">

                            <button type="submit" class="btn d-block w-100 fw-medium btn-primary" id="start-workday-btn">
                                <i class="ti ti-player-play"></i>
                                Iniciar Jornada
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reloj en tiempo real
    function updateRealTimeClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-ES', { hour12: false });
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const dateString = now.toLocaleDateString('es-ES', dateOptions);
        
        const timeDisplay = document.getElementById('current-time-display');
        const dateDisplay = document.getElementById('current-date-display');
        
        if (timeDisplay) timeDisplay.textContent = timeString;
        if (dateDisplay) dateDisplay.textContent = dateString;
    }
    
    setInterval(updateRealTimeClock, 1000);
    updateRealTimeClock();
});
</script>
