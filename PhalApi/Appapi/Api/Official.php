<?php

/**
 * 公众号充值界面
 */
class Api_Official extends PhalApi_Api
{

    public function getRules()
    {
        return [
            'charge'          => [
                'uid'    => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'pay_id' => [
                    'name'    => 'pay_id',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '充值ID',
                ],
                'type'   => [
                    'name'    => 'type',
                    'type'    => 'int',
                    'default' => '0',
                    'desc'    => '1 安卓 ，2 ios',
                ],
                'pay_type'   => [
                    'name'    => 'pay_type',
                    'type'    => 'string',
                    'default' => 'ali',
                    'desc'    => 'ali 支付宝支付 wx 微信支付',
                ],
            ],
            'userInfo' => [
                'uid'   => [
                    'name'    => 'uid',
                    'require' => true,
                    'type'    => 'int',
                    'desc'    => '用户ID',
                ],
            ],
            'chargeRule'    => [],
        ];
    }

    /**
     * 获取用户信息
     *
     * @desc 用于公众号获取获取用户信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info['id'] 用户id
     * @return string info['user_nicename'] 昵称
     * @return string msg 提示信息
     */
    public function userInfo()
    {
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $result = getUserInfoDuck($uid);
        if (!$result) {
            $rs['code'] = 10000;
            $rs['msg']  = '用户不存在';
            return $rs;
        }
        unset($result['avatar']);
        $rs['info'] = $result;
        return $rs;
    }

    /**
     * 充值
     *
     * @desc 用于微信公众号充值
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function charge()
    {
        $rs     = ['code' => 0, 'msg' => '', 'info' => []];
        $uid    = checkNull($this->uid);
        $userInfo = getUserInfoDuck($uid);
        if (!$userInfo) {
            $rs['code'] = 10000;
            $rs['msg']  = '用户不存在';
            return $rs;
        }
        $pay_id = checkNull($this->pay_id);
        $type       = checkNull($this->type);
        $pay_type = checkNull($this->pay_type);
        $model = new Domain_Pay();
        list($rs['code'], $rs['msg'], $rs['info'][]) = $model->pay($uid,
            $pay_id, $pay_type, $type);
        return $rs;
    }

    /**
     * 充值规则
     *
     * @desc 用于微信公众号获取充值列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[]['id'] 主键
     * @return string info[]['coin'] 金币
     * @return string info[]['coin_ios'] ios金币
     * @return string info[]['money'] 价格(人民币)
     * @return string info[]['money_ios'] ios价格(人民币)
     * @return string msg 提示信息
     */
    public function chargeRule()
    {
        $rs     = ['code' => 0, 'msg' => '', 'info' => []];
        $domain = new Domain_User();
        $list = $domain->getChargeRules();
        foreach ($list as &$v) {
            unset($v['product_id'], $v['give']);
        }
        $rs['info'] = $list;
        return $rs;
    }
}
