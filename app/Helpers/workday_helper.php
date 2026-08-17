<?php

if (!function_exists('calculate_workday_data')) {
    /**
     * Calcula los datos de la jornada a partir de sus eventos
     *
     * @param string $date Fecha de la jornada
     * @param array $events Eventos de la jornada
     * @param float|null $userDailyHours Horas diarias del usuario (opcional)
     * @return array|null
     */
    function calculate_workday_data($date, $events, $userDailyHours = null)
    {
        // Verificar que hay eventos para procesar
        if (empty($events)) {
            return null;
        }

        // Inicializar variables de cálculo
        $startTime = null;      // Hora de entrada
        $endTime = null;        // Hora de salida
        $totalBreakTime = 0;    // Tiempo total de pausas en segundos
        $breakStart = null;     // Inicio de pausa actual
        $autoclose = false;     // Indica si fue cerrada automáticamente

        // Procesar eventos cronológicamente
        foreach ($events as $event) {
            switch ($event['event_type']) {
                case 'start':
                    // Evento de entrada - registrar hora de inicio
                    $startTime = $event['event_time'];
                    break;

                case 'stop':
                    // Evento de salida - registrar hora de fin
                    $endTime = $event['event_time'];
                    // Verificar si fue cierre automático
                    if ($event['autoclose']) {
                        $autoclose = true;
                    }
                    break;

                case 'pause':
                    // Inicio de pausa - guardar hora de inicio
                    $breakStart = $event['event_time'];
                    break;

                case 'resume':
                    // Fin de pausa - calcular duración y sumar al total
                    if ($breakStart) {
                        $breakDuration = strtotime($event['event_time']) - strtotime($breakStart);
                        $totalBreakTime += $breakDuration;
                        $breakStart = null;  // Resetear para próxima pausa
                    }
                    break;
            }
        }

        // Si hay una pausa activa sin break_end (jornada aún en pausa), contabilizar ese tiempo también
        if ($breakStart) {
            $endTimeForBreak = $endTime ?? date('Y-m-d H:i:s');
            $totalBreakTime += strtotime($endTimeForBreak) - strtotime($breakStart);
        }

        // Determinar estado de la jornada
        if ($startTime && $endTime) {
            $status = 'completed';  // Jornada completa (entrada y salida)
        } elseif ($startTime) {
            $status = 'in_progress'; // Jornada en progreso (solo entrada)
        } else {
            return null;  // Jornada inválida (sin entrada)
        }

        // Calcular horas trabajadas
        $totalHours = 0;
        if ($startTime) {
            // Si no hay salida, usar hora actual para cálculo en tiempo real
            $endTimeForCalculation = $endTime ?? date('Y-m-d H:i:s');

            // Calcular tiempo total en segundos
            $totalSeconds = strtotime($endTimeForCalculation) - strtotime($startTime);

            // Restar tiempo de pausas
            $totalSeconds -= $totalBreakTime;

            // Convertir a horas decimales
            $totalHours = max(0, $totalSeconds / 3600);
        }

        // Calcular horas extras
        $overtimeHours = 0;
        if ($userDailyHours && $totalHours > $userDailyHours) {
            $overtimeHours = $totalHours - $userDailyHours;
        }

        // Retornar datos calculados de la jornada
        return [
            'date' => $date,
            'start_time' => $startTime ? date('H:i', strtotime($startTime)) : null,
            'start_date' => $startTime ? date('d/m/Y', strtotime($startTime)) : null,
            'end_time' => $endTime ? date('H:i', strtotime($endTime)) : null,
            'end_date' => $endTime ? date('d/m/Y', strtotime($endTime)) : null,
            'total_hours' => $totalHours,
            'overtime_hours' => $overtimeHours,
            'status' => $status,
            'autoclose' => $autoclose,
            'break_time' => $totalBreakTime / 3600  // Tiempo de pausas en horas
        ];
    }
}
