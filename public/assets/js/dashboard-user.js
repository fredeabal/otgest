/* =================================================================================
   DASHBOARD USER JS - OtGest
   Lógica para el calendario de actividad del usuario.
   ================================================================================= */

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    if (calendarEl && typeof FullCalendar !== 'undefined') {
        const eventsUrl = calendarEl.getAttribute('data-events-url');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            events: eventsUrl,
            eventClassNames: function() {
                return ['fc-minimal-event'];
            },
            eventContent: function(arg) {
                let color = arg.event.backgroundColor || arg.event.extendedProps?.color || '#5d87ff';
                let type = arg.event.extendedProps?.type;
                let absType = arg.event.extendedProps?.absence_type;
                
                // Determinar clase del icono de Tabler
                let iconClass = 'ti ti-calendar'; // por defecto
                if (type === 'workday') {
                    iconClass = 'ti ti-check';
                } else if (type === 'absence') {
                    if (absType === 'vacaciones') {
                        iconClass = 'ti ti-umbrella';
                    } else if (['baja', 'accidente', 'enfermedad'].includes(absType)) {
                        iconClass = 'ti ti-activity-heartbeat'; // o ti-medical-cross
                    } else {
                        iconClass = 'ti ti-clock';
                    }
                }
                
                // Contenedor del badge circular
                let badge = document.createElement('div');
                badge.className = 'fc-custom-event-badge mx-auto d-flex align-items-center justify-content-center';
                badge.style.backgroundColor = color;
                badge.style.width = '28px';
                badge.style.height = '28px';
                badge.style.borderRadius = '50%';
                badge.style.cursor = 'pointer';
                badge.title = arg.event.title;
                
                // Icono interno
                let icon = document.createElement('i');
                icon.className = iconClass + ' text-white';
                icon.style.fontSize = '14px';
                
                badge.appendChild(icon);
                
                return { domNodes: [badge] };
            },
            eventClick: function(info) {
                if (info.event.url) {
                    info.jsEvent.preventDefault();
                    window.location.href = info.event.url;
                }
            },
            height: 'auto',
            firstDay: 1, // Lunes
            handleWindowResize: true,
            displayEventTime: false,
            themeSystem: 'bootstrap5'
        });
        
        calendar.render();
    }
});
