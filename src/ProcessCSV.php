<?php

namespace Data;

use DLTools\Controllers\DLConfig;
use Shuchkin\SimpleXLSX;


class ProcessCSV extends DLConfig {
    /**
     * Archivo CSV que se desea procesar.
     *
     * @var string
     */
    private string $filename;

    /**
     * Cadena de texto en formato SQL
     *
     * @var string
     */
    private string $dataSQL;

    /**
     * Seleccione el archivo CSV a renderizar
     *
     * @param string $filename
     */

    /**
      * Columnas separada por comas.
      *
      * @var string
      */
    private string $columns;

    /**
     * Indica la cantiddad de registros almacenados en la base de datos.
     *
     * @var integer
     */
    private int $registerCount = 0;

    /**
     * Objecto PDO
     *
     * @var \PDO
     */
    private \PDO $pdo;

    /**
     * Tabla seleccionada por el usuario.
     *
     * @var string
     */
    private string $table = "table";

    /**
     * Undocumented variable
     *
     * @var SimpleXLSX|FALSE
     */
    private SimpleXLSX $dataExcel;

    private string $params;
    public function __construct(string $filename = "Data.csv") {
        $this->filename = dirname(__FILE__, 2) . "/$filename";
        parent::__construct();
        
        $this->pdo = $this->getPDO();
    }

    /**
     * Renderiza un archivo CSV a formato SQL
     * 
     * @param string $operator Separador de columna.
     * @param string $limit Establece el límite de registro a insertar en la 
     * tabla. El valor por defecto es `$limit = 0`. Cuando vale cero (0), entonces, registra todo
     * el contenido de la hoja de cálculo.
     * 
     * > **Importante:** el límite solo tiene efecto para archivos de excel y no en formato CSV.
     */
    public function render(string $separator = ",", int $limit = 0): void {
        $data = [];

        if (!file_exists($this->filename)) {
            echo "El archivo '$this->filename' no se encuentra. Revise que haya escrito correctamente la ruta del archivo e intente nuevamente.";
            echo "\n\n";
            exit;
        }

        $type = $this->getType(
            $this->getExtension($this->filename)
        );
        

        if ($type === "csv") {
            $this->parseCSV($separator);
        }

        if ($type === "excel") {
            $this->parseExcel($limit);
        }
    }

    /**
     * Parsea un archivo de Excel para prepararlo para su inserción en una base de datos.
     *
     * @param integer $limit Establecer la cantidad de registro a insertar.
     * @return void
     */
    private function parseExcel(int $limit = 0): void {
        $excel = SimpleXLSX::parse($this->filename);

        $this->dataExcel = $excel !== FALSE ? $excel : null;
        
        $columns = [];
        $dataSQL = [];
        $data = "";

        foreach($this->dataExcel->rows(0, $limit) as $key => $register) {
            $columns[] = $register;
            $values = $this->createSQL($register);

            if ($key > 0 && !empty(trim($values))) {
                $dataSQL[] = $values;
            }
        }

        $this->dataSQL = "VALUES " . join(", ", $dataSQL);
        $header = array_shift($columns);

        $this->columns = "(" . $this->createColumn($header) . ")";
        $this->data = (object) $columns;

        $fields = [];
        foreach($header as $column) {
            array_push($fields, ":$column");
        }

        $this->params = join(", ", $fields);
    }

    /**
     * Parsear archivos CSV
     * 
     * @param string $separator Separador a utilizar para leer archivos CSV
     * @return void
     */
    private function parseCSV(string $separator = ", "): void {
        $dataString = file_get_contents($this->filename);
        $lines = preg_split("/\n/", $dataString);

        foreach($lines as $key => $line) {
            $line = trim($line);
            if (empty(trim($line))) continue;

            $columns = preg_split("/\\{$separator}/", $line);
            $data[] = $columns;

            if ($key > 0) $dataSQL[] = $this->createSQL($columns);
        }

        $header = array_shift($data);
        
        $fields = [];
        foreach($header as $column) {
            array_push($fields, ":$column");
        }
        
        $params = join(", ", $fields);

        $this->dataSQL = "VALUES " . join(", ", $dataSQL);
        $this->data = (object) $data;
        $this->params = $params;

        $this->columns = "(" . $this->createColumn($header). ")";
    }

    /**
     * Devuelve el archivo CSV en formato de objeto
     *
     * @return object
     */
    public function getData(): object {
        return $this->data;
    }

    /**
     * Devuelve una cadena en formato SQL
     *
     * @return string
     */
    public function getSQL(): string {
        return trim($this->dataSQL);
    }

    /**
     * Devuelve en una cadena de texto las columnas separa por comas.
     *
     * @return string
     */
    public function getColumns(): string {
        return trim($this->columns);
    }

    /**
     * Devuelve en una cadena de texto los parámetros de una sentencia preparada.
     *
     * @return string
     */
    public function getParams(): string {
        return trim($this->params);
    }

    /**
     * Crea una cadena SQL
     *
     * @param array $fields
     * @return string
     */
    private function createSQL(array $fields): string {
        $columns = [];

        foreach($fields as $field) {
            if (empty(trim($field))) {
                return "";
            }

            $columns[] = !is_numeric($field) ? "'$field'" : (float) $field;
        }


        return "(" . join(", ", $columns) . ")";
    }
    
    /**
     * Envía el contenido de un archivo CSV a la base de datos.
     *
     * @param string $table
     * @return boolean
     */
    public function push(string $table = "data"): bool {
        $this->table = trim($table);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS cantidad FROM $table");
        $stmt->execute();

        $registerCount = (object) $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->registerCount = $registerCount->cantidad ?? 0;

        $query = "INSERT INTO $table {$this->columns} {$this->dataSQL}";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute();
    }

    private function createColumn(array $fields): string {
        $columns = [];

        foreach($fields as $field) {
            if (!is_string($field)) continue;
            $columns[] = trim($field);
        }

        return join(", ", $columns);
    }

    /**
     * Devuelve la cantidad de registros que se almacenaron en la base de datos
     *
     * @return integer
     */
    public function getRegisterCount(): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS cantidad FROM $this->table");
        $stmt->execute();
        $data = (object) $stmt->fetch(\PDO::FETCH_ASSOC);

        $previouCount = $this->registerCount;
        $registered = abs($previouCount - $data->cantidad ?? 0);
        return $registered ?? 0;
    }

    /**
     * Devuelve la tabla seleccionada por el usuario.
     *
     * @return string
     */
    public function getTable(): string {
        return trim($this->table);
    }

    public function getFormatRegisterCount(): string {
        return number_format($this->getRegisterCount(), 0, ",", ".");
    }

    private function getExtension(string $filename): string {
        
        /**
         * Partes del nombre de archivo.
         * 
         * @var array $parts
         */
        $parts = preg_split("/\./", $filename);

        /**
         * Extensión del archivo
         * 
         * @var string $extension
         */
        $extension = $parts[count($parts) - 1];

        return trim($extension);
    }

    private function getType(string $extension): string {

        $types = [
            "xls" => "excel",
            "xlsx" => "excel",
            "csv" => "csv"
        ];

        return $types[$extension] ?? 'Desconocido';
    }
}