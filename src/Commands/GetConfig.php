<?php

namespace ChiefGroup\NacosClient\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use ChiefGroup\NacosClient\NacosClient;

class GetConfig extends Command
{

    protected $signature = 'nacos:get-config';

    protected $description = 'get config from nacos';

    public function handle(Filesystem $filesystem)
    {
        $lockPath = storage_path('framework/nacos-get-config.lock');
        $filesystem->ensureDirectoryExists(dirname($lockPath));
        $lockHandle = fopen($lockPath, 'c');

        if ($lockHandle === false || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
            $this->warn('nacos:get-config is already running, skipped.');
            return 0;
        }

        try {
            return $this->refresh($filesystem);
        } finally {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
        }
    }

    protected function refresh(Filesystem $filesystem)
    {
        $nacosClient = $this->laravel->make(NacosClient::class);

        try {
            $config = $nacosClient->get(config('nacos.data_id'), config('nacos.group'), config('nacos.namespace'));
        } catch (\Throwable $e) {
            $this->warn('Failed to fetch config from nacos: ' . $e->getMessage());
            return 1;
        }

        if (empty($config)) {
            $this->warn('Nacos returned empty config, keeping existing cache.');
            return 1;
        }

        $configArr = json_decode($config, true);
        if (!is_array($configArr)) {
            $this->warn('Nacos returned invalid JSON, keeping existing cache.');
            return 1;
        }

        // 同目录 tmp + rename 原子替换，读方永远看到完整的新文件或旧文件
        $path = config('nacos.path');
        $tmpPath = $path . '.tmp';
        $filesystem->put($tmpPath, $config);
        rename($tmpPath, $path);

        config($configArr);

        $cachePath = $this->laravel->getCachedConfigPath();
        $filesystem->ensureDirectoryExists(dirname($cachePath));
        $tmpCachePath = $cachePath . '.tmp';
        $filesystem->put($tmpCachePath, '<?php return ' . var_export($this->laravel['config']->all(), true) . ';' . PHP_EOL);
        rename($tmpCachePath, $cachePath);

        $this->info('Nacos config refreshed.');
        return 0;
    }

}