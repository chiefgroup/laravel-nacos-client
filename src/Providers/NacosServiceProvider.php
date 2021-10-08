<?php

namespace Donjan\NacosClient\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Donjan\NacosClient\Commands\GetConfig;

class NacosServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Filesystem $filesystem)
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/nacos.php' => config_path('nacos.php')], 'nacos');
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
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $provides = [
            'Donjan\NacosClient\Providers\NacosServiceProvider',
        ];

        foreach ($provides as $provider) {
            $this->app->register($provider);
        }
    }
}
