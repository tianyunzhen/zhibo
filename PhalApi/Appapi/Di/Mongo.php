<?php

class Di_Mongo{
    private static $_instance = null; //实例对象

    public static function getInstance(){
        if(self::$_instance === null){
            require_once VENDOR . 'autoload.php';
            $config          = DI()->config->get('mongo');
            $username        = $config['username'];
            $pass            = $config['password'];
            $host            = $config['host'];
            $port            = $config['port'];
            $database        = $config['database'];
            $url             = "mongodb://{$username}:{$pass}@{$host}:{$port}";
            self::$_instance = (new \MongoDB\Client($url))->$database;
        }
        return self::$_instance;
    }
}