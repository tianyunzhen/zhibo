<?php

/**
 * 任务
 */
class Api_Usertask extends PhalApi_Api
{

    public function getRules()
    {
        return [
            'isSignIn' => [
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
            'userSign' => [
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
     * 是否签到
     *
     * @desc 用于 是否签到
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[is_sign_in] 是否签到  1是  0否
     * @return string info[money] 金币
     **/
    public function isSignIn()
    {
        $rs         = ['code' => 0, 'msg' => 'success', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domian = new Domain_UserTask();
        $res    = $domian->isSignIn($uid);
        if ($res) {
            $rs['info']['is_sign_in'] = 1;
        } else {
            $rs['info']['is_sign_in'] = 0;
        }
        $rs['info']['money'] = 1000;
        return $rs;
    }

    /**
     * 签到
     *
     * @desc 用于 签到
     * @return int code 操作码，0表示成功
     * @return array info
     **/
    public function userSign()
    {
        $rs         = ['code' => 0, 'msg' => 'success', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_UserTask();
        list($code, $msg) = $domain->userSign($uid);
        if ($code > 0) {
            $rs['code'] = 101;
            $rs['msg']  = $msg;
        }
        return $rs;
    }
}
