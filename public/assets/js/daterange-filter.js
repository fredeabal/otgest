/**
 * Daterangepicker - Inicialización para filtros de listado
 * Patrón: input visible #daterange + inputs ocultos #date_from y #date_to
 * Usado en: documents/list, documents/sent, absences/list, absences/manage,
 *           expenses/my-expenses, expenses/manage, workdays/manage
 */
document.addEventListener("DOMContentLoaded", function () {
    var dateFromInput  = document.getElementById('date_from');
    var dateToInput    = document.getElementById('date_to');
    var daterangeInput = document.getElementById('daterange');

    if (!dateFromInput || !dateToInput || !daterangeInput) return;

    var start = dateFromInput.value ? moment(dateFromInput.value, 'YYYY-MM-DD') : null;
    var end   = dateToInput.value   ? moment(dateToInput.value,   'YYYY-MM-DD') : null;

    $(daterangeInput).daterangepicker({
        startDate: start || moment().subtract(29, 'days'),
        endDate:   end   || moment(),
        showDropdowns: true,
        autoUpdateInput: false,
        opens: 'left',
        drops: 'down',
        locale: {
            format: 'DD/MM/YYYY',
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
    });

    $(daterangeInput).on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        dateFromInput.value = picker.startDate.format('YYYY-MM-DD');
        dateToInput.value   = picker.endDate.format('YYYY-MM-DD');
    });

    $(daterangeInput).on('cancel.daterangepicker', function () {
        $(this).val('');
        dateFromInput.value = '';
        dateToInput.value   = '';
    });

    // Prerellenar el input visible si ya vienen fechas en la URL
    if (start && end) {
        daterangeInput.value = start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY');
    }
});
