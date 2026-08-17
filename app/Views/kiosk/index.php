<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosco - Fichaje</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url() ?>assets/images/logos/favicon.png" />
    <!-- CSS Principal de la app para asegurar los mismos colores -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/styles.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/libs/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/custom.css" />
</head>
<body class="kiosk-body kiosk-index">

    <div class="kiosk-clock" id="clock">00:00:00</div>
    <div class="kiosk-date" id="date">--</div>

    <div class="kiosk-instruction">
        <i class="ti ti-scan text-primary me-2"></i> Pasa tu tarjeta por el lector
    </div>

    <form id="scan-form" action="<?= base_url('kiosk/scan') ?>" method="post">
        <?= csrf_field() ?>
        <input type="text" name="token" id="token_input" class="kiosk-token-input" autofocus autocomplete="off">
        <input type="hidden" name="latitud" id="latitude">
        <input type="hidden" name="longitud" id="longitude">
    </form>

    <script src="<?= base_url() ?>assets/libs/sweetalert2/dist/sweetalert2.min.js"></script>
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('date').textContent = now.toLocaleDateString('es-ES', options);
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Captura de ubicación GPS para el Kiosco
        if ("geolocation" in navigator) {
            navigator.geolocation.watchPosition(
                function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                },
                function(error) {
                    console.warn("No se pudo obtener la ubicación GPS del kiosco: " + error.message);
                },
                { enableHighAccuracy: true, maximumAge: 60000, timeout: 10000 }
            );
        }

        const tokenInput = document.getElementById('token_input');
        tokenInput.focus();
        document.addEventListener('click', () => tokenInput.focus());

        // Focus loss check
        setInterval(() => {
            if (document.activeElement !== tokenInput) {
                tokenInput.focus();
            }
        }, 1000);

        const systemAlert = Swal.mixin({
            position: 'center',
            showConfirmButton: false,
            buttonsStyling: false,
            timer: 5000,
            timerProgressBar: true,
            background: 'var(--bs-body-bg)',
            color: 'var(--bs-body-color)',
            showCloseButton: false,
            heightAuto: false,
            customClass: {
                popup: 'rounded-4 shadow-lg p-3'
            }
        });

        <?php if (session()->getFlashdata('message') || session()->getFlashdata('success')): ?>
            systemAlert.fire({
                icon: 'success',
                title: '¡Fichaje registrado!',
                html: `<div class="text-center fs-4 mt-2"><?= esc(session()->getFlashdata('message') ?? session()->getFlashdata('success')) ?></div>`,
                iconColor: 'var(--bs-success)'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            systemAlert.fire({
                icon: 'error',
                title: 'Error',
                html: `<div class="text-center fs-4 mt-2"><?= esc(session()->getFlashdata('error')) ?></div>`,
                iconColor: 'var(--bs-danger)'
            });
        <?php endif; ?>
    </script>
</body>
</html>
