<?php

namespace ChiefGroup\NacosClient\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use ChiefGroup\NacosClient\NacosClient;
use Illuminate\Support\Facades\Artisan;

class GetConfig extends Command
{

    protected $signature = 'nacos:get-config';

    protected $description = 'get config from aliyun nacos';

    public function handle(Filesystem $filesystem)
    {
        Artisan::call('config:clear');
        $nacosClient = new NacosClient(config('nacos.base_url'), config('nacos.url_port'), config('nacos.guzzle.config'));
        $dataId    = config('nacos.data_id');
        $group     = config('nacos.group');
        $namespace = config('nacos.namespace');

        $config = $nacosClient->get($dataId, $group, $namespace);

        if (!empty($config)) {
            $filesystem->put(config('nacos.path'), $config);

            $this->info('nacos.success');
            Artisan::call('config:cache');
        }
    }

}
