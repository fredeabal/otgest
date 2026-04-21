/* =================================================================================
   DASHBOARD ADMIN JS - OtGest
   Lógica para el gráfico de rendimiento vs ausencias.
   ================================================================================= */

document.addEventListener('DOMContentLoaded', function() {
    const chartEl = document.querySelector("#dual_performance_chart");
    
    if (chartEl && typeof ApexCharts !== 'undefined') {
        const absencesData = JSON.parse(chartEl.getAttribute('data-absences') || '[]');
        const attendanceData = JSON.parse(chartEl.getAttribute('data-attendance') || '[]');
        const chartLabels = JSON.parse(chartEl.getAttribute('data-labels') || '[]');

        const options = {
            chart: { 
                height: 380, 
                type: 'area', 
                fontFamily: 'Plus Jakarta Sans', 
                foreColor: '#7C8FAC', 
                toolbar: { show: false }, 
                stacked: false, 
                background: 'transparent'
            },
            stroke: { 
                curve: 'smooth', 
                width: [3, 3] 
            },
            fill: { 
                type: 'gradient', 
                gradient: { shadeIntensity: 1, opacityFrom: [0.1, 0.1], opacityTo: [0.01, 0.01], stops: [0, 95, 100] } 
            },
            series: [
                { name: 'Ausencia', type: 'area', data: absencesData },
                { name: 'Asistencia', type: 'area', data: attendanceData }
            ],
            xaxis: { 
                categories: chartLabels, 
                axisBorder: { show: false }, 
                axisTicks: { show: false } 
            },
            markers: { size: [5, 5], strokeWidth: 0, hover: { size: 7 } },
            colors: ['#ff3361', '#5d87ff'],
            grid: { borderColor: 'rgba(255,255,255,0.05)', strokeDashArray: 4 },
            legend: { show: true, position: 'top', horizontalAlign: 'right', labels: { colors: '#7C8FAC' } },
            tooltip: { theme: 'dark' }
        };

        new ApexCharts(chartEl, options).render();
    }
});
