/* =================================================================================
   ABSENCES JS - OtGest
   Lógica para la gestión de solicitudes de ausencia.
   ================================================================================= */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar búsqueda si existe
    if (document.getElementById('absenceTableSearch')) {
        initTableSearch('absenceTableSearch', 'table');
    }

    // Cancelar solicitud (Usuario)
    document.querySelectorAll('.cancel-absence-swal').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            
            Swal.fire({
                title: '¿Cancelar solicitud?',
                text: 'La solicitud será cancelada y no podrá ser recuperada.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No, mantener'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitViaPost(url);
                }
            });
        });
    });

    // Aprobar solicitud (Admin)
    document.querySelectorAll('.approve-absence-swal').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            Swal.fire({
                title: '¿Aprobar solicitud?',
                text: 'La solicitud será aprobada y el usuario será notificado.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitViaPost(url);
                }
            });
        });
    });

    // Rechazar solicitud (Admin) - Abre el modal
    document.querySelectorAll('.reject-absence-swal').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const absenceId = this.getAttribute('data-id');
            const form = document.getElementById('rejectForm');
            if (form) {
                // Obtenemos la base URL desde un atributo en el body o similar, o la asumimos
                const baseUrl = window.location.origin;
                // Mejor usar un data-attribute para la acción base
                const actionBase = form.getAttribute('data-action-base');
                form.action = actionBase + absenceId;
                const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
                modal.show();
            }
        });
    });
});

/**
 * Utilidad para enviar un POST rápido con CSRF (requiere que exista el hash en el DOM)
 */
function submitViaPost(url) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;

    // Buscar el token CSRF en el sitio (usualmente en meta o en formularios existentes)
    const csrfNameMeta = document.querySelector('meta[name="csrf-token-name"]');
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    
    const csrfName = csrfNameMeta ? csrfNameMeta.content : 'csrf_test_name';
    const csrfHash = csrfTokenMeta ? csrfTokenMeta.content : (document.querySelector('input[name="csrf_test_name"]')?.value || '');

    if (csrfHash) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = csrfName;
        csrfInput.value = csrfHash;
        form.appendChild(csrfInput);
    }

    document.body.appendChild(form);
    form.submit();
}
