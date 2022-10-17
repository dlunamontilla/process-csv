<?php

use PHPUnit\Framework\TestCase;
use DLTools\Models\Database;

class DatabaseTest extends TestCase {

    /**
     * Database instance to test
     * @vawr Database $db
     */
    private Database $db;

    /**
     * @before Test before each test
     * @return void
     */
    public function setUp(): void {
        $this->db = new Database;
    }

    /**
     * @test Test to function getToken(), get the hash of a password.
     * @return void
     */
    public function testGetToken(): void {
        $hash = $this->db->getToken('tu-contraseña');
        $this->assertIsString($hash);
        $this->assertSame(95, strlen($hash));
    }

    /**
     * @test Test to function validateToken(), validate a hash of a password.
     * @return void
     */
    public function testValidateToken(): void {
        $hash = $this->db->getToken('tu-contraseña');
        $this->assertTrue($this->db->validateToken('tu-contraseña', $hash));
    }

    /**
     * @test Test to function query(), return a instance of Database.
     * @return void
     */
    public function testQuery(): void {
        $query = $this->db->query('SELECT * FROM ' . $this->db->getCredentials()->DL_TABLE_NAME);
        $this->assertInstanceOf(Database::class, $query);
    }
}
