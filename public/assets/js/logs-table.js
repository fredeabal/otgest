/**
 * logs-table.js
 * Lógica de la tabla de logs.
 */

document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('logTableSearch');
    if (!searchInput) return;

    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('.table tbody tr');

        let visibleRows = 0;
        let noRecordsRow = null;

        rows.forEach(row => {
            // Saltar el marcador de "sin registros" si está presente
            if (row.children.length === 1 && row.children[0].hasAttribute('colspan')) {
                noRecordsRow = row;
                row.style.display = 'none'; // Se manejará explícitamente esta fila más abajo
                return;
            }

            const textContent = row.textContent.toLowerCase();
            if (textContent.includes(filter)) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        // Mostrar el mensaje "Sin registros encontrados" dinámicamente
        const tbody = document.querySelector('.table tbody');
        let dynamicNoRecordsRow = tbody.querySelector('.dynamic-no-records');

        if (visibleRows === 0 && rows.length > 0) {
            if (!dynamicNoRecordsRow) {
                dynamicNoRecordsRow = document.createElement('tr');
                dynamicNoRecordsRow.className = 'dynamic-no-records';
                dynamicNoRecordsRow.innerHTML = '<td colspan="5" class="text-center">No hay registros que coincidan con la búsqueda.</td>';
                tbody.appendChild(dynamicNoRecordsRow);
            } else {
                dynamicNoRecordsRow.style.display = '';
            }
        } else if (dynamicNoRecordsRow) {
            dynamicNoRecordsRow.style.display = 'none';
        }
        
        if (noRecordsRow && rows.length === 1) {
            noRecordsRow.style.display = '';
        }
    });
});
