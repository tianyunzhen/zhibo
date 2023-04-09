<?php

class Model_CarUser extends PhalApi_Model_NotORM
{
    protected $tableName = 'car_user';

    public function getCarUserInfo($uid, $car_id, $field = '*')
    {
        return DI()->notorm->car_user
            ->where('uid = ? and carid = ?', $uid, $car_id)
            ->select($field)
            ->fetchOne();
    }

    public function getCarUserList($uid, $fields)
    {
        return DI()->notorm->car_user
            ->where('uid = ?', $uid)
            ->select($fields)
            ->fetchAll();
    }

    public function insertData($data)
    {
        return DI()->notorm->car_user->insert($data);
    }

    /**
     * 获取背包
     *
     * @param $uid
     *
     * @return mixed
     */
    public function getCarInfoList($uid, $is_use = '')
    {
        $sql = "select car.`name`,car.thumb,car_user.id,car_user.`status`,car_user.addtime,car_user.endtime,car.expire from cmf_car_user car_user left join cmf_car car on car_user.carid = car.id 
where car_user.uid = :uid and car_user.endtime >= :endtime";
        $data = [
            ':uid'     => $uid,
            ':endtime' => time(),
        ];
        $res  = DI()->notorm->car_user->queryAll($sql, $data);
        foreach ($res as &$v) {
            if (!$v['expire']) {
                $v['expire'] = "永久";
            } else {
                $v['expire'] .= "天";
            }
            $v['thumb'] = get_upload_path($v['thumb']);
        }
        return $res;
    }


    public function getUserUseCar($uid)
    {
        $sql = "select car_user.id,car.swf,car.swftime,car.words,car_user.endtime,car.swftype from cmf_car_user car_user left join cmf_car car on car_user.carid = car.id 
where car_user.uid = :uid and car_user.status = 1 and car_user.endtime >= :endtime";
        $data = [
            ':uid'     => $uid,
            ':endtime' => time(),
        ];
        $res  = DI()->notorm->car_user->queryAll($sql, $data);
        if($res)
        {
            $res[0]['swf'] = get_upload_path($res[0]['swf']);
        }
        return $res[0] ?? [];
    }
}
