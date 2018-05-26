<?php

namespace App;

use Faker\Factory as Faker;

class Column
{
    protected $name;

    protected $replacement;

    private $faker;

    public function __construct(string $name, string $replacement)
    {
        $this->name = $name;
        $this->replacement = $replacement;
        $this->faker = Faker::create();
    }

    public function generate()
    {
        return $this->faker->format($this->replacement);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}