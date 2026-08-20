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
                        iconClass = 'ti ti-check';
                    }
                }
                
                // Detectar si el evento es de múltiples días
                let isMultiDay = false;
                if (arg.event.start && arg.event.end) {
                    let diffDays = Math.round((arg.event.end - arg.event.start) / (24 * 60 * 60 * 1000));
                    if (diffDays > 1) {
                        isMultiDay = true;
                    }
                }
                
                let container = document.createElement('div');
                
                if (isMultiDay) {
                    // Renderizado de banda (píldora) para múltiples días
                    container.className = 'fc-custom-event-band d-flex align-items-center px-2 text-white';
                    container.style.backgroundColor = color;
                    container.style.height = '28px';
                    container.style.borderRadius = '14px';
                    container.style.width = '100%';
                    container.style.overflow = 'hidden';
                    container.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
                    container.style.cursor = 'pointer';
                    container.title = arg.event.title;
                    
                    let icon = document.createElement('i');
                    icon.className = iconClass + ' fs-4 me-2 flex-shrink-0';
                    
                    let text = document.createElement('span');
                    text.className = 'fs-2 fw-semibold text-truncate';
                    text.innerText = arg.event.title;
                    
                    container.appendChild(icon);
                    container.appendChild(text);
                } else {
                    // Renderizado circular para un solo día
                    container.className = 'fc-custom-event-badge mx-auto d-flex align-items-center justify-content-center';
                    container.style.backgroundColor = color;
                    container.style.width = '28px';
                    container.style.height = '28px';
                    container.style.borderRadius = '50%';
                    container.style.cursor = 'pointer';
                    container.title = arg.event.title;
                    
                    let icon = document.createElement('i');
                    icon.className = iconClass + ' text-white';
                    icon.style.fontSize = '14px';
                    
                    container.appendChild(icon);
                }
                
                return { domNodes: [container] };
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
