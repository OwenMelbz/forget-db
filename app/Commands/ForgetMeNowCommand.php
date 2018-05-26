<?php

namespace App\Commands;

use App\Services\DatabaseService;
use App\Services\ForgetDbService;
use App\Services\UtilityService;
use LaravelZero\Framework\Commands\Command;

/**
 * Class ForgetMeNowCommand
 * @package App\Commands
 */
class ForgetMeNowCommand extends Command
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'forget { config : Path to a config file }';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Runs forget-db based off a config file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $configPath = realpath($this->argument('config'));

        if (!$configPath || ($configPath && !file_exists($configPath))) {
            $this->notify('Whoops', 'Looks like something didn\'t go to plan...');
            $this->fail('Cannot find config at ' . $this->argument('config'));
            exit(0);
        }

        try {
            $config = UtilityService::parseConfig($configPath);
            $forgetdb = new ForgetDbService($config);
        } catch (\Exception $e) {
            $this->notify('Whoops', 'Looks like something didn\'t go to plan...');
            $this->fail($e->getMessage());
            exit(0);
        }

        $this->message(sprintf('%d %s configured to forget.', count($config), str_plural('table', count($config))));

        $connected = false;

        while (!$connected) {
            try {
                $connected = (new DatabaseService(
                    $this->getDatabaseConfig()
                ))->testConnection();
            } catch (\Exception $e) {
                $this->notify('Whoops', '🐳 Get the fail whale out...');
                $this->fail($e->getMessage());
                $this->line('');
                $this->fail('😒 let\'s try again shall we?');
            }
        }

        $this->message('database connection established, we have lift off! 🚀');

        if (!$this->confirm('Are you ready to start? This is your last chance to bail 💦', !config('app.production'))) {
            $this->notify('Whoops', 'Bailing! 💦💦💦');
            $this->fail('Bailing! 💦💦💦');
            exit(0);
        }

        try {
            $forgetdb->forget($this); // Right, this is where shit goes down and all the heavy lifting now starts!
        } catch (\Exception $e) {
            $this->notify('Whoops', 'Looks like something didn\'t go to plan...');
            $this->fail($e->getMessage());
            exit(0);
        }

        $this->notify('Who are you again?', 'We seem to have forgotten everything');
        $this->line('');
        $this->warn('🎉⭐🍕⚡🎉⭐🍕⚡🎉 FINISHED ⭐🍕⚡🎉⭐🍕⚡🎉⭐🍕⚡');
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

    /**
     * This starts a mini wizard which collects the users
     * database details for passing to the DatabaseService.
     *
     * @return array
     */
    private function getDatabaseConfig(): array
    {
        $driver = $this->choice('Which database driver do you need?', ['mysql', 'pgsql', 'sqlite', 'sqlsrv'], 0);

        $this->message('Please provide us your configuration options for ' . $driver);

        switch ($driver) {
            case 'pgsql':
                $options = DatabaseService::optionsForPgSql();
                break;
            case 'sqlite':
                $options = DatabaseService::optionsForSqlite();
                break;
            case 'sqlsrv':
                $options = DatabaseService::optionsForSqlSrv();
                break;
            default:
                $options = DatabaseService::optionsForMySql();
                break;
        }

        $usersConfiguration = [];
        $confirmTable = [];

        foreach ($options as $option => $default) {
            if ($option == 'driver') {
                continue;
            }

            if ($option === 'password') {
                $usersConfiguration[$option] = $this->secret('Database:: ' . $option, $default);
            } else {
                $usersConfiguration[$option] = $this->ask('Database:: ' . $option, $default);
            }

            $confirmTable[] = [
                'option' => $option,
                'value' => $option === 'password' ? '********' : $usersConfiguration[$option],
            ];
        }

        $this->table(['option', 'value'], $confirmTable);

        while (!$confirmed = $this->confirm('Do the above settings look correct?', !config('app.production'))) {
            return $this->getDatabaseConfig();
        }

        return $usersConfiguration;
    }

}
