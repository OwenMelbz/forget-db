<?php

namespace App\Services;

use josegonzalez\Dotenv\Loader as DotEnv;

/**
 * Class EnvService
 * @package App\Services
 */
class EnvService
{

    public static $env = [];

    /**
     * Checks to see if there is a .env file in the directory.
     *
     * @return bool
     */
    public static function checkIfCanUseDotEnv(): bool
    {
        $potentialPath = getcwd() . '/.env';

        return file_exists($potentialPath);
    }

    /**
     * Load the environment variables.
     *
     * @return void
     */
    public static function loadDotEnv(): void
    {
        static::$env = (new DotEnv(getcwd() . '/.env'))->parse()->toArray();
    }

    /**
     * Return the environment variable by key.
     *
     * @param $key
     * @param null $default
     * @return string
     */
    public static function get($key, $default = null)
    {
        return array_key_exists($key, static::$env) ? static::$env[$key] : $default;
    }
}