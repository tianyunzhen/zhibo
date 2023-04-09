<?php

class Model_UserSuper extends PhalApi_Model_NotORM
{
    protected $tableName = 'user_super';

    public function isSuper($uid)
    {
        return $this->getORM()
            ->where(['uid'=>$uid])
            ->count();
    }
}
