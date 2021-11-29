<?php

namespace Fowitech\Localization;

use Illuminate\Support\ServiceProvider;

class LocalizationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('localization.php'),
        ], 'config');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['modules.handler', 'modules'];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $packageConfigFile = __DIR__.'/../config/config.php';

        $this->mergeConfigFrom(
            $packageConfigFile, 'localization'
        );

        $this->registerBindings();

        $this->registerCommands();
    }

    /**
     * Registers app bindings and aliases.
     */
    protected function registerBindings()
    {
        $this->app->singleton(Localization::class, function () {
            return new Localization();
        });

        $this->app->alias(Localization::class, 'localization');
    }

    /**
     * Registers route caching commands.
     */
    protected function registerCommands()
    {
        $this->app->singleton('localizationroutecache.cache', Commands\RouteTranslationsCacheCommand::class);
        $this->app->singleton('localizationroutecache.clear', Commands\RouteTranslationsClearCommand::class);
        $this->app->singleton('localizationroutecache.list', Commands\RouteTranslationsListCommand::class);

        $this->commands([
            'localizationroutecache.cache',
            'localizationroutecache.clear',
            'localizationroutecache.list',
        ]);
    }
}
