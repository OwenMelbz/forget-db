<?php

namespace App\Services;


use Illuminate\Support\Facades\DB;

class DatabaseService
{
    public function __construct(array $config)
    {
        switch (data_get($config, 'driver', 'mysql'))
        {
            case "pgsql":
                $options = static::optionsForPgSql();
                break;
            case "sqlite":
                $options = static::optionsForSqlite();
                break;
            case "sqlsrv":
                $options = static::optionsForSqlSrv();
                break;
            default:
                $options = static::optionsForMySql();
                break;
        }

        foreach ($options as $setting => $default) {
            config([('database.connections.default.' . $setting) => data_get($config, $setting, $default)]);
        }
    }

    public static function optionsForMySql()
    {
        return [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'my_magical_database',
            'username' => 'root',
            'password' => '',
            'prefix' => '',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'strict' => false,
            'engine' => null,
        ];
    }

    public static function optionsForPgSql()
    {
        return [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'port' => '5432',
            'database' => 'my_magical_database',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ];
    }

    public static function optionsForSqlite()
    {
        return [
            'driver' => 'sqlite',
            'database' => 'database.sqlite',
            'prefix' => '',
        ];
    }

    public static function optionsForSqlSrv()
    {
        return [
            'driver' => 'sqlsrv',
            'host' => 'localhost',
            'port' => '1433',
            'database' => 'my_magical_database',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => '',
        ];
    }

    public function testConnection()
    {
        DB::reconnect();

        return DB::connection()->getPdo();
    }
}