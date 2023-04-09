<?php

/**
 * 消息(WANGLIN)
 */
class Api_Msg extends PhalApi_Api
{

    public function getRules()
    {
        return [
            'notReadNum' => [
                'uid'   => [
                    'name'    => 'uid',
                    'require' => true,
                    'type'    => 'int',
                    'desc'    => '用户ID',
                ],
                'token' => ['name'    => 'token',
                            'require' => true,
                            'type'    => 'string',
                            'desc'    => '用户token',
                ],
            ],
            'msgList'    => [
                'uid'   => [
                    'name'    => 'uid',
                    'require' => true,
                    'type'    => 'int',
                    'desc'    => '用户ID',
                ],
                'token' => ['name'    => 'token',
                            'require' => true,
                            'type'    => 'string',
                            'desc'    => '用户token',
                ],
                'page'  => ['name'    => 'page',
                            'require' => true,
                            'min'     => 1,
                            'type'    => 'int',
                            'desc'    => '页码',
                ],
            ],
            'readAll'    => [
                'uid'   => [
                    'name'    => 'uid',
                    'require' => true,
                    'type'    => 'int',
                    'desc'    => '用户ID',
                ],
                'token' => ['name'    => 'token',
                            'require' => true,
                            'type'    => 'string',
                            'desc'    => '用户token',
                ],
            ],
        ];
    }

    /**
     * 获取官方未读消息数
     *
     * @desc 用于获取官方未读消息数
     * @return int code 操作码，0表示成功
     * @return array info.[0] 消息数
     * @return string msg 提示信息
     */
    public function notReadNum()
    {
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $mode         = new Domain_Msg();
        $rs['info'][] = $mode->notRead($uid);
        return $rs;
    }

    /**
     * 获取官方消息列表
     *
     * @desc 用于获取官方消息列表
     * @return int code 操作码，0表示成功
     * @return array info.[0].is_read 是否阅读 1
     * @return array info.[0].add_time 时间
     * @return array info.[0].title 标题
     * @return array info.[0].content 内容
     * @return string msg 提示信息
     */
    public function msgList()
    {
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $page       = $this->page;
        $token      = $this->token;
        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $mode       = new Domain_Msg();
        $rs['info'] = $mode->msgList($uid, $page);
        return $rs;
    }

    /**
     * 阅读所有官方消息
     *
     * @desc 用于获取官方未读消息数
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function readAll()
    {
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = $this->token;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $mode = new Domain_Msg();
        list($rs['code'], $rs['msg']) = $mode->readAll($uid);
        return $rs;
    }
}
