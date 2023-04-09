<?php
/**
 * Created by PhpStorm.
 * User: fengpeng
 * Date: 2020/9/14
 * Time: 14:40
 */

namespace app\common;


use Elasticsearch\ClientBuilder;
use think\Config;

class Search
{
    private $client;
    public function __construct()
    {
        $params = [
            'host' => Config::get('search.host'),
            'port' => Config::get('search.port'),
            'user' => Config::get('search.user'),
            'pass' => Config::get('search.pass'),
            'retries' => Config::get('search.retries'),
        ];
        $singleHandler = ClientBuilder::singleHandler();
        $this->client = ClientBuilder::create()->setHosts([$params])->setHandler($singleHandler)->setSSLVerification(false)->build();
    }

    public function index($index_name, $body)
    {
        $params = [
            'index' => $index_name,
            'body' => $body
        ];
        return $this->client->search($params);
    }
}