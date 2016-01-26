<?php namespace DavinBao\LaravelXunSearch;

use DavinBao\LaravelXunSearch\Model\Config as ModelsConfig;
use Config;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/config/config.php', config_path('laravel-xun-search'));
    }

    public function register()
    {
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('laravel-xun-search.php'),
        ]);

        $this->app->singleton('laravel-xun-search.project', function () {
            return Config::get('laravel-xun-search.project');
        });

        $this->app->singleton('search', function ($app) {
            return new Search(
                $app['laravel-xun-search.project'],
                $app['laravel-xun-search.models.config']
            );
        });

        $this->app->singleton('laravel-xun-search.models.config', function ($app) {
            return new ModelsConfig(
                Config::get('laravel-xun-search.index.models'),
                $app->make('DavinBao\LaravelXunSearch\Model\Factory')
            );
        });

        $this->app->singleton('command.search.rebuild', function () {
            return new Console\RebuildCommand;
        });

        $this->app->singleton('command.search.clear', function () {
            return new Console\ClearCommand;
        });

        $this->commands(array('command.search.rebuild', 'command.search.clear'));
    }
}
