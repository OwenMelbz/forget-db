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
        $raw = ltrim($this->raw, 'where ');
        $raw = ltrim($raw, 'WHERE ');
        $raw = ltrim($raw, 'Where ');

        return $raw;
    }

}