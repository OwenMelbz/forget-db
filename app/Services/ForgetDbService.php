<?php

namespace App\Services;

use App\Table;
use Exception;
use App\Column;
use App\Condition;
use LaravelZero\Framework\Commands\Command;

/**
 * Class ForgetDbService
 * @package App\Services
 */
class ForgetDbService
{

    protected $config;

    protected $scheme;

    protected $messenger;

    protected $tables;

    /**
     * ForgetDbService constructor.
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->tables = $this->generateTablesFromConfig($config);
    }

    /**
     * @param Command $messenger
     */
    public function forget(Command $messenger)
    {
        $this->messenger = $messenger;

        foreach ($this->tables as $table) {
            $table->forget($this->messenger);
        }
    }

    /**
     * @param array $tables
     * @return array
     * @throws Exception
     */
    private function generateTablesFromConfig(array $tables)
    {
        $scheme = [];

        foreach ($tables as $tableName => $properties) {
            $tablePrimaryKey = data_get($properties, 'key', null);

            if (empty($tablePrimaryKey)) {
                /** @noinspection PhpUnhandledExceptionInspection */
                throw new Exception('No `key` property found for `' . $tableName . '` table');
            }

            $columns = collect();
            $conditions = collect();

            foreach (array_wrap($properties['conditions'] ?? []) as $condition) {
                $conditions->push(
                    new Condition($condition)
                );
            }

            foreach (array_wrap($properties['columns'] ?? []) as $column => $faker) {
                $columns->push(
                    new Column($column, $faker)
                );
            }

            $table = new Table($tableName, $tablePrimaryKey, $conditions, $columns);

            $scheme[] = $table;
        }

        return $scheme;
    }
}