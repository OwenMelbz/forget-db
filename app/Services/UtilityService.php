<?php

namespace App\Services;

use Symfony\Component\Yaml\Yaml;

/**
 * Class UtilityService
 * @package App\Services
 */
class UtilityService
{

    /**
     * This takes a path and tries to normalise it,
     * things get a little crazy with bash style paths
     * especially with the .phar version so this has
     * to do some odd things. Please allow it!
     *
     * @param $path
     * @return mixed|string
     */
    public static function cleansePath($path): string
    {
        $path = str_replace('./', '', $path);
        $path = ltrim($path, '.');

        // Looks like the user used a path like ./ etc,
        // so we just that with the cwd.
        if (empty($path)) {
            $path = getcwd();
        }

        $path = rtrim($path, '/'); // We'll put this back on later.

        return $path;
    }

    /**
     * This takes a full path to a file and
     * tries to create the folder for it if needed.
     *
     * @param $fullPath
     * @return bool|string
     */
    public static function createFolderForFile($fullPath): string
    {
        $filenameInfo = pathinfo($fullPath);

        $folder = static::cleansePath(
            data_get($filenameInfo, 'dirname', './')
        );

        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        return realpath($folder);
    }

    /**
     * Tries to normalise the filename for the config,
     * you know, just in case people include extensions etc.
     *
     * @param $filename
     * @return string
     */
    public static function cleanseFilename($filename): string
    {
        $filenameInfo = pathinfo($filename);

        return str_slug(
            data_get($filenameInfo, 'filename')
        ) . '.yml'; // .yaml or .yml?
    }

    /**
     * Takes a file path to a yaml file and
     * tries to parse it into an array for processing.
     *
     * @param $configPath
     * @return array
     */
    public static function parseConfig($configPath): array
    {
        return Yaml::parseFile($configPath);
    }

    /**
     * This returns a data structure to generate an example
     * config for a yaml file, due to .phar restrictions
     * accessing a stub file becomes problematic, so
     * we do this instead!
     *
     * @return string
     */
    public static function stubConfig(): string
    {
        $config = [
            'table_one' => [
                'key' => 'user_id',
                'conditions' => [
                    'where user_id != 1',
                    'or user_email LIKE "%@%"',
                ],
                'columns' => [
                    'user_name' => 'name',
                    'user_email' => 'email',
                ],
            ],
            'table_two' => [
                'key' => 'user_id',
                'columns' => [
                    'user_name' => 'name',
                    'user_email' => 'email',
                ],
            ],
            'table_three' => [
                'key' => 'user_id',
                'conditions' => 'user_id = 1',
                'columns' => [
                    'user_name' => 'name',
                    'user_email' => 'email',
                ],
            ],
        ];

        return Yaml::dump($config); // Ironically this returns a different structure when written to file...
    }

    /**
     * This is simply a brand related function to prefix any
     * messages with the package branding.
     *
     * @param string $string
     * @return string
     */
    public static function message(string $string): string
    {
        return '🧠  forget-db :: ' . $string; // Boop.
    }

    /**
     * Helper function to return the ordinal for a number...
     *
     * @param $number
     * @return string
     */
    public static function ordinal($number)
    {
        $ends = ['th','st','nd','rd','th','th','th','th','th','th'];

        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        } else {
            return $number . $ends[$number % 10];
        }
    }
}