# Leer archivos de Excel y CSV

Toma los datos de un archivo de Excel o CSV y los convierte a una secuencia SQL que posteriormente se insertarán a la tabla elegida por el desarrollador.

## Instalación

Clone el repositorio:

```bash
git clone git@github.com:dlunamontilla/process-csv.git
```

O con HTTP:

```bash
git clone https://github.com/dlunamontilla/process-csv.git
```

Y después, ingrese al directorio:

```bash
cd process-csv
```

E Instale las dependencias:

```bash
composer install
```

Y por último, escriba la siguiente línea para configurar la conexión con la base de datos:

```bash
composer configure
```

## Probar la herramienta

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

## Uso en la terminal

Para usarlo en la terminal, solo debes escribir la siguiente línea:

```php
composer push
```
