<?php

/**
 * 装备信息(H5)
 */
class Api_Equipment extends PhalApi_Api
{

    public function getRules()
    {
        return [
            'index'           => [
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
            ],
            'EquipmentSwitch' => [
                'uid'    => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'  => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户Token',
                ],
                'id'     => [
                    'name'    => 'id',
                    'type'    => 'int',
                    'require' => true,
                    'min'     => 1,
                    'desc'    => '主键id',
                ],
                'type'   => [
                    'name'    => 'type',
                    'type'    => 'int',
                    'min'     => 1,
                    'max'     => 2,
                    'require' => true,
                    'desc'    => '类型 1 坐骑 2 靓号',
                    'default' => 1,
                ],
                'status' => ['name'    => 'status',
                             'type'    => 'int',
                             'min'     => 0,
                             'max'     => 1,
                             'require' => true,
                             'desc'    => '状态 0 卸载 1 使用',
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
    public function index()
    {
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $di         = DI()->notorm;
        $uid        = (int)checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
//       /* 靓号信息 */
        $liang_list = $di->liang->where(["uid" => $uid])->order("buytime desc")
            ->fetchOne() ?: [];

        /* 坐骑信息 */
        $car_key  = 'user:carinfo';
        $car_list = getcaches($car_key);
        if (!$car_list) {
            $car_list = $di->car->fetchAll();
            if ($car_list) {
                setcaches($car_key, $car_list);
            }
        }
        foreach ($car_list as $k => $v) {
            $v['thumb']         = get_upload_path($v['thumb']);
            $v['swf']           = get_upload_path($v['swf']);
            $carlist2[$v['id']] = $v;
        }

        /* 用户坐骑 */
        $nowtime = time();
        $where   = [
            ['uid', '=', $uid],
            ['endtime', '>', $nowtime],
        ];

        $user_carlist = $di->car_user->where($where)->fetchAll();
        foreach ($user_carlist as $k => $v) {
            if ($carlist2[$v['carid']]) {
                $user_carlist[$k]['carinfo']      = $carlist2[$v['carid']];
                $user_carlist[$k]['endtime_date'] = date("Y-m-d",
                    $v['endtime']);
            } else {
                unset($user_carlist[$k]);
            }
        }
        $data       = [
            'liang_list'    => $liang_list,
            'user_car_list' => $user_carlist,
        ];
        $rs['info'] = $data;
        return $rs;
    }

    /**
     * 坐骑靓号使用开关
     *
     * @desc 用于坐骑靓号使用开关
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function EquipmentSwitch()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid    = checkNull($this->uid);
        $token  = checkNull($this->token);
        $type   = checkNull($this->type);
        $status = checkNull($this->status);
        $id     = checkNull($this->id);

        if ($uid < 0 || $token == '') {
            $rs['code'] = 1000;
            $rs['msg']  = '信息错误';
            return $rs;
        }

        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain = new Domain_Backpack();
        $res    = $domain->EquipmentSwitch($id, $uid, $type, $status);
        if (!$res) {
            $rs['code'] = 10001;
            $rs['msg']  = "操作失败";
        }
        return $rs;
    }
}
