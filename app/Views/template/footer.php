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
<!-- Select2 -->
<script src="<?= base_url() ?>assets/libs/select2/dist/js/select2.min.js"></script>
<script src="<?= base_url() ?>assets/js/forms/select2.init.js?v=<?= time() ?>"></script>
<!-- Daterangepicker -->
<script src="<?= base_url() ?>assets/js/extra-libs/moment/moment.min.js"></script>
<script src="<?= base_url() ?>assets/libs/daterangepicker/daterangepicker.js"></script>

<!-- Librerías Gráficas -->
<script src="<?= base_url() ?>assets/libs/apexcharts/dist/apexcharts.min.js"></script>
<script src="<?= base_url() ?>assets/libs/fullcalendar/index.global.min.js"></script>

<!-- Scripts de Dashboard -->
<script src="<?= base_url() ?>assets/js/dashboard-admin.js"></script>
<script src="<?= base_url() ?>assets/js/dashboard-user.js"></script>

<!-- Scripts Globales y Módulos -->
<script src="<?= base_url() ?>assets/js/utils.js"></script>
<script src="<?= base_url() ?>assets/js/absences.js"></script>
<script src="<?= base_url() ?>assets/js/workdays.js"></script>

<!-- Bootstrap Datepicker (formularios de creación/edición) -->
<script src="<?= base_url() ?>assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker-custom.js"></script>

<!-- Daterangepicker - filtros de listado y formulario de ausencias -->
<script src="<?= base_url() ?>assets/js/daterange-filter.js"></script>
<script src="<?= base_url() ?>assets/js/daterange-absence.js"></script>

<!-- Script para cambio de tema persistente -->
<script src="<?= base_url('assets/js/theme/theme-switcher.js') ?>"></script>


<!-- =================================================================================
// Alertas del Sistema (SweetAlert2 - FileCrew Style)
// ================================================================================= -->
<script>
    // SweetAlert2 global submit interceptor (for forms)
    document.addEventListener("submit", function(e) {
      let form = e.target;
      if (form.hasAttribute("data-confirm") && !form.dataset.confirmed) {
        e.preventDefault();
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        Swal.fire({
          title: '¿Confirmas esta acción?',
          text: form.getAttribute("data-confirm"),
          icon: 'warning',
          background: isDark ? '#0b1114' : '#f8f9fa',
          color: isDark ? '#ffffff' : '#0b1114',
          iconColor: '#F38020',
          showCancelButton: true,
          reverseButtons: true,
          customClass: {
            confirmButton: 'btn btn-primary ms-2',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false,
          confirmButtonText: 'Sí, confirmar',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            form.dataset.confirmed = "true";
            form.submit();
          }
        });
      }
    });

    // SweetAlert2 global click interceptor (for links/buttons con data-confirm)
    document.addEventListener("click", function(e) {
      let confirmEl = e.target.closest("[data-confirm]");
      if (confirmEl) {
        let form = confirmEl.closest("form");
        
        if (!form || !form.hasAttribute("data-confirm")) {
          e.preventDefault();
          const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
          Swal.fire({
            title: '¿Confirmas esta acción?',
            text: confirmEl.getAttribute("data-confirm"),
            icon: 'warning',
            background: isDark ? '#0b1114' : '#f8f9fa',
            color: isDark ? '#ffffff' : '#0b1114',
            iconColor: '#F38020',
            showCancelButton: true,
            reverseButtons: true,
            customClass: {
              confirmButton: 'btn btn-primary ms-2',
              cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false,
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
              if (form) {
                form.dataset.confirmed = "true";
                form.submit();
              } else if (confirmEl.tagName === 'A') {
                window.location.href = confirmEl.href;
              }
            }
          });
        }
      }
    });

    // Validar globalmente el peso de los archivos a subir
    document.addEventListener("change", function(e) {
      if (e.target && e.target.type === "file") {
        const maxSize = 8 * 1024 * 1024; // 8MB para igualar el límite estándar de PHP
        let totalSize = 0;
        
        for (let i = 0; i < e.target.files.length; i++) {
            totalSize += e.target.files[i].size;
        }

        if (totalSize > maxSize) {
            e.preventDefault();
            e.target.value = ""; // Limpiar el input para prevenir que se envíe
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            Swal.fire({
                title: 'Archivo demasiado pesado',
                text: 'El tamaño de los archivos seleccionados excede el límite máximo de 8 MB. Por favor, selecciona un archivo más pequeño.',
                icon: 'error',
                background: isDark ? '#0b1114' : '#f8f9fa',
                color: isDark ? '#ffffff' : '#0b1114',
                confirmButtonColor: '#b31b34', // Color de error
                confirmButtonText: 'Entendido'
            });
        }
      }
    });

    document.addEventListener("DOMContentLoaded", function() {
      const toastMessage = <?= json_encode(session()->getFlashdata('message') ?? session()->getFlashdata('success')) ?>;
      const toastError = <?= json_encode(session()->getFlashdata('error')) ?>;
      const toastErrors = <?= json_encode(session()->getFlashdata('errors')) ?>;
      
      const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
      
      window.systemAlert = Swal.mixin({
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
        window.systemAlert.fire({ icon: 'success', title: '¡Completado!', html: `<div class="text-center">${toastMessage}</div>`, iconColor: '#10B981' });
      }
      if (toastError) {
        window.systemAlert.fire({ icon: 'error', title: 'Error', html: `<div class="text-center">${toastError}</div>`, iconColor: '#b31b34' });
      }
      if (toastErrors) {
        const errorContent = typeof toastErrors === 'object' && toastErrors !== null
          ? (Array.isArray(toastErrors) ? toastErrors : Object.values(toastErrors)).join('<br>') 
          : toastErrors;
        window.systemAlert.fire({ icon: 'error', title: 'Error de Validación', html: `<div class="text-center">${errorContent}</div>`, iconColor: '#b31b34' });
      }
    });
</script>



</body>

</html>
