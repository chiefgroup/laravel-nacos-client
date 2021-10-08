<?php
    return [
        'base_url' => env('ALIYUN_NACOS_HOST', 'mse-dfde8f34-p.nacos-ans.mse.aliyuncs.com:8848'),
        'namespace'=> env('ALIYUN_NACOS_NAMESPACE', 'ada556ed-93f8-4bdf-83b6-c653108d237e'),
        'data_id'  => env('ALIYUN_NACOS_DATA_ID', 'adj'),
        'group'    => env('ALIYUN_NACOS_GROUP', 'adj'),
        'guzzle'   => [
            'config' => [
                'headers' => [
                    'charset' => 'UTF-8',
                ],
            ],
        ],
        'path'     => base_path() . DIRECTORY_SEPARATOR . 'nacos.json'
    ];