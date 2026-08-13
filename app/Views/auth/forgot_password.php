<!-- =================================================================================
     Vista: Recuperar contraseña - Plantilla moderna
     ================================================================================= -->

<!DOCTYPE html>
<html lang="es" dir="ltr" data-bs-theme="dark" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- =================================================================================
    // Script para evitar el parpadeo del tema
    // ================================================================================= -->
    <script>
    (function() {
        var userTheme = '<?= session()->get('user_theme') ?? '' ?>';
        var localTheme = localStorage.getItem('theme');
        var theme = userTheme || localTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        
        document.documentElement.setAttribute('data-bs-theme', theme);
        if (userTheme) {
            document.documentElement.setAttribute('data-user-theme', userTheme);
        }
    })();
    </script>
    <!-- Cabecera y recursos principales -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" type="image/png" href="<?= base_url() ?>assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/styles.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/custom.css?v=<?= time() ?>" />
    <title>Recuperar Contraseña</title>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <img src="<?= base_url() ?>assets/images/logos/favicon.png" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <div id="main-wrapper" class="auth-customizer-none">
        <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 w-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3 auth-card">
                        <div class="card mb-0">
                            <div class="card-body">
                                <!-- Logo -->
                                <div class="text-center mb-5">
                                    <a href="<?= base_url() ?>" class="text-nowrap logo-img d-inline-block">
                                        <img src="<?= base_url() ?>assets/images/logos/dark-logo.svg" class="logo-dark"
                                            alt="Logo-Dark" />
                                        <img src="<?= base_url() ?>assets/images/logos/light-logo.svg" class="logo-light"
                                            alt="Logo-light" />
                                    </a>
                                </div>
                                <!-- Mensajes de error y éxito -->
                                <?php if (session('errors')): ?>
                                <div class="alert alert-danger">
                                    <?php foreach(session('errors') as $error): ?>
                                    <div><?= esc($error) ?></div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                <?php if (session('success')): ?>
                                <div class="alert alert-success text-success"> <?= esc(session('success')) ?> </div>
                                <?php endif; ?>
                                <!-- Formulario de recuperación de contraseña -->
                                <form method="post" action="<?= site_url('forgot-password') ?>">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Correo electrónico</label>
                                        <input type="text" name="email" id="email" class="form-control"
                                            value="<?= old('email') ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">Enviar
                                        enlace</button>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <a class="text-primary fw-medium" href="<?= site_url('login') ?>">Volver al
                                            login</a>
                                    </div>
                                </form>
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