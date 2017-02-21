<?php

namespace LeeOvery\Flasher;

use Illuminate\Support\ServiceProvider;

class FlasherServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // use this if your package has views
        $this->loadViewsFrom(
            realpath(__DIR__ . '/resources/views'), 'flasher'
        );

        $this->publishes([
            __DIR__ . '/resources/views' => base_path('resources/views/vendor/flasher'),
        ], 'flasher-views');

        // use this if your package needs a config file
        $this->publishes([
            $this->configPath() => config_path('flasher.php'),
        ], 'flasher-config');

        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(
            $this->configPath(), 'flasher'
        );
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/config.php';
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            SessionStore::class,
            LaravelSessionStore::class
        );

        $this->app->bind(
            CacheStore::class,
            LaravelCacheStore::class
        );

        $this->app->bind('flasher', function ($app) {
            return new FlasherNotifier(
                $app->make(SessionStore::class),
                $app->make(CacheStore::class),
                $app->make('config')->get('flasher')
            );
        });
    }
}
