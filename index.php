<?php

include dirname(__FILE__, 1) . "/vendor/autoload.php";

use Data\ProcessCSV;

$csv = new ProcessCSV();
$csv->render();
$it_saved = $csv->push("data");

if ($it_saved) {
    echo "Se almacenaron " . $csv->getRegisterCount() . " registros a la tabla " . $csv->getTable();
}