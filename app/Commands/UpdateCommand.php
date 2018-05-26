<?php

namespace App\Commands;

use Exception;
use App\Services\UpdateService;
use App\Services\UtilityService;
use LaravelZero\Framework\Commands\Command;

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
    protected $description = 'Will attempt to update forget-db!';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $updater = new UpdateService();

        if (!$updater->shouldUpdate()) {
            $this->fail('You should only be updating the self contained .phar version!');
            exit(0);
        }

        if (!$updateAvailable = $updater->searchForUpdates()) {
            $this->fail('Looks like you\'re a-okay kid, no updates needed!');
            exit(0);
        }

        $newVersion = data_get($updateAvailable, 'release.tag_name');

        $this->message(
            sprintf('Found v%s, trying to update from v%s', $newVersion, config('app.version'))
        );

        try {
            $this->message('Downloading... This might take a moment!');
            $this->message('Fun fact whilst you wait, you are the ' . UtilityService::ordinal(data_get($updateAvailable, 'download.download_count')) . ' person to update.');
            $updater->updateTo($updateAvailable);
        } catch (Exception $e) {
            $this->notify('Whoops', 'Looks like something didn\'t go to plan...');
            $this->fail($e->getMessage());
            exit(0);
        }

        $this->line('');
        $this->warn('🎉⭐🍕⚡🎉⭐🍕⚡🎉 UPDATED TO ' . $newVersion . ' ⭐🍕⚡🎉⭐🍕⚡🎉⭐🍕⚡');
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
