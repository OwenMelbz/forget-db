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
        $where = trim($this->raw);
        $where = preg_replace('/^where/i', '', $where);
        $where = preg_replace('/^or/i', '', $where);
        $where = preg_replace('/^\|\|/i', '', $where);
        $where = preg_replace('/^and/i', '', $where);
        $where = preg_replace('/^\&\&/i', '', $where);
        $where = trim($where);

        return $where;
    }

}