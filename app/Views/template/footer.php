<!-- footer -->
<div class="dark-transparent sidebartoggler"></div>
<script src="<?= base_url() ?>assets/js/vendor.min.js"></script>
<!-- Import Js Files -->
<script src="<?= base_url() ?>assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url() ?>assets/libs/simplebar/dist/simplebar.min.js"></script>
<!-- theme -->
<script src="<?= base_url() ?>assets/js/theme/app.dark.init.js"></script>
<script src="<?= base_url() ?>assets/js/theme/theme.js"></script>
<script src="<?= base_url() ?>assets/js/theme/app.min.js"></script>
<script src="<?= base_url() ?>assets/js/theme/sidebarmenu.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>assets/libs/sweetalert2/dist/sweetalert2.min.js"></script>

<!-- Script para cambio de tema persistente -->
<script src="<?= base_url('assets/js/theme/theme-switcher.js') ?>"></script>


<!-- Script para ocultar los alerts después de x segundos -->
<script>
// Oculta automáticamente los alerts después de x segundos
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.querySelectorAll('.alert-success, .alert-danger').forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        });
    }, 15000);
});
</script>


<!-- =================================================================================
// Alertas del Sistema
// ================================================================================= -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <?php if (session('success')): ?>
    <div class="alert alert-success text-success fade show mb-2 text-center" role="alert" style="width: fit-content; margin-left: auto;">
        <?= esc(session('success')) ?>
    </div>
    <?php endif; ?>
    <?php if (session('errors')): ?>
    <div class="alert alert-danger text-danger fade show mb-2" role="alert" style="width: fit-content; margin-left: auto;">
        <?php
            $errors = session('errors');
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    echo esc($error) . '<br>';
                }
            } else {
                echo esc($errors);
            }
            ?>
    </div>
    <?php endif; ?>
</div>
<!-- =================================================================================
// Fin de alertas globales
// ================================================================================= -->

<!-- =================================================================================
// Funciones globales
// ================================================================================= -->
<script>
    function goBack() {
        window.history.back();
    }
</script>

</body>

</html>
