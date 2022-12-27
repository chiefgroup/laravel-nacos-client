<?php

namespace Chiefgroup\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{

    protected $loadEnvironmentVariables = true;

    protected function getPackageProviders($app)
    {
        return [
            'ChiefGroup\NacosClient\Providers\NacosServiceProvider',
        ];
    }

//    public static function applicationBasePath()
//    {
//        return __DIR__.'/../';
//    }
}