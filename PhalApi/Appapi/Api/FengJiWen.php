<?php

/**
 * 支付
 */
class Api_FengJiWen extends PhalApi_Api{
    public function getRules(){
        return [
            'chongZ'          => [
                'uid'      => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid'    => [
                    'name' => 'touid',
                    'type' => 'int',
                    'min'  => 1,
                    'desc' => '充值用户ID',
                ],
                'token'    => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户token',
                ],
                'pay_id'   => [
                    'name'    => 'pay_id',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '充值ID',
                ],
                'type'     => [
                    'name'    => 'type',
                    'type'    => 'int',
                    'default' => '0',
                    'desc'    => '1 安卓 ，2 ios',
                ],
                'pay_type' => [
                    'name'    => 'pay_type',
                    'type'    => 'string',
                    'default' => 'ali',
                    'desc'    => 'ali 支付宝支付 wx 微信支付',
                ],
            ],
            'fengjiwenRecord' => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token' => ['name'    => 'token',
                            'type'    => 'string',
                            'require' => true,
                            'desc'    => '用户token',
                ],
                'page'  => ['name'    => 'page',
                            'type'    => 'int',
                            'min'     => 1,
                            'require' => true,
                            'desc'    => '页码',
                ],
            ],
            'charge'          => [
                'uid'      => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid'    => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '充值用户ID',
                ],
                'token'    => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户token',
                ],
                'pay_id'   => [
                    'name'    => 'pay_id',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '充值ID',
                ],
                'type'     => [
                    'name'    => 'type',
                    'type'    => 'int',
                    'default' => '0',
                    'desc'    => '1 安卓 ，2 ios',
                ],
                'pay_type' => [
                    'name'    => 'pay_type',
                    'type'    => 'string',
                    'default' => 'ali',
                    'desc'    => 'ali 支付宝支付 wx 微信支付',
                ],
            ],
        ];
    }

    /**
     * 充值（汪林）
     *
     * @desc 用于充值
     * @return int code 操作码，0表示成功
     * @return array info //支付串
     * @return string msg 提示信息
     */
    public function chongZ(){
        $rs       = ['code' => 0, 'msg' => '', 'info' => []];
        $toUid    = checkNull($this->uid);
        $uid      = $this->touid ?: $toUid;
        $token    = checkNull($this->token);
        $pay_id   = checkNull($this->pay_id);
        $type     = checkNull($this->type);
        $pay_type = checkNull($this->pay_type);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
        }
        $model = new Domain_Pay();
        list($rs['code'], $rs['msg'], $rs['info'][]) = $model->pay($uid, $pay_id, $pay_type, $type, $toUid);
        return $rs;
    }

    /**
     * 支付宝回调（汪林）
     *
     * @desc 用于支付宝回调
     * @return int code 操作码，0表示成功
     * @return array info[0] 支付字符串
     * @return string msg 提示信息
     */
    public function aliPayNotify(){
        $arr = DI()->request->getAll();
        file_put_contents(API_ROOT . '/Runtime/newAli.log', json_encode($arr), FILE_APPEND);
//        DI()->logger->info('支付宝支付回调新', json_encode($arr));
        $model = new Domain_Pay();
        $model->aliNotify($arr);
    }

    /**
     * 充值记录（汪林）
     *
     * @desc 用于获取充值记录
     * @return int code 操作码，0表示成功
     * @return array info[0].addtime 时间
     * @return array info[0].money 金额
     * @return array info[0].status 0充值中 1成功 2失败
     * @return string msg 提示信息
     */
    public function fengjiwenRecord(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
        }
        $page        = checkNull($this->page);
        $model       = new Domain_Charge();
        $res['info'] = $model->record($uid, $page);
        return $res;
    }

    /**
     * 微信支付回调
     *
     * @desc 用于微信支付回调
     * @return int code 操作码，0表示成功
     * @return array info[0] 支付字符串
     * @return string msg 提示信息
     */
    public function wxPayNotify(){
        $testxml = file_get_contents("php://input");
        $jsonxml = json_encode(simplexml_load_string($testxml, 'SimpleXMLElement', LIBXML_NOCDATA));
        DI()->logger->info('微信支付回调新', $jsonxml);
        $arr   = json_decode($jsonxml, true);
        $model = new Domain_Pay();
        $model->wxNotify($arr);
    }


    /**
     * 充值
     *
     * @desc 用于微信公众号充值
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function charge(){
        $rs       = ['code' => 0, 'msg' => '', 'info' => []];
        $touid    = $this->uid;
        $uid      = checkNull($this->touid) ?: $touid;
        $userInfo = getUserInfoDuck($uid);
        if(!$userInfo){
            $rs['code'] = 10000;
            $rs['msg']  = '用户不存在';
            return $rs;
        }
        $pay_id   = checkNull($this->pay_id);
        $type     = checkNull($this->type);
        $pay_type = checkNull($this->pay_type);
        $model    = new Domain_Pay();
        list($rs['code'], $rs['msg'], $rs['info'][]) = $model->pay($uid, $pay_id, $pay_type, $type, $touid, true);
        return $rs;
    }
}
