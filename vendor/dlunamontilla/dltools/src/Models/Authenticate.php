<?php

namespace DLTools\Models;

// session_start();

/**
 * Authenticate user with protection csrf token
 * It responsible for the user authentication.
 * @author David E Luna M <davidlunamontilla@gmail.com>
 */
class Authenticate extends Database {
    private string $tableName = "";
    private string $fieldName = "";
    private string $tokenName = "token";

    public function __construct() {
        parent::__construct();
    }

    public function findUser(string $user): bool {
        $fieldName = $this->getFieldName();
        $tableName = $this->getTableName();

        if (empty($fieldName) || empty($tableName)) {
            throw new \Exception('El nombre de la tabla o campo no está definido. Utilice el método setTableName() y setFieldName() para definir el nombre de la tabla y el campo respectivo.');
        }

        $register = $this->select($fieldName)
            ->from($tableName)
            ->where($fieldName, '=', $user)
            ->first();


        return !empty($register);
    }

    /**
     * Get the table name.
     * 
     * @return string
     */
    public function getTableName(): string {
        return trim($this->tableName);
    }

    /**
     * Get the field name.
     * 
     * @return string
     */
    public function getFieldName(): string {
        return trim($this->fieldName);
    }

    /**
     * Set the table name.
     * 
     * @return void
     */
    public function setTableName(string $tableName): void {
        $this->tableName = $tableName;
    }

    /**
     * Set the field name.
     * 
     * @param string|array $fieldName
     * @return void
     */
    public function setFieldName(string|array $fieldName): void {
        if (is_array($fieldName)) {
            $this->fieldName = implode(', ', $fieldName);
        }

        $this->fieldName = $fieldName;
    }

    /**
     * Registrar a new user in the database.
     * 
     * @param string $username
     * @para string $password
     * @return bool
     */
    public function registerUser(string $username, string $password): bool {
        $fieldName = $this->getFieldName();
        $passwordName = $this->getPasswordName();
        $tableName = $this->getTableName();

        if (empty($fieldName) || empty($passwordName) || empty($tableName)) {
            throw new \Exception('Establezca el nombre del campo de usuario y contraseña con las funciones setFiledName(), setPasswordName() y setTableName().');
        }

        $found_user = $this->findUser($username);

        if ($found_user) {
            return false;
        }

        return $this->from($tableName)
            ->insert([
                $fieldName => $username,
                $passwordName => $this->getToken($password)
            ]);
    }

    /**
     * Set the field password name.
     * 
     * @param string $passwordName
     * @return void
     */
    public function setPasswordName(string $passwordName): void {
        $this->passwordName = $passwordName;
    }

    /**
     * Get the field password name.
     * 
     * @return string
     */
    public function getPasswordName(): string {
        return trim($this->passwordName);
    }

    /**
     * Set the token name.
     * 
     * @param string $tokenName
     */
    public function setTokenName(string $tokenName): void {
        $this->tokenName = trim($tokenName);
    }

    /**
     * Get the token name.
     * 
     * @return string
     */
    public function getTokenName(): string {
        return trim($this->tokenName);
    }

    /**
     * Login user
     * 
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function user(string $username, string $password): bool {
        $find_user = $this->findUser($username);
        $passwordName = $this->getPasswordName();
        $tableName = $this->getTableName();
        $tokenName = $this->getTokenName();
        $fieldName = $this->getFieldName();

        if (!$find_user) {
            return false;
        }

        $passwordHash = $this->select($passwordName)
            ->from($tableName)
            ->where($fieldName, '=', $username)
            ->first()[$passwordName] ?? '';

        $hash = sha1($password . $username);

        
        return $this->validateToken($password, $passwordHash);
    }
}
