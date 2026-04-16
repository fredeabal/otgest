<!-- =================================================================================
     Página de error 400 - Vista
     Plantilla moderna con Bootstrap y detección de tema automático
     ================================================================================= -->

<!DOCTYPE html>
<html lang="es" dir="ltr" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- Cabecera y recursos principales -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" type="image/png" href="<?= base_url() ?>assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/styles.css" />
    <title>400 - Solicitud incorrecta</title>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <img src="<?= base_url() ?>assets/images/logos/favicon.png" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <div id="main-wrapper" class="auth-customizer-none">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100 w-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-4 auth-card">
                        <div class="card mb-0">
                            <div class="card-body text-center">

                                <!-- Contenido de error -->
                                <div class="mb-5">
                                    <h1 class="display-1 fw-bold text-primary">400</h1>
                                    <h2 class="fs-4 mb-4">Solicitud Incorrecta</h2>
                                    
                                    <?php if (ENVIRONMENT !== 'production') : ?>
                                        <p class="text-muted mb-4"><?= nl2br(esc($message)) ?></p>
                                    <?php else : ?>
                                        <p class="text-muted mb-4">La solicitud no pudo ser procesada debido a un error en los datos enviados.</p>
                                    <?php endif; ?>
                                    
                                    <a href="<?= base_url() ?>" class="btn btn-primary py-8 px-4 rounded-2">
                                        Volver al Inicio
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts de la plantilla -->
    <script src="<?= base_url() ?>assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>assets/libs/simplebar/dist/simplebar.min.js"></script>
    <script src="<?= base_url() ?>assets/js/theme/app.init.js"></script>
    <script src="<?= base_url() ?>assets/js/theme/theme.js"></script>
    <script src="<?= base_url() ?>assets/js/theme/app.min.js"></script>
    <script src="<?= base_url('assets/js/theme/theme-switcher.js') ?>"></script>
</body>

</html>
