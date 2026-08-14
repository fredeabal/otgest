/**
 * Bootstrap Datepicker - Inicialización global en español
 * Patrón: input visible #expense_date_display + input oculto #expense_date
 */
document.addEventListener("DOMContentLoaded", function () {
    // Locale en español
    $.fn.datepicker.dates['es'] = {
        days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
        daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
        daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sá"],
        months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
        today: "Hoy",
        clear: "Borrar",
        format: "dd/mm/yyyy",
        titleFormat: "MM yyyy",
        weekStart: 1
    };

    var displayInput = document.getElementById('expense_date_display');
    var hiddenInput  = document.getElementById('expense_date');

    if (displayInput && hiddenInput) {
        $(displayInput).datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            language: 'es'
        }).on('changeDate', function (e) {
            if (e.date) {
                var yyyy = e.date.getFullYear();
                var mm   = String(e.date.getMonth() + 1).padStart(2, '0');
                var dd   = String(e.date.getDate()).padStart(2, '0');
                hiddenInput.value = yyyy + '-' + mm + '-' + dd;
            } else {
                hiddenInput.value = '';
            }
        });
    }
});
