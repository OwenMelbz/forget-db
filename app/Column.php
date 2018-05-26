<?php

namespace App;

use Faker\Factory as Faker;

/**
 * Class Column
 * @package App
 */
class Column
{

    /**
     * Contains the name of the column to be transformed.
     *
     * @var string
     */
    protected $name;

    /**
     * Stores the name of the method from Faker to be used
     * to replace the required information.
     *
     * @var string
     */
    protected $replacementMethod;

    /**
     * Just an instance of Faker to replace the information.
     *
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * Column constructor.
     *
     * @param string $name
     * @param string $replacementMethod
     */
    public function __construct(string $name, string $replacementMethod)
    {
        $this->name = $name;
        $this->replacementMethod = $replacementMethod;
        $this->faker = Faker::create();
    }

    /**
     * Generates a random string based off the replacement method.
     *
     * @return mixed
     */
    public function generate(): string
    {
        return $this->faker->format($this->replacementMethod);
    }

    /**
     * Just returns the name of the column.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}