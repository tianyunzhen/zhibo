<?php
/**
 * 统一初始化
 */
/* error_reporting(E_ALL);
ini_set('display_errors','On'); */
/** ---------------- 根目录定义，自动加载 ---------------- **/
date_default_timezone_set('Asia/Shanghai');
defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . '/..');

require_once API_ROOT . '/PhalApi/PhalApi.php';
$loader = new PhalApi_Loader(API_ROOT, 'Library');

/** ---------------- 注册&初始化 基本服务组件 ---------------- **/

//自动加载
DI()->loader = $loader;

//配置
DI()->config = new PhalApi_Config_File(API_ROOT . '/Config');

//调试模式，$_GET['__debug__']可自行改名
//DI()->debug = !empty($_GET['__debug__']) ? true : DI()->config->get('sys.debug');
DI()->debug = false;

//日记纪录
DI()->logger = new PhalApi_Logger_File(API_ROOT . '/Runtime', PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);

//数据操作 - 基于NotORM，$_GET['__sql__']可自行改名
//DI()->notorm = new PhalApi_DB_NotORM(DI()->config->get('dbs'), !empty($_GET['__sql__']));
DI()->notorm = new PhalApi_DB_NotORM(DI()->config->get('dbs'), false);

// json中文显示不被转码，方便查看
//DI()->response = new PhalApi_Response_Json(JSON_UNESCAPED_UNICODE);

//翻译语言包设定
SL('zh_cn');

/** ---------------- 定制注册 可选服务组件 ---------------- **/

require_once API_ROOT . '/Common/functions.php';
if(!DI()->redis){
    DI()->redis=connectionRedis();
}

/**
//签名验证服务
DI()->filter = 'PhalApi_Filter_SimpleMD5';
 */

/**
//缓存 - Memcache/Memcached
DI()->cache = function () {
    return new PhalApi_Cache_Memcache(DI()->config->get('sys.mc'));
};
 */

//支持JsonP的返回
if (!empty($_GET['callback'])) {
    DI()->response = new PhalApi_Response_JsonP($_GET['callback']);
}
   /* 七牛上传 */
DI()->qiniu = new Qiniu_Lite();
 
    /* 本地/云 上传 */
DI()->ucloud = new UCloud_Lite();

 /* 创蓝闪验 */
DI()->flash = new Flash_Lite();

//DI()->response->addHeaders('Access-Control-Allow-Origin', '*');

DI()->response->addHeaders("Access-Control-Allow-Origin", "*");                                       // 这是允许访问所有域
DI()->response->addHeaders("Access-Control-Allow-Methods", "POST, GET"); //服务器支持的所有跨域请求的方法,为了避免浏览次请求的多次'预检'请求
   // header的类型
DI()->response->addHeaders("Access-Control-Allow-Headers", "Authorization, Content-Length, X-CSRF-Token, Token,session,X_Requested_With,Accept, Origin, Host, Connection, Accept-Encoding, Accept-Language,DNT, X-CustomHeader, Keep-Alive, UserName-Agent, X-Requested-With, If-Modified-Since, Cache-Control, Content-Type, Pragma,x-token, XHR");
   // 允许跨域设置                                                                                                      可以返回其他子段
DI()->response->addHeaders("Access-Control-Expose-Headers", "Content-Length, Access-Control-Allow-Origin, Access-Control-Allow-Headers,Cache-Control,Content-Language,Content-Type,Expires,Last-Modified,Pragma,FooBar");// 跨域关键设置 让浏览器可以解析
DI()->response->addHeaders("Access-Control-Max-Age", "172800");                                                                                                                                                   // 缓存请求信息 单位为秒
DI()->response->addHeaders("Access-Control-Allow-Credentials", "true");