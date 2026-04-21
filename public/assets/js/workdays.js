/* =================================================================================
   WORKDAYS JS - OtGest
   Lógica para el fichaje y geolocalización.
   ================================================================================= */

document.addEventListener('DOMContentLoaded', function() {
    // Si estamos en una vista de fichaje, inicializar GPS
    if (document.getElementById('latitud') || document.getElementById('latitud-start') || document.getElementById('latitud-pause')) {
        const fields = ['latitud', 'longitud', 'latitud-pause', 'longitud-pause', 'latitud-end', 'longitud-end'];
        fillGeolocationFields(fields);
    }

    // Inicializar búsqueda en gestión si existe
    if (document.getElementById('workdayTableSearch')) {
        initTableSearch('workdayTableSearch', 'table');
    }

    // Procesar geocodificación inversa para elementos que lo requieran
    document.querySelectorAll('.reverse-geocode').forEach(async el => {
        const lat = el.getAttribute('data-lat');
        const lon = el.getAttribute('data-lon');
        if (lat && lon) {
            const address = await reverseGeocode(lat, lon);
            el.innerHTML = address + ' <iconify-icon icon="solar:link-bold-duotone" class="ms-1 small"></iconify-icon>';
        }
    });
});

