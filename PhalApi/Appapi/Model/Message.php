<?php

class Model_Message extends PhalApi_Model_NotORM
{
    /* 信息列表 */
    public function getList($uid, $p)
    {
        if ($p < 1) {
            $p = 1;
        }
        $pnum  = 50;
        $start = ($p - 1) * $pnum;

        $list = DI()->notorm->pushrecord
            ->select('content,addtime')
            ->where("touid='' or( touid!='' and (touid = '{$uid}' or touid like '{$uid},%' or touid like '%,{$uid},%' or touid like '%,{$uid}') )")
            ->order('addtime desc')
            ->limit($start, $pnum)
            ->fetchAll();

        return $list;
    }

    public function getUnreadNum($uid)
    {
        $push      = DI()->notorm->pushrecord
            ->where("touid=$uid and is_read=0")
            ->count();
        $attention = DI()->notorm->user_attention
            ->where("touid=$uid and is_read=0")
            ->count();
        return [$push, $attention];
    }

    public function readMessage($uid, $table)
    {
        $result = DI()->notorm->$table
            ->where("touid=$uid and is_read=0")
            ->update(['is_read' => 1]);
        if ($result === false) {
            return 10001;
        }
        return 1;
    }

}
