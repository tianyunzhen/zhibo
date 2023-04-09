<?php

class Model_UserRemark extends PhalApi_Model_NotORM{
    protected $tableName = 'user_remark';

    public function getUserRemark($uid)
    {
        $sql = "select b.`name`,b.icon,b.auth_desc,a.addtime,b.level from cmf_user_remark a left join cmf_remark b on a.remark_id = b.id where a.uid = {$uid} and a.`status` = 1";
        $data = $this->getORM()->queryAll($sql);
        if($data)
        {
            return $data[0];
        }else{
            return [];
        }
    }
    public function getList($files = '*')
    {
        $sql = "select {$files} from cmf_user_remark a left join cmf_user b on a.uid = b.id";
        return $this->getORM()->queryAll($sql);
    }

    public function uidSelect($uid,$fields)
    {
        return $this->getORM()->where(['uid'=>$uid])->fetchOne($fields);
    }

    public function isRemark($where)
    {
        return $this->getORM()->where($where)->count();
    }

    public function getStartTime($uid)
    {
        $time = $this->getORM()
            ->where([
                'uid' => $uid
            ])
            ->fetchOne('addtime');
        return $time;
    }
}
