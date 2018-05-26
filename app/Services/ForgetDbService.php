<?php

namespace App\Services;


use App\Column;
use App\Condition;
use App\Table;
use LaravelZero\Framework\Commands\Command;

class ForgetDbService
{
    protected $config;

    protected $scheme;

    protected $messenger;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->tables = $this->generateTablesFromConfig($config);
    }

    public function forget(Command $messenger)
    {
        $this->messenger = $messenger;

        foreach ($this->tables as $table) {
            $table->forget($this->messenger);
        }
    }

    private function generateTablesFromConfig(array $tables)
    {
        $scheme = [];

        foreach ($tables as $tableName => $properties) {

            $tablePrimaryKey = data_get($properties, 'key', null);

            if (empty($tablePrimaryKey)) {
                throw new \Exception('No `key` property found for `' . $tableName . '` table');
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