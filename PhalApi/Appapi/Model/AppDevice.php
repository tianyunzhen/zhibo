<?php

class Model_AppDevice extends PhalApi_Model_NotORM{
    protected $tableName = 'app_device';

    public function selectDeviceId($deviceId, $userId, $fields = '*'){
        return $this->getORM()
            ->where('device_id = ? and (user_id = 0 or user_id = ?)', $deviceId, $userId)
            ->select($fields)
            ->order('user_id desc')
            ->fetchOne();
    }
}
