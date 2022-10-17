<?php
namespace DLTools\Models;

use PDOStatement;
use DLTools\Controllers\DLRequest;

/**
 * Authenticate user with protection csrf token
 */
class DLUser extends DLRequest {
    /**
     * @var Database $db
     */
    private string $db;

    public function __construct(Database $db = new Database) {
        parent::__construct();

        $this->db = $db;

        if ($this->isGet()) $this->setCSRF();
    }

    /**
     * Generate a token random for the user
     * @return void 
     */
    private function setCSRF(): void {
        $_SESSION['csrf'] = sha1((string) rand(0, 100) + time());
    }

    /**
     * Validate the csrf token
     * @return bool
     */
    public function validateCSRF(): bool {
        $value = $this->getValue('csrf');
        $session = $_SESSION['csrf'];
        return $value === $session;
    }

    /**
     * @param string $message
     * @return void 
     */
    public function error(string $message): void {
        setcookie('error', $message, time() + 1);
    }

    /**
     * Evaluate if exists a user from the username.
     * @param string $username
     * @return bool
     */
    public function existsUser(string $username): bool {
        $table = $this->db->getTableName();
        $field = $this->db->getFieldName();

        /**
         * Query parameters
         * @var array $parameters
         */
        $parameters = [];
        $parameters[":$field"] = $username;

        return !!$this->db
            ->select("SELECT $field FROM $table WHERE $field = :value")
            ->first($parameters);
    }
}

$user = new DLUser;