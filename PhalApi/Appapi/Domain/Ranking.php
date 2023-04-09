<?php

class Domain_Ranking{

    protected $ranking_key;

    public function __construct(){
        $this->ranking_key = Common_Cache::ACTIVE_DAY_WATER . date("Ymd");
    }

    /**
     * 主播日排行榜列表
     * @param $type
     * @param $page
     * @param $is_sys
     * @return mixed
     */
    public function rankingList($type, $page, $is_sys = 2){
        if($is_sys > 1){
            if($page > 20){
                $data['list'] = [];
                return $data;
            }
            $total = 9;
        }else{
            $total = $page;
            $page  = 0;
        }

        $keyArr = [
            1 => [
                'key'   => Common_Cache::ACTIVE_RANGKING_SAME,
                'times' => 3,
            ],
            2 => [
                'key'   => Common_Cache::ACTIVE_RANGKING_YESTER,
                'times' => strtotime(date("Ymd 23:59:59")) - time(),
            ],
        ];
        if($type == 2){
            $t    = strtotime('-1 day');
            $time = strtotime(date('Ymd 00:00:00', $t));
        }else{
            $time = strtotime(date('Ymd 00:00:00'));
        }
        $key   = $keyArr[$type]['key'] . $page . '_' . date('Ymd');
        $redis = DI()->redis;
        $res   = $redis->get($key);
        if(!$res){
            $list             = [];
            $liveRecordDomain = new Domain_LiveRecord();
            $cacheKey         = Common_Cache::ACTIVE_DAY_WATER . date('Ymd', $time);
            $res              = $this->getCacheNo($cacheKey, $page, $total);
            if($res){
                foreach($res as $k => &$v){
                    $userInfo = getUserInfo($k);
                    if($userInfo){
                        $liveTimes = $liveRecordDomain->userLiveTimes($k, $time);
                        $list[]    = [
                            'avatar_thumb'  => $userInfo['avatar_thumb'] ?? '',
                            'times'         => (Int)$liveTimes,
                            'totalcoin'     => (Int)$v,
                            'id'            => $k,
                            'user_nicename' => $userInfo['user_nicename'],

                        ];
                    }
                }
                $redis->set($key, json_encode($list), $keyArr[$type]['times']);
            }
        }else{
            $list = json_decode($res, true);
        }
        $data['list'] = $list ?: [];
        return $data;
    }

    /**
     * 主播日排行信息
     * @param $uid
     * @param $type
     * @return bool
     */
    public function rankingLiveInfo($uid, $type){
        if($type > 1){
            $t                 = strtotime('-1 day');
            $this->ranking_key = Common_Cache::ACTIVE_DAY_WATER . date("Ymd", $t);
            $s_time            = strtotime(date('Ymd 00:00:00', $t));
            $whereTime         = mktime(0, 0, 0, date('m'), date('d'), date('y'));
            $remark_model      = new Model_UserRemark();
            $where             = "uid = {$uid} and addtime < $whereTime";
            if($remark_model->isRemark($where) < 1){
                return false;
            }
        }else{
            $s_time = strtotime(date('Ymd 00:00:00'));
        }
        $userInfo = getUserInfo($uid);
        if(!$userInfo['remark_info']){
            return false;
        }
        $liveNoData             = $this->liveNo($uid);
        $adjacentBranch         = $this->adjacent($liveNoData['no']);
        $data['no']             = (string)$liveNoData['no'];
        $data['money']          = (string)$liveNoData['branch'];
        $data['adjacent_money'] = (string)abs($liveNoData['branch'] - $adjacentBranch);
        $data['name']           = $userInfo['user_nicename'];
        $data['head_pic']       = $userInfo['avatar_thumb'];
        $data['id']             = $userInfo['id'];
        $level_info             = $this->getLevel(Model_RankingLevel::USER, (Int)$liveNoData['branch']);
        if($level_info['nums'] == "0"){
            $level_no = 18;
        }elseif($level_info['nums'] == 1){
            $level_no = 2;
        }else{
            $level_no = $level_info['nums'] - 1;
        }
        $level_money         = $this->noBranch($level_no);
        $data['level_money'] = (string)abs($level_money - $liveNoData['branch']);
        $data['level_title'] = $level_info['title'];
        $data['level_num']   = (string)$level_info['nums'];
        $live_record_domain  = new Domain_LiveRecord();
        $data['times']       = $v['times'] = $live_record_domain->userLiveTimes($uid, $s_time ?? 0);
        return $data;
    }

    public function getLevel($type, $no){
        $model = new Model_RankingLevel();
        $level = $model->getLevel($type, $no);
        if($level){
            $data['title'] = $level[0]['title'];
            $data['nums']  = $level[0]['no'];
        }else{
            $data['title'] = "未上榜";
            $data['nums']  = "0";
        }
        return $data;
    }

    /**
     * @param $no
     * @return int
     */
    public function noBranch($no){
        $model = new Model_RankingLevel();
        $info  = $model->getNoInfo($no);
        return $info[0]['min'];
    }

    /**
     * 获取成员排名和分数
     * @param $live_uid
     */
    public function liveNo($live_uid){
        $no = DI()->redis->ZREVRANK($this->ranking_key, $live_uid);
        if($no === false){
            $no     = DI()->redis->ZCARD($this->ranking_key) ?: 0;
            $branch = 0;
        }else{
            $branch = DI()->redis->ZSCORE($this->ranking_key, $live_uid);
        }
        $no             += 1;
        $data['no']     = $no;
        $data['branch'] = $branch;
        return $data;
    }

    /**
     * 获取相邻成员分数
     * @param     $no
     * @param int $type
     * @return mixed
     */
    public function adjacent($no){
        if($no > 1){
            $no -= 1;
        }else{
            $no += 1;
        }
        $no  -= 1;
        $man = DI()->redis->Zrevrange($this->ranking_key, $no, $no);
        if($man){
            $nums = DI()->redis->Zscore($this->ranking_key, $man[0]);
        }else{
            $nums = 0;
        }
        return $nums;
    }

    /**
     * 获取家族日排行榜列表
     * @param $type
     * @param $page
     * @return array|mixed
     */
    public function rankingFamilyDayInfo($type, $page, $is_sys = 2){
        if($is_sys > 1){
            if($page > 3){
                return [];
            }
            $total = 9;
        }else{
            $total = $page;
            $page  = 0;
        }

        if($type == 1) //当日
        {
            $time = time();
            $key  = Common_Cache::ACTIVE_RANKING_FAMILY_SAME . $page . '_' . date('ymd');
            $t    = 60;
        }else{
            $time = strtotime("-1 day");
            $key  = Common_Cache::ACTIVE_RANKING_FAMILY_YESTER . $page . '_' . date('ymd', $time);
            $t    = strtotime(date('Y-m-d 23:59:59')) - time();
        }
        $redis = DI()->redis;
        $info  = $redis->get($key);
        if(!$info){
            $cache_key      = Common_Cache::ACTIVE_FAMILY_DAY_WATER . date('Ymd', $time);
            $user_cache_key = Common_Cache::ACTIVE_DAY_WATER . date('Ymd', $time);
            $list           = $this->getCacheNo($cache_key, $page, $total);
            if(!$list){
                return [];
            }
            $famili_user_model  = new Model_FamilyUser();
            $famili_model       = new Model_Family();
            $live_record_domain = new Domain_LiveRecord();
            foreach($list as $k => $v){
                $live_num = 0;
                //有效主播
                $user_list = $famili_user_model->getFamilyInfo($k, 'uid');
                foreach($user_list as $kk => $vv){
                    $scoer = $redis->ZSCORE($user_cache_key, $vv['uid']);
                    if($scoer < 8000000) continue;
                    $times = $live_record_domain->userLiveTimes($vv['uid'], $time);
                    if($times < 7200) continue;
                    ++$live_num;
                }
                $family_info = $famili_model->get($k, 'uid,name');
                $user_info   = getUserInfo($family_info['uid']);
                $info[]      = [
                    'counts'        => $live_num,
                    'user_nicename' => $user_info['user_nicename'],
                    'avatar_thumb'  => $user_info['avatar_thumb'],
                    'total'         => $v,
                    'name'          => $family_info['name'],
                    'family_id'     => $k,
                    'uid'           => $family_info['uid'],
                ];
            }
            if($info){
                $redis->set($key, json_encode($info), $t);
            }
        }else{
            $info = json_decode($info, true);
        }
        return $info;
    }

    /**
     * 家族周排行榜列表
     * @param $type
     * @return array|mixed
     */
    public function rankingFamilyWeekInfo($type, $page, $is_sys = 2){
        if($is_sys > 1){
            if($page > 3){
                return [];
            }
            $total = 9;
        }else{
            $total = $page;
            $page  = 0;
        }
        if($type == 1) //本周
        {
            $start = mktime(0, 0, 0, date('m'), date('d') - (date('w') ?: 7) + 1, date('y'));
            $key   = Common_Cache::ACTIVE_RANKING_FAMILY_WEEK_SAME . $page . '_' . date('ymd');
            $t     = 30;
        }else{
            $start = mktime(0, 0, 0, date("m"), date("d") - (date('w') ?: 7) + 1 - 7, date("Y"));
            $key   = Common_Cache::ACTIVE_RANKING_FAMILY_WEEK_YESTER . $page . '_' . date('ymd');
            $t     = strtotime(date('Ymd 23:59:59')) - time();
        }
        $info = getcaches($key);
        if(!$info){
            $family_cache_key = Common_Cache::ACTIVE_FAMILY_WEEK_WATER . date('Ymd', $start);
            $list             = $this->getCacheNo($family_cache_key, $page, $total);
            if(!$list){
                return [];
            }
            $family_model = new Model_Family();
            $w            = date('w');
            if($w >= 4 || $w == 1 || $type = 2){
                $effective_key  = Common_Cache::ACTIVE_EFFECTIVE . date('Ymd', $start);
                $effective_info = getcaches($effective_key);
                if($effective_info){
                    $effective_info = array_count_values(json_decode($effective_info, true));
                }
            }else{
                $effective_info = false;
            }
            foreach($list as $k => $v){
                $live_num = 0;
                if(($w >= 4 || $w == 1 || $type = 2) && $effective_info){
                    foreach($effective_info as $kk => $vv){
                        $exp = explode('_', $kk);
                        if($exp[0] == $k && $vv >= 4){
                            ++$live_num;
                            unset($effective_info[$kk]);
                        }
                    }
                }
                $family_info = $family_model->get($k, 'uid,name');
                if(!$family_info) continue;
                $user_info = getUserInfo($family_info['uid']);
                $info[]    = [
                    'counts'        => $live_num,
                    'user_nicename' => $user_info['user_nicename'],
                    'avatar_thumb'  => $user_info['avatar_thumb'],
                    'total'         => $v,
                    'name'          => $family_info['name'],
                    'uid'           => $family_info['uid'],
                    'family_id'     => $k,
                ];
            }
            if(intval($w) == 1 && !$effective_info && intval($type) > 1){
                $t = 10;
            }
            if($info){
                setcaches($key, json_encode($info), $t);
            }
        }else{
            $info = json_decode($info, true);
        }
        return $info;
    }

    public function getRewardList($type, $uid, $page){
        $list              = [];
        $user_remark_model = new Model_UserRemark();
        $add_time          = $user_remark_model->uidSelect($uid, 'addtime');
        $model             = new Model_RankingUser();
        if($type == 3){
            if($add_time){
                $time    = date("Y-m-d 00:00:00", $add_time);
                $lastday = strtotime("$time Sunday");
                $list    = $model->getWeekList($uid, $page, $lastday);
            }
        }else{
            $list = $model->getUserList($type, $uid, $page);
        }
        foreach($list as $k => &$v){
            if($v['add_time']){
                $date           = strtotime(date('Y-m-d 00:00:00', $v['add_time']));
                $time           = strtotime("+30 day", $date);
                $v['desc_time'] = $time - time();
            }else{
                $v['desc_time'] = 0;
            }
            if($v['desc_time'] < 1){
                $v['desc_time'] = 0;
            }

            $v['live_time'] = $v['times'] ?: 0;
            $v['id']        = $v['id'] ?: 0;
            $v['live_man']  = $v['mans'] ?: 0;
            $v['gear']      = $v['gear'] ?: '未上榜';
            $v['no']        = $v['no'] ?: '无名次';
            $v['money']     = $v['money'] ?: 0;
            $v['periods']   = $v['money'] ?: $v['time'];
            $v['status']    = $v['status'] ?: '5';
            $v['type']      = $v['type'] ?: 0;
            unset($v['add_time']);
            unset($v['times']);
            unset($v['mans']);

        }
        $data['list']                       = $list;
        $user_info                          = getUserInfo($uid);
        $data['user_info']['avatar_thumb']  = $user_info['avatar_thumb'];
        $data['user_info']['user_nicename'] = $user_info['user_nicename'];
        return $data;
    }

    public function jiSuanDay($start, $page, $total){
//        $cha = ($page - 1) * $total;
//        $date = mktime('00','00','00',date('m'),date('d') - $cha,date('y'));
//        for($i=1; $i < $page)
    }

    public function userReceive($id, $uid){
        $model      = new Model_RankingUser();
        $is_receive = $model->get($id);
        if($is_receive && $is_receive['status'] == 1){
            return [1, '你已经领取过了'];
        }
        if($is_receive && $is_receive['uid'] != $uid){
            return [1, '领取错误'];
        }
        $update['upd_time'] = time();
        $update['status']   = Model_RankingUser::YES;
        if(!$model->update($is_receive['id'], $update)){
            return [1, '领取失败1'];
        }
        return [0, '领取成功'];
//        $user_model         = new Model_User();
//        DI()->notorm->user->queryAll('begin');
//        try{
//            if(!$model->update($is_receive['id'], $update)){
//                DI()->notorm->user->queryAll('rollback');
//                return [1, '领取失败1'];
//            }
//            if($is_receive['type'] == 1)//钻石
//            {
//                $vote_data  = [
//                    'type'    => Model_UserVoteRecord::INCOME,
//                    'action'  => Model_UserVoteRecord::PAIWEI,
//                    'uid'     => $uid,
//                    'fromid'  => 0,
//                    'nums'    => 0,
//                    'total'   => $is_receive['money'],
//                    'showid'  => 0,
//                    'votes'   => $is_receive['money'],
//                    'addtime' => time(),
//                ];
//                $vote_model = new Model_UserVoteRecord();
//                if(!$vote_model->insert($vote_data)){
//                    DI()->notorm->user->queryAll('rollback');
//                    return [3, '领取失败3'];
//                }
//                $user_data = [
//                    'votes'      => new NotORM_Literal("votes - {$is_receive['money']}"),
//                    'votestotal' => new NotORM_Literal("votestotal + {$is_receive['money']}"),
//                ];
//            }else{
//                //用户中奖流水
//                $coin_data  = [
//                    'type'      => Model_UserCoinRecord::INCOME,
//                    'action'    => Model_UserCoinRecord::PAIWEI,
//                    'uid'       => $uid,
//                    'touid'     => 0,
//                    'giftcount' => 0,
//                    'totalcoin' => $is_receive['money'],
//                    'showid'    => 0,
//                    'addtime'   => time(),
//                ];
//                $coin_model = new Model_UserCoinRecord();
//                if(!$coin_model->insert($coin_data)){
//                    DI()->notorm->user->queryAll('rollback');
//                    return [4, '领取失败4'];
//                }
//                $user_data = [
//                    'coin' => new NotORM_Literal("coin - {$is_receive['money']}"),
//                ];
//            }
//            if(!$user_model->update($uid, $user_data)){
//                DI()->notorm->user->queryAll('rollback');
//                return [2, '领取失败2'];
//            }
//        }catch(\Exception $e){
//            DI()->notorm->user->queryAll('rollback');
//            return [2, '领取失败' . $e->getMessage()];
//        }
//        DI()->notorm->user->queryAll('commit');
//        return [0, '领取成功'];
    }

    /**
     * 主播日排行奖励计算
     * @return bool
     */
    public function timingUserDay(){
        $rankLevelModel  = new Model_RankingLevel();
        $rankLevelInfo   = $rankLevelModel->getMinMoney();
        $pageMax         = 100000;
        $targetMoney     = (Int)$rankLevelInfo['min'];
        $targetMoneyMax  = (Int)$rankLevelInfo['max'];
        $targetTime      = (Int)$rankLevelInfo['time'];
        $targetNo        = (Int)$rankLevelInfo['no'];
        $info            = $this->rankingList(2, $pageMax, 1);
        $levelModel      = new Model_RankingLevel();
        $data            = [];
        $date            = mktime('00', '00', '00', date('m'), date('d') - 1, date('Y'));
        $no_num          = 0;
        $userRemarkModel = new  Model_UserRemark();
        foreach($info['list'] as $k => $v){
            ++$no_num;
            $gear               = '';
            $money              = 0;
            $type               = Model_RankingUser::USER;
            $status             = Model_RankingUser::NOO;
            $userLevelGiftMoney = $v['totalcoin']; //用户总流水
            $userLevelTime      = (Int)$v['times']; //用户直播时间
            if($userLevelGiftMoney >= $targetMoney && $userLevelTime >= $targetTime){
                $status           = Model_RankingUser::NO;
                $levelInfo        = $levelModel->getLevel(Model_RankingLevel::USER, $userLevelGiftMoney);
                $levelMoney       = $levelInfo[0]['money']; //等级奖励金额
                $levelTitle       = $levelInfo[0]['title']; //等级名称
                $nowLevelInfoTime = (Int)$levelInfo[0]['time'];
                $levelNo          = (Int)$levelInfo[0]['no'];
                if($nowLevelInfoTime <= $userLevelTime){
                    $gear  = $levelTitle;
                    $money = $levelMoney * 10000 * 100;
                    if($levelNo == 18){
                        //判断星耀主播
                        $remarkInfo = $userRemarkModel->getUserRemark($v['id']);
                        if($remarkInfo['level'] == 2 && $userLevelTime >= 10800 && $userLevelGiftMoney >= 10000000){
                            $gear  = $levelTitle . '(星耀)';
                            $money = ($levelMoney + 10) * 10000 * 100;
                        }
                    }
                }elseif($userLevelGiftMoney >= $targetMoneyMax){
                    $levelInfo = $levelModel->getNoInfo($targetNo - 1);
                    $gear      = $levelInfo[0]['title'];
                    $money     = $levelInfo[0]['money'] * 10000 * 100;
                }else{
                    $status = Model_RankingUser::NOO;
                }
            }
            $data[] = [
                'uid'      => $v['id'],
                'no'       => $no_num,
                'type'     => $type,
                'gear'     => $gear,
                'money'    => $money,
                'water'    => $userLevelGiftMoney,
                'periods'  => $date,
                'status'   => $status,
                'add_time' => time(),
                'times'    => $userLevelTime,
            ];
        }
        if($data){
            $user_level_model = new Model_RankingUser();
            if(!$user_level_model->insertMore($data)){
                return false;
            }else{
                return true;
            }
        }
        return true;
    }

    public function timingFamilyDay(){
        $pageMax    = 100000;
        $targetTime = 5;
        $info       = $this->rankingFamilyDayInfo(2, $pageMax, 1);
        $levelModel = new Model_RankingLevel();
        $date       = mktime('00', '00', '00', date('m'), date('d') - 1, date('Y'));
        $noNum      = 0;
        $data       = [];
        foreach($info as $k => $v){
            ++$noNum;
            $money  = 0;
            $title  = '';
            $mans   = $v['counts'];
            $status = Model_RankingUser::NOO;
            if($mans >= $targetTime && $noNum <= 15){
                $levelInfo = $levelModel->getLevel(Model_RankingLevel::FAMILY_DAY, $noNum);
                if($levelInfo){
                    $money  = $levelInfo[0]['money'] * 10000;
                    $title  = $levelInfo[0]['title'];
                    $status = Model_RankingUser::NO;
                }
            }
            $data[] = [
                'uid'       => $v['uid'],
                'no'        => $noNum,
                'type'      => Model_RankingUser::FAMILY_DAY,
                'gear'      => $title,
                'money'     => $money,
                'water'     => $v['total'],
                'family_id' => $v['family_id'],
                'periods'   => $date,
                'status'    => $status,
                'add_time'  => time(),
                'mans'      => $v['counts'],
            ];
        }

        if($data){
            $user_level_model = new Model_RankingUser();
            if(!$user_level_model->insertMore($data)){
                return false;
            }else{
                return true;
            }
        }
        return true;
    }

    public function timingFamilyWeek(){
        $page_max    = 100000;
        $target_time = 9;
        $info        = $this->rankingFamilyWeekInfo(2, $page_max, 1);
        $level_model = new Model_RankingLevel();
        $start       = mktime(0, 0, 0, date("m"), date("d") - (date("w") ?: 7) + 1 - 7, date("Y"));
        $yes_num     = 0;
        $no_num      = 0;
        $data        = [];
        foreach($info as $k => $v){
            ++$no_num;
            $status = Model_RankingUser::NOO;
            $money  = 0;
            $title  = '';
            if($v['counts'] > $target_time){
                ++$yes_num;
                $level_info = $level_model->getLevel(Model_RankingLevel::FAMILY_WEEK, $yes_num);
                if($level_info){
                    if($yes_num <= 10){
                        $money  = $level_info[0]['money'] * 10000;
                        $title  = $level_info[0]['title'];
                        $status = Model_RankingUser::NO;
                    }
                }
            }
            $data[] = [
                'uid'       => $v['uid'],
                'no'        => $no_num,
                'type'      => Model_RankingUser::FAMILY_WEEK,
                'gear'      => $title,
                'money'     => $money,
                'water'     => $v['total'],
                'family_id' => $v['family_id'],
                'periods'   => $start,
                'status'    => $status,
                'add_time'  => time(),
                'mans'      => $v['counts'],
            ];
        }

        if($data){
            $user_level_model = new Model_RankingUser();
            if(!$user_level_model->insertMore($data)){
                return false;
            }else{
                return true;
            }
        }
        return true;
    }

    /**
     * 获取集合数据
     * @param $key
     * @param $page
     * @param $total
     * @return mixed
     */
    public function getCacheNo($key, $page, $total){
        $pages = ($page > 1) ? (($page - 1) * $total) + ($page - 1) : 0;
        $total += $pages;
        $res   = DI()->redis->Zrevrange($key, $pages, $total, true);
        return $res;
    }

    /**
     * 计算每日有效主播
     */
    public function effective(){
        //获取最低金额
        $rankLevelModel     = new Model_RankingLevel();
        $levelInfo          = $rankLevelModel->getMinMoney();
        $time               = date('Ymd', strtotime('-1 day'));
        $key                = Common_Cache::ACTIVE_DAY_WATER . $time;
        $data               = DI()->redis->ZRANGEBYSCORE($key, 8000000, 'inf');
        $live_record_domain = new Domain_LiveRecord();
        if(date('w') == 1){
            $date = mktime(0, 0, 0, date("m"), date("d") - (date('w') ?: 7) + 1 - 7, date("Y"));
        }else{
            $date = mktime(0, 0, 0, date("m"), date("d") - (date('w') ?: 7) + 1, date("Y"));
        }
        $cache_key = Common_Cache::ACTIVE_EFFECTIVE . date('Ymd', $date);
        $param     = getcaches($cache_key);
        if($param){
            $arr = json_decode($param, true);
        }else{
            $arr = [];
        }
        $family_model = new Model_FamilyUser();
        foreach($data as $k => $v){
            //获取家族ID
            $familyId = $family_model->getFamiliId($v);
            if($familyId){
                //判断直播时长
                $times = $live_record_domain->userLiveTimes($v, strtotime($time));
                if($times >= 7200){
                    array_push($arr, $familyId . '_' . $v);
                }
            }
        }
        $tt = mktime(23, 59, 59, date("m"), date("d") - (date('w') ?: 7) + 8, date("Y"));
        setcaches($cache_key, json_encode($arr), $tt);
    }
}
