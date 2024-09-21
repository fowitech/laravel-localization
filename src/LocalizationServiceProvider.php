<?php

namespace Fowitech\Localization;

use Fowitech\Localization\Middleware\LocaleCookieRedirect;
use Fowitech\Localization\Middleware\LocaleSessionRedirect;
use Fowitech\Localization\Middleware\LocalizationRedirectFilter;
use Fowitech\Localization\Middleware\LocalizationRoutes;
use Fowitech\Localization\Middleware\LocalizationViewPath;
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
        if ($this->app->runningInConsole()) {
            $this->publishResources();
        }

        $this->app['router']->aliasMiddleware('localize', LocalizationRoutes::class);
        $this->app['router']->aliasMiddleware('localizationRedirect', LocalizationRedirectFilter::class);
        $this->app['router']->aliasMiddleware('localeSessionRedirect', LocaleSessionRedirect::class);
        $this->app['router']->aliasMiddleware('localeCookieRedirect', LocaleCookieRedirect::class);
        $this->app['router']->aliasMiddleware('localeViewPath', LocalizationViewPath::class);
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

    protected function publishResources()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('localization.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../flags' => public_path('vendor/localization/flags'),
        ], 'public');

       /* $this->publishes([
            __DIR__ . '/../database/migrations/2021_11_28_214908_create_languages_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_languages_table.php'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../database/seeders/LanguageTableSeeder.php' => database_path('seeders/LanguageTableSeeder.php'),
        ], 'seeders');*/
    }

    /**
     * Registers route caching commands.
     */
    protected function registerCommands()
    {
        $this->app->singleton('route.trans.cache', Commands\RouteTranslationsCacheCommand::class);
        $this->app->singleton('route.trans.clear', Commands\RouteTranslationsClearCommand::class);
        $this->app->singleton('route.trans.list', Commands\RouteTranslationsListCommand::class);

        $this->commands([
            'route.trans.cache',
            'route.trans.clear',
            'route.trans.list',
        ]);
    }
}
