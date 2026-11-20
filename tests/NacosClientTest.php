<?php

namespace Chiefgroup\Tests;

use ChiefGroup\NacosClient\NacosClient;

class NacosClientTest extends TestCase
{
    public function test_client_applies_default_timeouts()
    {
        $client = (new NacosClient('http://127.0.0.1', '8848', []))->client();

        $this->assertSame(3, $client->getConfig('connect_timeout'));
        $this->assertSame(10, $client->getConfig('timeout'));
    }

    public function test_user_guzzle_config_overrides_default_timeouts()
    {
        $client = (new NacosClient('http://127.0.0.1', '8848', [
            'connect_timeout' => 1,
            'timeout' => 5,
        ]))->client();

        $this->assertSame(1, $client->getConfig('connect_timeout'));
        $this->assertSame(5, $client->getConfig('timeout'));
    }
}