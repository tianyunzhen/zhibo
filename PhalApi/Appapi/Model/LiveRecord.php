<?php

class Model_LiveRecord extends PhalApi_Model_NotORM{
    protected $tableName = 'live_record';

    public function getUserTimes($uid, $start, $end){
//        $sql = "select starttime,endtime from cmf_live_record where uid = $uid and
//((starttime <= $start and endtime >= $end) or (starttime > $start and starttime < $end) or (endtime > $start and endtime < $end))";
        $sql = "select min(starttime) min_t, max(endtime) max_t,sum(endtime - starttime) total from cmf_live_record where uid = :uid and endtime >= :start and starttime <= :end and starttime > 0";
        return $this->getORM()->queryAll($sql, [
            ':uid'   => $uid,
            ':start' => $start,
            ':end'   => $end,
        ]);
    }

    public static function getLiveList($fields, $where, $order){
        return DI()->notorm->live_record
            ->select($fields)
            ->where($where)
            ->where('starttime > ?', 0)
            ->order($order)
            ->fetchAll();
    }
}
