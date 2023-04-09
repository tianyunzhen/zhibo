<?php

class Model_RankingUser extends PhalApi_Model_NotORM{
    protected $tableName = 'ranking_user';

    const USER        = 1; //主播榜
    const FAMILY_DAY  = 2; //家族日榜
    const FAMILY_WEEK = 3; //家族周榜
    const LIVE_TIME   = 4; //任务奖励

    const YES = 1;
    const NO  = 2;
    const NOO = 3;

    public function getUserList($type, $uid, $page){
        if($type == 1){
            $where = ' type in (1,4)';
        }else{
            $where = 'type = ' . $type;
        }
        $time       = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
        $page_total = 20;
        $page       = ($page > 0) ? ($page - 1) * $page_total : 0;
        $sql        = "select * from cmf_date a left join (
select id,`no`,gear,money,periods,status,add_time,`type`,times,mans from 
cmf_ranking_user where uid = {$uid} and {$where}) b on a.time = b.periods where a.time between (select UNIX_TIMESTAMP(FROM_UNIXTIME(addtime,'%Y%m%d')) from cmf_user_remark 
where uid = {$uid} limit 1) and {$time} limit {$page},{$page_total}";
        return $this->getORM()->queryAll($sql);
    }

    public function insertMore($data){
        return $this->getORM()->insert_multi($data);
    }

    public function isCunZai($uid, $type, $pri){
        $sql = "select * from cmf_ranking_user where uid = {$uid} and periods = {$pri} and type={$type}";
        return $this->getORM()->queryAll($sql);
    }

    public function getWeekList($uid, $page, $lastday){

        $end        = time();
        $page_total = 20;
        $page       = ($page > 0) ? ($page - 1) * $page_total : 0;
        $sql        = "select * from cmf_date a left join (
select id,`no`,gear,money,periods,status,add_time,`type`,times,mans from 
cmf_ranking_user where uid = {$uid} and type = 3) b on a.time = b.periods where a.w = 1 and  a.time between {$lastday} and {$end} limit {$page},{$page_total}";
        return $this->getORM()->queryAll($sql);
    }
}
