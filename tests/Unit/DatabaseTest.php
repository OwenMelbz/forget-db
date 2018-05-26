<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\DatabaseService;

class DatabaseTest extends TestCase
{
    public function test_i_can_connect_to_a_database()
    {
        $db = new DatabaseService([
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);

        $this->assertInstanceOf('PDO', $db->testConnection());
    }

    public function test_i_can_get_default_options()
    {
        $sql = DatabaseService::optionsForMySql();
        $pgsql = DatabaseService::optionsForPgSql();
        $sqlite = DatabaseService::optionsForSqlite();
        $sqlsrv = DatabaseService::optionsForSqlSrv();

        $this->assertEquals('mysql', $sql['driver']);
        $this->assertArrayHasKey('driver', $sql);
        $this->assertArrayHasKey('host', $sql);
        $this->assertArrayHasKey('port', $sql);
        $this->assertArrayHasKey('database', $sql);
        $this->assertArrayHasKey('username', $sql);
        $this->assertArrayHasKey('password', $sql);
        $this->assertArrayHasKey('prefix', $sql);

        $this->assertEquals('pgsql', $pgsql['driver']);
        $this->assertArrayHasKey('driver', $pgsql);
        $this->assertArrayHasKey('host', $pgsql);
        $this->assertArrayHasKey('port', $pgsql);
        $this->assertArrayHasKey('database', $pgsql);
        $this->assertArrayHasKey('username', $pgsql);
        $this->assertArrayHasKey('password', $pgsql);
        $this->assertArrayHasKey('prefix', $pgsql);

        $this->assertEquals('sqlite', $sqlite['driver']);
        $this->assertArrayHasKey('driver', $sqlite);
        $this->assertArrayHasKey('database', $sqlite);
        $this->assertArrayHasKey('prefix', $sqlite);

        $this->assertEquals('sqlsrv', $sqlsrv['driver']);
        $this->assertArrayHasKey('host', $sqlsrv);
        $this->assertArrayHasKey('port', $sqlsrv);
        $this->assertArrayHasKey('database', $sqlsrv);
        $this->assertArrayHasKey('username', $sqlsrv);
        $this->assertArrayHasKey('password', $sqlsrv);
        $this->assertArrayHasKey('prefix', $sqlsrv);
    }
}
