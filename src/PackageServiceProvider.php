<?php

namespace AmRo045\LaravelHashId;

use AmRo045\LaravelHashId\Console\Commands\InstallCommand;
use Hashids\Hashids;
use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/app.php', 'laravelhashid');

        $this->app->singleton(Hashids::class, fn() => new Hashids(
            salt: config('laravelhashid.salt'),
            minHashLength: config('laravelhashid.min_hash_length'),
            alphabet: config('laravelhashid.alphabet')
        ));
    }
    
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole())
        {
            $this->commands([
                InstallCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/app.php' => config_path('laravelhashid.php'),
            ], 'laravelhashid-config');
        }
    }
}