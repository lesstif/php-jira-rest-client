<?php
namespace JiraRestApi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

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
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register() {

        $this->app->bind(ConfigurationInterface::class, function(){
            return new DotEnvConfiguration(base_path());
        });
    }
}