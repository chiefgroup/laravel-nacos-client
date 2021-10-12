<?php
    return [
        //请求域名
        'base_url' => env('ALIYUN_NACOS_HOST'),
        //域名端口
        'url_port' => env('ALIYUN_NACOS_PORT'),
        //命名空间
        'namespace'=> env('ALIYUN_NACOS_NAMESPACE'),
        //ID值
        'data_id'  => env('ALIYUN_NACOS_DATA_ID'),
        //分组
        'group'    => env('ALIYUN_NACOS_GROUP'),
        //相关配置信息
        'guzzle'   => [
            'config' => [
                'headers' => [
                    'charset' => 'UTF-8',
                ],
            ],
        ],
        //用户名
        'username' => env('ALIYUN_NACOS_USERNAME'),
        //密码
        'password' => env('ALIYUN_NACOS_PASSWORD'),
        //文件存储路径
        'path'     => base_path() . DIRECTORY_SEPARATOR . 'nacos.json'
    ];