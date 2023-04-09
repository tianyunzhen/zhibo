<?php
/**
 * 框架版本号
 */
defined('PHALAPI_VERSION') || define('PHALAPI_VERSION', '1.4.2');

/**
 * 项目根目录
 */
defined('PHALAPI_ROOT') || define('PHALAPI_ROOT', dirname(__FILE__));

require_once PHALAPI_ROOT . DIRECTORY_SEPARATOR . 'PhalApi' . DIRECTORY_SEPARATOR . 'Loader.php';
require_once PHALAPI_ROOT . DIRECTORY_SEPARATOR . 'PhalApi' . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 * PhalApi 应用类
 *
 * - 实现远程服务的响应、调用等操作
 *
 * <br>使用示例：<br>
 * ```
 * $api = new PhalApi();
 * $rs = $api->response();
 * $rs->output();
 * ```
 *
 * @package     PhalApi\Response
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-12-17
 */
class PhalApi{

    /**
     * 响应操作
     *
     * 通过工厂方法创建合适的控制器，然后调用指定的方法，最后返回格式化的数据。
     *
     * @return mixed 根据配置的或者手动设置的返回格式，将结果返回
     *  其结果包含以下元素：
     * ```
     *  array(
     *      'ret'   => 200,                //服务器响应状态
     *      'data'  => array(),            //正常并成功响应后，返回给客户端的数据
     *      'msg'   => '',                //错误提示信息
     *  );
     * ```
     */
    public function response(){
        $di        = DI();
        $rs        = $di->response;
        try{
//             接口调度与响应
            $requests = $di->request->getAll();
            $msg      = $di->request->getService();
            $sign     = $paramStr = '';
            $arr      = [
                'Boya_FengJiWen.aliPayNotify',
                'Boya_Live.BreakPull',
                'Boya_FengJiWen.wxPayNotify',
                'Boya_Live.getZombie',
                'Boya_Live.getLiveUserList',
                'Boya_Home.getDownApp',
                'Boya_Family.Anchors',
                'Boya_Ranking.countUser',
                'Boya_Crontab.test',
                'Boya_Ranking.effective',
                'Boya_Crontab.createBlackLive',
                'Boya_Crontab.goalList'
            ];
            if(!in_array($msg, $arr)){
                list($sign, $paramStr) = $this->validatorFilter($requests);
            }
            $Jdata = '---请求参数---:';
            foreach($requests as $k => $v){
                if($k == "file"){
                    continue;
                }
                $Jdata .= PHP_EOL . $k . ':' . $v;
            }
            $Jdata .= PHP_EOL . '---服务端签名串---:' . $paramStr . PHP_EOL;
            $Jdata .= "---服务端生成的sign---:" . $sign . PHP_EOL;

            $di->logger->info($msg, $Jdata);
            if(!in_array($msg, $arr) && $sign != ($requests['sign'] ?? '')){
//                throw new PhalApi_Exception_ValidatorError('签名不正确');
            }
            $api    = PhalApi_ApiFactory::generateService();
            $action = $di->request->getServiceAction();
            $data   = call_user_func([$api, $action]);
            $rs->setData($data);
            $Rdata = '---返回参数---:' . json_encode($data) . PHP_EOL;
            $di->logger->info($msg, $Rdata);

//
        }catch(PhalApi_Exception $ex){
            // 框架或项目可控的异常
            $rs->setRet($ex->getCode());
            $rs->setMsg($ex->getMessage());
            $di->logger->error($di->request->getService(), strval($ex));
        }catch(Exception $ex){
//            var_dump($ex->getMessage());die;
            // 不可控的异常
            $di->logger->error($di->request->getService(), strval($ex));

            if($di->debug){
                $rs->setRet($ex->getCode());
                $rs->setMsg($ex->getMessage());
                $rs->setDebug('exception', $ex->getTrace());
            }else{
                throw $ex;
            }
        }

        $rs->setDebug('stack', $di->tracer->getStack());
        $rs->setDebug('sqls', $di->tracer->getSqls());
        $rs->setDebug('version', PHALAPI_VERSION);

        return $rs;
    }

    public function validatorFilter($requests){
        if(empty($requests['timestamp'])){
//            throw new PhalApi_Exception_ValidatorError('参数异常，时间戳不能为空');
        }
        if(empty($requests['sign'])){
//            throw new PhalApi_Exception_ValidatorError('参数异常，签名不能为空');
        }

        $returnData = [];
        foreach($requests as $key => $value){
            if($key == 'sign' || $key == "file" || $value === null || $value === "null"){
                continue;
            }
            $returnData[$key] = $value;
        }
        list($sign, $paramStr) = $this->createSign($returnData);
        return [$sign, $paramStr];
    }

    /**
     * @description: 生成签名
     * @dateTime   : 2020年07月06日 13:42:48
     * @param $params
     * @return string
     */
    public function createSign($params){
        // 把数组中的键转换成小写
        $params = array_change_key_case($params, CASE_LOWER);
        // 把对应的key值按ascii码从小到大排序然后现拼接成url形式
        ksort($params);
//        header('Content-type: text/html; charset=utf-8');
        $paramStr = '';
        foreach($params as $k => $v){
            $paramStr .= $k . '=' . $v . '&';
        }
        $paramStr = trim($paramStr, '&');
        $tem      = md5($paramStr);
        return [$tem, $paramStr];
    }

}
