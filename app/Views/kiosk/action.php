<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosco - Acción</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url() ?>assets/images/logos/favicon.png" />
    <!-- CSS Principal de la app para asegurar los mismos colores -->
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/styles.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/custom.css" />
</head>
<body class="kiosk-body">

    <div class="kiosk-user-info">
        <img src="<?= base_url('users/avatar/' . esc($user['avatar'])) ?>" alt="Avatar" class="kiosk-avatar" onerror="this.onerror=null;this.src='<?= base_url('assets/images/profile/user-default.svg') ?>'">
        <h1 class="mb-2">¿Qué deseas hacer, <?= esc($user['name']) ?>?</h1>
        <p class="text-muted fs-5">Actualmente estás trabajando.</p>
    </div>

    <form id="action-form" action="<?= base_url('kiosk/action') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= esc($token) ?>">
        <input type="hidden" name="action" id="action-input" value="">
        <input type="hidden" name="latitud" id="latitude">
        <input type="hidden" name="longitud" id="longitude">
        
        <div class="kiosk-actions">
            <button type="button" class="btn kiosk-btn-action kiosk-btn-pause" onclick="submitAction('pause')">
                <i class="ti ti-player-pause"></i>
                Iniciar Pausa
            </button>
            <button type="button" class="btn kiosk-btn-action kiosk-btn-stop" onclick="submitAction('stop')">
                <i class="ti ti-player-stop"></i>
                Finalizar Jornada
            </button>
        </div>
    </form>

    <div class="kiosk-timeout-bar"></div>

    <script>
        function submitAction(action) {
            document.getElementById('action-input').value = action;
            document.getElementById('action-form').submit();
        }

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

        setTimeout(() => {
            window.location.href = '<?= base_url('kiosk') ?>';
        }, 10000);
    </script>
</body>
</html>
