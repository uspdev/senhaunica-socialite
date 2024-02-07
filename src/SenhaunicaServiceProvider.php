<?php

namespace Uspdev\SenhaunicaSocialite;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Uspdev\SenhaunicaSocialite\Providers\AuthServiceProvider;
use Uspdev\SenhaunicaSocialite\Providers\EventServiceProvider;

class SenhaunicaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/services.php', 'services');
        $this->mergeConfigFromReversed(__DIR__ . '/../config/auth.guards.php', 'auth.guards');
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'senhaunica');

        // registra eventos
        $this->app->register(EventServiceProvider::class);

        // registra gates
        if (config('senhaunica.permission')) {
            $this->app->register(AuthServiceProvider::class);
        }
    }

    /**
     * Copiado de Illuminate\Support\ServiceProvider mas alterando a ordem do array
     */
    public function mergeConfigFromReversed($path, $key)
    {
        if (!($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app->make('config');

            $config->set($key, array_merge(
                $config->get($key, []), require $path
            ));
        }
    }

    public function boot()
    {
        // registra rotas se habilitado no config
        if (config('senhaunica.routes')) {
            $this->registerRoutes();
        }

        // Views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'senhaunica');

        if ($this->app->runningInConsole()) {
            // exporta views
            // php artisan vendor:publish --provider="Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider" --tag="views"
            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/senhaunica'),
            ], 'views');

            // exportar configuracao
            // php artisan vendor:publish --provider="Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider" --tag="config"
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('senhaunica.php'),
            ], 'config');

            // Export the migration
            // php artisan vendor:publish --provider="Uspdev\SenhaunicaSocialite\SenhaunicaServiceProvider" --tag="migrations"
            $this->publishes([
                __DIR__ . '/../database/migrations/update_senhaunica_users_table.php.stub' => $this->getMigrationFilename(),
            ], 'migrations');
        }

        // não sei se deve ficar aqui mas assim funciona
        Paginator::useBootstrap();
    }

    protected function getMigrationFilename()
    {
        $match = glob(database_path('migrations/' . '*_update_senhaunica_users_table.php'));
        if ($match) {
            return $match[0];
        } else {
            return database_path('migrations/' . date('Y_m_d_His', time()) . '_update_senhaunica_users_table.php');
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
