<?php
require_once VENDOR . 'autoload.php';

class Elast_Base{
    protected $connect;
    protected $index;
    protected $type = '';

    public function __construct(){
        $this->connect = $this->_connect();
    }

    public function _connect(){
        $config        = [
            "host"    => '',
            "port"    => '',
            'user'    => '',
            'pass'    => '',
            'retries' => 3,
        ];
        $singleHandler = \Elasticsearch\ClientBuilder::singleHandler();
        $elasticSearch = \Elasticsearch\ClientBuilder::create()
            ->setHosts([$config])
            ->setHandler($singleHandler)
//            ->setConnectionPool('\Elasticsearch\ConnectionPool\SniffingConnectionPool', [])
//                ->setConnectionPool('\Elasticsearch\ConnectionPool\StaticConnectionPool', [])
            ->setRetries($config['retries'])
            ->build();
        return $elasticSearch;
    }

    public function _search($param){
        $data['index'] = $this->index;
        $data['type']  = $this->type;
        $data['body']  = $param;

        return $this->connect->search($data);
    }

    protected function turn($body, $data){
        foreach($data as $k => $v){
            $body = str_replace($k, $v, $body);
        }
        return $body;
    }

    protected function turn_page($page, $total){
        return [$page > 0 ? ($page - 1) * $total : 0, $total];
    }
}