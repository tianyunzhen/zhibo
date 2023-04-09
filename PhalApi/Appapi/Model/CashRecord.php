<?php

class Model_CashRecord extends PhalApi_Model_NotORM{
    protected $tableName = 'cash_record';

    const SHZ = 0; //审核中
    const TG  = 1; //通过
    const JJ  = 2; //拒绝
    const CG  = 3; //成功
    const SB  = 4; //失败

    public function getRecord($uid, $page){
        $page_total = 20;
        $page       = ($page < 1) ? 1 : ($page - 1) * $page_total;
        return $this->getORM()
            ->select('money,status,addtime')
            ->where('uid = ?', $uid)
            ->limit($page, $page_total)
            ->fetchAll();
    }

    public function getNowRecord($uid, $addTime){
        $sql = "select `status` from cmf_cash_record where uid = :uid and addtime >= :addTime";
        return $this->getORM()->queryAll($sql, [':uid' => $uid, ':addTime' => $addTime]);
    }
}
