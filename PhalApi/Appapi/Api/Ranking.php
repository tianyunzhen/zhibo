<?php

/**
 * 排位赛
 */
class Api_Ranking extends PhalApi_Api{

    public function getRules(){
        return [
            'rankingList'           => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'],
                'page'  => ['name' => 'page', 'type' => 'int', 'require' => true, 'min' => 1, 'default' => 0, 'desc' => '页码'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'type'  => ['name' => 'type', 'type' => 'int', 'min' => 1, 'max' => 2, 'require' => true, 'desc' => '1当日  2昨日'],
            ],
            'rankingLiveInfo'       => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'require' => true, 'desc' => '主播ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'type'    => ['name' => 'type', 'type' => 'string', 'min' => 1, 'max' => 2, 'default' => 1, 'desc' => '1今天 2昨天'],
            ],
            'rankingFamilyDayInfo'  => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'page'  => ['name' => 'page', 'type' => 'int', 'require' => true, 'min' => 1, 'default' => 0, 'desc' => '页码'],
                'type'  => ['name' => 'type', 'type' => 'string', 'require' => true, 'desc' => '1今日 2昨日'],
            ],
            'rankingFamilyWeekInfo' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'],
                'page'  => ['name' => 'page', 'type' => 'int', 'require' => true, 'min' => 1, 'default' => 0, 'desc' => '页码'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'type'  => ['name' => 'type', 'type' => 'string', 'require' => true, 'desc' => '1本周 2上周'],
            ],
            'rankingUser'           => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'type'  => ['name' => 'type', 'type' => 'string', 'require' => true, 'desc' => '1直播奖励 2家族日奖励 3家族周奖励'],
                'page'  => ['name' => 'page', 'type' => 'string', 'require' => true, 'desc' => '页码'],
            ],
            'userReceive'           => [
                'uid'        => ['name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'],
                'token'      => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'ranking_id' => ['name' => 'ranking_id', 'type' => 'int', 'require' => true, 'desc' => '奖励ID'],
            ],
        ];
    }

    /**
     * 主播日排行榜
     * @desc 用于 获取主播日排行榜
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[list]
     * @return string user_nicename 昵称
     * @return string avatar_thumb 头像
     * @return string totalcoin 总金币
     * @return string times 直播时长
     *
     * @return string info[is_bang] 1上榜 2未上榜
     * @return string msg 提示信息
     */
    public function rankingList(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $type       = checkNull($this->type);
        $page       = checkNull($this->page);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $model      = new Domain_Ranking();
        $rs['info'] = $model->rankingList($type, $page);
        return $rs;
    }

    /**
     * 主播排行信息
     * @desc 用于 获取主播排行信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string no 排名
     * @return string money 流水
     * @return string adjacent_money 与上一名流水差距
     * @return string name 用户名
     * @return string head_pic 头像
     * @return string id 用户ID
     * @return string level_money 档位金额差
     * @return string level_title 档位标题
     * @return string level_num 档位等级
     * @return string msg 提示信息
     */
    public function rankingLiveInfo(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $live_id    = checkNull($this->liveuid);
        $token      = checkNull($this->token);
        $type       = checkNull($this->type);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }
        $model = new Domain_Ranking();
        $info  = $model->rankingLiveInfo($live_id, $type);
        if(!$info){
            $rs['code'] = 1000;
        }else{
            $rs['info'] = $info;
        }
        return $rs;
    }

    /**
     * 家族日排行榜
     * @desc 用于 获取家族日排行榜
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string counts 有效主播
     * @return string user_nicename 家族长昵称
     * @return string avatar_thumb 头像
     * @return string total 金额
     * @return string name 家族名
     * @return string msg 提示信息
     */
    public function rankingFamilyDayInfo(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $type       = checkNull($this->type);
        $page       = checkNull($this->page);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }
        $model      = new Domain_Ranking();
        $rs['info'] = $model->rankingFamilyDayInfo($type, $page);
        return $rs;
    }

    /**
     * 家族周排行榜
     * @desc 用于 获取家族周排行榜
     * @return int code 操作码，0表示成功
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string counts 有效主播
     * @return string user_nicename 家族长昵称
     * @return string avatar_thumb 头像
     * @return string total 金额
     * @return string name 家族名
     * @return string msg 提示信息
     */
    public function rankingFamilyWeekInfo(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $type       = checkNull($this->type);
        $page       = checkNull($this->page);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }
        $model      = new Domain_Ranking();
        $rs['info'] = $model->rankingFamilyWeekInfo($type, $page);
        return $rs;
    }

    /**
     * 奖励列表
     * @desc 用于 获取奖励列表
     * @return int code 操作码，0表示成功
     * @return int code 操作码，0表示成功
     * @return array info
     *               list 奖励信息
     * @return string list.id id
     * @return string list.no 排名
     * @return string list.gear 档位
     * @return string list.money 金额
     * @return string list.periods 期数
     * @return string list.status 状态 1已领取 2未领取 3待审核 (为空是表示未达成)
     * @return string list.type 类型 1主播榜 2家族日榜 3家族周榜 4任务奖励
     * @return string list.live_time 直播时长
     * @return string list.live_man 有效主播
     *                user_info 个人信息
     * @return string user_info.avatar_thumb 头像
     * @return string user_info.user_nicename 昵称
     */
    public function rankingUser(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $page       = checkNull($this->page);
        $type       = checkNull($this->type);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }
        $model      = new Domain_Ranking();
        $rs['info'] = $model->getRewardList($type, $uid, $page);
        return $rs;
    }

    /**
     * 领取奖励
     * @desc 用于领取奖励
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
    public function userReceive(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $ranking_id = checkNull($this->ranking_id);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效，请重新登陆！';
            return $rs;
        }
        $model      = new Domain_Ranking();
        $rs['info'] = $model->userReceive($ranking_id, $uid);
        return $rs;
    }

//    public function calculationUserDay(){
//        $domain = new Domain_Ranking();
//        $domain->timingUserDay();
//    }
//
//    public function calculationFamilyDay(){
//        $domain = new Domain_Ranking();
//        $domain->timingFamilyDay();
//    }
//
//    public function calculationFamilyWeek(){
//        $domain = new Domain_Ranking();
//        $domain->timingFamilyWeek();
//    }

    public function effective(){
        set_time_limit(0);
        $domain = new Domain_Ranking();
        $redis     = DI()->redis;
        $log_path = API_ROOT.'/Runtime/ranking.log';
        //有效主播
        $setnx_key = 'effective' . date("ymd");
        $setnx     = $redis->SETNX($setnx_key, 1);
        if($setnx){
            $redis->EXPIRE($setnx_key, 83000);
            try{
                $domain->effective();
                $msg = date('Y-m-d H:i:s') . ' ' . '执行成功：effective' . PHP_EOL;
                file_put_contents($log_path,$msg,FILE_APPEND);
            }catch(\Exception $e){
                $msg = date('Y-m-d H:i:s') . ' ' . '执行失败：effective '. $e->getMessage() . PHP_EOL;
                file_put_contents($log_path,$msg,FILE_APPEND);
            }
        }

        //主播日奖励
        $calculationUserDay = 'timingUserDay' . date("ymd");
        $setnx1     = $redis->SETNX($calculationUserDay, 1);
        if($setnx1){
            try{
                $redis->EXPIRE($calculationUserDay, 83000);
                $domain->timingUserDay();
                $msg = date('Y-m-d H:i:s') . ' ' . '执行成功：timingUserDay' . PHP_EOL;
                file_put_contents($log_path,$msg,FILE_APPEND);
            }catch(\Exception $e){
                $msg = date('Y-m-d H:i:s') . ' ' . '执行失败：timingUserDay '. $e->getMessage() . PHP_EOL;
                file_put_contents($log_path,$msg,FILE_APPEND);
            }
        }

        //家族日奖励
        $timingFamilyDay = 'timingFamilyDay' . date("ymd");
        $setnx2     = $redis->SETNX($timingFamilyDay, 1);
        if($setnx2){
            try{
                $redis->EXPIRE($timingFamilyDay, 83000);
                $domain->timingFamilyDay();
                $msg = date('Y-m-d H:i:s') . ' ' . '执行成功：timingFamilyDay' . PHP_EOL;
                file_put_contents($log_path,$msg,FILE_APPEND);
            }catch(\Exception $e){
                $msg = date('Y-m-d H:i:s') . ' ' . '执行失败：timingFamilyDay '. $e->getMessage() . PHP_EOL;
                file_put_contents($log_path,$msg,FILE_APPEND);
            }
        }

        //家族周奖励
        if(date('w') == 1)
        {
            $timingFamilyWeek = 'timingFamilyWeek' . date("ymd");
            $setnx3     = $redis->SETNX($timingFamilyWeek, 1);
            if($setnx3){
                try{
                    $redis->EXPIRE($timingFamilyWeek, 83000);
                    $domain->timingFamilyWeek();
                    $msg = date('Y-m-d H:i:s') . ' ' . '执行成功：timingFamilyWeek' . PHP_EOL;
                    file_put_contents($log_path,$msg);
                }catch(\Exception $e){
                    $msg = date('Y-m-d H:i:s') . ' ' . '执行失败：timingFamilyWeek '. $e->getMessage() . PHP_EOL;
                    file_put_contents($log_path,$msg);
                }
            }
        }
    }
}