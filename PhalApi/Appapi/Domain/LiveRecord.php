<?php

class Domain_LiveRecord{

    public function userLiveTimes($uid, $time = 0){
        //计算加v时间
        $user_remark_model = new Model_UserRemark();
        $start_time        = $user_remark_model->getStartTime($uid);
        $start             = strtotime(date('Ymd 00:00:00',$time));
        if (intval($start_time) > $start){
            $start = intval($start_time);
        }
        $end          = strtotime(date('Ymd 23:59:59',$time));
        $record_model = new Model_LiveRecord();
        $record_info  = $record_model->getUserTimes($uid, $start, $end);
        $times        = 0;
        foreach($record_info as $k => $v){
            if(!$v['min_t']){
                break;
            }
            $times = $v['total'] ?: 0;
            if($v['min_t'] < $start)
            {
                $times -= $start - $v['min_t'];
            }
            if($v['max_t'] > $end)
            {
                $times -= $v['max_t'] - $end;
            }
        }
        $level_model = new Model_Live();
        $starttime   = $level_model->getLiveTime($uid);
        if($starttime){
            time() < $end && $end = time();
            if($starttime < $start)
            {
                $times += $end - $start;
            }elseif($starttime > $start && $starttime < $end){
                $times += $end - $starttime;
            }
        }

        return $times;
    }
}
