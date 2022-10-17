<?php

namespace DLTools\Controllers;

/**
 * Clase para manejar fechas en formato ISO 8601.
 */
class DLCalendar extends DLRequest {

    public function __construct() {
        parent::__construct();
    }
    /**
     * @param string $year  Año en formato 4 digitos.
     * @param string $month  Mes en formato 2 digitos.
     * @param string $day  Dia en formato 2 digitos.
     * @return string  Fecha en formato YYYY-MM-DD.
     */
    public function get_calendar(int $year, int $month, int $day) {
        header("Content-Type: application/json");

        $calendar = [];

        $calendar['year'] = $year;
        $calendar['month'] = $month;
        $calendar['day'] = $day;

        $calendar['days'] = [];

        $calendar['days']['first'] = date('N', strtotime($year . '-' . $month . '-01'));
        $calendar['days']['last'] = date('t', strtotime($year . '-' . $month . '-01'));

        $calendar['days']['names'] = [];

        for ($i = 1; $i <= 7; $i++) {
            $calendar['days']['names'][$i] = date('D', strtotime('próximo domingo +' . ($i - 1) . ' días'));
        }

        $calendar['days']['numbers'] = [];

        for ($i = 1; $i <= $calendar['days']['last']; $i++) {
            $calendar['days']['numbers'][$i] = $i;
        }

        return json_encode($calendar);
    }
}

$calendar = new DLCalendar();

echo $calendar->get_calendar(2020, 1, 1);