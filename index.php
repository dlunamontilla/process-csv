<?php

include dirname(__FILE__, 1) . "/vendor/autoload.php";

use Data\ProcessCSV;

$csv = new ProcessCSV();
$csv->render();
$it_saved = $csv->push("data");

if ($it_saved) {
    echo "\nSe almacenaron: \e[92m" . $csv->getFormatRegisterCount() . "\e[93m registros a la tabla " . $csv->getTable();
    echo "\n\n";
}