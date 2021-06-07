<?php

namespace Uspdev\SenhaunicaSocialite;

use Illuminate\Support\ServiceProvider;
use Uspdev\SenhaunicaSocialite\Providers\AuthServiceProvider;
use Uspdev\SenhaunicaSocialite\Providers\EventServiceProvider;
use Illuminate\Support\Facades\Route;

class SenhaunicaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/services.php', 'services');
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'senhaunica');

        // registra eventos
        $this->app->register(EventServiceProvider::class);

        // registra gates
        $this->app->register(AuthServiceProvider::class);
    }

    public function boot()
    {

        // registra rotas
        $this->registerRoutes();
        //$this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            // exportar configuracao
            // php artisan vendor:publish --provider="Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider" --tag="config"
            //$this->publishes([__DIR__ . '/../config/config.php' => config_path('senhaunica.php')], 'config');

            // Export the migration
            // php artisan vendor:publish --provider="Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider" --tag="migrations"
            if (!class_exists('UpdateUsersTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/update_users_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_update_users_table.php'),
                ], 'migrations');
            }
        }

    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => config('senhaunica.prefix'),
            'middleware' => config('senhaunica.middleware'),
        ];
    }

}
