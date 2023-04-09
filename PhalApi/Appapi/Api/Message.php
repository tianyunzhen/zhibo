<?php

/**
 * 系统消息
 */
class Api_Message extends PhalApi_Api
{

    public function getRules()
    {
        return [
            'getList'      => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token' => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户Token',
                ],
                'p'     => [
                    'name'    => 'p',
                    'type'    => 'int',
                    'default' => 1,
                    'desc'    => '页码',
                ],
            ],
            'sendMsg'      => [
//                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
            ],
            'getUnreadNum' => [
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
                            'desc'    => '用户Token',
                ],
            ],
            'readMessage'  => [
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
                            'desc'    => '用户Token',
                ],
            ],
            'readFans'     => [
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
                            'desc'    => '用户Token',
                ],
            ],

        ];
    }

    /**
     * 系统消息
     *
     * @desc 用于 获取系统消息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0] 支付信息
     * @return string msg 提示信息
     */
    public function getList()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = $this->uid;
        $token = checkNull($this->token);
        $p     = checkNull($this->p);

        if ($p < 1) {
            $p = 1;
        }


        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain = new Domain_Message();
        $list   = $domain->getList($uid, $p);

        foreach ($list as $k => $v) {
            $v['addtime'] = date('Y-m-d H:i', $v['addtime']);
            $list[$k]     = $v;
        }


        $rs['info'] = $list;
        return $rs;
    }

    /**
     * 系统消息
     *
     * @desc 用于 获取系统消息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0] 支付信息
     * @return string msg 提示信息
     */
    public function sendMsg()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];
//        $myResult = DI()->notorm->user_coinrecord
//            ->where("uid=37163 and touid=37189")
//            ->sum('totalcoin');
//        $uid = checkNull($this->uid);
        $configpri = getConfigPri();
        /* 极光推送 */
//        $app_key = $configpri['jpush_key'];
        $app_key = 'a7f161f953fb2a3b2282f653';
//        $master_secret = $configpri['jpush_secret'];
        $master_secret = 'c4d187dcb6f032ce5ad68296';
        $domain        = new Domain_Live();
        require API_ROOT . '/../sdk/JPush/autoload.php';
        $client          = new \JPush\Client($app_key, $master_secret, null);
        $apns_production = true;
        if ($configpri['jpush_sandbox']) {
            $apns_production = true;
        }
//        var_dump($apns_production);die;
        $pushid = ['140fe1da9e249438a2f'];
//        $pushid = '140fe1da9e249438a2f';
//        var_dump($pushid);die;

        $title = '你的好友正在直播，邀请你一起';
        try {
            $result = $client->push()
                ->setPlatform('android')
                ->addRegistrationId($pushid)
                ->setNotificationAlert($title)
//                ->iosNotification($title, array(
//                    'sound' => 'sound.caf',
//                    'category' => 'jiguang',
//                    'extras' => array(
//                        'type' => '1',
//                        'userinfo' => "哈哈"
//                    ),
//                ))
                ->androidNotification('ios测试', [
                    'extras' => [
                        'title'    => $title,
                        'type'     => '1',
                        'userinfo' => "官方",
                    ],
                ])
                ->options([
                    'sendno'          => 100,
                    'time_to_live'    => 0,
                    'apns_production' => $apns_production,
                ])
                ->send();
            var_dump($result);
            die;
        } catch (\JPush\Exceptions\JPushException $e) {
            var_dump($e->getMessage());
            die;
        }
        return $rs;
    }

    /**
     * 获取未读消息数和粉丝数
     *
     * @desc 用于 获取未读消息数量和未查看粉丝数量
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info['msg_num'] 未读消息数量
     * @return string info['fans_num'] 未读粉丝数量
     * @return string msg 提示信息
     */
    public function getUnreadNum()
    {
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_Message();
        list($push, $attention) = $domain->getUnreadNum($uid);
        $rs['info'] = [
            'msg_num'  => $push,
            'fans_num' => $attention,
        ];
        return $rs;
    }

    /**
     * 消息阅读
     *
     * @desc 用于消息阅读
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function readMessage()
    {
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_Message();
        $info   = $domain->readMessage($uid, 'pushrecord');
        if ($info == 10001) {
            $rs['code'] = $info;
            $rs['msg']  = '读取失败';
        }
        return $rs;
    }

    /**
     * 粉丝阅读
     *
     * @desc 用于粉丝阅读
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function readFans()
    {
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_Message();
        $info   = $domain->readMessage($uid, 'user_attention');
        if ($info == 10001) {
            $rs['code'] = $info;
            $rs['msg']  = '读取失败';
        }
        return $rs;
    }
}
