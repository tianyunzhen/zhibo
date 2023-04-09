<?php

class Model_Liang extends PhalApi_Model_NotORM
{
    public function getInfo($id, $fiels = '*')
    {
        return DI()->notorm->liang
            ->where('id = ?', $id)
            ->select($fiels)
            ->fetchOne();
    }

    /**
     * 获取用户靓号
     *
     * @param $uid
     *
     * @return array
     */
    public function userList($uid)
    {
        $sql
            = "select id,`name`,addtime,state as `status`,expire from cmf_liang where uid = :uid and (end_time = 0 or end_time > :end_time)";
        $res =  $this->getORM()->queryAll($sql,
            [':uid' => $uid, ':end_time' => time()]);
        foreach ($res as &$v) {
            if (!$v['expire']) {
                $v['expire'] = "永久";
            } else {
                $v['expire'] .= "天";
            }
        }
        return $res;
    }
}
