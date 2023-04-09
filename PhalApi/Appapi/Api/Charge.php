<?php

/**
 * 充值
 */
class Api_Charge extends PhalApi_Api
{

    public function getRules()
    {
        return [
            'getIosOrder' => [
                'uid'      => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'changeid' => [
                    'name'    => 'changeid',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '充值规则ID',
                ],
                'coin'     => [
                    'name'    => 'coin',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '钻石',
                ],
                'money'    => [
                    'name'    => 'money',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '充值金额',
                ],
            ],
        ];
    }

    /* 获取订单号 */
    protected function getOrderid($uid)
    {
        $orderid = $uid . '_' . date('YmdHis') . rand(100, 999);
        return $orderid;
    }

    /**
     * 苹果支付
     *
     * @desc 用于苹果支付 获取订单号
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].orderid 订单号
     * @return string msg 提示信息
     */
    public function getIosOrder()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid      = $this->uid;
        $changeid = $this->changeid;
        $coin     = checkNull($this->coin);
        $money    = checkNull($this->money);

        $orderid = $this->getOrderid($uid);
        $type    = 3;

        if ($coin == 0) {
            $rs['code'] = 1002;
            $rs['msg']  = '信息错误';
            return $rs;
        }

        $configpri = getConfigPri();

        $orderinfo = [
            "uid"     => $uid,
            "touid"   => $uid,
            "money"   => $money,
            "coin"    => $coin,
            "orderno" => $orderid,
            "type"    => $type,
            "status"  => 0,
            "addtime" => time(),
        ];

        $domain = new Domain_Charge();
        $info   = $domain->getOrderId($changeid, $orderinfo);
        if ($info == 1003) {
            $rs['code'] = 1003;
            $rs['msg']  = '订单信息有误，请重新提交';
        } else {
            if (!$info) {
                $rs['code'] = 1001;
                $rs['msg']  = '订单生成失败';
            }
        }
        $rs['info'][0]['orderid'] = $orderid;
        return $rs;
    }
}
