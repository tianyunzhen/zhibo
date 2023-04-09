<?php

/**
 * 红包
 */
class Api_Pk extends PhalApi_Api
{

    public function getRules()
    {
        return [
            'sendRed' => [
                'uid'        => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'      => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户Token',
                ],
                'stream'     => [
                    'name'    => 'stream',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '流名',
                ],
                'type'       => [
                    'name'    => 'type',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '红包类型，0普通，1手气',
                ],
                'type_grant' => ['name'    => 'type_grant',
                                 'type'    => 'int',
                                 'require' => true,
                                 'desc'    => '发放类型，0立即 1延迟',
                ],
                'coin'       => ['name'    => 'coin',
                                 'type'    => 'int',
                                 'require' => true,
                                 'desc'    => '钻石',
                ],
                'nums'       => ['name'    => 'nums',
                                 'type'    => 'int',
                                 'require' => true,
                                 'desc'    => '数量',
                ],
                'des'        => ['name'    => 'des',
                                 'type'    => 'string',
                                 'default' => '恭喜发财，大吉大利',
                                 'desc'    => '描述',
                ],
            ]
        ];
    }

    /**
     * 发送红包
     *
     * @desc 用于 发送红包
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].redid 红包ID
     * @return string msg 提示信息
     */
    public function sendRed()
    {
        $rs = ['code' => 0, 'msg' => '发送成功', 'info' => []];

        $uid        = $this->uid;
        $token      = checkNull($this->token);
        $stream     = checkNull($this->stream);
        $type       = $this->type;
        $type_grant = $this->type_grant;
        $coin       = $this->coin;
        $nums       = $this->nums;
        $des        = checkNull($this->des);


        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        if ((int)$coin == 0) {
            $rs['code'] = 1002;
            $rs['msg']  = '请输入正确的金额';
            return $rs;
        }

        if ((int)$nums == 0) {
            $rs['code'] = 1003;
            $rs['msg']  = '请输入正确的个数';
            return $rs;
        }

        if ($type == 0) {
            /* 平均 */
            $avg  = $coin;
            $coin = $avg * $nums;
        } else {
            if ($nums > $coin) {
                $rs['code'] = 1004;
                $rs['msg']  = '红包数量不能超过红包金额';
                return $rs;
            }
        }

        if (mb_strlen($des) > 20) {
            $rs['code'] = 1004;
            $rs['msg']  = '红包名称最多20个字';
            return $rs;
        }


        $stream_a = explode("_", $stream);
        $liveuid  = $stream_a[0];
        $showid   = $stream_a[1];
        if ((int)$liveuid == 0 || (int)$showid == 0) {
            $rs['code'] = 1007;
            $rs['msg']  = '信息错误';
            return $rs;
        }

        $nowtime    = time();
        $addtime    = $nowtime;
        $effecttime = $nowtime;
        if ($type_grant == 1) {
            $effecttime = $nowtime + 3 * 60;
        }

        $data   = [
            "uid"        => $uid,
            "liveuid"    => $liveuid,
            "showid"     => $showid,
            "type"       => $type,
            "type_grant" => $type_grant,
            "coin"       => $coin,
            "nums"       => $nums,
            "des"        => $des,
            "effecttime" => $effecttime,
            "status"     => 0,
            "addtime"    => $addtime,
        ];
        $domain = new Domain_Red();
        $result = $domain->sendRed($data);
        if ($result['code'] != 0) {
            return $result;
        }
        $redinfo = $result['info'];

        $redid = $redinfo['id'];

        $key = 'red_list_' . $stream;
        DI()->redis->rPush($key, $redid);

        $key2     = 'red_list_' . $stream . '_' . $redid;
        $red_list = $this->redlist($coin, $nums, $type);
        foreach ($red_list as $k => $v) {
            DI()->redis->rPush($key2, $v);
        }
        $rs['info'][0]['redid'] = (string)$redid;

        return $rs;
    }

}
