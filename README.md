# Leer archivos de Excel y CSV

Toma los datos de un archivo de Excel o CSV y los convierte a una secuencia SQL que posteriormente se insertarán a la tabla elegida por el desarrollador.

## Probar la herramienta

Puede probar la escribiendo la siguiente línea:

```php
include dirname(__FILE__, 1) . "/vendor/autoload.php";

use Data\ProcessCSV;

$csv = new ProcessCSV();
$csv->render();
$it_saved = $csv->push("data");

if ($it_saved) {
    echo "\nSe almacenaron: \e[92m" . $csv->getFormatRegisterCount() . "\e[93m registros a la tabla " . $csv->getTable();
    echo "\n\n";
}
```

Primero debes crear una tabla donde almacenar la información. Si el archivo CSV tiene 4 columnas, asegúrese que su tabla tenga al menos esas cuatro (04) columnas.

Primero debemos incluir el archivo `autoload.php` para utilizar nombre de espacios:

```php
include dirname(__FILE__, 1) . "/vendor/autoload.php";
```

Y Luego siga los pasos a continuación:

1. Utilice el nombre de espacio que apunte a la clase que necesite usar:

    ```php
    use Data\ProcessCSV;
    ```

2. Instáncielo:

    ```php
    $csv = new ProcessCSV('archivo.csv');
    ```

3. Renderícelo:

```php
$csv->render();
```

4. Y por últilo, envíolo a la base de datos:

```php
$csv->push();
```
