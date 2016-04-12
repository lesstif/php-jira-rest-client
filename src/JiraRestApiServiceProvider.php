<?php

namespace JiraRestApi;

use Illuminate\Support\ServiceProvider;
use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Configuration\DotEnvConfiguration;

class JiraRestApiServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $this->app->bind(ConfigurationInterface::class, function () {
            return new DotEnvConfiguration(base_path());
        });
    }
}
