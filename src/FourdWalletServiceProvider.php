<?php

namespace Falconeri\FourdWallet;

use Illuminate\Support\ServiceProvider;

class FourdWalletServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('fourd-wallet.php'),
            ], 'config');
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            dirname(__DIR__) . '/database/migrations/' => database_path('migrations'),
        ], 'fourd-wallet-migrations');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'fourd-wallet');

        // Register the main class to use with the facade
        $this->app->singleton('fourd-wallet', function () {
            return new FourdWallet;
        });
    }
}
