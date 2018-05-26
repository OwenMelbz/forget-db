<?php

namespace App\Services;

use Symfony\Component\Yaml\Yaml;

class UtilityService
{
    public static function createFolderForFile($fullPath)
    {
        $filenameInfo = pathinfo($fullPath);
        $folder = data_get($filenameInfo, 'dirname', './');

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

    public static function message(string $string)
    {
        return '🧠  forget-db :: ' . $string;
    }
}