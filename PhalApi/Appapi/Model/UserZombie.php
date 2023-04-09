<?php

class Model_UserZombie extends PhalApi_Model_NotORM
{
    protected $tableName = 'user_zombie';

    public function insertUid($list,$liveId)
    {
        if($list)
        {
            $data = [];
            foreach($list as $k=>$v)
            {
                $data[] = [
                    'uid' => $v['id'],
                    'showid' =>$liveId
                ];
            }
            if(!$this->getORM()->insert_multi($data))
            {
                return false;
            }
        }
        return true;
    }

    public function deleteZombie($showid)
    {
        $sql = "delete from cmf_user_zombie where showid = {$showid}";
        return $this->getORM()->queryAll($sql);
    }
}
