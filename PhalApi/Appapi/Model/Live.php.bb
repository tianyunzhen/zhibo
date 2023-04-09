<?php

class Model_Live extends PhalApi_Model_NotORM{
    /* 创建房间 */
    public function createRoom($uid, $data){

//        $this->winPrize(1,1,4);

        /* 获取主播 推荐、热门 */
        $data['ishot']       = '0';
        $data['isrecommend'] = '0';
        $userinfo            = DI()->notorm->user
            ->select("ishot,isrecommend")
            ->where('id=?', $uid)
            ->fetchOne();
        if($userinfo){
            $data['ishot']       = $userinfo['ishot'];
            $data['isrecommend'] = $userinfo['isrecommend'];
        }
        $isexist = DI()->notorm->live
            ->select("uid,isvideo,islive,stream")
            ->where('uid=?', $uid)
            ->fetchOne();
        if($isexist){
            /* 判断存在的记录是否为直播状态 */
            if($isexist['isvideo'] == 0 && $isexist['islive'] == 1){
                /* 若存在未关闭的直播 关闭直播 */
                $this->stopRoom($uid, $isexist['stream']);

                /* 加入 */
                $rs = DI()->notorm->live->insert($data);
            }else{
                /* 更新 */
                $rs = DI()->notorm->live->where('uid = ?', $uid)->update($data);
            }
        }else{
            /* 加入 */
            $rs = DI()->notorm->live->insert($data);
        }
        if(!$rs){
            return $rs;
        }
        return 1;
    }

    /* 主播粉丝 */
    public function getFansIds($touid){

        $list    = [];
        $fansids = DI()->notorm->user_attention
            ->select("uid")
            ->where('touid=?', $touid)
            ->fetchAll();

        if($fansids){
            $uids = array_column($fansids, 'uid');

            $pushids = DI()->notorm->user_pushid
                ->select("pushid")
                ->where('uid', $uids)
                ->fetchAll();
            $list    = array_column($pushids, 'pushid');
            $list    = array_filter($list);
        }
        return $list;
    }

    /* 修改直播状态 */
    public function changeLive($uid, $stream, $status){

        if($status == 1){
            $info = DI()->notorm->live
                ->select("*")
                ->where('uid=? and stream=?', $uid, $stream)
                ->fetchOne();
            if($info){
                DI()->notorm->live
                    ->where('uid=? and stream=?', $uid, $stream)
                    ->update(["islive" => 1]);
                $user_info             = DI()->notorm->user
                    ->select("avatar,avatar_thumb,user_nicename")
                    ->where('id = ?', $uid)
                    ->fetchOne();
                $info['avatar']        = $user_info['avatar'];
                $info['avatar_thumb']  = $user_info['avatar_thumb'];
                $info['user_nicename'] = $user_info['user_nicename'];
            }
            return $info;
        }else{
            $this->stopRoom($uid, $stream);
            return 1;
        }
    }

    /* 修改直播状态 */
    public function changeLiveType($uid, $stream, $data){
        return DI()->notorm->live
            ->where('uid=? and stream=?', $uid, $stream)
            ->update($data);
    }

    /* 关播 */
    public function stopRoom($uid, $stream){

        $info = DI()->notorm->live
            ->select("uid,showid,starttime,title,province,city,stream,lng,lat,type,type_val,liveclassid,thumb")
            ->where('uid=? and stream=? and islive="1"', $uid, $stream)
            ->fetchOne();
        file_put_contents(API_ROOT . '/Runtime/stopRoom_' . date('Y-m-d') . '.txt', date('Y-m-d H:i:s') . ' 提交参数信息 info:' . json_encode($info) . "\r\n", FILE_APPEND);
        if($info){
            $isdel = DI()->notorm->live
                ->where('uid=?', $uid)
                ->delete();
            if(!$isdel){
                return 0;
            }
            $nowtime         = time();
            $info['endtime'] = $nowtime;
            $info['time']    = date("Y-m-d", $info['showid']);
            $votes           = DI()->notorm->user_coinrecord
                ->where('uid !=? and touid=? and showid=?', $uid, $uid, $info['showid'])
                ->sum('totalcoin');
            $info['votes']   = 0;
            if($votes){
                $info['votes'] = $votes;
            }
            $nums = DI()->redis->zCard('user_' . $stream);
            DI()->redis->hDel("livelist", $uid);
            DI()->redis->del($uid . '_zombie');
            DI()->redis->del($uid . '_zombie_uid');
            DI()->redis->del('attention_' . $uid);
            DI()->redis->del('user_' . $stream);
            $info['nums'] = $nums;
            $result       = DI()->notorm->live_record->insert($info);
            file_put_contents(API_ROOT . '/Runtime/stopRoom_' . date('Y-m-d') . '.txt', date('Y-m-d H:i:s') . ' 提交参数信息 result:' . json_encode($result['id']) . "\r\n", FILE_APPEND);

            /* 解除本场禁言 */
            $list2 = DI()->notorm->live_shut
                ->select('uid')
                ->where('liveuid=? and showid!=0', $uid)
                ->fetchAll();
            DI()->notorm->live_shut->where('liveuid=? and showid!=0', $uid)->delete();

            foreach($list2 as $k => $v){
                DI()->redis->hDel($uid . 'shutup', $v['uid']);
            }

            /* 游戏处理 */
            $game  = DI()->notorm->game
                ->select("*")
                ->where('stream=? and liveuid=? and state=?', $stream, $uid, "0")
                ->fetchOne();
            $total = [];
            if($game){
                $total = DI()->notorm->gamerecord
                    ->select("uid,sum(coin_1 + coin_2 + coin_3 + coin_4 + coin_5 + coin_6) as total")
                    ->where('gameid=?', $game['id'])
                    ->group('uid')
                    ->fetchAll();
                foreach($total as $k => $v){
                    DI()->notorm->user
                        ->where('id = ?', $v['uid'])
                        ->update(['coin' => new NotORM_Literal("coin + {$v['total']}")]);

                    $insert = ["type" => '1', "action" => '20', "uid" => $v['uid'], "touid" => $v['uid'], "giftid" => $game['id'], "giftcount" => 1, "totalcoin" => $v['total'], "showid" => 0, "addtime" => $nowtime];
                    DI()->notorm->user_coinrecord->insert($insert);
                }

                DI()->notorm->game
                    ->where('id = ?', $game['id'])
                    ->update(['state' => '3', 'endtime' => time()]);
                $brandToken = $stream . "_" . $game["action"] . "_" . $game['starttime'] . "_Game";
                DI()->redis->delete($brandToken);
            }

        }
        return 1;
    }

    /* 关播信息 */
    public function stopInfo($stream){

        $rs = [
            'nums'   => 0,
            'length' => 0,
            'votes'  => 0,
        ];

        $stream2   = explode('_', $stream);
        $liveuid   = $stream2[0];
        $starttime = $stream2[1];
        $liveinfo  = DI()->notorm->live_record
            ->select("starttime,endtime,nums,votes")
            ->where('uid=? and starttime=?', $liveuid, $starttime)
            ->fetchOne();
        if($liveinfo){
            $cha          = $liveinfo['endtime'] - $liveinfo['starttime'];
            $rs['length'] = getSeconds($cha, 1);
            $rs['nums']   = $liveinfo['nums'];
        }
        if($liveinfo['votes']){
            $rs['votes'] = $liveinfo['votes'];
        }
        return $rs;
    }

    /* 直播状态 */
    public function checkLive($uid, $liveuid, $stream){

        /* 是否被踢出 */
        $isexist = DI()->notorm->live_kick
            ->select("id")
            ->where('uid=? and liveuid=?', $uid, $liveuid)
            ->fetchOne();
        if($isexist){
            return 1008;
        }

        $islive = DI()->notorm->live
            ->select("islive,type,type_val,starttime")
            ->where('uid=? and stream=?', $liveuid, $stream)
            ->fetchOne();

        if(!$islive || $islive['islive'] == 0){
            return 1005;
        }
        $rs['type']     = $islive['type'];
        $rs['type_val'] = '0';
        $rs['type_msg'] = '';

        $userinfo = DI()->notorm->user
            ->select("issuper")
            ->where('id=?', $uid)
            ->fetchOne();
        if($userinfo && $userinfo['issuper'] == 1){

            if($islive['type'] == 6){

                return 1007;
            }
            $rs['type']     = '0';
            $rs['type_val'] = '0';
            $rs['type_msg'] = '';

            return $rs;
        }

        if($islive['type'] == 1){
            $rs['type_msg'] = md5($islive['type_val']);
        }elseif($islive['type'] == 2){
            $rs['type_msg'] = '本房间为收费房间，需支付' . $islive['type_val'] . '钻石';
            $rs['type_val'] = $islive['type_val'];
            $isexist        = DI()->notorm->user_coinrecord
                ->select('id')
                ->where('uid=? and touid=? and showid=? and action=6 and type=0', $uid, $liveuid, $islive['starttime'])
                ->fetchOne();
            if($isexist){
                $rs['type']     = '0';
                $rs['type_val'] = '0';
                $rs['type_msg'] = '';
            }
        }elseif($islive['type'] == 3){
            $rs['type_val'] = $islive['type_val'];
            $rs['type_msg'] = '本房间为计时房间，每分钟需支付' . $islive['type_val'] . '钻石';
        }

        return $rs;

    }

    /* 用户余额 */
    public function getUserCoin($uid){
        $userinfo = DI()->notorm->user
            ->select("coin")
            ->where('id=?', $uid)
            ->fetchOne();
        return $userinfo;
    }

    /* 房间扣费 */
    public function roomCharge($uid, $liveuid, $stream){
        $islive = DI()->notorm->live
            ->select("islive,type,type_val,starttime")
            ->where('uid=? and stream=?', $liveuid, $stream)
            ->fetchOne();
        if(!$islive || $islive['islive'] == 0){
            return 1005;
        }

        if($islive['type'] == 0 || $islive['type'] == 1){
            return 1006;
        }

        $total = $islive['type_val'];
        if($total <= 0){
            return 1007;
        }

        /* 更新用户余额 消费 */
        $ifok = DI()->notorm->user
            ->where('id = ? and coin >= ?', $uid, $total)
            ->update(['coin' => new NotORM_Literal("coin - {$total}"), 'consumption' => new NotORM_Literal("consumption + {$total}")]);
        if(!$ifok){
            return 1008;
        }

        $action = '6';
        if($islive['type'] == 3){
            $action = '7';
        }

        $giftid    = 0;
        $giftcount = 0;
        $showid    = $islive['starttime'];
        $addtime   = time();


        /* 更新直播 映票 累计映票 */
        DI()->notorm->user
            ->where('id = ?', $liveuid)
            ->update(['votes' => new NotORM_Literal("votes + {$total}"), 'votestotal' => new NotORM_Literal("votestotal + {$total}")]);

        $insert_votes = [
            'type'     => '1',
            'action'   => $action,
            'uid'      => $liveuid,
            'fromid'   => $uid,
            'actionid' => $giftid,
            'nums'     => $giftcount,
            'total'    => $total,
            'showid'   => $showid,
            'votes'    => $total,
            'addtime'  => time(),
        ];
        DI()->notorm->user_voterecord->insert($insert_votes);

        /* 更新直播 映票 累计映票 */
        DI()->notorm->user_coinrecord
            ->insert(["type" => '0', "action" => $action, "uid" => $uid, "touid" => $liveuid, "giftid" => $giftid, "giftcount" => $giftcount, "totalcoin" => $total, "showid" => $showid, "addtime" => $addtime]);

        $userinfo2  = DI()->notorm->user
            ->select('coin')
            ->where('id = ?', $uid)
            ->fetchOne();
        $rs['coin'] = $userinfo2['coin'];
        return $rs;

    }

    /* 判断是否僵尸粉 */
    public function isZombie($uid){
        $userinfo = DI()->notorm->user
            ->select("iszombie")
            ->where("id='{$uid}'")
            ->fetchOne();

        return $userinfo['iszombie'];
    }

    /* 僵尸粉 */
    public function getZombie($stream, $where){
        $ids = DI()->notorm->user_zombie
            ->select('uid')
            ->where("uid not in ({$where})")
            ->limit(0, 10)
            ->fetchAll();

        $info = [];

        if($ids){
            foreach($ids as $k => $v){

                $userinfo = getUserInfo($v['uid'], 1);
                if(!$userinfo){
                    DI()->notorm->user_zombie->where("uid={$v['uid']}")->delete();
                    continue;
                }

                $info[] = $userinfo;

                $score = '0.' . ($userinfo['level'] + 100) . '1';
                DI()->redis->zAdd('user_' . $stream, $score, $v['uid']);
            }
        }
        return $info;
    }

    /* 礼物列表 */
    public function getGiftList(){

        $rs = DI()->notorm->gift
            ->select("id,type,mark,giftname,needcoin,gifticon,sticker_id,swftime,isplatgift")
            ->where('type!=2')
            ->order("list_order asc,addtime desc")
            ->fetchAll();

        return $rs;
    }

    /* 礼物：道具列表 */
    public function getPropgiftList(){

        $rs = DI()->notorm->gift
            ->select("id,type,mark,giftname,needcoin,gifticon,sticker_id,swftime,isplatgift")
            ->where("type=2")
            ->order("list_order asc,addtime desc")
            ->fetchAll();

        return $rs;
    }

    /* 赠送礼物 */
    public function sendGift($uid, $liveuid, $stream, $giftid, $giftcount, $ispack){

        /* 礼物信息 */
        $giftinfo = DI()->notorm->gift
            ->select("type,mark,giftname,gifticon,needcoin,swftype,swf,swftime,isplatgift,sticker_id, commission_rate")
            ->where('id=?', $giftid)
            ->fetchOne();
        if(!$giftinfo){
            /* 礼物信息不存在 */
            return 1002;
        }

        $total = $giftinfo['needcoin'] * $giftcount * $giftinfo['commission_rate'] / 100;

        $addtime = time();
        $type    = '0';
        $action  = '1';

        $stream2 = explode('_', $stream);
        $showid  = $stream2[1];

        if($ispack == 1){
            /* 背包礼物 */
            $ifok = DI()->notorm->backpack
                ->where('uid=? and giftid=? and nums>=?', $uid, $giftid, $giftcount)
                ->update(['nums' => new NotORM_Literal("nums - {$giftcount} ")]);
            if(!$ifok){
                /* 数量不足 */
                return 1003;
            }
        }else{
            /* 更新用户余额 消费 */
            $ifok = DI()->notorm->user
                ->where('id = ? and coin >=?', $uid, $total)
                ->update(['coin' => new NotORM_Literal("coin - {$total}"), 'consumption' => new NotORM_Literal("consumption + {$total}")]);
            if(!$ifok){
                /* 余额不足 */
                return 1001;
            }

            $insert = ["type" => $type, "action" => $action, "uid" => $uid, "touid" => $liveuid, "giftid" => $giftid, "giftcount" => $giftcount, "totalcoin" => $total, "showid" => $showid, "mark" => $giftinfo['mark'], "addtime" => $addtime];
            DI()->notorm->user_coinrecord->insert($insert);
        }

        $anthor_total = $total;
        /* 幸运礼物分成 */
        if($giftinfo['type'] == 0 && $giftinfo['mark'] == 3){
            $jackpotset   = getJackpotSet();
            $anthor_total = floor($anthor_total * $jackpotset['luck_anchor'] * 0.01);
        }
        /* 幸运礼物分成 */

        /* 家族分成之后的金额 */
        $anthor_total = setFamilyDivide($liveuid, $anthor_total);

        /* 更新直播 魅力值 累计魅力值 */
        $istouid = DI()->notorm->user
            ->where('id = ?', $liveuid)
            ->update(['votes' => new NotORM_Literal("votes + {$anthor_total}"), 'votestotal' => new NotORM_Literal("votestotal + {$total}")]);
        if($anthor_total){
            $insert_votes = [
                'type'     => '1',
                'action'   => $action,
                'uid'      => $liveuid,
                'fromid'   => $uid,
                'actionid' => $giftid,
                'nums'     => $giftcount,
                'total'    => $total,
                'showid'   => $showid,
                'votes'    => $anthor_total,
                'addtime'  => time(),
            ];
            DI()->notorm->user_voterecord->insert($insert_votes);
        }

        /* 更新主播热门 */
        if($giftinfo['mark'] == 1){
            DI()->notorm->live
                ->where('uid = ?', $liveuid)
                ->update(['hotvotes' => new NotORM_Literal("hotvotes + {$total}")]);
        }

        if($giftinfo['type'] == 0 && $giftinfo['mark'] == 3){
            $this->winPrize($giftid, $uid, $giftcount);
        }

        DI()->redis->zIncrBy('user_' . $stream, $total, $uid);
//        /* 清除缓存 */
        delCache("user:userinfo_" . $uid);
        delCache("user:userinfo_" . $liveuid);

        $userinfo2 = DI()->notorm->user
            ->select('consumption,coin')
            ->where('id = ?', $uid)
            ->fetchOne();
//
        $level = getLevel($userinfo2['consumption']);
        $gifttoken = md5(md5($action . $uid . $liveuid . $giftid . $giftcount . $total . $showid . $addtime . rand(100, 999)));

        $result = [
            "uid"       => $uid,
            "level"     => $level,
            "coin"      => $userinfo2['coin'],
            "gifttoken" => $gifttoken,
        ];
        return $result;
    }

    /* 发送弹幕 */
    public function sendBarrage($uid, $liveuid, $stream, $giftid, $giftcount, $content){

        $configpri = getConfigPri();

        $giftinfo = [
            "giftname" => '弹幕',
            "gifticon" => '',
            "needcoin" => $configpri['barrage_fee'],
        ];

        $total = $giftinfo['needcoin'] * $giftcount;
        if($total <= 0){
            return 1002;
        }
        $addtime = time();
        $type    = '0';
        $action  = '2';

        /* 更新用户余额 消费 */
        $ifok = DI()->notorm->user
            ->where('id = ? and coin >=?', $uid, $total)
            ->update(['coin' => new NotORM_Literal("coin - {$total}"), 'consumption' => new NotORM_Literal("consumption + {$total}")]);
        if(!$ifok){
            /* 余额不足 */
            return 1001;
        }


        /* 更新直播 魅力值 累计魅力值 */
        $istouid = DI()->notorm->user
            ->where('id = ?', $liveuid)
            ->update(['votes' => new NotORM_Literal("votes + {$total}"), 'votestotal' => new NotORM_Literal("votestotal + {$total}")]);

        $stream2 = explode('_', $stream);
        $showid  = $stream2[1];
        if(!$showid){
            $showid = 0;
        }

        $insert_votes = [
            'type'     => '1',
            'action'   => $action,
            'uid'      => $liveuid,
            'fromid'   => $uid,
            'actionid' => $giftid,
            'nums'     => $giftcount,
            'total'    => $total,
            'showid'   => $showid,
            'votes'    => $total,
            'addtime'  => time(),
        ];
        DI()->notorm->user_voterecord->insert($insert_votes);


        /* 写入记录 或更新 */
        $insert = ["type" => $type, "action" => $action, "uid" => $uid, "touid" => $liveuid, "giftid" => $giftid, "giftcount" => $giftcount, "totalcoin" => $total, "showid" => $showid, "addtime" => $addtime];
        $isup   = DI()->notorm->user_coinrecord->insert($insert);

        $userinfo2 = DI()->notorm->user
            ->select('consumption,coin')
            ->where('id = ?', $uid)
            ->fetchOne();

        $level = getLevel($userinfo2['consumption']);

        /* 清除缓存 */
        delCache("user:userinfo_" . $uid);
        delCache("user:userinfo_" . $liveuid);

        $votestotal = $this->getVotes($liveuid);

        $barragetoken = md5(md5($action . $uid . $liveuid . $giftid . $giftcount . $total . $showid . $addtime . rand(100, 999)));

        $result = ["uid" => $uid, "content" => $content, "giftid" => $giftid, "giftcount" => $giftcount, "totalcoin" => $total, "giftname" => $giftinfo['giftname'], "gifticon" => $giftinfo['gifticon'], "level" => $level, "coin" => $userinfo2['coin'], "votestotal" => $votestotal, "barragetoken" => $barragetoken];

        return $result;
    }

    /* 设置/取消 管理员 */
    public function setAdmin($liveuid, $touid){

        $isexist = DI()->notorm->live_manager
            ->select("*")
            ->where('uid=? and  liveuid=?', $touid, $liveuid)
            ->fetchOne();
        if(!$isexist){
            $count = DI()->notorm->live_manager
                ->where('liveuid=?', $liveuid)
                ->count();
            if($count >= 5){
                return 1004;
            }
            $rs = DI()->notorm->live_manager
                ->insert(["uid" => $touid, "liveuid" => $liveuid]);
            if($rs !== false){
                return 1;
            }else{
                return 1003;
            }

        }else{
            $rs = DI()->notorm->live_manager
                ->where('uid=? and  liveuid=?', $touid, $liveuid)
                ->delete();
            if($rs !== false){
                return 0;
            }else{
                return 1003;
            }
        }
    }

    /* 管理员列表 */
    public function getAdminList($liveuid){
        $rs = DI()->notorm->live_manager
            ->select("uid")
            ->where('liveuid=?', $liveuid)
            ->fetchAll();
        foreach($rs as $k => $v){
            $rs[$k] = getUserInfo($v['uid']);
        }

        $info['list']  = $rs;
        $info['nums']  = (string)count($rs);
        $info['total'] = '5';
        return $info;
    }

    /* 举报类型 */
    public function getReportClass(){
        return DI()->notorm->report_classify
            ->select("*")
            ->order("list_order asc")
            ->fetchAll();
    }

    /* 举报 */
    public function setReport($uid, $touid, $content, $type, $image){
        return DI()->notorm->report
            ->insert(["uid" => $uid, "touid" => $touid, 'content' => $content, 'type' => $type, 'image' => $image, 'addtime' => time()]);
    }

    /* 主播总映票 */
    public function getVotes($liveuid){
        $userinfo = DI()->notorm->user
            ->select("votestotal")
            ->where('id=?', $liveuid)
            ->fetchOne();
        return $userinfo['votestotal'];
    }

    /* 是否禁言 */
    public function checkShut($uid, $liveuid){

        $isexist = DI()->notorm->live_shut
            ->where('uid=? and liveuid=? ', $uid, $liveuid)
            ->fetchOne();
        if($isexist){
            DI()->redis->hSet($liveuid . 'shutup', $uid, 1);
        }else{
            DI()->redis->hDel($liveuid . 'shutup', $uid);
        }
        return 1;
    }

    /* 禁言 */
    public function setShutUp($uid, $liveuid, $touid, $showid){

        $isexist = DI()->notorm->live_shut
            ->where('uid=? and liveuid=? ', $touid, $liveuid)
            ->fetchOne();
        if($isexist){
            if($isexist['showid'] == $showid){
                return 1002;
            }


            if($isexist['showid'] == 0 && $showid != 0){
                return 1002;
            }

            $rs = DI()->notorm->live_shut->where('id=?', $isexist['id'])->update(['uid' => $touid, 'liveuid' => $liveuid, 'actionid' => $uid, 'showid' => $showid, 'addtime' => time()]);

        }else{
            $rs = DI()->notorm->live_shut->insert(['uid' => $touid, 'liveuid' => $liveuid, 'actionid' => $uid, 'showid' => $showid, 'addtime' => time()]);
        }


        return $rs;
    }

    /* 踢人 */
    public function kicking($uid, $liveuid, $touid){

        $isexist = DI()->notorm->live_kick
            ->where('uid=? and liveuid=? ', $touid, $liveuid)
            ->fetchOne();
        if($isexist){
            return 1002;
        }

        $rs = DI()->notorm->live_kick->insert(['uid' => $touid, 'liveuid' => $liveuid, 'actionid' => $uid, 'addtime' => time()]);


        return $rs;
    }

    /* 是否禁播 */
    public function checkBan($uid){

        $isexist = DI()->notorm->live_ban
            ->where('liveuid=? ', $uid)
            ->fetchOne();
        if($isexist){
            return 1;
        }
        return 0;
    }

    /* 超管关闭直播间 */
    public function superStopRoom($uid, $liveuid, $type){

        $userinfo = DI()->notorm->user
            ->select("issuper")
            ->where('id=? ', $uid)
            ->fetchOne();

        if($userinfo['issuper'] == 0){
            return 1001;
        }

        if($type == 1){

            /* 禁播列表 */
            $isexist = DI()->notorm->live_ban->where('liveuid=? ', $liveuid)->fetchOne();
            if($isexist){
                return 1002;
            }
            DI()->notorm->live_ban->insert(['liveuid' => $liveuid, 'superid' => $uid, 'addtime' => time()]);
        }

        if($type == 2){
            /* 关闭并禁用 */
            DI()->notorm->user->where('id=? ', $liveuid)->update(['user_status' => 0]);
        }


        $info = DI()->notorm->live
            ->select("stream")
            ->where('uid=? and islive="1"', $liveuid)
            ->fetchOne();
        if($info){
            $this->stopRoom($liveuid, $info['stream']);
        }


        return 0;

    }

    /* 获取用户本场贡献 */
    public function getContribut($uid, $liveuid, $showid){
        $sum = DI()->notorm->user_coinrecord
            ->where('action=1 and uid=? and touid=? and showid=? ', $uid, $liveuid, $showid)
            ->sum('totalcoin');
        if(!$sum){
            $sum = 0;
        }

        return (string)$sum;
    }

    /* 检测房间状态 */
    public function checkLiveing($uid, $stream){
        $info = DI()->notorm->live
            ->select('uid')
            ->where('uid=? and stream=? ', $uid, $stream)
            ->fetchOne();
        if($info){
            return '1';
        }

        return '0';
    }

    /* 获取直播信息 */
    public function getLiveInfo($liveuid){

        $info = DI()->notorm->live
            ->select("uid,title,city,stream,pull,thumb,isvideo,type,type_val,goodnum,anyway,starttime,isshop,game_action")
            ->where('uid=? and islive=1', $liveuid)
            ->fetchOne();
        if($info){

            $info = handleLive($info);

        }

        return $info;
    }

    protected function winPrize(Int $giftId, Int $uid, Int $number){
        require_once VENDOR . "autoload.php";
        $con     = new \PhpAmqpLib\Connection\AMQPStreamConnection('127.0.0.1', '5672', 'guest', 'guest');
        $channel = $con->channel();
        $channel->queue_declare('win_prize', false, false, false, false);
        $data['gift_id'] = $giftId;
        $data['uid']     = $uid;
        $data['number']  = $number;
        $msg             = new \PhpAmqpLib\Message\AMQPMessage(json_encode($data));
        $channel->basic_publish($msg, '', 'win_prize');
        $channel->close();
        $con->close();
    }

    public function getFamilyLiveList($liveuid, $lng, $lat){
        $sql    = 'select uid,title,city,stream,pull,thumb,isvideo,type,type_val,goodnum,anyway,starttime,isshop,game_action,lng,lat from cmf_live where uid in(select uid from cmf_family_user where islive = 1 and familyid = (select familyid from cmf_family_user where uid = :uid) and uid <> :uid) order by starttime desc limit 30';
        $params = [':uid' => $liveuid];
        $result = DI()->notorm->live->queryAll($sql, $params);

        foreach($result as $k => $v){

            $v = handleLive($v);

            $distance = '好像在火星';
            if($lng != '' && $lat != '' && $v['lat'] != '' && $v['lng'] != ''){
                $distance = getDistance($lat, $lng, $v['lat'], $v['lng']) . 'km';
            }elseif($v['city']){
                $distance = $v['city'];
            }

            $v['distance'] = $distance;
            unset($v['lng']);
            unset($v['lat']);

            $result[$k] = $v;

        }
//        if($result){
//            $last=end($result);
//            $_SESSION['new_starttime']=$last['starttime'];
//        }

        return $result;
    }
}
