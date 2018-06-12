<?php

namespace App;

use Exception;

/**
 * Class Relationship
 * @package App
 */
class Relationship
{

    /**
     * Contains the raw query provided from the config.
     *
     * @var string
     */
    private $raw;

    /**
     * Contains the type of join
     *
     * @var string
     */
    private $type = 'inner';

    /**
     * Contains the name of the joining table
     *
     * @var string
     */
    private $table;

    /**
     * Contains the name of the first column to join on
     *
     * @var string
     */
    private $tableOneColumn;

    /**
     * Contains the name of the second column to join on
     *
     * @var string
     */
    private $tableTwoColumn;

    /**
     * Contains the operator for the join condition.
     *
     * @var string
     */
    private $operator;

    /**
     * Relationship constructor.
     *
     * @param string $raw
     * @throws Exception
     */
    public function __construct(string $raw)
    {
        $this->raw = $raw;
        $this->parseJoin();
    }

    /**
     * Does this warrant a comment?
     *
     * @return string
     */
    public function getRaw(): string
    {
        return $this->raw;
    }

    /**
     * Returns the type of join.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Returns an array of arguments ready for deconstructing.
     *
     * @return array
     */
    public function getJoin(): array
    {
        return [
            $this->table,
            $this->tableOneColumn,
            $this->operator,
            $this->tableTwoColumn
        ];
    }

    /**
     * This takes a native join query and converts it into arguments.
     *
     * @return void
     * @throws Exception
     */
    private function parseJoin(): void
    {
        $joinParts = explode(' ', $this->raw);

        if (count($joinParts) !== 5) {
            throw new Exception('Invalid join format of "' . $this->raw . '", please use the following format "users on users.id = address.user_id"');
        }

        $joinModifier = explode(':', $joinParts[0]);
        $this->table = end($joinModifier);

        if (count($joinModifier) > 1) {
            $this->type = $joinModifier[0];
        }

        $this->tableOneColumn = $joinParts[2];
        $this->operator = $joinParts[3];
        $this->tableTwoColumn = $joinParts[4];
    }

}