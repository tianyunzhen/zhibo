<?php

class Model_Car extends PhalApi_Model_NotORM
{
    protected $tableName = 'car';

    public function getCarUserInfo($uid, $car_id, $field = '*')
    {
        return DI()->notorm->car_user
            ->where('uid = ?', $uid)
            ->where('carid = ?', $car_id)
            ->select($field)
            ->fetchOne();
    }

    public function insertData($data)
    {
        return DI()->notorm->car_user->insert($data);
    }
}
