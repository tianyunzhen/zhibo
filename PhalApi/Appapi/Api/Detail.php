<?php

/**
 * 明细（H5）
 */
class Api_Detail extends PhalApi_Api
{

    public function getRules()
    {
        return [
            'coinIncome'    => [
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
                'start_time' => [
                    'name' => 'start_time',
                    'type' => 'string',
                    'desc' => '开始时间',
                ],
                'end_time'   => [
                    'name' => 'end_time',
                    'type' => 'string',
                    'desc' => '结束时间',
                ],
                'type'       => [
                    'name'    => 'type',
                    'type'    => 'int',
                    'desc'    => '类型 1 官方直充 2 代理充值 3 任务 4 兑换 5 中奖',
                    'default' => 0,
                ],
                'page'       => [
                    'name'    => 'page',
                    'type'    => 'int',
                    'desc'    => '页码',
                    'default' => 1,
                ],
            ],
            'coinSpend'     => [
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
                'start_time' => [
                    'name' => 'start_time',
                    'type' => 'string',
                    'desc' => '开始时间',
                ],
                'end_time'   => [
                    'name' => 'end_time',
                    'type' => 'string',
                    'desc' => '结束时间',
                ],
                'type'       => [
                    'name'    => 'type',
                    'type'    => 'int',
                    'desc'    => '类型 6 送礼 7 靓号',
                    'default' => 0,
                ],
                'page'       => [
                    'name'    => 'page',
                    'type'    => 'int',
                    'desc'    => '页码',
                    'default' => 1,
                ],
            ],
            'diamondIncome' => [
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
                'start_time' => [
                    'name' => 'start_time',
                    'type' => 'string',
                    'desc' => '开始时间',
                ],
                'end_time'   => [
                    'name' => 'end_time',
                    'type' => 'string',
                    'desc' => '结束时间',
                ],
                'type'       => [
                    'name'    => 'type',
                    'type'    => 'int',
                    'desc'    => '类型 1 收礼',
                    'default' => 0,
                ],
                'page'       => [
                    'name'    => 'page',
                    'type'    => 'int',
                    'desc'    => '页码',
                    'default' => 1,
                ],
            ],
            'diamondSpend'  => [
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
                'start_time' => [
                    'name' => 'start_time',
                    'type' => 'string',
                    'desc' => '开始时间',
                ],
                'end_time'   => [
                    'name' => 'end_time',
                    'type' => 'string',
                    'desc' => '结束时间',
                ],
                'type'       => [
                    'name'    => 'type',
                    'type'    => 'int',
                    'desc'    => '类型 2 提现 3 兑换',
                    'default' => 0,
                ],
                'page'       => [
                    'name'    => 'page',
                    'type'    => 'int',
                    'desc'    => '页码',
                    'default' => 1,
                ],
            ],
            'test'       => [
                'uid'        => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
            ],
        ];
    }

    /**
     * 金币收入明细
     *
     * @desc 用于 获取金币收入明细
     * @return int code 操作码，0表示成功
     * @return string info['total_coin'] 金币总数
     * @return array info['list'] 流水列表
     * @return string info['list'][0]['id'] 流水id
     * @return string info['list'][0]['coin'] 金币数量
     * @return string info['list'][0]['add_time'] 时间
     * @return string msg 提示信息
     */
    public function coinIncome()
    {
        $rs  = ['code' => 0, 'msg' => '', 'info' => []];
        $uid = (int)checkNull($this->uid);
        $token = checkNull($this->token);
        $action     = checkNull($this->type);
        $start_time = checkNull($this->start_time);
        $end_time   = checkNull($this->end_time);
        $domain     = new Domain_Detail();
        $page       = checkNull($this->page);
        $checkToken = checkToken($uid,$token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg'] = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $where = ['type' => 1, 'uid' => $uid];
        if ($action) {
            $where['action'] = $action;
        }
        list($total_coin, $record, $count) = $domain->coin($where, $start_time,
            $end_time, $page);
        $rs['info']['total_coin'] = $total_coin;
        $rs['info']['list']       = $record;
        $rs['info']['total_page'] = $count;
        return $rs;
    }

    /**
     * 金币支出明细
     *
     * @desc 用于 获取金币支出明细
     * @return int code 操作码，0表示成功
     * @return string info['total_coin'] 金币总数
     * @return array info['list'] 流水列表
     * @return string info['list'][0]['id'] 流水id
     * @return string info['list'][0]['coin'] 金币数量
     * @return string info['list'][0]['add_time'] 时间
     * @return string msg 提示信息
     */
    public function coinSpend()
    {
        $rs  = ['code' => 0, 'msg' => '', 'info' => []];
        $uid = (int)checkNull($this->uid);
        $token = checkNull($this->token);
        $action     = checkNull($this->type);
        $start_time = checkNull($this->start_time);
        $end_time   = checkNull($this->end_time);
        $domain     = new Domain_Detail();
        $page       = checkNull($this->page);
        $checkToken = checkToken($uid,$token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg'] = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $where = ['type' => 0, 'uid' => $uid];
        if ($action) {
            $where['action'] = $action;
        }
        list($total_coin, $record, $count) = $domain->coin($where, $start_time,
            $end_time, $page);
        $rs['info']['total_coin'] = $total_coin;
        $rs['info']['list']       = $record;
        $rs['info']['total_page'] = $count;
        return $rs;
    }

    /**
     * 钻石收入明细
     *
     * @desc 用于 获取钻石收入明细
     * @return int code 操作码，0表示成功
     * @return string info['total_diamond'] 钻石总数
     * @return array info['list'] 流水列表
     * @return string info['list'][0]['id'] 流水id
     * @return string info['list'][0]['diamond'] 钻石数量
     * @return string info['list'][0]['add_time'] 时间
     * @return string msg 提示信息
     */
    public function diamondIncome()
    {
        $rs  = ['code' => 0, 'msg' => '', 'info' => []];
        $uid = (int)checkNull($this->uid);
        $token = checkNull($this->token);
        $action     = checkNull($this->type);
        $start_time = checkNull($this->start_time);
        $end_time   = checkNull($this->end_time);
        $domain     = new Domain_Detail();
        $checkToken = checkToken($uid,$token);
        $page = checkNull($this->page);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg'] = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $where = ['type' => 1, 'uid' => $uid];
        if ($action) {
            $where['action'] = $action;
        }
        list($total_coin, $record, $count) = $domain->diamond($where,
            $start_time, $end_time, $page);
        $rs['info']['total_diamond'] = (string) round($total_coin / 100, 2);
        $rs['info']['list']          = $record;
        $rs['info']['total_page']    = $count;
        return $rs;
    }

    /**
     * 钻石支出明细
     *
     * @desc 用于 获取钻石支出明细
     * @return int code 操作码，0表示成功
     * @return string info['total_diamond'] 钻石总数
     * @return array info['list'] 流水列表
     * @return string info['list'][0]['id'] 流水id
     * @return string info['list'][0]['diamond'] 钻石数量
     * @return string info['list'][0]['add_time'] 时间
     * @return string msg 提示信息
     */
    public function diamondSpend()
    {
        $rs  = ['code' => 0, 'msg' => '', 'info' => []];
        $uid = (int)checkNull($this->uid);
        $token = checkNull($this->token);
        $action     = checkNull($this->type);
        $start_time = checkNull($this->start_time);
        $end_time   = checkNull($this->end_time);
        $domain     = new Domain_Detail();
        $checkToken = checkToken($uid,$token);
        $page = checkNull($this->page);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg'] = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $where = ['type' => 0, 'uid' => $uid];
        if ($action) {
            $where['action'] = $action;
        }
        list($total_coin, $record, $count) = $domain->diamond($where,
            $start_time, $end_time, $page);
        $rs['info']['total_diamond'] = (string) round($total_coin / 100, 2);
        $rs['info']['list']          = $record;
        $rs['info']['total_page']    = $count;
        return $rs;
    }

    public function test()
    {
        $uid = (int)checkNull($this->uid);
        $push = new Common_JPush($uid);
//        $push->setAlias();
        $push->sendByRegistrationId('测试', '哈哈哈哈');
    }
}
