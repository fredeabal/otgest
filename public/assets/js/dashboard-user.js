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
                // Renderizar solo un círculo del color del evento
                let color = arg.event.backgroundColor || arg.event.extendedProps?.color || '#5d87ff';
                let dot = document.createElement('div');
                dot.className = 'fc-custom-dot mx-auto';
                dot.style.backgroundColor = color;
                dot.style.width = '12px';
                dot.style.height = '12px';
                dot.style.borderRadius = '50%';
                dot.style.cursor = 'pointer';
                dot.title = arg.event.title;
                return { domNodes: [dot] };
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
