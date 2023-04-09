<?php

class Model_FamilyUser extends PhalApi_Model_NotORM
{
    protected $tableName = 'family_user';

    public function getFamilyInfo($fid,$columns = '*')
    {
        $sql = "select {$columns} from cmf_family_user where familyid = :fid";
        return $this->getORM()->queryAll($sql,[
            ':fid' => $fid
        ]);
    }

    public function getFamiliId($uid)
    {
        return $this->getORM()
            ->where(['uid'=>$uid])
            ->fetchOne('familyid');
    }
}