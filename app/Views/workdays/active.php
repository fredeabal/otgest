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
