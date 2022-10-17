<?php

use PHPUnit\Framework\TestCase;
use DLTools\Models\Authenticate;

class TestAuthenticate extends TestCase {
    private Authenticate $authenticate;
    private object $credentials;
    /**
     * @before Execute before each test.
     */
    public function setup(): void {
        $this->authenticate = new Authenticate;
        $this->credentials = $this->authenticate->getCredentials();

        $this->tableName = $this->credentials->DL_TABLE_NAME;
        $this->fieldName = $this->credentials->DL_FIELD_NAME;
    }

    /**
     * @test Simular la creación de una cookie con una duración de 20 segundos.
     */
    public function test_setCookie(): void {
        $_COOKIE['test'] = 'test';
        $created = $_COOKIE['test'] === 'test';
        $this->assertTrue($created);
    }

    /**
     * @test Test if the user it find in the database.
     * 
     * @return void
     */
    public function test_find_user(): void {
        $inserted = $this->authenticate
            ->from($this->tableName)
            ->insertMultiple([
                [
                    $this->fieldName => 'userA'
                ],

                [
                    $this->fieldName => 'userB',
                ]
            ]);

        $this->assertTrue($inserted);

        $this->authenticate->setTableName($this->tableName);
        $this->authenticate->setFieldName($this->fieldName);

        $find = $this->authenticate->findUser('userA');
        $this->assertTrue($find);

        $find = $this->authenticate->findUser('dlunamontillaA');
        $this->assertFalse($find);

        $find = $this->authenticate->findUser('dlunamontillaB');
        $this->assertFalse($find);

        $find = $this->authenticate->findUser('userB');
        $this->assertTrue($find);

        $it_truncate = $this->authenticate
            ->query("truncate table $this->tableName")
            ->execute();

        $this->assertTrue($it_truncate);
    }

    /**
     * @test Test if the user it registered in the database.
     * 
     * @return void
     */
    public function test_user_register(): void {
        $this->authenticate->setFieldName($this->fieldName);
        $this->authenticate->setPasswordName('password');
        $this->authenticate->setTableName($this->tableName);

        $inserted = $this->authenticate
            ->registerUser('username', 'password');

        $this->assertTrue($inserted);

        $inserted = $this->authenticate
            ->registerUser('username', 'password');

        $this->assertFalse($inserted);

        $this->authenticate
            ->query("truncate table $this->tableName")
            ->execute();
    }

    public function test_login_user(): void {
        $password = "tu-nueva-contraseña";
        $username = "tu-usuario";

        $this->authenticate->setFieldName($this->fieldName);
        $this->authenticate->setPasswordName('password');
        $this->authenticate->setTableName($this->tableName);
        $this->authenticate->setTokenName('token');
        $this->assertSame('token', $this->authenticate->getTokenName());

        $this->authenticate
            ->registerUser($username, $password);

        $logged = $this->authenticate
            ->user($username, $password);

        $this->assertTrue($logged);

        $logged = $this->authenticate
            ->user($username, 'otra-contraseña');

        $this->assertFalse($logged);

        $this->authenticate
            ->query("truncate table $this->tableName")
            ->execute();
    }
}