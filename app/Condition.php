<?php

namespace App;


class Condition
{
    private $raw;

    public function __construct(string $raw)
    {
        $this->raw = $raw;
    }

    /**
     * @return string
     */
    public function getRaw(): string
    {
        return $this->raw;
    }

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