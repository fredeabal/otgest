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

    // Rechazar solicitud (Admin) - Modal con SweetAlert
    document.querySelectorAll('.reject-absence-swal').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const absenceId = this.getAttribute('data-id');
            // Check if the URL is provided directly in data-url, else fallback to data-action-base
            let url = this.getAttribute('data-url');
            if (!url) {
                const actionBase = document.getElementById('rejectForm')?.getAttribute('data-action-base') || window.location.origin + '/absences/reject/';
                url = actionBase + absenceId;
            }

            Swal.fire({
                title: '¿Rechazar solicitud?',
                text: 'La solicitud será rechazada y se notificará al usuario.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, rechazar',
                cancelButtonText: 'Cancelar',
                input: 'textarea',
                inputPlaceholder: 'Escribe el motivo del rechazo...',
                inputAttributes: {
                    'aria-label': 'Motivo del rechazo'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    
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

                    if (result.value) {
                        const reasonInput = document.createElement('input');
                        reasonInput.type = 'hidden';
                        reasonInput.name = 'admin_comments';
                        reasonInput.value = result.value;
                        form.appendChild(reasonInput);
                    }

                    document.body.appendChild(form);
                    form.submit();
                }
            });
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
