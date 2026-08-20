<!-- =================================================================================
     Login de usuario - Vista
     Plantilla moderna con Bootstrap y detección de tema automático
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
    <link rel="stylesheet" href="<?= base_url() ?>assets/libs/sweetalert2/dist/sweetalert2.min.css">
    <title>Iniciar Sesión</title>
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
                                        <img src="<?= base_url() ?>assets/images/logos/dark-logo-mini.svg" class="logo-dark"
                                            alt="Logo-Dark" />
                                        <img src="<?= base_url() ?>assets/images/logos/light-logo-mini.svg" class="logo-light"
                                            alt="Logo-light" />
                                    </a>
                                </div>

                                <!-- Formulario de login -->
                                <form action="<?= site_url('login') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="<?= old('email') ?>">
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">Iniciar
                                        Sesión</button>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <a class="text-primary fw-medium"
                                            href="<?= site_url('forgot-password') ?>">¿Olvidaste tu contraseña?</a>
                                    </div>
                                </form>
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
        <script src="<?= base_url() ?>assets/libs/sweetalert2/dist/sweetalert2.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toastError = <?= json_encode(session()->getFlashdata('error')) ?>;
            const toastErrors = <?= json_encode(session()->getFlashdata('errors')) ?>;
            const toastMessage = <?= json_encode(session()->getFlashdata('success')) ?>;
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            
            const systemAlert = Swal.mixin({
                position: 'center',
                showConfirmButton: false,
                buttonsStyling: false,
                timer: 5000,
                timerProgressBar: true,
                background: isDark ? '#0b1114' : '#f8f9fa',
                color: isDark ? '#fff' : '#0b1114',
                showCloseButton: false
            });
            
            if (toastMessage) {
                systemAlert.fire({ icon: 'success', title: '¡Completado!', html: `<div class="text-center">${toastMessage}</div>`, iconColor: '#10B981' });
            }
            if (toastError) {
                systemAlert.fire({ icon: 'error', title: 'Error', html: `<div class="text-center">${toastError}</div>`, iconColor: '#b31b34' });
            }
            if (toastErrors) {
                const errorContent = typeof toastErrors === 'object' && toastErrors !== null
                    ? (Array.isArray(toastErrors) ? toastErrors : Object.values(toastErrors)).join('<br>') 
                    : toastErrors;
                systemAlert.fire({ icon: 'error', title: 'Error de Validación', html: `<div class="text-center">${errorContent}</div>`, iconColor: '#b31b34' });
            }
        });
        </script>
</body>

</html>