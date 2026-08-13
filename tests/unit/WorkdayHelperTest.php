<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class WorkdayHelperTest extends TestCase
{
    private $helperLoaded = false;

    protected function setUp(): void
    {
        if (!$this->helperLoaded) {
            require_once APPPATH . 'Helpers/workday_helper.php';
            $this->helperLoaded = true;
        }
    }

    public function test_jornada_completa_sin_pausa()
    {
        $events = [
            ['event_type' => 'in', 'event_time' => '2025-01-15 09:00:00', 'autoclose' => false],
            ['event_type' => 'out', 'event_time' => '2025-01-15 18:00:00', 'autoclose' => false],
        ];

        $result = calculate_workday_data('2025-01-15', $events, 8);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(9.0, round($result['total_hours'], 1)); // 9h trabajadas
        $this->assertEquals(1.0, $result['overtime_hours']);        // 1h extra sobre 8h
        $this->assertEquals(0.0, $result['break_time']);
    }

    public function test_jornada_con_pausa()
    {
        $events = [
            ['event_type' => 'in', 'event_time' => '2025-01-15 09:00:00', 'autoclose' => false],
            ['event_type' => 'break_start', 'event_time' => '2025-01-15 13:00:00', 'autoclose' => false],
            ['event_type' => 'break_end', 'event_time' => '2025-01-15 13:30:00', 'autoclose' => false],
            ['event_type' => 'out', 'event_time' => '2025-01-15 18:00:00', 'autoclose' => false],
        ];

        $result = calculate_workday_data('2025-01-15', $events, 8);

        $this->assertEquals('completed', $result['status']);
        $this->assertEquals(8.5, round($result['total_hours'], 1)); // 9h - 0.5h pausa = 8.5h
        $this->assertEquals(0.5, round($result['break_time'], 1));
    }

    public function test_jornada_activa_sin_salida()
    {
        $events = [
            ['event_type' => 'in', 'event_time' => '2025-01-15 09:00:00', 'autoclose' => false],
        ];

        $result = calculate_workday_data('2025-01-15', $events, 8);

        $this->assertEquals('in_progress', $result['status']);
        $this->assertNotNull($result['total_hours']);
    }

    public function test_jornada_invalida_sin_entrada()
    {
        $events = [
            ['event_type' => 'out', 'event_time' => '2025-01-15 18:00:00', 'autoclose' => false],
        ];

        $result = calculate_workday_data('2025-01-15', $events);

        $this->assertNull($result);
    }

    public function test_jornada_vacia()
    {
        $result = calculate_workday_data('2025-01-15', []);

        $this->assertNull($result);
    }
}
