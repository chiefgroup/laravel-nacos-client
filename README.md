# laravel-nacos-client

## 介绍
该包主要用于拉取阿里云 nacos 配置中心配置信息

## 使用
1.执行 composer require chiefgroup/laravel-nacos-client 拉取composer包到项目

2.执行 php artisan vendor:publish 生成配置文件
php artisan vendor:publish --provider="Chiefgroup\NacosClient\Providers\NacosServiceProvider"

3.拉去配置中心配置：php artisan nacos:get-config