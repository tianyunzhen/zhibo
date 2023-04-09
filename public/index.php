<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | 来来1号店-二次开发-QQ125050230
// +----------------------------------------------------------------------
namespace think;
//phpinfo();die;

// [ 入口文件 ]

// 调试模式开关
define('APP_DEBUG', true);

// 定义CMF根目录,可更改此目录
define('CMF_ROOT', dirname(__DIR__) . '/');

// 定义CMF数据目录,可更改此目录
define('CMF_DATA', CMF_ROOT . 'data/');

// 定义应用目录
define('APP_PATH', CMF_ROOT . 'app/');

// 定义网站入口目录
define('WEB_ROOT', __DIR__ . '/');
define('DB_FIELD_CACHE',false);
define('HTML_CACHE_ON',false);
define('TMPL_CACHE_ON', false);
// 加载基础文件
require CMF_ROOT . 'vendor/thinkphp/base.php';
// 执行应用并响应
Container::get('app', [APP_PATH])->run()->send();
