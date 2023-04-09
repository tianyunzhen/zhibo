<?php

class Model_UserAttention extends PhalApi_Model_NotORM{
    protected $tableName = 'user_attention';

    public function getFollowList($uid, $page){
        $pageTotal = 10;
        $page      = $this->paging($page, $pageTotal);
        $sql       = "select uid,pkuid,stream from cmf_live where uid in (select touid from cmf_user_attention where uid = :uid) and islive = 1 and is_black = 0 
order by starttime desc limit :page , :pageTotal";
        return $this->getORM()->queryAll($sql, [':uid' => $uid, ':page' => $page, ':pageTotal' => $pageTotal]);
    }

    public function mutual($uid,$toUid)
    {
        return $this->getORM()
            ->where("(uid = {$uid} and touid = {$toUid}) or (uid = {$toUid} and touid = {$uid})")
            ->count();
    }
}
