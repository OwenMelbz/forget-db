<?php

namespace App;

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
     * This flags if the field is required to be unique,
     * which solves unique constraints such as emails.
     *
     * @var string
     */
    protected $unique;

    /**
     * Column constructor.
     *
     * @param string $name
     * @param string $replacementMethod
     */
    public function __construct(string $name, string $replacementMethod)
    {
        $this->name = $name;
        $this->unique = $this->checkIfUnique($replacementMethod);
        $this->replacementMethod = $this->getReplacementMethod($replacementMethod);
    }

    /**
     * Generates a random string based off the replacement method.
     *
     * @return mixed
     */
    public function generate(): string
    {
        if ($this->unique) {
            return app('faker')->unique()->format($this->replacementMethod);
        }

        return app('faker')->format($this->replacementMethod);
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

    /**
     * Returns true/false depending if the unique modifier has been used.
     *
     * @return bool
     */
    private function checkIfUnique(string $string): bool
    {
        $parts = explode(':', $string);

        return current($parts) === 'unique';
    }

    /**
     * Returns the name of the faker method to be used.
     *
     * @return string
     */
    private function getReplacementMethod(string $string): string
    {
        $parts = explode(':', $string);

        return end($parts);
    }

}