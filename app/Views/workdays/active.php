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
                        <iconify-icon icon="solar:play-circle-bold-duotone" class="mb-3 text-primary" style="font-size: 3rem;"></iconify-icon><br>
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
                                        <iconify-icon icon="solar:pause-circle-bold-duotone"></iconify-icon>
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
                                        <iconify-icon icon="solar:stop-circle-bold-duotone"></iconify-icon>
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
    // Función para obtener ubicación GPS
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Asignar coordenadas a ambos formularios
                    document.getElementById('latitud-pause').value = position.coords.latitude;
                    document.getElementById('longitud-pause').value = position.coords.longitude;
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