<?php

namespace App\Services;

use App\Table;
use Exception;
use App\Column;
use App\Condition;
use App\Relationship;
use LaravelZero\Framework\Commands\Command;

/**
 * Class ForgetDbService
 * @package App\Services
 */
class ForgetDbService
{

    /**
     * Stores the parsed config for access later.
     *
     * @var array
     */
    protected $config;

    /**
     * The LaravelZero\Framework\Commands\Command which
     * acts as a message bus for output to the console.
     *
     * @var Command
     */
    protected $messenger;

    /**
     * Contains an array of App\Table ready for processing.
     *
     * @var array
     */
    protected $tables;

    /**
     * Defines if we're running in dry-run mode
     *
     * @var boolean
     */
    protected $dry = false;

    /**
     * ForgetDbService constructor.
     *
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config, bool $dry = false)
    {
        $this->dry = $dry;
        $this->config = $config;
        $this->tables = $this->generateTablesFromConfig($config);
    }

    /**
     * Returns an array of tables ready for processing.
     *
     * @return array
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * @param Command $messenger
     */
    public function forget(Command $messenger): void
    {
        $this->messenger = $messenger;

        foreach ($this->getTables() as $table) {
            if ($this->dry) {
                $table->preview($this->messenger);
                continue;
            }

            $table->forget($this->messenger);
        }
    }

    /**
     * Here we convert the config into instances of individual
     * classes before handing them off to be processed.
     *
     * @param array $tables
     * @return array
     * @throws Exception
     */
    private function generateTablesFromConfig(array $tables): array
    {
        $scheme = [];

        foreach ($tables as $tableName => $properties) {
            $tablePrimaryKey = data_get($properties, 'key', null);

            if (empty($tablePrimaryKey)) {
                throw new Exception('No `key` property found for `' . $tableName . '` table');
            }

            $columns = collect();
            $conditions = collect();
            $relationships = collect();

            foreach (array_wrap($properties['conditions'] ?? []) as $condition) {
                $conditions->push(
                    new Condition($condition)
                );
            }

            foreach (array_wrap($properties['joins'] ?? []) as $relationship) {
                $relationships->push(
                    new Relationship($relationship)
                );
            }

            foreach (array_wrap($properties['columns'] ?? []) as $column => $faker) {
                $columns->push(
                    new Column($column, $faker)
                );
            }

            if ($columns->isEmpty()) {
                throw new Exception('No column mappings found, you need to define which columns you want to replace with fake data.');
            }

            $table = new Table($tableName, $tablePrimaryKey, $conditions, $relationships, $columns);

            $scheme[] = $table;
        }

        return $scheme;
    }

}