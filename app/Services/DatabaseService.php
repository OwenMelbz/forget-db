<?php

namespace App\Services;

use PDO;
use Illuminate\Support\Facades\DB;

/**
 * Class DatabaseService
 * @package App\Services
 */
class DatabaseService
{

    /**
     * DatabaseService constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        switch (data_get($config, 'driver', 'mysql')) {
            case 'pgsql':
                $options = static::optionsForPgSql();
                break;
            case 'sqlite':
                $options = static::optionsForSqlite();
                break;
            case 'sqlsrv':
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

    /**
     * Returns a list of the default database options
     * from Laravel for the mysql driver.
     *
     * @return array
     */
    public static function optionsForMySql(): array
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

    /**
     * Returns a list of the default database options
     * from Laravel for the pgsql driver.
     *
     * @return array
     */
    public static function optionsForPgSql(): array
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

    /**
     * Returns a list of the default database options
     * from Laravel for the sqlite driver.
     *
     * @return array
     */
    public static function optionsForSqlite(): array
    {
        return [
            'driver' => 'sqlite',
            'database' => 'database.sqlite',
            'prefix' => '',
        ];
    }

    /**
     * Returns a list of the default database options
     * from Laravel for the sqlsrv driver.
     *
     * @return array
     */
    public static function optionsForSqlSrv(): array
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

    /**
     * Checks we can connect to the database using
     * the provided connection details.
     *
     * @return PDO
     * @throws \Exception
     */
    public function testConnection(): PDO
    {
        DB::reconnect(); // We need to reconnect in case the database credentials changed.

        return DB::connection()->getPdo(); // If it cannot connect, this will throw an exception.
    }
}