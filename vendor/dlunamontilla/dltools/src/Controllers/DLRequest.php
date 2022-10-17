<?php

namespace DLTools\Controllers;

/**
 * @version 1.0.0b (2022-06-01) - Initial release
 */
class DLRequest {
    /**
     * @var string $method;
     */
    private string $method;

    /**
     * @var array $parametersValues;
     */
    private array $parametersValue;

    public function __construct() {
        /** Get the method of the petition */
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Obtener el valor de un parámetro de la petición
     * @param string $key
     * @return string
     */
    private function getRequestValue(string $key): string {
        return $_REQUEST[$key] ?? '';
    }

    /**
     * Validate a request parameter
     * @param string $key
     * @param bool $value
     * @return bool
     */
    private function validateParam(string $key, bool $value): bool {
        $requestValue = $this->getRequestValue($key);

        if (
            (empty($requestValue) && $value) ||
            !array_key_exists($key, $_REQUEST)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Validate the parameters of the request
     * @param array $param
     * @return bool
     */
    private function validate(array $param): bool {
        /**
         * @var array $validate
         */
        $validate = [];

        foreach ($param as $key => $value) {
            array_push($validate, $this->validateParam($key, $value));
        }

        foreach ($validate as $value) {
            if (!$value) {
                return false;
            }
        }

        foreach ($param as $key => $value) {
            $this->parametersValue[$key] = is_numeric($this->getRequestValue($key))
                ? (int) $this->getRequestValue($key)
                : (string) $this->getRequestValue($key);
        }

        return true;
    }

    /**
     * Get value of a request parameter
     * @param string $key
     * @return string
     */
    public function getValue(string $key): string {
        return $this->getRequestValue($key);
    }

    /**
     * Obtiene todos los valores de los parámetros de la petición
     * 
     * @param string $prefix
     * @param array $fields
     * @return array
     */
    public function getValues(string $prefix = NULL, array $fields = []): array {

        if (!$prefix && count($fields) === 0) {
            return $this->parametersValue;
        }

        /**
         * @var array $parameters
         */
        $parameters = [];


        if ($prefix && count($fields) === 0) {
            foreach ($this->parametersValue as $key => $value) {
                $parameters[$prefix . $key] = $value;
            }
        }

        if ($prefix && count($fields) > 0) {
            foreach ($fields as $key => $field) {
                $parameters[$prefix . $key] = $this->parametersValue[$key];
            }
        }

        if (!$prefix && count($fields) > 0) {
            foreach ($fields as $key => $field) {
                $parameters[$key] = $this->parametersValue[$field];
            }
        }

        return $parameters;
    }

    /**
     * Devuelve verdadero si los parametros son válidos y la petición es GET.
     * @param array $parameters
     * @return bool
     */
    public function get(array $parameters): bool {
        if (!($this->method === 'GET')) {
            return false;
        }
        
        return $this->validate($parameters);
    }

    /**
     * Devuelve verdadero si los parametros son válidos y la petición es POST.
     * 
     * @param array $parameters
     * @return bool
     */
    public function post(array $parameters): bool {
        if (!($this->method === 'POST')) {
            return false;
        }
        
        /**
         * @var array $fields Almacenar campos de la petición
         * donde su contenido es obligatorio.
         */
        $fields = [];

        foreach ($parameters as $key) {
            $fields[$key] = true;
        }

        return $this->validate($fields);
    }

    /**
     * Valida si cualquiera de los parámetros de la petición son válidos.
     * @param array $parameters
     * @return bool
     */
    public function any(array $parameters): bool {
        $method = $this->method;

        if ($method === 'GET') {
            foreach ($parameters as $key) {
                if (array_key_exists($key, $_GET)) {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    /**
     * Evalúa si la petición es POST.
     * @return bool
     */
    public function isPost(): bool {
        return $this->method === 'POST';
    }

    /**
     * Evalúa si la petición es GET.
     * @return bool
     */
    public function isGet(): bool {
        return $this->method === 'GET';
    }

    /**
     * Evalúa si el usuario está en la página de inicio.
     * @return bool
     */
    public function isHome(): bool {
        return count($_GET) === 0;
    }
}
