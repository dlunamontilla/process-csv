<?php

use DLTools\Models\Database;
use PHPUnit\Framework\TestCase;

class TestQuery extends TestCase {
    private object $credentials;
    private string $tableName;
    private string $fieldName;
    private Database $db;

    /**
     * @before Test before each test
     * @return void
     */
    public function setup(): void {
        $this->db = new Database;
        $this->credentials = $this->db->getCredentials();

        /**
         * Get the table name and field name from the file .env
         */
        $this->tableName = $this->credentials->DL_TABLE_NAME;
        $this->fieldName = $this->credentials->DL_FIELD_NAME;
    }

    /**
     * @test Test the query to the database
     * @return void
     */
    public function testQuery(): void {
        $result = $this->db
            ->query("SELECT $this->fieldName FROM $this->tableName")
            ->first();

        $this->assertIsArray($result);
    }

    /**
     * @test Test the inserction of data into the database.
     * @return void
     */
    public function test_insert_data_of_the_table(): void {
        /** @var array */
        $field = [];
        $field[$this->fieldName] = $this->db->getToken('tu-contraseña');

        $it_registred = $this->db
            ->from($this->tableName)
            ->insert($field);

        $this->db->query("truncate table $this->tableName")->first();

        $this->assertTrue($it_registred);
    }

    /**
     * @test Test the data delete from the database.
     * @return void
     */
    public function test_delete_from_table(): void {
        /** @var array */
        $field = [];
        $field[$this->fieldName] = $this->db->getToken('tu-contraseña');

        $this->db
            ->from($this->tableName)
            ->insert($field);


        $it_deleted = $this->db
            ->from($this->tableName)
            ->delete();

        $this->assertTrue($it_deleted, "El método delete() no está eliminando los datos de la base de datos\n\n");
    }

    /**
     * @test Test for validate the deleting the register from a table.
     * @return void
     */
    public function test_validate_truncate_table(): void {
        /**
         * @var bool $it_truncated
         */
        $it_truncated = $this->db->truncate($this->tableName);
        $this->assertTrue($it_truncated, "El método truncate() no está eliminando los datos de la tabla\n\n");
    }

    /**
     * @test Test for validate the function where()
     * @return void
     */
    public function test_for_valida_the_function_where() {
        /**
         * @var string $query
         */
        $query = $this->db->from('tabla')
            ->where('campo', '=', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo = :campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', '<>', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo <> :campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', '>', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo > :campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', '<', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo < :campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', '>=', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo >= :campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', '<=', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo <= :campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'like', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo like :campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'not like', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo not like :campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'in', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo in :campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'not in', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo not in :campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'between', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo between :campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'not between', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo not between :campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'is null')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo is null', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'is not null')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo is not null', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'is true')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo is true', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'is not true')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo is not true', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'is false')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo is false', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'is not false')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo is not false', $query);

        $query = $this->db->from('tabla')
            ->where('campo', '%like%', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo like %:campo%', $query);

        $query = $this->db->from('tabla')
            ->where('campo', '%not like%', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo not like %:campo%', $query);

        $query = $this->db->from('tabla')
            ->where('campo', '%like', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo like %:campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', '%not like', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo not like %:campo', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'like%', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo like :campo%', $query);

        $query = $this->db->from('tabla')
            ->where('campo', 'not like%', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT * FROM tabla WHERE campo not like :campo%', $query);

        $params = $this->db->from('tabla')
            ->where('campo', 'like', 'valor')
            ->getParams();

        $this->assertEquals([
            ':campo' => 'valor'
        ], $params);

        $params = $this->db->select('campo')
            ->from('tabla')
            ->where('campo', '=', 'valor')
            ->getParams();

        $this->assertEquals([
            ':campo' => 'valor'
        ], $params);

        $query = $this->db->select('campo')
            ->from('tabla')
            ->where('campo', 'like', 'valor')
            ->getQuery();

        $this->assertEquals('SELECT campo FROM tabla WHERE campo like :campo', $query);

        $params = $this->db->select('campo')
            ->from('tabla')
            ->where('campo', 'not like', 'valor')
            ->getParams();
        
        $this->assertEquals([
            ':campo' => 'valor'
        ], $params);
    }

    /**
     * @test Test the fields selected for user.
     * @return void
     */
    public function test_fields(): void {
        $fields = $this->db->select([
            "field1",
            "field2",
            "field3"
        ])->getFields();

        $this->assertSame("field1, field2, field3", $fields);

        $fields = $this->db->select()->getFields();
        $this->assertSame("*", $fields);

        $fields = $this->db->select(" field1, field2, field3 ")->getFields();
        $this->assertSame("field1, field2, field3", $fields);

        $fields = $this->db->select([
            "  field1   ",
            "  field2   ",
            "   field3   "
        ])->getFields();

        $this->assertSame("field1, field2, field3", $fields);
    }

    /**
     * @test Test the fields selected for user.
     * @return void
     */
    public function test_errors_in_the_fields_with_format_bad(): void {
        $this->expectError();

        $this->db->select([
            " field1 ",
            " field2 ciencia de datos ",
            " field3 "
        ])->getFields();
    }

    /**
     * @test Test the fields selected for user.
     * @return void
     */
    public function test_errors_in_the_fields_with_format_bad_2(): void {
        $this->expectError();

        $this->db->select([
            " field1 ",
            "field2 as ciencia de datos as ciencia ",
            " field3 "
        ])->getFields();
    }

    /**
     * @test Test for verification of the fields selected for user.
     * @return void
     */
    public function test_set_params(): void {
        $this->db
            ->setParams([
                'campo' => 'valor'
            ]);

        $this->assertEquals([
            ':campo' => 'valor'
        ], $this->db->getParams());

        $this->db
            ->setParams([
                ':campo' => 'valor'
            ]);

        $this->assertEquals([
            ':campo' => 'valor'
        ], $this->db->getParams());
    }
}
