<?php

namespace Donjan\NacosClient;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class NacosClient
{

    private $baseUrl = '';

    private $guzzle = '';

    public function __construct($base_url, $port, $guzzle)
    {
        $this->baseUrl = $base_url.'::'.$port;
        $this->guzzle = $guzzle;
    }

    /**
     * 获取配置信息
     * @param string $dataId
     * @param string $group
     * @param string|null $tenant
     * @return string
     */
    public function get(string $dataId, string $group, ?string $tenant = null)
    {
        return $response = $this->request('GET', '/nacos/v1/cs/configs', [
            RequestOptions::QUERY => $this->filter([
                'dataId' => $dataId,
                'group'  => $group,
                'tenant' => $tenant,
            ]),
        ]);
    }

    /**
     * 数据处理
     * @param array $input
     * @return array
     */
    protected function filter(array $input): array
    {
        $result = [];
        foreach ($input as $key => $value) {
            if ($value !== null) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * 发送请求
     * @param $method
     * @param $uri
     * @param array $options
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri, array $options = [])
    {
        $response = $this->client()->request($method, $uri, $options);

        return $response->getBody()->getContents();
    }

    /**
     * 配置相关信息
     * @return Client
     */
    public function client(): Client
    {
        $config = array_merge($this->guzzle, [
            'base_uri' => $this->baseUrl,
        ]);

        return new Client($config);
    }

}
