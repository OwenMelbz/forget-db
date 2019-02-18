<?php

namespace App\Commands;

use Exception;
use App\Services\UpdateService;
use App\Services\UtilityService;
use LaravelZero\Framework\Commands\Command;

/**
 * Class UpdateCommand
 * @package App\Commands
 */
class UpdateCommand extends Command
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'update';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Update forget-db to latest release';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $updater = new UpdateService();

        if (!$updater->shouldUpdate()) {
            $this->fail('This command can only be used to update the self contained .phar version, exiting...');
            exit(1);
        }

        if (!$updateAvailable = $updater->searchForUpdates()) {
            $this->fail('Already on latest version.');
            exit(0);
        }

        $newVersion = data_get($updateAvailable, 'release.tag_name');

        $this->message(
            sprintf('Found v%s, trying to update from v%s', $newVersion, config('app.version'))
        );

        try {
            $this->message('Downloading latest version...');

            $updatePath = $updater->updateTo($updateAvailable);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
            exit(1);
        }

        $this->line('');
        $this->line('Previous version has been backed up as ' . $updatePath . '.backup' );
        $this->line('');
        $this->message('Successfully updated to version ' . $newVersion );
        exit;
    }

    /**
     * Just an abstraction to apply branding to the info messages.
     *
     * @param string $string
     */
    public function message(string $string): void
    {
        $this->info(UtilityService::message($string));
    }

    /**
     * Just an abstraction to apply branding to the warn messages.
     *
     * @param string $string
     */
    public function fail(string $string): void
    {
        $this->warn(UtilityService::message($string));
    }

}
