<?php

class Model_ChargeUser extends PhalApi_Model_NotORM
{
    protected $tableName = 'charge_user';
    const WAIT    = 0;//支付中
    const SUCCESS = 1;//成功
    const FAIL    = 2;//失败

    public function nowMonthMoney($uid)
    {
        $start_time = mktime(00, 00, 00, date('m'), 1, date('Y'));
        $end_time   = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        $sql        = 'select sum(money) counts from cmf_charge_user where uid = :uid and status = :status and addtime between :start_time and :end_time';
        $data       = [
            ':uid'        => $uid,
            ':start_time' => $start_time,
            ':end_time'   => $end_time,
            ':status'   => 1,
        ];
        return DI()->notorm->charge_user->queryAll($sql, $data);
    }
}
