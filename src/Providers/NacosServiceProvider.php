<?php

namespace ChiefGroup\NacosClient\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use ChiefGroup\NacosClient\Commands\GetConfig;

class NacosServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Filesystem $filesystem)
    {
        $this->publishes([__DIR__ . '/../../config/nacos.php' => config_path('nacos.php')], 'nacos');

        if ($this->app->runningInConsole()) {
            $this->commands([
                GetConfig::class
            ]);
        }

        $file = config('nacos.path');
        if ($filesystem->exists($file)) {
            $configArr = json_decode($filesystem->get($file), true);
            config($configArr);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/nacos.php', 'nacos'
        );
    }

}
