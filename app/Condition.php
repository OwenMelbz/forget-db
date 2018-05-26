<?php

namespace App;

/**
 * Class Condition
 * @package App
 */
class Condition
{

    /**
     * Contains the raw query provided from the config.
     *
     * @var string
     */
    private $raw;

    /**
     * Condition constructor.
     *
     * @param string $raw
     */
    public function __construct(string $raw)
    {
        $this->raw = $raw;
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
     * This just acts as a poor mans sanitiser
     * to mutate some common keywords that
     * might break the query builder.
     *
     * @return string
     */
    public function getWhere(): string
    {
        $where = ltrim($this->raw, 'where ');
        $where = ltrim($where, 'WHERE ');
        $where = ltrim($where, 'Where ');

        $where = ltrim($where, 'or ');
        $where = ltrim($where, 'OR ');
        $where = ltrim($where, 'Or ');
        $where = ltrim($where, '|| ');

        $where = ltrim($where, 'and ');
        $where = ltrim($where, 'AND ');
        $where = ltrim($where, 'And ');
        $where = ltrim($where, '&& ');

        return $where;
    }

}