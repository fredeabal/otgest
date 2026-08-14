/**
 * Daterangepicker - Inicialización para formularios de solicitud de ausencia
 * Patrón: input visible #daterange + inputs ocultos #start_date, #end_date, #start_time, #end_time
 * Incluye timepicker con segundos para seleccionar rango de fechas y horas exactas.
 */
document.addEventListener("DOMContentLoaded", function () {
    var startDateInput = document.getElementById('start_date');
    var endDateInput   = document.getElementById('end_date');
    var startTimeInput = document.getElementById('start_time');
    var endTimeInput   = document.getElementById('end_time');
    var daterangeInput = document.getElementById('daterange');

    if (!startDateInput || !endDateInput || !daterangeInput) return;

    var startStr = startDateInput.value
        ? startDateInput.value + (startTimeInput && startTimeInput.value ? ' ' + startTimeInput.value : '')
        : null;
    var endStr = endDateInput.value
        ? endDateInput.value + (endTimeInput && endTimeInput.value ? ' ' + endTimeInput.value : '')
        : null;

    var start = startStr ? moment(startStr) : moment().startOf('hour');
    var end   = endStr   ? moment(endStr)   : moment().startOf('hour').add(1, 'hour');

    $(daterangeInput).daterangepicker({
        startDate: start,
        endDate: end,
        showDropdowns: true,
        timePicker: true,
        timePicker24Hour: true,
        timePickerSeconds: true,
        timePickerIncrement: 5,
        opens: 'center',
        drops: 'down',
        locale: {
            format: 'DD/MM/YYYY HH:mm:ss',
            separator: ' - ',
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar',
            fromLabel: 'Desde',
            toLabel: 'Hasta',
            customRangeLabel: 'Personalizado',
            weekLabel: 'S',
            daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            firstDay: 1
        }
    }, function (start, end) {
        startDateInput.value = start.format('YYYY-MM-DD');
        endDateInput.value   = end.format('YYYY-MM-DD');
        if (startTimeInput) startTimeInput.value = start.format('HH:mm:ss');
        if (endTimeInput)   endTimeInput.value   = end.format('HH:mm:ss');
    });

    // Inicializar inputs ocultos si están vacíos al cargar
    if (!startDateInput.value) {
        startDateInput.value = start.format('YYYY-MM-DD');
        if (startTimeInput) startTimeInput.value = start.format('HH:mm:ss');
    }
    if (!endDateInput.value) {
        endDateInput.value = end.format('YYYY-MM-DD');
        if (endTimeInput) endTimeInput.value = end.format('HH:mm:ss');
    }
});
