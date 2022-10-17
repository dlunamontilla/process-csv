<?php

namespace DLTools\Models;

use DLTools\Controllers\DLConfig;
use phpDocumentor\Reflection\PseudoTypes\LowercaseString;

/**
 * Pendiente por continuar y probar.
 */

/**
 * @version 1.0.0b (2022-06-01) - Initial release
 * @author David E Luna M <davidlunamonilla@gmail.com>
 * @copyright (c) 2020 David E Luna M <davidlunamontilla@gmail.com>
 * @license MIT
 * @package DLTools
 */

class Database extends DLConfig {
    private \PDO $db;
    private \PDOStatement|false $stmt;

    /**
     * @var string $fields - Fields to be selected
     */
    private string $fields = "*";

    /**
     * @var string $query - Query to execute.
     */
    private string $query = "";

    private string $table = "";

    /**
     * @var string $insert - Query for insert data.
     */
    private string $insert = "";

    /**
     * @var array $params Values to insert.
     */
    private array $params = [];

    /**
     * @var string $update - Query for update data.
     */
    private string $update = "";

    /**
     * @var string $delete - Query for delete data.
     */
    private string $delete = "";

    /**
     * @var array $conditionals For use with the function where()
     */
    private array $conditionals = [];

    public function __construct() {
        parent::__construct();
        $this->db = $this->getPDO();
    }

    /**
     * Select the fields for the query.
     * @param string|array $fields
     * @return $this
     */
    public function select(string|array $fields = "*"): Database {
        if (is_array($fields)) {
            foreach ($fields as $key => $field) {
                $fields[$key] = trim($field);
            }
        }
        // foreach($matches[0] as $match) {
        // }
        // if (!!preg_match($regex, $fields)) {
        //     throw new \Error("Los campos seleccionados están mal formados\n\n");
        // }

        $this->fields = is_array($fields) ? implode(", ", $fields) : $fields;

        $this->fields = preg_replace("/(\s)+/", " ", $this->fields);


        $regex = '/[\w]+(?=\s)/';
        $fields = explode(",", $this->fields);

        foreach ($fields as $field) {
            $field = trim($field);
            $words = explode(" ", $field);
            $words_count = count($words);
            $second_word = strtolower($words[1] ?? "");

            if ($words_count === 3 && $second_word !== "as") {
                throw new \Error("Los campos seleccionados están mal formados\n\n");
            }

            if ($words_count !== 1 && $second_word !== "as") {
                throw new \Error("Los campos seleccionados están mal formados\n\n");
            }

            if ($words_count > 3) {
                throw new \Error("Los campos seleccionados están mal formados\n\n");
            }
        }


        return $this;
    }

    /**
     * Return the data of the query
     * @return array
     */
    public function get(array $parameters = []): array {
        return $this->getData($parameters, true);
    }

    /**
     * Execute a query with or without parameters
     */
    public function execute(array $array = []): bool {
        preg_match('/where/i', $this->query, $matches);

        $params = empty($this->conditionals) ? $this->params : $this->conditionals;

        try {
            if (!!$matches) {
                return $this->stmt->execute($params);
            }

            if (count($array) !== 0) {
                return $this->stmt->execute($array);
            }

            if (!$matches && count($array) === 0) {
                return $this->stmt->execute();
            }
        } catch (\PDOException $e) {
            $this->conditionals = [];
            $this->params = [];
        }
    }

    /**
     * Return the first value of the query
     * @return array
     */
    public function first(array $array = []): array {
        return $this->getData($array);
    }

    /**
     * Get the data of the database. If $multiple is true,
     * return an array with the data, otherwise return the first value.
     * 
     * @param array $array
     * @param bool $multiple
     * @return array
     */
    private function getData(array $array, bool $multiple = false): array {
        $params = $this->getParams();
        $params_count = count($params);
        $query = $this->getQuery();

        if (empty($query)) {
            throw new \Error("No se ha definido la consulta");
        }

        $this->stmt = $this->db->prepare($query);

        $array = $params_count > 0 ? $params : $array;

        $this->execute($array);

        if (!empty(trim($this->query))) {
            $this->query = "";
        }

        $data = $multiple
            ? $data = $this->stmt->fetchAll(\PDO::FETCH_ASSOC)
            : $this->stmt->fetch(\PDO::FETCH_ASSOC);

        return is_bool($data)
            ? []
            : $data;
    }

    /**
     * Select the table to use
     * @deprecated This function is deprecated. Use from() instead.
     * @param string $table
     * @return Database
     */
    public function selectTable(string $table): Database {
        $this->table = (string) $table;
        return $this;
    }

    /**
     * Return the table name
     * @return string
     */
    public function getTableName(): string {
        $table = $this->table;
        $this->table = "";

        return $table;
    }

    /**
     * Return the field name
     * @return string
     */
    public function getFieldName(): string {
        $field = $this->fields;
        $this->fields = "";

        return $field;
    }

    /**
     * Function pendiente por continuar y probar.
     */
    public function field(string | array $field): Database {
        if (is_array($field)) {
            $field = implode(', ', $field);
        }

        $this->fields = $field;
        return $this;
    }

    public function where(string|array $field, string $operator = "=", string $value = NULL): Database {
        $is_array = is_array($field);
        $fields = $this->getFields();
        $is_value = !!$value;

        $table = $this->getTableName();

        preg_match('/^[%]/', $operator, $_symbol_start);
        preg_match('/[%]$/', $operator, $_symbol_end);

        $symbol_start = $_symbol_start[0] ?? "";
        $symbol_end = $_symbol_end[0] ?? "";

        $operator = preg_replace('/[%]/', "", $operator);

        if (empty(trim($table))) {
            throw new \Error("No se ha seleccionado una tabla\n\n");
        }

        if (empty(trim($fields))) {
            $fields = "*";
        }

        if (is_string($field) && $is_value) {
            $this->params = [":{$field}" => trim($value)];
            $field = "{$field} {$operator} {$symbol_start}:{$field}{$symbol_end}";
        }

        if (is_string($field) && !$is_value) {
            $field = "{$field} {$operator}";
        }

        /**
         * @var array $_fields
         */
        $_fields = [];

        if ($is_array && $is_value) {

            foreach ($_fields as $key => $value) {
                $_fields[$key] = ":{$key}";
            }

            $field = implode(" $operator ", $_fields);
        }

        /**
         * @var string $query Query to execute.
         */
        $query = "";

        if ($is_value) {
            $query = "SELECT {$fields} FROM {$table} WHERE $field";
        }

        if (!$is_value) {
            $query = "SELECT {$fields} FROM {$table} WHERE $field";
        }

        $this->query = $query;
        return $this;
    }

    /**
     * Prepare the query traditionally for use.
     * @param string $query The query to use.
     * @return Database
     */
    public function query(string $query): Database {
        $this->query = $query;
        $this->stmt = $this->db->prepare($this->query);

        return $this;
    }

    /**
     * Return a query constructed with the parameters given.
     *  @return string
     */
    public function getQuery(): string {
        return $this->query;
    }

    /**
     * Prepare the values to insert or update.
     * @param array $fields
     * @return bool
     */
    public function insert(array $fields): bool {
        if (empty(trim($this->table))) {
            return false;
        }

        $parameters = [];

        $this->db->beginTransaction();

        foreach ($fields as $key => $value) {
            $parameters[":$key"] = is_numeric($value) ? (float) $value : (string) "$value";
        }

        $query = 'INSERT INTO ' . $this->table . '(' . implode(', ', array_keys($fields)) . ') VALUES(' . implode(', ', array_keys($parameters)) . ');';
        $this->stmt = $this->db->prepare($query);


        $this->table = "";
        $this->stmt->execute($parameters);
        return $this->db->commit();
    }

    /**
     * Insert multiple rows in the database.
     * @param array $fields
     * @return bool
     */
    public function insertMultiple(array $fields): bool {
        if (empty(trim($this->table))) {
            return false;
        }


        $this->db->beginTransaction();

        foreach ($fields as $rows) {
            $parameters = [];

            foreach ($rows as $key => $value) {
                $parameters[":$key"] = is_numeric($value) ? (float) $value : (string) "$value";
            }

            $query = 'INSERT INTO ' . $this->table . '(' . implode(', ', array_keys($rows)) . ') VALUES(' . implode(', ', array_keys($parameters)) . ');';
            $this->stmt = $this->db->prepare($query);
            $this->stmt->execute($parameters);
        }

        $this->table = "";
        return $this->db->commit();
    }

    /**
     * Generate hash from a password using the Argon2i algorithm.
     * @param string $password
     * @param array $options_param
     * @return string
     */
    public function getToken(string $password, array $options_param = []): string {

        /**
         * @var array $options
         */
        $options = [
            'memory_cost' => 4 << 10,
            'time_cost' => 2,
            'threads' => 2,
        ];

        foreach ($options_param as $key => $value) {
            if (array_key_exists($key, $options)) {
                $options[$key] = $value;
            }
        }

        return password_hash($password, PASSWORD_ARGON2I, $options);
    }

    /**
     * Verify if the password is correct.
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function validateToken(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    /**
     * Return true if delete the data in the database.
     * @return bool
     */
    public function delete(array $array = []): bool {
        if (empty(trim($this->table))) {
            return false;
        }

        $count = count($array);

        if ($count === 0) {
            $db = $this->query("DELETE FROM $this->table");
            return $db->execute();
        }
    }

    public function truncate(string $table): bool {
        $db = $this->query("TRUNCATE TABLE $table");
        return $db->execute();
    }

    /**
     * Return the fields selected for user.
     * @return string
     */
    public function getFields(): string {
        return trim($this->fields);
    }

    /**
     * Select the table to use
     */
    public function from(string $table): Database {
        $this->table = trim($table);
        return $this;
    }

    /**
     * Return the params used in the query.
     * @return array<string, string|int>
     */
    public function getParams(): array {
        return $this->params;
    }

    public function setParams(array $params): void {
        $this->params = [];

        foreach ($params as $key => $value) {
            $field = preg_replace('/[:]/', "", $key);
            $this->params[":{$field}"] = trim($value);
        }
    }

    public function update(string $tableName): Database {

        $this->table = $tableName;
        return $this;
    }
}
