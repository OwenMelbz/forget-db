<?php

namespace App;

use Faker\Factory as Faker;

/**
 * Class Column
 * @package App
 */
class Column
{

    protected $name;

    protected $replacement;

    private $faker;

    /**
     * Column constructor.
     * @param string $name
     * @param string $replacement
     */
    public function __construct(string $name, string $replacement)
    {
        $this->name = $name;
        $this->replacement = $replacement;
        $this->faker = Faker::create();
    }

    /**
     * @return mixed
     */
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