<?php

namespace Donjan\AcmClient\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Donjan\AcmClient\Commands\GetConfig;

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

}
