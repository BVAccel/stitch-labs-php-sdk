<?php 

namespace StitchLabs\FrameworkSupport\Laravel;

use Illuminate\Support\ServiceProvider;
use StitchLabs\StitchLabs;

class StitchLabsServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $config = __DIR__ . '/config/config.php';
        $this->mergeConfigFrom($config, 'stitchlabs');
        $this->publishes([$config => config_path('stitchlabs.php')]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('stitchlabs', function ($app) {

            return new Infusionsoft(config('stitchlabs'));

        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('stitchlabs');
    }
}