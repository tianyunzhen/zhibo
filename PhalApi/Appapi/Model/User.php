<?php

class Model_User extends PhalApi_Model_NotORM{
    /* 用户全部信息 */
    public function getBaseInfo($uid){
        $info = DI()->notorm->user
            ->select("id,user_nicename,avatar,avatar_thumb,sex,signature,coin,votes,consumption,votestotal,province,city,birthday,location")
            ->where('id=?  and user_type="2"', $uid)
            ->fetchOne();
        if($info){
            $info['avatar']       = get_upload_path($info['avatar']);
            $info['avatar_thumb'] = get_upload_path($info['avatar_thumb']);
            $info['level']        = getLevelV2($info['consumption']);
            $info['level_anchor'] = getLevelAnchorV2($info['votestotal']);
            $info['lives']        = getLives($uid);
            $info['follows']      = getFollows($uid);
            $info['fans']         = getFans($uid);

            $info['vip']   = getUserVip($uid);
            $info['liang'] = getUserLiang($uid);

            if($info['birthday']){
                $info['birthday'] = date('Y-m-d', $info['birthday']);
            }else{
                $info['birthday'] = '';
            }
        }


        return $info;
    }

    /* 判断昵称是否重复 */
    public function checkName($uid, $name){
        $isexist = DI()->notorm->user
            ->select('id')
            ->where('id!=? and user_nicename=?', $uid, $name)
            ->fetchOne();
        if($isexist){
            return 0;
        }else{
            return 1;
        }
    }

    /* 修改信息 */
    public function userUpdate($uid, $fields){
        /* 清除缓存 */
        delCache(Common_Cache::USERINFO . $uid);

        if(!$fields){
            return false;
        }

        return DI()->notorm->user
            ->where('id=?', $uid)
            ->update($fields);
    }

    /* 修改密码 */
    public function updatePass($uid, $oldpass, $pass){
        $userinfo = DI()->notorm->user
            ->select("user_pass")
            ->where('id=?', $uid)
            ->fetchOne();
        $oldpass  = setPass($oldpass);
        if($userinfo['user_pass'] != $oldpass){
            return 1003;
        }
        $newpass = setPass($pass);
        return DI()->notorm->user
            ->where('id=?', $uid)
            ->update(["user_pass" => $newpass]);
    }

    /* 我的钻石 */
    public function getBalance($uid){
        return DI()->notorm->user
            ->select("coin,score,votes")
            ->where('id=?', $uid)
            ->fetchOne();
    }

    /* 充值规则 */
    public function getChargeRules(){

        $rules = DI()->notorm->charge_rules
            ->select('id,coin,coin_ios,money,money_ios,product_id,give')
            ->order('list_order asc')
            ->fetchAll();

        return $rules;
    }

    /* 我的收益 */
    public function getProfit($uid){
        $info = DI()->notorm->user
            ->select("votes,votestotal")
            ->where('id=?', $uid)
            ->fetchOne();

        $config = getConfigPri();

        //提现比例
        $cash_rate      = $config['cash_rate'];
        $cash_start     = $config['cash_start'];
        $cash_end       = $config['cash_end'];
        $cash_max_times = $config['cash_max_times'];
        //剩余票数
        $votes = $info['votes'];

        //总可提现数
        $total = (string)floor($votes / $cash_rate);

        if($cash_max_times){
            //$tips='每月'.$cash_start.'-'.$cash_end.'号可进行提现申请，收益将在'.($cash_end+1).'-'.($cash_end+5).'号统一发放，每月只可提现'.$cash_max_times.'次';
            $tips = '每月' . $cash_start . '-' . $cash_end . '号可进行提现申请，每月只可提现'
                . $cash_max_times . '次';
        }else{
            //$tips='每月'.$cash_start.'-'.$cash_end.'号可进行提现申请，收益将在'.($cash_end+1).'-'.($cash_end+5).'号统一发放';
            $tips = '每月' . $cash_start . '-' . $cash_end . '号可进行提现申请';
        }

        $rs = [
            "votes"      => $votes,
            "votestotal" => $info['votestotal'],
            "total"      => $total,
            "cash_rate"  => $cash_rate,
            "tips"       => $tips,
        ];
        return $rs;
    }

    /* 提现  */
    public function setCash($data){

        //判断是否绑定支付宝
        $cash_account_model = new Model_CashAccount();
        $cash_account_info  = $cash_account_model
            ->getORM()
            ->where('uid = ?', $data['uid'])
            ->select('name,account')
            ->fetchOne();
        if(!$cash_account_info){
            return [1, '请先绑定支付宝'];
        }

        //读取配置
        $rate = 10000;
        $max  = 100;

        //判断金额是否达标
        $votes = $data['cashvote'] * $rate * 100;
        if($data['cashvote'] < 100 || ($data['cashvote'] % $max != 0)){
            return [1, '单次提现必须100元以上,且只能整百提现'];
        }

        //判断余额
        $user_info = $this->get($data['uid'], 'votes');
        if($user_info['votes'] < $votes){
            return [1, '余额不足'];
        }

        //提现次数限制
        $cashRecordModel = new Model_CashRecord();
        $recordData      = $cashRecordModel->getNowRecord($data['uid'], strtotime(date('Y-m-d 00:00:00')));
        foreach($recordData as $k => $v){
            if($v['status'] == Model_CashRecord::SHZ || $v['status'] == Model_CashRecord::TG){
                return [9, '当前每天只能发起一次申请'];
            }
            if($v['status'] == Model_CashRecord::CG){
                return [9, '当前每天只能发起一次申请'];
            }
        }
        //更新数据
        try{
            $data = [
                "uid"     => $data['uid'],
                "money"   => $data['cashvote'],
                "votes"   => $votes,
                "orderno" => $data['uid'] . '_' . time() . rand(100, 999),
                "status"  => 0,
                "addtime" => time(),
                "uptime"  => time(),
                "type"    => Model_CashAccount::ALIPAY,
                "account" => $cash_account_info['account'],
                "name"    => $cash_account_info['name'],
            ];

            $this->getORM()->queryAll('begin');
            $cash_record_model = new Model_CashRecord();
            if(
            !$this->getORM()
                ->where('id = ? and votes >= ?', $data['uid'], $votes)
                ->update(['votes' => new NotORM_Literal("votes - {$votes}")])
            ){
                $this->getORM()->queryAll('rollback');
                return [2, '提现失败'];
            }
            $recordId = $cash_record_model->insert($data);
            if(!$recordId){
                $this->getORM()->queryAll('rollback');
                return [3, '提现失败'];
            }
            $voterData          = [
                "uid"      => $data['uid'],
                'type'     => Model_UserVoteRecord::OUT,
                'action'   => Model_UserVoteRecord::TIXIAN,
                'actionid' => $recordId,
                'votes'    => $votes,
                'addtime'  => time(),
            ];
            $voter_record_model = new Model_UserVoteRecord();
            if(!$voter_record_model->insert($voterData)){
                $this->getORM()->queryAll('rollback');
                return [3, '提现失败'];
            }
            $this->getORM()->queryAll('commit');
        }catch(\Exception $e){
            $this->getORM()->queryAll('rollback');
            return [1, '提现失败'];
        }
        Domain_Msg::addMsg('提现申请成功', Common_JPush::TXSQSL, $data['uid']);
        return [0, '提交成功'];
    }
//    public function setCash($data){
//
//        $nowtime = time();
//
//        $uid = $data['uid'];
////        $accountid = $data['accountid'];
//        $cashvote = $data['cashvote'];
//
//        $config         = getConfigPri();
//        $cash_start     = $config['cash_start'];
//        $cash_end       = $config['cash_end'];
//        $cash_max_times = $config['cash_max_times'];
//
//        $day = (int)date("d", $nowtime);
//
//        if($day < $cash_start || $day > $cash_end){
//            return 1005;
//        }
//
//        //本月第一天
//        $month       = date('Y-m-d', strtotime(date("Ym", $nowtime) . '01'));
//        $month_start = strtotime(date("Ym", $nowtime) . '01');
//
//        //本月最后一天
//        $month_end = strtotime("{$month} +1 month");
//
//        if($cash_max_times){
//            $isexist = DI()->notorm->cash_record
//                ->where('uid=? and addtime > ? and addtime < ?', $uid, $month_start, $month_end)
//                ->count();
//            if($isexist >= $cash_max_times){
//                return 1006;
//            }
//        }
//
//        $isrz = DI()->notorm->user_auth
//            ->select("status")
//            ->where('uid=?', $uid)
//            ->fetchOne();
//        if(!$isrz || $isrz['status'] != 2){
//            return 1003;
//        }
//
//        /* 钱包信息 */
//        $accountinfo = DI()->notorm->cash_account
//            ->select("*")
//            ->where('uid=?', $uid)
//            ->fetchOne();
//        if(!$accountinfo){
//            return 1006;
//        }
//
//
//        //提现比例
////        $cash_rate = $config['cash_rate'];
//        $cash_rate = 10000;
//        /* 最低额度 */
//        $cash_min = $config['cash_min'];
//
//        //提现钱数
////        $money = floor($cashvote / $cash_rate);
//        $money = floor($cashvote * $cash_rate);
//
//        if($money < $cash_min){
//            return 1004;
//        }
//
//
//        $cashvotes = $money * $cash_rate;
//
//
//        $ifok = DI()->notorm->user
//            ->where('id = ? and votes>=?', $uid, $cashvotes)
//            ->update(['votes' => new NotORM_Literal("votes - {$cashvotes}")]);
//        if(!$ifok){
//            return 1001;
//        }
//
//
//        $data = [
//            "uid"          => $uid,
//            "money"        => $money,
//            "votes"        => $cashvotes,
//            "orderno"      => $uid . '_' . $nowtime . rand(100, 999),
//            "status"       => 0,
//            "addtime"      => $nowtime,
//            "uptime"       => $nowtime,
//            "type"         => $accountinfo['type'],
//            "account_bank" => $accountinfo['account_bank'],
//            "account"      => $accountinfo['account'],
//            "name"         => $accountinfo['name'],
//        ];
//
//        $rs = DI()->notorm->cash_record->insert($data);
//        if(!$rs){
//            return 1002;
//        }
//
//
//        return $rs;
//    }

    /* 关注 */
    public function setAttent($uid, $touid){
        $isexist    = DI()->notorm->user_attention
            ->select("*")
            ->where('uid=? and touid=?', $uid, $touid)
            ->fetchOne();
        $label      = [
            Common_JPush::FOLLOW . $touid,
            Common_JPush::FOLLOW_PUSH . $touid,
        ];
        $push_model = new Common_JPush($uid);
        if($isexist){
            DI()->notorm->user_attention
                ->where('uid=? and touid=?', $uid, $touid)
                ->delete();
            $push_model->removeLabel($label);
            return 0;
        }else{
            DI()->notorm->user_black
                ->where('uid=? and touid=?', $uid, $touid)
                ->delete();
            DI()->notorm->user_attention
                ->insert([
                    "uid"     => $uid,
                    "touid"   => $touid,
                    'addtime' => time(),
                ]);
            $push_model->addLabel($label);
            $this->createBlackLive($touid, []);
            return 1;
        }
    }

    /* 拉黑 */
    public function setBlack($uid, $touid){
        $isexist = DI()->notorm->user_black
            ->select("*")
            ->where('uid=? and touid=?', $uid, $touid)
            ->fetchOne();
        if($isexist){
            DI()->notorm->user_black
                ->where('uid=? and touid=?', $uid, $touid)
                ->delete();
            return 0;
        }else{
            DI()->notorm->user_attention
                ->where('uid=? and touid=?', $uid, $touid)
                ->delete();
            DI()->notorm->user_black
                ->insert(["uid" => $uid, "touid" => $touid]);

            return 1;
        }
    }

    /* 关注列表 */
    public function getFollowsList($uid, $touid, $p){
        if($p < 1){
            $p = 1;
        }
        $pnum   = 50;
        $start  = ($p - 1) * $pnum;
        $touids = DI()->notorm->user_attention
            ->select("touid")
            ->where('uid=?', $touid)
            ->limit($start, $pnum)
            ->fetchAll();
        foreach($touids as $k => $v){
            $userinfo = getUserInfo($v['touid']);
            if($userinfo){
                if($uid == $touid){
                    $isattent = '1';
                }else{
                    $isattent = isAttention($uid, $v['touid']);
                }
                $userinfo['isattention'] = $isattent;

                /** 直播状态及信息 */
                $userinfo['is_live'] = 0;
                $live                = isLive($v['touid']);
                $userinfo['live']    = [];
                if($live){
                    $userinfo['is_live'] = 1;
                    $userinfo['live']    = [
                        'pull'    => $live['pull'],
                        'stream'  => $live['stream'],
                        'title'   => $live['title'],
                        'isvideo' => $live['isvideo'],
                        'anyway'  => $live['anyway'],
                    ];
                }
                $touids[$k] = $userinfo;
                $touids[$k] = $userinfo;
            }else{
                DI()->notorm->user_attention->where('uid=? or touid=?',
                    $v['touid'], $v['touid'])->delete();
                unset($touids[$k]);
            }
        }
        $touids = array_values($touids);
        return $touids;
    }

    /* 粉丝列表 */
    public function getFansList($uid, $touid, $p){
        if($p < 1){
            $p = 1;
        }
        $pnum   = 50;
        $start  = ($p - 1) * $pnum;
        $touids = DI()->notorm->user_attention
            ->select("uid")
            ->where('touid=?', $touid)
            ->limit($start, $pnum)
            ->fetchAll();
        foreach($touids as $k => $v){
            $userinfo = getUserInfo($v['uid']);
            if($userinfo){
                $userinfo['isattention'] = isAttention($uid, $v['uid']);
                /** 直播状态更新 */
                $userinfo['is_live'] = 0;
                $userinfo['live']    = [];
                $live                = isLive($v['uid']);
                if($live){
                    if($userinfo['isattention'] == 1 || $live['is_black'] == 0){
                        $userinfo['is_live'] = 1;
                        $userinfo['live']    = [
                            'pull'    => $live['pull'],
                            'stream'  => $live['stream'],
                            'title'   => $live['title'],
                            'isvideo' => $live['isvideo'],
                            'anyway'  => $live['anyway'],
                        ];
                    }
                }

                $touids[$k] = $userinfo;
                $touids[$k] = $userinfo;
            }else{
                DI()->notorm->user_attention->where('uid=? or touid=?',
                    $v['uid'], $v['uid'])->delete();
                unset($touids[$k]);
            }
        }
        $touids = array_values($touids);
        return $touids;
    }

    /* 黑名单列表 */
    public function getBlackList($uid, $touid, $p){
        if($p < 1){
            $p = 1;
        }
        $pnum   = 50;
        $start  = ($p - 1) * $pnum;
        $touids = DI()->notorm->user_black
            ->select("touid")
            ->where('uid=?', $touid)
            ->limit($start, $pnum)
            ->fetchAll();
        foreach($touids as $k => $v){
            $userinfo = getUserInfo($v['touid']);
            if($userinfo){
                $touids[$k] = $userinfo;
            }else{
                DI()->notorm->user_black->where('uid=? or touid=?', $v['touid'],
                    $v['touid'])->delete();
                unset($touids[$k]);
            }
        }
        $touids = array_values($touids);
        return $touids;
    }

    /* 直播记录 */
    public function getLiverecord($touid, $p){
        if($p < 1){
            $p = 1;
        }
        $pnum   = 50;
        $start  = ($p - 1) * $pnum;
        $record = DI()->notorm->live_record
            ->select("id,uid,nums,starttime,endtime,title,city")
            ->where('uid=?', $touid)
            ->order("id desc")
            ->limit($start, $pnum)
            ->fetchAll();
        foreach($record as $k => $v){
            $record[$k]['datestarttime'] = date("Y.m.d", $v['starttime']);
            $record[$k]['dateendtime']   = date("Y.m.d", $v['endtime']);
            $cha                         = $v['endtime'] - $v['starttime'];
            $record[$k]['length']        = getSeconds($cha);
        }
        return $record;
    }

    /* 个人主页 */
    public function getUserHome($uid, $touid){
        $info = getUserInfo($touid);
        unset($info['coin']);
        $info['follows']           = (string)getFollows($touid);
        $info['fans']              = (string)getFans($touid);
        $info['isattention']       = (string)isAttention($uid, $touid);
        $info['isblack']           = (string)isBlack($uid, $touid);
        $info['isblack2']          = (string)isBlack($touid, $uid);

        /* 直播状态及信息 */
        $info['is_live'] = 0;
        $info['live']    = [];
        $live            = isLive($touid);
        if($live && ($info['isattention'] || !$live['is_black'])){
            $info['is_live'] = 1;
            $info['live']    = [
                'pull'    => $live['pull'],
                'stream'  => $live['stream'],
                'title'   => $live['title'],
                'isvideo' => $live['isvideo'],
                'anyway'  => $live['anyway'],
            ];
        }
        /* 贡献榜前三 */
        $rs           = [];
        $record_model = new Model_GiftRecord();
        $rss          = $record_model->now_three($touid);
        foreach($rss as $k => $v){
            $rs[$k]['avatar'] = get_upload_path($v['avatar_thumb']);
        }
        $info['contribute'] = $rs;
        return $info;
    }

    /* 贡献榜 */
    public function getContributeList($touid, $p){
        if($p < 1){
            $p = 1;
        }
        $pnum  = 50;
        $start = ($p - 1) * $pnum;

        $rs = [];
        $rs = DI()->notorm->user_coinrecord
            ->select("uid,sum(totalcoin) as total")
            ->where('touid=?', $touid)
            ->group("uid")
            ->order("total desc")
            ->limit($start, $pnum)
            ->fetchAll();

        foreach($rs as $k => $v){
            $rs[$k]['userinfo'] = getUserInfo($v['uid']);
        }

        return $rs;
    }

    /* 设置分销 */
    public function setDistribut($uid, $code){

        $isexist = DI()->notorm->agent
            ->select("*")
            ->where('uid=?', $uid)
            ->fetchOne();
        if($isexist){
            return 1004;
        }

        //获取邀请码用户信息
        $oneinfo = DI()->notorm->agent_code
            ->select("uid")
            ->where('code=? and uid!=?', $code, $uid)
            ->fetchOne();
        if(!$oneinfo){
            return 1002;
        }

        //获取邀请码用户的邀请信息
        $agentinfo = DI()->notorm->agent
            ->select("*")
            ->where('uid=?', $oneinfo['uid'])
            ->fetchOne();
        if(!$agentinfo){
            $agentinfo = [
                'uid'     => $oneinfo['uid'],
                'one_uid' => 0,
            ];
        }
        // 判断对方是否自己下级
        if($agentinfo['one_uid'] == $uid){
            return 1003;
        }

        $data = [
            'uid'     => $uid,
            'one_uid' => $agentinfo['uid'],
            'addtime' => time(),
        ];
        DI()->notorm->agent->insert($data);
        return 0;
    }


    /* 印象标签 */
    public function getImpressionLabel(){

        $key  = "getImpressionLabel";
        $list = getcaches($key);
        if(!$list){
            $list = DI()->notorm->label
                ->select("*")
                ->order("list_order asc,id desc")
                ->fetchAll();
            if($list){
                setcaches($key, $list);
            }

        }

        return $list;
    }

    /* 用户标签 */
    public function getUserLabel($uid, $touid){
        $list = DI()->notorm->label_user
            ->select("label")
            ->where('uid=? and touid=?', $uid, $touid)
            ->fetchOne();

        return $list;

    }

    /* 获取关于我们列表 */
    public function getPerSetting(){
        $rs = [];

        $list = DI()->notorm->portal_post
            ->select("id,post_title")
            ->where("type='2'")
            ->order('list_order asc')
            ->fetchAll();
        foreach($list as $k => $v){

            $rs[] = [
                'id'    => '0',
                'name'  => $v['post_title'],
                'thumb' => '',
                'href'  => get_upload_path("/portal/page/index?id={$v['id']}"),
            ];
        }

        return $rs;
    }

    /* 提现账号列表 */
    public function getUserAccountList($uid){

        $list = DI()->notorm->cash_account
            ->select("id,name,account")
            ->where('uid=?', $uid)
            ->order("addtime desc")
            ->fetchAll();

        return $list;
    }

    /* 账号信息 */
    public function getUserAccount($where){

        $list = DI()->notorm->cash_account
            ->select("*")
            ->where($where)
            ->order("addtime desc")
            ->fetchAll();

        return $list;
    }

//    /* 设置提账号 */
//    public function setUserAccount($data){
//
//        $rs = DI()->notorm->cash_account
//            ->insert($data);
//
//        return $rs;
//    }

    public function setUserAccount($data){
        $model = new Model_CashAccount();
        try{
            if(
                $model->getORM()->where('uid = ?', $data['uid'])->count() > 0
            ){
                return $model->getORM()->where('uid = ?', $data['uid'])
                    ->update($data);
            }else{
                return $model->insert($data);
            }
        }catch(\Exception $e){
            return false;
        }
    }

    /* 删除提账号 */
    public function delUserAccount($data){

        $rs = DI()->notorm->cash_account
            ->where($data)
            ->delete();

        return $rs;
    }

    /* 登录奖励信息 */
    public function LoginBonus($uid){
        $rs = [
            'bonus_switch' => '0',
            'bonus_day'    => '0',
            'count_day'    => '0',
            'bonus_list'   => [],
        ];

        //file_put_contents(API_ROOT.'/Runtime/LoginBonus_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 uid:'.json_encode($uid)."\r\n",FILE_APPEND);
        $configpri = getConfigPri();
        if(!$configpri['bonus_switch']){
            return $rs;
        }
        $rs['bonus_switch'] = $configpri['bonus_switch'];

        //file_put_contents(API_ROOT.'/Runtime/LoginBonus_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 bonus_switch:'."\r\n",FILE_APPEND);
        /* 获取登录设置 */
        $key  = 'loginbonus';
        $list = getcaches($key);
        if(!$list){
            $list = DI()->notorm->loginbonus
                ->select("day,coin")
                ->fetchAll();
            if($list){
                setcaches($key, $list);
            }
        }

        //file_put_contents(API_ROOT.'/Runtime/LoginBonus_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 list:'."\r\n",FILE_APPEND);
        $rs['bonus_list'] = $list;
        $bonus_coin       = [];
        foreach($list as $k => $v){
            $bonus_coin[$v['day']] = $v['coin'];
        }

        /* 登录奖励 */
        $signinfo = DI()->notorm->user_sign
            ->select("bonus_day,bonus_time,count_day")
            ->where('uid=?', $uid)
            ->fetchOne();
        //file_put_contents(API_ROOT.'/Runtime/LoginBonus_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 signinfo:'."\r\n",FILE_APPEND);
        if(!$signinfo){
            $signinfo = [
                'bonus_day'  => '0',
                'bonus_time' => '0',
                'count_day'  => '0',
            ];
        }
        $nowtime = time();
        if($nowtime - $signinfo['bonus_time'] > 60 * 60 * 24){
            $signinfo['count_day'] = 0;
        }
        $rs['count_day'] = (string)$signinfo['count_day'];

        if($nowtime > $signinfo['bonus_time']){
            //更新
            $bonus_time = strtotime(date("Ymd", $nowtime)) + 60 * 60 * 24;
            $bonus_day  = $signinfo['bonus_day'];
            if($bonus_day > 6){
                $bonus_day = 0;
            }
            $bonus_day++;
            $coin = $bonus_coin[$bonus_day] ?? 0;

            if($coin){
                $rs['bonus_day'] = (string)$bonus_day;
            }

        }
        //file_put_contents(API_ROOT.'/Runtime/LoginBonus_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 rs:'."\r\n",FILE_APPEND);
        return $rs;
    }

    /* 获取登录奖励 */
    public function getLoginBonus($uid){
        $rs        = 0;
        $configpri = getConfigPri();
        if(!$configpri['bonus_switch']){
            return $rs;
        }

        /* 获取登录设置 */
        $key  = 'loginbonus';
        $list = getcaches($key);
        if(!$list){
            $list = DI()->notorm->loginbonus
                ->select("day,coin")
                ->fetchAll();
            if($list){
                setcaches($key, $list);
            }
        }

        $bonus_coin = [];
        foreach($list as $k => $v){
            $bonus_coin[$v['day']] = $v['coin'];
        }

        $isadd = 0;
        /* 登录奖励 */
        $signinfo = DI()->notorm->user_sign
            ->select("bonus_day,bonus_time,count_day")
            ->where('uid=?', $uid)
            ->fetchOne();
        if(!$signinfo){
            $isadd    = 1;
            $signinfo = [
                'bonus_day'  => '0',
                'bonus_time' => '0',
                'count_day'  => '0',
            ];
        }
        $nowtime = time();
        if($nowtime > $signinfo['bonus_time']){
            //更新
            $bonus_time = strtotime(date("Ymd", $nowtime)) + 60 * 60 * 24;
            $bonus_day  = $signinfo['bonus_day'];
            $count_day  = $signinfo['count_day'];
            if($bonus_day > 6){
                $bonus_day = 0;
            }
            if($nowtime - $signinfo['bonus_time'] > 60 * 60 * 24){
                $count_day = 0;
            }
            $bonus_day++;
            $count_day++;


            if($isadd){
                DI()->notorm->user_sign
                    ->insert([
                        "uid"        => $uid,
                        "bonus_time" => $bonus_time,
                        "bonus_day"  => $bonus_day,
                        "count_day"  => $count_day,
                    ]);
            }else{
                DI()->notorm->user_sign
                    ->where('uid=?', $uid)
                    ->update([
                        "bonus_time" => $bonus_time,
                        "bonus_day"  => $bonus_day,
                        "count_day"  => $count_day,
                    ]);
            }

            $coin = $bonus_coin[$bonus_day];

            if($coin){
                DI()->notorm->user
                    ->where('id=?', $uid)
                    ->update(["coin" => new NotORM_Literal("coin + {$coin}")]);


                /* 记录 */
                $insert = [
                    "type"      => '1',
                    "action"    => '3',
                    "uid"       => $uid,
                    "touid"     => $uid,
                    "giftid"    => $bonus_day,
                    "giftcount" => '0',
                    "totalcoin" => $coin,
                    "showid"    => '0',
                    "addtime"   => $nowtime,
                ];
                DI()->notorm->user_coinrecord->insert($insert);
            }
            $rs = 1;
        }

        return $rs;

    }

    //检测用户是否填写了邀请码
    public function checkIsAgent($uid){
        $info = DI()->notorm->agent->where("uid=?", $uid)->fetchOne();
        if(!$info){
            return 0;
        }

        return 1;
    }

    /* 个人主页 */
    public function getMyHome($touid){
        $info = getUserInfo($touid);
        unset($info['consumption']);
        $info['follows']     = (string)getFollows($touid);
        $info['fans']        = (string)getFans($touid);
        $auth                = DI()->notorm->user_auth->where(['uid' => $touid])
            ->select('status')->fetchOne();
        $info['auth_status'] = 0;
        if($auth){
            $info['auth_status'] = $auth['status'];
        }
        $rs = [];

        $record_model = new Model_GiftRecord();
        $rss          = $record_model->now_three($touid);
        $where        = ['uid = ?' => $touid, 'state <> ?' => 3];
        $family       = DI()->notorm->family_user
            ->select('is_admin,state')
            ->where($where)
            ->fetchOne();
        if(!$family){
            $info['family_state'] = 0;//不在任何家族
        }else{
            $info['family_state'] = 1;//某家族成员
            if($family['is_admin'] == 1){
                $info['family_state'] = 2;//某家族族长
            }
            if($family['state'] == 2){
                $info['family_state'] = 3;//退出申请中
            }
        }
        foreach($rss as $k => $v){
            $rs[$k]['avatar'] = get_upload_path($v['avatar_thumb']);
        }
        $info['contribute'] = $rs;
        $info['votestotal'] = (string)round($info['votestotal'] / 100, 2);
        return $info;
    }

    /**
     * 用户手机绑定认证
     */
    public function checkUserPhoneId($uid){
        $res = ['type' => 1, 'mobile' => ''];
//        $configpri = getConfigPri();
//        if($configpri['auth_islimit'] == 1){
        $phone_info = DI()->notorm->user
            ->select("mobile")
            ->where('id=?', $uid)
            ->fetchOne();
        if(!$phone_info['mobile']){
            return $res;
        }
        $authStatus    = authStatus($uid);
        $res['mobile'] = $phone_info['mobile'];
        if(!$authStatus){
            $res['type'] = 2;//身份未认证
        }elseif($authStatus['status'] == 1){
            $res['type'] = 3;//身份认证中
        }elseif($authStatus['status'] == 3){
            $res['type'] = 4;//身份认证失败，请重新认证
        }else{
            $res['type'] = 0;//认证通过
        }
        return $res;
    }

    public function getCover($uid){
        $lastTime = DI()->notorm->live_record
            ->select('thumb')
            ->where('uid = ?', $uid)
            ->order('id desc')
            ->fetchOne();
        if(!empty($lastTime['thumb'])){
            return $lastTime['thumb'];
        }

        $headPic = DI()->notorm->user
            ->select('avatar')
            ->where('id = ?', $uid)
            ->fetchOne();
        return $headPic['avatar'];

    }

    public function autoIdCardAuthen($uid, $name, $idCard){

        //检测是否已经认证
        $isAuthen = DI()->notorm->user_auth
            ->select('status')
            ->where('uid', $uid)
            ->fetchOne();
        if($isAuthen && $isAuthen['status'] == 1){
            return [1, '您已实名了哦'];
        }
        //腾讯二要素检测
        list($code, $msg, $result) = checkIdCard($name, $idCard);
        if($code > 0){
            return [$code, $msg];
        }
        if($result['res'] != 1){
            return [10008, '姓名和身份证信息不一致'];
        }
        $data['real_name'] = $name;
        $data['car_no']    = $idCard;
        $data['status']    = Model_UserAuth::ADOPT;
        $data['uptime']    = time();
        $data['reason']    = '自动认证';
        //更新数据或者添加
        if($isAuthen){
            $dataRes = DI()->notorm->user_auth
                ->where('uid', $uid)
                ->update($data);
        }else{
            $data['addtime'] = $data['uptime'];
            $data['uid']     = $uid;
            $dataRes         = DI()->notorm->user_auth->insert($data);
        }
        if(!$dataRes){
            return [1, '姓名和身份证信息不一致'];
        }
        DI()->notorm->user
            ->where('id', $uid)
            ->update(['is_auth' => 1]);
        $count = DI()->notorm->user_attention
                ->where('touid=?', $uid)
                ->count() ?? 0;
        if($count){
            $this->createBlackLive($uid, [], 1);
        }
        return [0, '实名认证成功'];
    }

    /**
     * 获取当前等级和等级上限值
     *
     * @param $uid
     *
     * @return mixed
     */
    public function getUserLevel($uid){
        $now_level_num      = DI()->notorm->user
            ->select('consumption')
            ->where('id = ?', $uid)
            ->fetchOne();
        $level_list         = getLevelList();
        $arr['consumption'] = $now_level_num['consumption'];
        foreach($level_list as $k => $v){
            $arr['level_list'][] = [
                'levelname' => $v['levelname'],
                'level_up'  => $v['level_up'],
                'levelid'   => $v['levelid'],
            ];
        }
//        $arr           = [];
//        foreach ($level_list as $k => $v) {
//            if ($v['level_up'] > $now_level_num['consumption']) {
//                $arr['level']       = $v['levelid'];
//                $arr['now_score']   = $now_level_num['consumption'];
//                $arr['level_score'] = $v['level_up'];
//            }
//        }
//        if (!$arr) {
//            $last               = array_slice($level_list, -1, 1)[0];
//            $arr['level']       = $last['levelid'];
//            $arr['now_score']   = $now_level_num['consumption'];
//            $arr['level_score'] = $last['level_up'];
//        }
        return $arr;
    }

    public function transferMoney($uid, $otherid, $money){

        $user_info = DI()->notorm->user
            ->select('agent_money')
            ->where('id = ?', $uid)
            ->fetchOne();
        if(!$user_info){
            return [1, '转账失败'];
        }
        //判断余额
        if($user_info['agent_money'] < $money){
            return [1, '余额不足'];
        }
        //判断收款方
        $is_other = DI()->notorm->user
            ->select('id')
            ->where('id = ?', $otherid)
            ->fetchOne();
        if(!$is_other){
            return [1, '收款方不存在'];
        }
        $transferModel = new Model_TransferRecord();
        try{
            //更新数据
            DI()->notorm->user->queryAll('begin');
            if(
            !DI()->notorm->user
                ->where('id = ?', $uid)
                ->update(['agent_money' => new NotORM_Literal("agent_money - {$money}")])
            ){
                DI()->notorm->user->queryAll('rollback');
                return [1, '余额更新失败'];
            }
            if(
            !DI()->notorm->user
                ->where('id = ?', $otherid)
                ->update(['coin' => new NotORM_Literal("coin + {$money}")])
            ){
                DI()->notorm->user->queryAll('rollback');
                return [1, '余额更新失败'];
            }
            $time          = time();
            $transfer_data = [
                'uid'     => $uid,
                'touid'   => $otherid,
                'money'   => $money,
                'addtime' => time(),
            ];
            $insert        = [
                [
                    "type"      => Model_UserCoinRecord::OUT,
                    "action"    => Model_UserCoinRecord::DLZC,
                    "uid"       => $uid,
                    "touid"     => $otherid,
                    "totalcoin" => $money,
                    "addtime"   => $time,
                ],
                [
                    "type"      => Model_UserCoinRecord::INCOME,
                    "action"    => Model_UserCoinRecord::DLZC,
                    "uid"       => $otherid,
                    "touid"     => $uid,
                    "totalcoin" => $money,
                    "addtime"   => $time,
                ],
            ];
            if(!$transferModel->insert($transfer_data)){
                DI()->notorm->user->queryAll('rollback');
                return [2, '更新记录失败'];
            }
            if(
            !DI()->notorm->user_coinrecord->insert_multi($insert)
            ){
                DI()->notorm->user->queryAll('rollback');
                return [1, '更新记录失败'];
            }
        }catch(\Exception $d){
            DI()->notorm->user->queryAll('rollback');
            return [99, '异常'];
        }
        DI()->notorm->user->queryAll('commit');
        $title      = '充值成功';
        $content    = sprintf(Common_JPush::CZCG, intval($money), '金币');
        $push_model = new Common_JPush($uid);
        $push_model->sendAlias($title, $content);
        return [0, '转账成功'];
    }

    public function feedBack($uid, $type, $content, $remark){
        $insert = [
            'uid'     => $uid,
            'content' => $content,
            'type'    => $type,
            'title'   => $remark,
            'uptime'  => time(),
            'addtime' => time(),
        ];
        if(
        !DI()->notorm->feedback->insert($insert)
        ){
            return [1, '提交失败'];
        }
        Domain_Msg::addMsg('反馈成功', Common_JPush::FKXG, $uid);
        return [0, '提交成功'];
    }

    public function getUserInfo(int $uid, string $field = '*'){
        return DI()->notorm->user
            ->where('id = ?', $uid)
            ->select($field)
            ->fetchOne();
    }

    public function decCoin(int $uid, int $money){
        return DI()->notorm->user
            ->where('id = ? and coin >=?', $uid, $money)
            ->update([
                'coin'        => new NotORM_Literal("coin - {$money}"),
                'consumption' => new NotORM_Literal("consumption + {$money}"),
                'updtime'     => time(),
            ]);
    }

    public function fans($nums){
        $sql = "select id,avatar_thumb from cmf_user where is_js = 1 order by rand() limit {$nums}";
        return $this->getORM()->queryAll($sql);
    }

    /** 创建假直播间 */
    public function createBlackLive($uid, $data = [], $isAuth = 0){
        if($isAuth || isAuth($uid)){
            $exist = DI()->notorm->live->where(['uid' => $uid])->fetchOne();
            if(!$exist){
                $model = new Model_Live();
                if(!$data){
                    $time   = time();
                    $stream = $uid . '_' . $time;
                    $data   = [
                        "uid"         => $uid,
                        "showid"      => $time,
                        "title"       => '波鸭直播',
                        "province"    => '浙江',
                        "city"        => '杭州',
                        "stream"      => $stream,
                        "thumb"       => '',
                        "pull"        => $stream,
                        "type"        => 0,
                        "goodnum"     => 0,
                        "isvideo"     => 0,
                        "islive"      => 1,
                        "hotvotes"    => 0,
                        "pkuid"       => 0,
                        "pkstream"    => '',
                        "banker_coin" => 10000000,
                        "is_black"    => 1,
                    ];
                }
                $userinfo = $this->getUserInfo($uid);
                $model->createRoom($uid, $data, 1);
                $tokenInfo = DI()->notorm->user_token
                    ->select('token,expire_time')
                    ->where('user_id = ?', $uid)
                    ->fetchOne();
                setcaches(Common_Cache::ENTER_ROOM_TOKEN . $tokenInfo['token'], $userinfo, 3600);
//                DI()->redis->set(Common_Cache::ENTER_ROOM_TOKEN . $tokenInfo['token'], json_encode($userinfo));
                DI()->redis->hset('LiveConnect', $uid, 0);
                DI()->redis->hset('LivePK', $uid, 0);
                DI()->redis->hset('LivePK_gift', $uid, 0);
            }
        }
    }

    public function updateCoin($uid, $money, $type){
        $where = "id = {$uid}";
        if($type == Model_UserCoinRecord::OUT){
            $where .= " and coin >= {$money}";
            $money = -$money;
        }
        return $this->getORM()
            ->where($where)
            ->update([
                'coin'        => new NotORM_Literal("coin + {$money}"),
                'consumption' => new NotORM_Literal("consumption + {$money}"),
                'give_money'  => new NotORM_Literal("give_money + {$money}"),
            ]);
    }
}
