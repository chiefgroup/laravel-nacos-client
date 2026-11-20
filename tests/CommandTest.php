<?php

namespace Chiefgroup\Tests;

use ChiefGroup\NacosClient\NacosClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class CommandTest extends TestCase
{
    protected $lockHandle;

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('nacos.base_url', 'http://127.0.0.1');
        $app['config']->set('nacos.url_port', '8848');
        $app['config']->set('nacos.data_id', 'test-data-id');
        $app['config']->set('nacos.group', 'test-group');
        $app['config']->set('nacos.path', $this->nacosPath());

        $this->cleanupFiles($app);
    }

    protected function tearDown(): void
    {
        if (is_resource($this->lockHandle)) {
            flock($this->lockHandle, LOCK_UN);
            fclose($this->lockHandle);
            $this->lockHandle = null;
        }

        $this->cleanupFiles($this->app);

        parent::tearDown();
    }

    public function test_success_writes_file_and_rebuilds_cache_with_new_values()
    {
        $this->mockNacos([new Response(200, [], '{"nacos_test":{"foo":"bar"}}')]);

        $this->artisan('nacos:get-config')->assertExitCode(0);

        $this->assertSame('{"nacos_test":{"foo":"bar"}}', file_get_contents($this->nacosPath()));
        $this->assertSame('bar', config('nacos_test.foo'));

        $cached = require $this->app->getCachedConfigPath();
        $this->assertSame('bar', $cached['nacos_test']['foo']);
    }

    public function test_fetch_failure_keeps_old_cache_and_file()
    {
        $this->seedOldState();
        $this->mockNacos([
            new ConnectException('connection timed out', new Request('GET', 'http://127.0.0.1:8848/nacos/v1/cs/configs')),
        ]);

        $this->artisan('nacos:get-config')->assertExitCode(1);

        $this->assertOldStateUntouched();
    }

    public function test_empty_response_keeps_old_cache_and_file()
    {
        $this->seedOldState();
        $this->mockNacos([new Response(200, [], '')]);

        $this->artisan('nacos:get-config')->assertExitCode(1);

        $this->assertOldStateUntouched();
    }

    public function test_invalid_json_keeps_old_cache_and_file()
    {
        $this->seedOldState();
        $this->mockNacos([new Response(200, [], 'not-json')]);

        $this->artisan('nacos:get-config')->assertExitCode(1);

        $this->assertOldStateUntouched();
    }

    public function test_skips_when_another_run_holds_the_lock()
    {
        $lockPath = storage_path('framework/nacos-get-config.lock');
        if (!is_dir(dirname($lockPath))) {
            mkdir(dirname($lockPath), 0777, true);
        }
        $this->lockHandle = fopen($lockPath, 'c');
        $this->assertTrue(flock($this->lockHandle, LOCK_EX | LOCK_NB));

        $this->mockNacos([new Response(200, [], '{"nacos_test":{"foo":"bar"}}')]);

        $this->artisan('nacos:get-config')->assertExitCode(0);

        $this->assertFileDoesNotExist($this->nacosPath());
        $this->assertFileDoesNotExist($this->app->getCachedConfigPath());
    }

    protected function mockNacos(array $responses): void
    {
        $this->app->instance(NacosClient::class, new NacosClient('http://127.0.0.1', '8848', [
            'handler' => HandlerStack::create(new MockHandler($responses)),
        ]));
    }

    protected function seedOldState(): void
    {
        file_put_contents($this->nacosPath(), '{"old":true}');
        file_put_contents($this->app->getCachedConfigPath(), '<?php return ["old" => true];');
    }

    protected function assertOldStateUntouched(): void
    {
        $this->assertSame('{"old":true}', file_get_contents($this->nacosPath()));
        $this->assertSame('<?php return ["old" => true];', file_get_contents($this->app->getCachedConfigPath()));
    }

    protected function nacosPath(): string
    {
        return sys_get_temp_dir() . '/nacos-client-test-' . getmypid() . '.json';
    }

    protected function cleanupFiles($app): void
    {
        $files = [$this->nacosPath(), $this->nacosPath() . '.tmp'];

        if ($app !== null) {
            $cachePath = $app->getCachedConfigPath();
            $files[] = $cachePath;
            $files[] = $cachePath . '.tmp';
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}