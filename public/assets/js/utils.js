/* =================================================================================
   UTILS JS - OtGest
   Funciones de utilidad globalmente disponibles.
   ================================================================================= */

/**
 * Filtra una tabla en base a un input de búsqueda.
 * @param {string} inputId ID del input de búsqueda.
 * @param {string} tableSelector Selector de la tabla.
 */
function initTableSearch(inputId, tableSelector) {
    const searchInput = document.getElementById(inputId);
    const table = document.querySelector(tableSelector);
    if (!searchInput || !table) return;

    const rows = table.querySelectorAll('tbody tr');

    searchInput.addEventListener('input', function() {
        const search = this.value.toLowerCase();
        rows.forEach(row => {
            if (row.cells.length <= 1 && row.cells[0].getAttribute('colspan')) return; // Saltar fila de "no hay datos"
            const rowText = Array.from(row.querySelectorAll('td'))
                .map(td => td.innerText.toLowerCase())
                .join(' ');
            row.style.display = rowText.includes(search) ? '' : 'none';
        });
    });
}

/**
 * Obtiene la ubicación GPS y rellena los campos ocultos.
 * @param {Array} inputIds Array de IDs de los inputs a rellenar.
 */
function fillGeolocationFields(inputIds) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                inputIds.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = position.coords.latitude + ',' + position.coords.longitude;
                    // Algunos formularios separan lat y long, manejamos ambos casos
                    const elLat = document.getElementById('latitud' + (id.includes('-') ? '-' + id.split('-')[1] : ''));
                    const elLon = document.getElementById('longitud' + (id.includes('-') ? '-' + id.split('-')[1] : ''));
                    if (elLat) elLat.value = position.coords.latitude;
                    if (elLon) elLon.value = position.coords.longitude;
                });
            },
            function(error) {
                console.warn('Geolocation error:', error);
            }
        );
    }
}

/**
 * Función global para volver atrás.
 */
function goBack() {
    window.history.back();
}

/**
 * Obtiene el nombre de una ubicación a partir de coordenadas GPS.
 * @param {number} lat Latitud.
 * @param {number} lon Longitud.
 * @returns {Promise<string>} Dirección formateada.
 */
async function reverseGeocode(lat, lon) {
    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`,
            { headers: { 'User-Agent': 'OtGest-Geolocalizacion/1.0' } }
        );
        const data = await response.json();
        return data.display_name || `${lat}, ${lon}`;
    } catch (error) {
        console.error('Reverse geocode error:', error);
        return `${lat}, ${lon}`;
    }
}

