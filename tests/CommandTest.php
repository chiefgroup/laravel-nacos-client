<?php

namespace Chiefgroup\Tests;

class CommandTest extends TestCase
{
//    public function setUp(): void
//    {
//        parent::setUp();
//
//        config(['nacos.base_url' => '']);
//        config(['nacos.url_port' => '8848']);
//        config(['nacos.guzzle.config' => [
//
//            'config' => [
//                'headers' => [
//                    'charset' => 'UTF-8',
//                ],
//            ]
//        ]]);
//
//        config(['nacos.data_id' => '']);
//        config(['nacos.group' => '']);
//        config(['nacos.namespace' => 'ada556ed-93f8-4bdf-83b6-c653108d237e']);
//        config(['nacos.path'=>  __dir__ . '/../' . 'nacos.json']);
//    }

    public function testAbc()
    {
        $this->artisan('nacos:get-config')->assertExitCode(0);
        $this->assertTrue(true);
    }
}