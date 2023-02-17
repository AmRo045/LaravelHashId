<?php

namespace AmRo045\LaravelHashId\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravelhashid:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the AmRo045/LaravelHashId package';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Installing the AmRo045/LaravelHashId package...');

        if ($this->configExists()) {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration(force: true);
            } else {
                $this->info('Existing configuration was not overwritten.');
            }
        } else {
            $this->publishConfiguration();
        }

        return 0;
    }

    private function configExists(): bool
    {
        return File::exists(config_path("laravelhashid.php"));
    }

    private function shouldOverwriteConfig(): bool
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration($force = false)
    {
        $params = [
            '--provider' => "AmRo045\LaravelHashId\PackageServiceProvider",
            '--tag' => "laravelhashid-config"
        ];

        if ($force === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }
}
