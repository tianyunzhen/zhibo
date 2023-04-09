<?php

class Model_LivePkUser extends PhalApi_Model_NotORM{
    protected $tableName = 'live_pk_user';

    public function getPkDesc($uid){
        return $this->getORM()
            ->where(['uid' => $uid])
            ->select("count(if(`status`=1,1,null)) win_nums,count(*) count_nums")
            ->fetchOne();
    }

    public function pkUserList($pkId){
        return $this->getORM()
            ->where(['pk_id' => $pkId])
            ->select('uid,status,gift_money')
            ->fetchAll();
    }

    public function getNowUidInfo($uid, $columns = '*'){
        return $this->getORM()
            ->where("uid = {$uid} and status = 0")
            ->select($columns)
            ->fetchOne();
    }

    public function saveData($uid, $data){
        return $this->getORM()
            ->where(['uid' => $uid, 'status' => 0])
            ->update($data);
    }

    public function saveMoney($uid, $pkId, $data){
        return $this->getORM()
            ->where(['uid' => $uid, 'pk_id' => $pkId])
            ->update($data);
    }
}
