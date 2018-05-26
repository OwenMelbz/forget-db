<?php

namespace App\Services;

use Symfony\Component\Yaml\Yaml;

class UtilityService
{
    public static function cleansePath($path)
    {
        $path = str_replace('./', '', $path);
        $path = ltrim($path, '.');

        if (empty($path)) {
            $path = getcwd();
        }

        $path = rtrim($path, '/');

        return $path;
    }

    public static function createFolderForFile($fullPath)
    {
        $filenameInfo = pathinfo($fullPath);
        $folder = static::cleansePath(
            data_get($filenameInfo, 'dirname', './')
        );

        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        return realpath($fullPath);
    }

    public static function cleanseFilename($filename)
    {
        $filenameInfo = pathinfo($filename);

        return data_get($filenameInfo, 'filename') . '.yml';
    }

    public static function parseConfig($configPath)
    {
        return Yaml::parseFile($configPath);
    }

    public static function stubConfig()
    {
        $config = [
            'table_one' => [
                'key' => 'id',
                'conditions' => [
                    'where user_id != 1',
                    'or cake LIKE "%test%"',
                ],
                'columns' => [
                    'user_name' => 'name',
                    'user_email' => 'email',
                ],
            ],
            'table_two' => [
                'key' => 'id',
                'columns' => [
                    'user_name' => 'name',
                    'user_email' => 'email',
                ],
            ],
            'table_three' => [
                'key' => 'id',
                'conditions' => 'user_id = 1',
                'columns' => [
                    'user_name' => 'name',
                    'user_email' => 'email',
                ],
            ],
        ];

        return Yaml::dump($config);
    }

    public static function message(string $string)
    {
        return '🧠  forget-db :: ' . $string;
    }
}