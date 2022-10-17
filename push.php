<?php

include dirname(__FILE__, 1) . "/vendor/autoload.php";


/**
 * Lee la entraada del usuario.
 *
 * @return object
 */
function keyboardInput(): object {
    /**
     * @var string|FALSE
     */
    $table = FALSE;
    
    /**
     * @var string|FALSE $filename
     */
    $filename = FALSE;

    while ($table === FALSE || $filename === FALSE) {
        if ($table === FALSE) {
            $table = readline("Ingrese el nombre de la tabla: ");
            continue;
        }

        if ($filename === FALSE) {
            $filename = readline("Ingrese el nombre del archivo a seleccionar: ");
            continue;
        }
    }

    return (object) [
        "table" => $table,
        "filename" => $filename 
    ];
}

$inputs = keyboardInput();

use Data\ProcessCSV;

$csv = new ProcessCSV($inputs->filename);

$csv->render();
$it_saved = $csv->push((string) $inputs->table);

if ($it_saved) {
    echo "\nSe almacenaron: \e[92m" . $csv->getFormatRegisterCount() . "\e[93m registros a la tabla " . $csv->getTable();
    echo "\n\n";
}