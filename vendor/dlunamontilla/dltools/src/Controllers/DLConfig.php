<?php

namespace DLTools\Controllers;

/**
 * Permitirá capturar todas las variables de entorno.
 * 
 * @package DLConfig
 * @version 1.0.0
 * @author David E Luna <davidlunamontilla@gmail.com>
 * @copyright (c) 2022 - David E Luna M
 * @license MIT
 */
class DLConfig {
    /**
     * @var string $path
     */
    protected string $path;

    /**
     * @param string $path
     */
    public function __construct(string $path = ".env") {
        $this->path = $path;

        if (!file_exists($this->path)) {
            $this->path = "../.env";
        }

        if (!file_exists($this->path)) {
            echo "<h2>Copie el archivo <code>.env.example</code> en <code>.env</code></h2>\n";
            echo "<h2>La ruta que intenta acceder es: $this->path</h2>\n";
            exit;
        }

        // Se cargan las variables de entorno:
        $this->credentials = $this->env();
    }

    /**
     * Update the path of the .env file.
     * @param string $path
     * @return void
     */
    public function setPath(string $path = "/../../.env"): void {
        $this->path = __DIR__ . $path;
    }

    /**
     * Devuelve las credenciales del archivo .env.
     * @return array
     */
    private function env(): array {
        if (!file_exists($this->path))
            return [];

        /**
         * @var array
         */
        $credentials = [];

        // Obtenemos las líneas del archivo:
        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            list($name, $value) = explode("=", $line, 2);

            $name = trim($name);
            $value = trim($value);

            putenv(sprintf("%s=%s", $name, $value));

            $credentials[$name] = $value;
        }

        return $credentials;
    }

    /**
     * Devuelve las credenciales almacenadas en .env
     * @return object
     */
    public function getCredentials(): object {
        return (object) $this->credentials;
    }

    /**
     * Establece y obtiene una conexión con el motor de base de datos.
     * @return \PDO
     */
    public function getPDO(): \PDO {
        $username = getenv("DL_DATABASE_USER");
        $password = getenv("DL_DATABASE_PASSWORD");
        $database = getenv("DL_DATABASE_NAME");
        $host = getenv("DL_DATABASE_HOST");
        $drive = getenv("DL_DATABASE_DRIVE");

        /**
         * @var string
         */
        $dsn = "$drive:dbname=$database;host=$host";
        
        try {
            $pdo = new \PDO($dsn, $username, $password);
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo "<h2>" . $e->getMessage() . "</h2>";
            exit;
        }

        return $pdo;
    }
}
