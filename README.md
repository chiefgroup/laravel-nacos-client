# laravel-nacos-client

## 介绍
该包主要用于拉取 Nacos 配置

## 使用
1.执行 composer require chiefgroup/laravel-nacos-client 拉取composer包到项目

2.执行 php artisan vendor:publish 生成配置文件
php artisan vendor:publish --provider="Chiefgroup\NacosClient\Providers\NacosServiceProvider"

3.拉去配置中心配置：php artisan nacos:get-config


## testbench
- Laravel6 用4.X https://github.com/orchestral/testbench/tree/v4.18.0
- Laravel8 用6.X https://packages.tools/testbench/getting-started/introduction.html