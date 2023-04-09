<?php

class Model_GiftRecord extends PhalApi_Model_NotORM{
    protected $tableName = 'gift_record';
    const DUCK_NUM = 20000000;
    const MULTIPlE = 50;

    public function getNowDayCoin($uid, $live_id){
        $sql  = "select sum(totalcoin) total_coin from cmf_gift_record where uid = {$uid} and touid = {$live_id} and to_days(addtime) = to_days(now());";
        $data = $this->getORM()->queryAll($sql);
        return $data[0]['total_coin'] ?? 0;
    }

    public function rankingUser($type = 1, $is_limit = 1) //1当日 2昨日
    {
        if($type == 1){
            $time = date('ymd');
        }else{
            $time = date('ymd', strtotime("-1 day"));
        }
        $limit = '';
        if($is_limit > 0){
            $limit = 'limit 0,150';
        }

        $sql = "select u.id,u.user_nicename,u.avatar_thumb,c.totalcoin from (
select sum(b.totalcoin) totalcoin,a.uid from cmf_user_remark a left join cmf_gift_record b on a.uid = b.touid where to_days(from_unixtime(b.addtime,'%y%m%d')) = to_days(:time) and  b.addtime >= a.addtime group by a.uid order by totalcoin desc :limit
) c left join cmf_user u on c.uid = u.id";
        return $this->getORM()->queryAll($sql,[':time'=>$time,'limit'=>$limit]);
    }

    public function familyDay($t,$type = 0){
        $time  = date('ymd', $t);
        $times = date('ymd', strtotime("-1 day", $t));
        if($type < 1){
            $sql = "select (select count(*) from (
select (@num := @num+1) num,touid,
ifnull((select familyid from cmf_family_user where uid = a.touid),0) f
from (select sum(totalcoin) total,b.touid from cmf_user_remark a left join cmf_gift_record b 
on a.uid=b.touid where to_days(from_unixtime(b.addtime,'%y%m%d')) = to_days({$time}) 
group by b.touid order by total desc) a,(select @num:=0) c) gg where num <= 150 and f=a.familyid) counts,
cu.user_nicename,cu.avatar_thumb,a.total,cf.`name`,cf.uid
from (select sum(b.totalcoin) total,a.familyid from cmf_family_user a left join cmf_gift_record b on a.uid = b.touid where 
to_days(from_unixtime(b.addtime,'%y%m%d')) = to_days({$time}) and b.addtime >= a.addtime group by a.familyid order by total desc limit 30) a left join cmf_family cf on a.familyid = cf.id left join cmf_user cu on cf.uid = cu.id limit 30";
        }else{
            $sql = "select (select count(*) from (
select (@num := @num+1) num,touid,
ifnull((select familyid from cmf_family_user where uid = a.touid),0) f
from (select sum(totalcoin) total,b.touid from cmf_user_remark a left join cmf_gift_record b 
on a.uid=b.touid where to_days(from_unixtime(b.addtime,'%y%m%d')) = to_days({$times}) 
group by b.touid order by total desc) a,(select @num:=0) c) gg where num <= 150 and f=a.familyid) counts,
cu.user_nicename,cu.avatar_thumb,a.total,cf.`name`,cf.uid
from (select sum(b.totalcoin) total,a.familyid from cmf_family_user a left join cmf_gift_record b on a.uid = b.touid where a.familyid in (select f from (
select (@num := @num+1) num,touid,
ifnull((select familyid from cmf_family_user where uid = a.touid),0) f
from (select sum(totalcoin) total,b.touid from cmf_user_remark a left join cmf_gift_record b 
on a.uid=b.touid where to_days(from_unixtime(b.addtime,'%y%m%d')) = to_days({$times}) 
group by b.touid order by total desc) a,(select @num:=0) c) gg where num <= 150 and f > 0  group by f having count(*) >= 4
) and to_days(from_unixtime(b.addtime,'%y%m%d')) = to_days({$times}) and b.addtime >= a.addtime group by a.familyid order by total desc limit 30) a left join cmf_family cf on a.familyid = cf.id left join cmf_user cu on cf.uid = cu.id limit 15";
        }
        return $this->getORM()->queryAll($sql);
    }

    public function familyWeek($start, $end, $type = 0){
        if($type > 0){
            $sql = "select
(select aaa.familyid from (
select count(*) counts,f_u.familyid from cmf_ranking_user r_u left join cmf_family_user f_u on r_u.uid = f_u.uid where r_u.add_time between {$start} and {$end} GROUP BY r_u.uid having counts > 3
) aaa group by aaa.familyid having count(*) > 9) counts,
cu.user_nicename,
cu.avatar_thumb,
a.total,
cf.`name`
from
(select sum(b.totalcoin) total,a.familyid from cmf_family_user a left join cmf_gift_record b on a.uid = b.touid where a.familyid in (
select aaa.familyid from (
select count(*) counts,f_u.familyid from cmf_ranking_user r_u left join cmf_family_user f_u on r_u.uid = f_u.uid GROUP BY r_u.uid having counts > 3
) aaa group by aaa.familyid having count(*) > 9) and b.addtime >= a.addtime and b.addtime between {$start} and {$end} group by a.familyid order by total desc limit 20) a left join cmf_family cf on a.familyid = cf.id left join cmf_user cu on cf.uid = cu.id";
        }else{
            $sql = "select
ifnull((select aaa.familyid from (
select count(*) counts,f_u.familyid from cmf_ranking_user r_u left join cmf_family_user f_u on r_u.uid = f_u.uid where r_u.add_time between {$start} and {$end} GROUP BY r_u.uid having counts > 3
) aaa group by aaa.familyid having count(*) > 9),0) counts,
cu.user_nicename,
cu.avatar_thumb,
a.total,
cf.`name`
from
(select sum(b.totalcoin) total,a.familyid from cmf_family_user a left join cmf_gift_record b on a.uid = b.touid where
 b.addtime >= a.addtime and b.addtime between {$start} and {$end} group by a.familyid order by total desc limit 20) a left join cmf_family cf on a.familyid = cf.id left join cmf_user cu on cf.uid = cu.id";
        }
        return $this->getORM()->queryAll($sql);
    }

    public function glamour_list($where){
        $where = $where ? 'where ' . $where : '';
        $sql   = "select ifnull(a.votes_sum,0) votes_sum,b.verify is_vip,
b.user_nicename,b.votestotal,b.consumption,b.id,b.avatar_thumb,b.signature from (
select sum(totalcoin) votes_sum,touid uid from cmf_gift_record {$where} group by touid having votes_sum > 0 order by votes_sum desc limit 30
) a left join cmf_user b on a.uid = b.id";
        return $this->getORM()->queryAll($sql);
    }

    public function now_three($uid, $where=''){
        $where = $where ? 'and ' . $where : '';
        $sql   = "select b.avatar_thumb,b.signature from (
select uid from cmf_gift_record where touid = :touid {$where} group by uid order by sum(totalcoin) desc limit 3
) a left join cmf_user b on a.uid = b.id";
        $data  = [
            ':touid' => $uid,
        ];
        return $this->getORM()->queryAll($sql, $data);
    }

    public function gerenbang($uid, $where){
        $where = $where ? 'and ' . $where : '';
        $sql   = "select user_nicename,signature,votestotal,consumption,id,avatar_thumb,
(select count(*) from cmf_live where uid = :touid) is_live,cmf_user.verify is_vip,
(select ifnull(sum(totalcoin),0) from cmf_gift_record where touid = :touid {$where}) votes_sum
from cmf_user where id = :touid;";
        $data  = [
            ':touid' => $uid,
        ];
        return $this->getORM()->queryAll($sql, $data);
    }

    public function wealth_list($where,$type){
        $where = $where ? 'where ' . $where : '';

        if($type == 3)
        {
            $sql   = "select ifnull(a.totalcoin_sum,0) totalcoin_sum,b.verify is_vip,b.id,b.user_nicename,b.verify,b.votestotal,b.consumption,b.avatar_thumb from (
select sum(totalcoin) totalcoin_sum,uid from cmf_gift_record {$where} group by uid order by totalcoin_sum desc limit 30
) a left join cmf_user b on a.uid = b.id";
        }else{
            
        }
        return $this->getORM()->queryAll($sql);
    }

    public function user_wealth($where, $uid){
        $where = $where ? 'and ' . $where : '';
        $sql   = "select id,user_nicename,votestotal,consumption,avatar_thumb,
(select count(*) from cmf_live where uid = :uid) is_live,cmf_user.verify is_vip,
(select ifnull(sum(totalcoin),0) from cmf_gift_record where uid=:uid {$where}) totals
from cmf_user where id = :uid";
        $data  = [
            ':uid' => $uid,
        ];
        return $this->getORM()->queryAll($sql, $data);
    }

    public function reward($liveuid, $where)
    {
        $sql = "select a.totalcoin_sum,b.id,b.user_nicename,b.verify,b.votestotal,b.consumption,b.avatar_thumb,b.signature from (
select sum(totalcoin) totalcoin_sum,uid from cmf_gift_record where touid = :touid {$where} group by uid order by totalcoin_sum desc limit 30
) a left join cmf_user b on a.uid = b.id";
        $data = [
            ':touid' => $liveuid,
        ];
        return $this->getORM()->queryAll($sql,$data);
    }

    public function myReward($liveuid, $uid, $where)
    {
        $sql = "select id,user_nicename,verify,votestotal,consumption,avatar_thumb,signature,
(select sum(totalcoin) totalcoin_sum from cmf_gift_record where touid = :touid and uid = :uid {$where}) totals
from cmf_user where id = :uid";
        $data = [
            ':touid' => $liveuid,
            ':uid' => $uid,
        ];
        return $this->getORM()->queryAll($sql,$data);
    }

    public function glamour_listV2($where){
        $where = $where ? 'where ' . $where : '';
        $sql   = "select b.verify is_vip,b.user_nicename,b.votestotal,b.consumption,b.id,b.avatar_thumb,b.signature,sum(totalcoin) votes_sum,touid uid from cmf_gift_record a {$where} group by touid having votes_sum > 0 order by votes_sum desc limit 30
 a left join cmf_user b on a.uid = b.id";
        return $this->getORM()->queryAll($sql);
    }


    public function now_threeV2($uid, $where=''){
        $where = $where ? 'and ' . $where : '';
        $sql   = "select b.avatar_thumb,b.signature from cmf_gift_record a left join cmf_user b on a.uid = b.id where touid = :touid {$where} group by uid order by sum(totalcoin) desc limit 3 ";
        $data  = [
            ':touid' => $uid,
        ];
        return $this->getORM()->queryAll($sql, $data);
    }


    public function gerenbangV2($uid, $where){
        $where = $where ? 'and ' . $where : '';
        $sql   = "select user_nicename,signature,votestotal,consumption,avatar_thumb,is_live,cmf_user.verify is_vip,
sum(totalcoin)  votes_sum from cmf_gift_record left join cmf_user on {$where} where id = :touid;";
        $data  = [
            ':touid' => $uid,
        ];
        return $this->getORM()->queryAll($sql, $data);
    }

    public function attackListV2($type)
    {
        $todayTime = strtotime(date('Y-m-d 00:00:00'));
        $startTime = strtotime('2020-10-02 00:00:00');
        $endTime = strtotime('2020-10-08 24:00:00');
        $sql = "select gr.uid,sum(totalcoin) coin from cmf_gift_record gr left join cmf_user u on gr.touid=u.id where u.verify=1";
        if ($type == 1) {
            $sql .= " and gr.addtime >= $todayTime and gr.addtime < $endTime";
        } else {
            $sql .= " and gr.addtime >= $startTime and gr.addtime < $endTime";
        }
        $sql .= " group by gr.uid order by coin desc limit 50";
        return $this->getORM()->queryAll($sql);
    }

    public function deadListV2($type)
    {
        $todayTime = strtotime(date('Y-m-d 00:00:00'));
        $startTime = strtotime('2020-10-02 00:00:00');
        $endTime = strtotime('2020-10-08 24:00:00');
        $sql = "select gr.touid,sum(totalcoin) coin,u.user_nicename,u.avatar from cmf_gift_record gr left join cmf_user u on gr.touid=u.id where u.verify=1";
        if ($type == 1) {
            $sql .= " and gr.addtime >= $todayTime and gr.addtime < $endTime";
        } else {
            $sql .= " and gr.addtime >= $startTime and gr.addtime < $endTime";
        }
        $sql .= " group by gr.touid order by coin desc limit 50";
        return $this->getORM()->queryAll($sql);
    }

    public function nationalDay() {
        ini_set ('memory_limit', '2048M');
        $startTime = strtotime('2020-10-02 00:00:00');
        $endTime = strtotime('2020-10-08 24:00:00');
//        $sql = "select gr.uid,gr.touid,gr.totalcoin from cmf_gift_record gr left join cmf_user u on gr.touid=u.id where u.verify=1 and gr.addtime >= $startTime and gr.addtime < $endTime";
//        $list = $this->getORM()->queryAll($sql);
        $elastic = new Elast_GiftRecord();
        $list = $elastic->goalList($startTime, $endTime);
        var_dump($list);die;
        $toUid = array_unique(array_column($list, 'touid'));
        $toUidArr = [];
        foreach ($toUid as $v) {
            $toUidArr[$v] = [
                'uid' => $v,
                'coin' => 0
            ];
        }
        $uid = array_unique(array_column($list, 'uid'));
        $uidArr = [];
        foreach ($uid as $v) {
            $uidArr[$v] = [
                'uid' => $v,
                'duck' => 0
            ];
        }
        foreach ($list as $v) {
            $before = $toUidArr[$v['touid']]['coin'];
            $toUidArr[$v['touid']]['coin'] += $v['totalcoin'];
            $now = $toUidArr[$v['touid']]['coin'];
            $beforeDuck = floor($before / self::DUCK_NUM);
            $nowDuck = floor($now / self::DUCK_NUM);
            $ducks = $nowDuck - $beforeDuck;
            if ($ducks) {
                $uidArr[$v['uid']]['duck'] += $ducks;
            }
        }
        $duck = [];
        foreach ($uidArr as $v) {
            if (!$v['duck']) {
                continue;
            }
            $tem = [
                'uid'           => $v['uid'],
                'duck' => $v['duck'],
                'add_date' => '2020-10-08',
            ];
            $duck[] = $tem;
        }
        DI()->notorm->duck->insert_multi($duck);
        die;
//        setcaches('live:duck:goalList:2020-10-02', $duck);
    }
}
