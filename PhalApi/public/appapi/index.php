<?php
//echo 1;die;
/**
 * Appapi 统一入口
 */
require_once dirname(__FILE__) . '/../init.php';
define('VENDOR',__DIR__ . '/../../vendor/');
define('RUNTIME_PATHS',__DIR__ . '/../../Runtime/');
//装载你的接口
DI()->loader->addDirs('Appapi');

/** ---------------- 响应接口请求 ---------------- **/
$loader->addDirs('Library');
// 其他代码....
//显式初始化，并调用分发
DI()->fastRoute = new FastRoute_Lite();
DI()->fastRoute->dispatch();
$api = new PhalApi();
$rs = $api->response();
$rs->output();

