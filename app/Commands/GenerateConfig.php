<?php

namespace App\Commands;

use App\Services\UtilityService;

use LaravelZero\Framework\Commands\Command;

/**
 * Class GenerateConfig
 * @package App\Commands
 */
class GenerateConfig extends Command
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'new';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generates a yaml config file for filling out';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $filename = $this->ask('What do you want the config to be called? (skip the .yml)', 'forgetdb.yml');
        $path = $this->ask('Where do you want to save the config file to? (don\'t include the file name)', './');

        $filename = UtilityService::cleanseFilename($filename);
        $outputFilePath = UtilityService::cleansePath($path) . '/' . $filename;

        if (file_exists($outputFilePath)) {
            if (!$this->confirm(realpath($outputFilePath) . ' already exists, do you want to overwrite it?')) {
                $this->error(UtilityService::message('Bye then!'));
                exit(0);
            }
        }

        UtilityService::createFolderForFile($outputFilePath);

        file_put_contents($outputFilePath, UtilityService::stubConfig());

        $this->info(UtilityService::message(realpath($outputFilePath) . ' has been created for you to configure.'));
    }

}
