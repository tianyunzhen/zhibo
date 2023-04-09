<?php
//echo 1;die;
/**
 * $APP_NAME 统一入口
 */
require_once dirname(__FILE__) .'/init.php';
define('VENDOR',__DIR__ . '/../vendor/');
define('RUNTIME_PATH',__DIR__ . '/../Runtime/');
//装载你的接口
DI()->loader->addDirs('Appapi');

/** ---------------- 响应接口请求 ---------------- **/
$loader->addDirs('Library');
// 其他代码....
//显式初始化，并调用分发
DI()->fastRoute = new FastRoute_Lite();
DI()->fastRoute->dispatch();
/** ------------- 响应接口请求 ---------------- **/
$api = new PhalApi();
$rs = $api->response();
$rs->output();

