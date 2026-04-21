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
                left: 'prev,next',
                center: 'title',
                right: ''
            },
            events: eventsUrl,
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
            eventDisplay: 'block',
            themeSystem: 'bootstrap5'
        });
        
        calendar.render();
    }
});
