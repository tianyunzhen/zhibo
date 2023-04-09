<?php

class Model_Family extends PhalApi_Model_NotORM{
    protected $tableName = 'family';

    /**
     * 家族主播收入榜
     *
     * @param $where
     */
    public function listNo($where=''){
        if($where)
        {
            $where = ' and ' . $where;
        }
        $sql = "select jg.total votes,jg.uid,jg.`name` user_nicename,u.votestotal,u.consumption,u.avatar_thumb,
  (select count(*) from cmf_user_remark where uid = u.id and status = 1) verify from 
(select f.name,tj.total,f.id,f.uid from (
select ifnull(sum(totalcoin),0) total,fu.familyid from cmf_family_user fu left join cmf_gift_record gr on fu.uid = gr.touid where gr.addtime >= fu.addtime {$where}  group by fu.familyid having total > 0 order by total desc limit 30) tj 
left join cmf_family f on tj.familyid = f.id) jg left join cmf_user u on jg.uid = u.id order by votes desc";
        return $this->getORM()->queryAll($sql);
    }

    public function familyInfo($uid, $fields = "*"){
        $sql          = "select {$fields} from cmf_family where uid = :uid";
        $data[':uid'] = $uid;
        $res          = $this->getORM()->queryAll($sql, $data);
        if($res){
            return $res[0];
        }else{
            return [];
        }
    }

    public function familyLsTj($familyId,$where)
    {
        $sql = "select ifnull(sum(gr.totalcoin),0) totals from cmf_family_user fu left join cmf_gift_record gr on fu.uid = gr.touid where fu.familyid = :fid and gr.addtime >= fu.uptime {$where}";
        $res = $this->getORM()->queryAll($sql,[':fid'=>$familyId]);
        return $res;
    }

    public function getFamilyAdmin($uid)
    {
        $sql = "select f.id,f.name,u.user_nicename,u.avatar from cmf_family f join cmf_user u on f.uid = u.id where f.uid = :sid and f.state = 2";
        return $this->getORM()->query($sql, [':sid' => $uid])->fetch();
    }

    public function familyProfit($familyId, $start_time, $end_time)
    {
        $sql = "select sum(fp.profit) as family_profit, sum(fp.profit_anthor) as anchor_profit from cmf_family_profit fp join cmf_family_user fu where fu.uid = fp.uid and fp.addtime >= fu.addtime and fp.familyid = :fid and fu.state != 3";
        $arr = [':fid' => $familyId];
        $profit = $this->getORM()->query($sql, $arr)->fetch();
        if (!$start_time && !$end_time) {
            $timeProfit = $profit;
        } else {
            if ($start_time) {
                $sql           .= " and fp.addtime >= :stime";
                $arr[':stime'] = $start_time;
            }
            if ($end_time) {
                $sql           .= " and fp.addtime <= :etime";
                $arr[':etime'] = $end_time + 86400;
            }
            $timeProfit = $this->getORM()->query($sql, $arr)->fetch();
        }
        return [$profit, $timeProfit];
    }

    public function getLiveRecord($familyId, $start_time, $end_time)
    {
        $sql = "select sum(lr.endtime - lr.starttime) as live_length, sum(lr.gift_count) as gift_count, count(lr.id) as live_count
            from cmf_family_user fu left join cmf_live_record lr on fu.uid = lr.uid where fu.familyid = :fid and lr.addtime >= fu.addtime and fu.state != 3 and starttime > 0";
        $arr = [':fid' => $familyId];
        $record = $this->getORM()->query($sql, $arr)->fetch();
        if (!$start_time && !$end_time) {
            $timeRecord = $record;
        } else {
            if ($start_time) {
                $sql .= " and lr.addtime >= :stime";
                $arr[':stime'] = $start_time;
            }
            if ($end_time) {
                $sql .= " and lr.addtime <= :etime";
                $arr[':etime'] = $end_time + 86400;
            }
            $timeRecord = $this->getORM()->query($sql, $arr)->fetch();
        }
        return [$record, $timeRecord];
    }

    public function getUserFamilyInfo($anchorId)
    {
        $sql = "select f.id,f.name,u.user_nicename,u.avatar,f.state as f_state,fu.state as fu_state,fu.addtime from cmf_user u left join cmf_family_user fu on u.id = fu.uid left join cmf_family f on f.id = fu.familyid where u.id = :sid";
        $arr = [':sid' => $anchorId];
        return $this->getORM()->query($sql, $arr)->fetch();
    }

    public function getUserLiveRecord($anchorId, $start_time, $end_time, $add_time = 0)
    {
        $sql = "select sum(endtime - starttime) as live_length, sum(gift_count) as gift_count, count(*) as live_count, sum(nums) as audience_count,
                sum(gift_sender_num) as gift_sender_count, sum(votes) as total_votes from cmf_live_record where uid= :sid and starttime > 0";
        $arr = [':sid' => $anchorId];
        if ($add_time) {
            $sql .= " and addtime >= :atime";
            $arr[':atime'] = $add_time;
        }
        $record = $this->getORM()->query($sql, $arr)->fetch();
        if (!$start_time && !$end_time) {
            $timeRecord = $record;
        } else {
            if ($start_time) {
                $sql .= " and addtime >= :stime";
                $arr[':stime'] = $start_time;
            }
            if ($end_time) {
                $sql .= " and addtime <= :etime";
                $arr[':etime'] = $end_time + 86400;
            }
            $timeRecord = $this->getORM()->query($sql, $arr)->fetch();
        }
        return [$record, $timeRecord];
    }

    /**
     * 获取用户家族信息
     * @param        $uid
     * @param string $fields
     * @return array
     */
    public function userFamilyInfo($uid,$fields='*')
    {
        $sql = "select {$fields} from cmf_family_user a left join cmf_family b on a.familyid = b.id where a.uid={$uid}";
        return $this->getORM()->queryAll($sql);
    }

    public function familyUserList($uid)
    {
        $sql = "select 
uid,title,stream,pull,thumb,isvideo,type,type_val,goodnum,anyway,starttime,isshop,game_action,lng,lat
from cmf_live where uid in(select uid from cmf_family_user where familyid = (
select familyid from cmf_family_user where uid = {$uid}) and uid <> {$uid}) and is_black = 0 
order by starttime desc limit 30";
        return $this->getORM()->queryAll($sql);
    }


    public function getLiveRecordAfterV($familyId, $startDay, $endDay, $anchorId = 0)
    {
        $sql = "select group_concat(ur.uid) as uids,group_concat(lr.id) as ids, sum(endtime-starttime) live_length,sum(votes) anchor_profit,lr.time from 
                      cmf_user_remark ur inner join cmf_family_user fu on ur.uid=fu.uid inner join cmf_live_record lr on ur.uid=lr.uid 
        where lr.addtime >= fu.addtime and fu.state != 3 and starttime > 0 and lr.time >= :stime and lr.time <= :etime and fu.familyid = :fid and lr.addtime >= ur.addtime";
        $arr = [':fid' => $familyId, ':stime' => $startDay, ':etime' => $endDay];
        if ($anchorId) {
            $sql .= " and ur.uid = :uid";
            $arr[':uid'] = $anchorId;
        }
        $sql .= " group by lr.time";
        $list = $this->getORM()->query($sql, $arr)->fetchAll();
        $liveLength = 0;
        $uids = '';
        $ids = '';
        $newList = [];
        $anchorProfit = 0;
        foreach ($list as &$v) {
            $liveLength += $v['live_length'];
            $anchorProfit += $v['anchor_profit'];
            $uids .= $v['uids'] . ',';
            $ids .= $v['ids'] . ',';
            $v['live_count'] = $v['uids'] ? count(array_unique(explode(',', $v['uids']))) : 0;
            $v['live_times'] = $v['ids'] ? count(array_unique(explode(',', $v['ids']))) : 0;
            $newList[$v['time']] = $v;
        }
        if (!$uids) {
            $liveCount = 0;
        } else {
            $uidsArr = explode(',', rtrim($uids, ','));
            $liveCount = count(array_unique($uidsArr));
        }
        if (!$ids) {
            $liveTimes = 0;
        } else {
            $idsArr = explode(',', rtrim($ids, ','));
            $liveTimes = count(array_unique($idsArr));
        }
        return [
            'list' => $newList,
            'live_length' => $liveLength,
            'live_count' => $liveCount,
            'live_times' => $liveTimes,
            'anchor_profit' => $anchorProfit
        ];
    }

    public function getLiveRecordV2($familyId)
    {
        $sql = "select sum(lr.endtime - lr.starttime) as live_length from cmf_family_user fu left join cmf_live_record lr on fu.uid = lr.uid where fu.familyid = :fid and lr.addtime >= fu.addtime and fu.state != 3 and starttime > 0";
        $arr = [':fid' => $familyId];
        return $this->getORM()->query($sql, $arr)->fetch();
    }

    public function getFamilyUserV2($familyId)
    {
        $sql = "select count(*) as number_count from cmf_family_user where familyid = :fid and state != 3";
        $arr = [':fid' => $familyId];
        return $this->getORM()->query($sql, $arr)->fetch();
    }

    public function familyProfitV2($familyId)
    {
        $sql = "select sum(fp.profit) as family_profit, sum(fp.profit_anthor) as anchor_profit from cmf_family_profit fp join cmf_family_user fu where fu.uid = fp.uid and fp.addtime >= fu.addtime and fp.familyid = :fid and fu.state != 3";
        $arr = [':fid' => $familyId];
        return $this->getORM()->query($sql, $arr)->fetch();
    }

    public function getUserLiveRecordV2($anchorId, $add_time = 0)
    {
        $sql = "select sum(endtime - starttime) as live_length, count(*) as live_count,sum(votes) as total_votes from cmf_live_record where uid= :sid and starttime > 0";
        $arr = [':sid' => $anchorId];
        if ($add_time) {
            $sql .= " and addtime >= :atime";
            $arr[':atime'] = $add_time;
        }
        return $this->getORM()->query($sql, $arr)->fetch();
    }

    public function getUserLiveProfitV2($anchorId, $add_time = 0)
    {
        $sql = "select sum(votes) as total_votes from cmf_user_voterecord where uid= :sid and action=1";
        $arr = [':sid' => $anchorId];
        if ($add_time) {
            $sql .= " and addtime >= :atime";
            $arr[':atime'] = $add_time;
        }
        return $this->getORM()->query($sql, $arr)->fetch();
    }

    public function getLiveRecordAfterV2($familyId, $startDay, $endDay, $anchorId = 0)
    {
        $disTime = strtotime($endDay) - strtotime($startDay);
        $days = $disTime / 86400;
        $originArr = [];
        for($i = 0; $i <= $days; $i++) {
            $date = date("Y-m-d", strtotime("-$i days", strtotime($endDay . ' 00:00:00')));
            $originArr[$date] = [
                'day' => $date,
                'live_length' => 0,
                'live_count' => [],
                'live_times' => 0,
            ];
        }
        $startTime = strtotime($startDay . ' 00:00:00') - 86400;
        $endTime = strtotime($endDay . ' 00:00:00') + 86400;
        $sql = "select ur.uid,lr.id,endtime,starttime,lr.time from cmf_user_remark ur inner join cmf_family_user fu on ur.uid=fu.uid inner join cmf_live_record lr on ur.uid=lr.uid 
        where lr.addtime >= fu.addtime and fu.state != 3 and starttime > 0 and starttime > :stime and starttime < :etime and fu.familyid = :fid and lr.addtime >= ur.addtime";
        $arr = [':fid' => $familyId, ':stime' => $startTime, ':etime' => $endTime];
        if ($anchorId) {
            $sql .= " and ur.uid = :uid";
            $arr[':uid'] = $anchorId;
        }
        $list = $this->getORM()->query($sql, $arr)->fetchAll();
        $totalLiveCount = count(array_unique(array_column($list, 'uid')));//总开播人数
        $totalLiveTime = count($list);//总开播次数
        foreach ($list as $v) {
           if (date('Y-m-d', $v['endtime']) == $v['time']) {
               $length = $v['endtime'] - $v['starttime'];
           } else {
               $nextDay = date('Y-m-d',  $v['endtime']);
               $nextTime = strtotime($nextDay);
               $length = $nextTime - $v['starttime'];
               $nextLength = $v['endtime'] - $nextTime;
               if ($v['starttime'] >= $nextTime) {
                   $nextLength = $v['endtime'] - $v['starttime'];
               }
               if (isset($originArr[$nextDay])) {
                   $originArr[$nextDay]['live_length'] += $nextLength;
               }
           }
           if ($length < 0) {
               $length = 0;
           }
           if (isset($originArr[$v['time']])) {
               $originArr[$v['time']]['live_length'] += $length;
               $originArr[$v['time']]['live_times'] += 1;
               array_push($originArr[$v['time']]['live_count'],$v['uid']);
           }
        }
        $totalLiveLength = array_sum(array_column($originArr, 'live_length'));
        return [
            'list' => $originArr,
            'live_length' => $totalLiveLength,
            'live_count' => $totalLiveCount,
            'live_times' => $totalLiveTime,
        ];
    }

    public function getLiveProfitAfterV2($familyId, $startDay, $endDay, $anchorId = 0)
    {
        $disTime = strtotime($endDay) - strtotime($startDay);
        $days = $disTime / 86400;
        $originArr = [];
        for($i = 0; $i <= $days; $i++) {
            $date = date("Y-m-d", strtotime("-$i days", strtotime($endDay . ' 00:00:00')));
            $originArr[$date] = [
                'day' => $date,
                'anchor_profit' => 0,
            ];
        }
        $startTime = strtotime($startDay . ' 00:00:00');
        $endTime = strtotime($endDay . ' 00:00:00') + 86400;
        $sql = "select sum(uv.votes) as anchor_profit,FROM_UNIXTIME(uv.addtime,'%Y-%m-%d') as time from cmf_user_remark ur inner join cmf_family_user fu on ur.uid=fu.uid inner join cmf_user_voterecord uv on ur.uid=uv.uid 
        where uv.addtime >= fu.addtime and fu.state != 3 and uv.addtime >= :stime and uv.addtime < :etime and fu.familyid = :fid and uv.addtime >= ur.addtime and uv.action=1 ";
        $arr = [':fid' => $familyId, ':stime' => $startTime, ':etime' => $endTime];
        if ($anchorId) {
            $sql .= " and uv.uid = :uid";
            $arr[':uid'] = $anchorId;
        }
        $sql .= " group by FROM_UNIXTIME(uv.addtime,'%Y-%m-%d')";
        $list = $this->getORM()->query($sql, $arr)->fetchAll();
        $totalAnchorProfit = 0;
        foreach ($list as $v) {
            $totalAnchorProfit += $v['anchor_profit'];
            if (isset($originArr[$v['time']])) {
                $originArr[$v['time']]['anchor_profit'] += $v['anchor_profit'];
            }
        }
        return [
            'list' => $originArr,
            'anchor_profit' => $totalAnchorProfit,
        ];
    }

    public function getLiveRecordDailyV2($familyId, $day, $page)
    {
        $dayTime = strtotime($day . ' 00:00:00');
        $startTime = $dayTime - 86400;
        $endTime = $dayTime + 86400;
        $pNum = 20;
        $offset = ($page - 1) * $pNum;
        $arr = [':fid' => $familyId, ':stime' => $startTime, ':etime' => $endTime];
        $commonSql = "select group_concat(starttime) starttimes,group_concat(endtime) endtimes,group_concat(lr.time) times,ur.uid";
        $sql = " from cmf_user_remark ur inner join cmf_family_user fu on ur.uid=fu.uid inner join cmf_live_record lr on ur.uid=lr.uid 
        inner join cmf_user u on ur.uid=u.id where familyid = :fid and starttime > :stime and starttime < :etime and fu.state != 3 and lr.addtime >= fu.addtime and lr.addtime >= ur.addtime group by ur.uid";
        $pageSql = $commonSql . ",u.user_nicename". $sql ." limit $offset,$pNum";
        $allSql = $commonSql . $sql;
        $pageRes = $this->getORM()->query($pageSql, $arr)->fetchAll();
        $allRes = $this->getORM()->query($allSql, $arr)->fetchAll();
        $totalLiveLength = 0;
        $totalLiveCount = [];
        foreach ($allRes as $v) {
            $startArr = explode(',', $v['starttimes']);
            $endArr = explode(',', $v['endtimes']);
            $timeArr = explode(',', $v['times']);
            foreach ($startArr as $k => $vv) {
                $length = 0;
                if (date('Y-m-d', $vv) == $day && date('Y-m-d', $endArr[$k]) == $day) {
                    $length = $endArr[$k] - $vv;
                }
                if (date('Y-m-d', $vv) == date('Y-m-d', $startTime) && date('Y-m-d', $endArr[$k]) == $day) {
                    $length = $endArr[$k] - $dayTime;
                }
                if (date('Y-m-d', $vv) == $day && date('Y-m-d', $endArr[$k]) == date('Y-m-d', $endTime)) {
                    $length = $endTime - $vv;
                }
                $totalLiveLength += $length;
                if ($timeArr[$k] == $day) {
                    array_push($totalLiveCount, $v['uid']);
                }
            }
        }
        foreach ($pageRes as &$v) {
            $startArr = explode(',', $v['starttimes']);
            $endArr = explode(',', $v['endtimes']);
            $temLength = 0;
            foreach ($startArr as $k => $vv) {
                $length = 0;
                if (date('Y-m-d', $vv) == $day && date('Y-m-d', $endArr[$k]) == $day) {
                    $length = $endArr[$k] - $vv;
                }
                if (date('Y-m-d', $vv) == date('Y-m-d', $startTime) && date('Y-m-d', $endArr[$k]) == $day) {
                    $length = $endArr[$k] - $dayTime;
                }
                if (date('Y-m-d', $vv) == $day && date('Y-m-d', $endArr[$k]) == date('Y-m-d', $endTime)) {
                    $length = $endTime - $vv;
                }
                $temLength += $length;
            }
            $v['live_length'] = $temLength;
            unset($v['starttimes']);
            unset($v['endtimes']);
            unset($v['times']);
        }
        return [
            'total_live_count' => count(array_unique($totalLiveCount)),
            'total_live_length' => $totalLiveLength,
            'list' => $pageRes,
        ];
    }

    public function getDailyProfitV2($familyId, $day, $uids)
    {
        $dayTime = strtotime($day . ' 00:00:00');
        $nextTime = $dayTime + 86400;
        $commonSql = "select ur.uid,sum(uv.votes) as anchor_profit from cmf_user_remark ur inner join cmf_family_user fu on ur.uid=fu.uid inner join cmf_user_voterecord uv on ur.uid=uv.uid 
        where uv.addtime >= fu.addtime and fu.state != 3 and fu.familyid = :fid and uv.addtime >= ur.addtime and uv.action=1 and uv.addtime >= :stime and uv.addtime < :etime";
        $sql = ' group by ur.uid';
        $pageSql = $commonSql . " and ur.uid in ($uids)"  . $sql;
        $allSql = $commonSql . $sql;
        $arr = [':fid' => $familyId, ':stime' => $dayTime, ':etime' => $nextTime];
        $originArr = [];
        if ($uids) {
            $uidsArr = explode(',', $uids);
            foreach ($uidsArr as $v) {
                $originArr[$v] = [
                    'anchor_profit' => 0,
                    'family_profit' => 0,
                ];
            }
            $pageList = $this->getORM()->query($pageSql, $arr)->fetchAll();
            foreach ($pageList as $v) {
                $originArr[$v['uid']]['anchor_profit'] += $v['anchor_profit'];
                $originArr[$v['uid']]['family_profit'] += $v['anchor_profit'] / 10;
            }
        }
        $allList = $this->getORM()->query($allSql, $arr)->fetchAll();
        $totalAnchorProfit = array_sum(array_column($allList, 'anchor_profit'));
        return [
            'total_anchor_profit' => $totalAnchorProfit,
            'list' => $originArr,
        ];
    }

    public function getLiveRecordAll($startDay, $endDay, $anchorId)
    {
        $disTime = strtotime($endDay) - strtotime($startDay);
        $days = $disTime / 86400;
        $originArr = [];
        for($i = 0; $i <= $days; $i++) {
            $date = date("Y-m-d", strtotime("-$i days", strtotime($endDay . ' 00:00:00')));
            $originArr[$date] = [
                'day' => $date,
                'live_length' => 0,
                'live_times' => 0,
            ];
        }
        $startTime = strtotime($startDay . ' 00:00:00') - 86400;
        $endTime = strtotime($endDay . ' 00:00:00') + 86400;
        $sql = "select endtime,starttime,time from cmf_live_record where starttime > 0 and starttime > :stime and starttime < :etime and uid=:uid";
        $arr = [':uid' => $anchorId, ':stime' => $startTime, ':etime' => $endTime];
        $list = $this->getORM()->query($sql, $arr)->fetchAll();
        $totalLiveTime = count($list);//总开播次数
        foreach ($list as $v) {
            if (date('Y-m-d', $v['endtime']) == $v['time']) {
                $length = $v['endtime'] - $v['starttime'];
            } else {
                $nextDay = date('Y-m-d',  $v['endtime']);
                $nextTime = strtotime($nextDay);
                $length = $nextTime - $v['starttime'];
                $nextLength = $v['endtime'] - $nextTime;
                if ($v['starttime'] >= $nextTime) {
                    $nextLength = $v['endtime'] - $v['starttime'];
                }
                if (isset($originArr[$nextDay])) {
                    $originArr[$nextDay]['live_length'] += $nextLength;
                }
            }
            if ($length < 0) {
                $length = 0;
            }
            if (isset($originArr[$v['time']])) {
                $originArr[$v['time']]['live_length'] += $length;
                $originArr[$v['time']]['live_times'] += 1;
            }
        }
        $totalLiveLength = array_sum(array_column($originArr, 'live_length'));
        return [
            'list' => $originArr,
            'live_length' => $totalLiveLength,
            'live_times' => $totalLiveTime,
        ];
    }

    public function getLiveProfitAll($startDay, $endDay, $anchorId)
    {
        $disTime = strtotime($endDay) - strtotime($startDay);
        $days = $disTime / 86400;
        $originArr = [];
        for($i = 0; $i <= $days; $i++) {
            $date = date("Y-m-d", strtotime("-$i days", strtotime($endDay . ' 00:00:00')));
            $originArr[$date] = [
                'day' => $date,
                'anchor_profit' => 0,
            ];
        }
        $startTime = strtotime($startDay . ' 00:00:00');
        $endTime = strtotime($endDay . ' 00:00:00') + 86400;
        $sql = "select sum(votes) as anchor_profit,FROM_UNIXTIME(addtime,'%Y-%m-%d') time from cmf_user_voterecord
        where addtime >= :stime and addtime < :etime and action=1 and uid=:uid";
        $arr = [':uid' => $anchorId, ':stime' => $startTime, ':etime' => $endTime];
        $sql .= " group by FROM_UNIXTIME(addtime,'%Y-%m-%d')";
        $list = $this->getORM()->query($sql, $arr)->fetchAll();
        $totalAnchorProfit = 0;
        foreach ($list as $v) {
            $totalAnchorProfit += $v['anchor_profit'];
            if (isset($originArr[$v['time']])) {
                $originArr[$v['time']]['anchor_profit'] += $v['anchor_profit'];
            }
        }
        return [
            'list' => $originArr,
            'anchor_profit' => $totalAnchorProfit,
        ];
    }

    public function getUserProfit($anchorId)
    {
        $sql = "select sum(votes) as anchor_profit from cmf_user_voterecord
        where action = 1 and uid=:uid";
        $arr = [':uid' => $anchorId];
        return $this->getORM()->query($sql, $arr)->fetch();
    }
}