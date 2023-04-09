<?php

class Model_Live extends PhalApi_Model_NotORM{
    const DUCK_NUM = 20000000;
    const MULTIPlE = 50;
    protected static $tableKeys = [
        '__default__' => 'uid',
    ];

    /* 创建房间 */
    public function createRoom($uid, $data, $type = 0){

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
            ->select("uid,isvideo,islive,stream,is_black,showid")
            ->where('uid=?', $uid)
            ->fetchOne();
        if($isexist){
            if($isexist && $isexist['islive'] == 0){
                $this->getORM()->where(["uid" => $uid])->delete();
            }
            /* 判断存在的记录是否为直播状态 */
            if($isexist['isvideo'] == 0 && !$isexist['is_black']){
                /* 若存在未关闭的直播 关闭直播 */
                $this->stopRoom($uid, $isexist['stream'], 0, 1);

                /* 加入 */
                $rs = DI()->notorm->live->insert($data);
            }else{
                $data['is_black'] = 0;
                unset($data['stream'], $data['showid']);
                /* 更新 */
                $rs                         = DI()->notorm->live->where('uid = ?', $uid)->update($data);
                $data['showid']             = $isexist['showid'];
                $data['stream']             = $isexist['stream'];
                $data['uid']                = (string)$isexist['uid'];
                $userinfo                   = getUserInfo($isexist['uid']);
                $data['user_nicename']      = $userinfo['user_nicename'];
                $data['avatar']             = $userinfo['avatar'];
                $data['level']              = $userinfo['level'];
                $data['level_thumb']        = $userinfo['level_thumb'];
                $data['level_anchor']       = $userinfo['level_anchor'];
                $data['level_anchor_thumb'] = $userinfo['level_anchor_thumb'];
                return [0, $data['stream']];
            }
            DI()->redis->set('common:live:black:' . $uid, json_encode($data));
        }else{
            if($type){
                $data['is_black'] = 1;
            }
            /* 加入 */
            $rs = DI()->notorm->live->insert($data);
        }
        if(!$rs){
            return [10008, ''];
        }
        return [0, ''];
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
            $this->stopRoom($uid, $stream, 0);
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
    public function stopRoom($uid, $stream, $type = 0, $flag = 0){

        $info = DI()->notorm->live
            ->select("uid,showid,starttime,title,province,city,stream,lng,lat,type,type_val,liveclassid,thumb,is_black")
//            ->where('uid=? and stream=? and islive="1"', $uid, $stream)
            ->where('uid=?', $uid)
            ->fetchOne();
        if($type){
            $start_time = explode('_', $stream)[1];
            $stream = $info['stream'];
        }
        file_put_contents(API_ROOT . '/Runtime/stopRoom_' . date('Y-m-d')
            . '.txt',
            date('Y-m-d H:i:s') . ' 提交参数信息 info:' . json_encode($info) . "\r\n",
            FILE_APPEND);
        if($info){
            $query = DI()->notorm->live
                ->where('uid=?', $uid)
                ->where('is_black=?', 0);
            if ($type) {
                $query = $query->where('starttime=?', $start_time);
            }
            $isdel = $query->delete();
            if(!$isdel){
                return 0;
            }
            $nowtime         = time();
            $info['endtime'] = $nowtime;
            if($info['is_black']){
                $info['time'] = date("Y-m-d", $info['showid']);
            }else{
                $info['time'] = date("Y-m-d", $info['starttime']);
            }
            unset($info['is_black']);
            $info['votes'] = DI()->notorm->user_voterecord
                    ->where('uid =? and fromid !=? and showid=?', $uid, $uid, $info['showid'])->sum('votes') ?? 0;
            $nums          = DI()->redis->zCard(Common_Cache::LIVE_NOW_NUMS . $stream) ?: 0;
            $live_nums     = DI()->redis->Scard(Common_Cache::LIVE_AUDIENCE . $stream) ?: 0;
            DI()->redis->hDel("livelist", $uid);
            DI()->redis->del($uid . '_zombie');
            DI()->redis->del($uid . '_zombie_uid');
            DI()->redis->del('attention_' . $uid);
            DI()->redis->del(Common_Cache::LIVE_NOW_NUMS . $stream);
            DI()->redis->del(Common_Cache::LIVE_AUDIENCE . $stream);
            DI()->redis->del(Common_Cache::CORPSE_INSET . $stream);
            $info['nums']      = $nums;
            $info['live_nums'] = $live_nums;
            /** 保存直播送礼个数 */
            $giftCount               = DI()->notorm->gift_record
                ->where([
                    'showid' => $info['showid'],
                    'touid'  => $uid,
                ])->sum('giftcount');
            $info['gift_count']      = $giftCount ?? 0;
            $info['addtime']         = time();
            $zKey                    = "live:sendGift:" . $stream . "_users";
            $info['gift_sender_num'] = DI()->redis->sCard($zKey);
            DI()->redis->del($zKey);
            $result = DI()->notorm->live_record->insert($info);
            file_put_contents(API_ROOT . '/Runtime/stopRoom_' . date('Y-m-d')
                . '.txt', date('Y-m-d H:i:s') . ' 提交参数信息 result:'
                . json_encode($result['id']) . "\r\n", FILE_APPEND);

            /* 解除本场禁言 */
            $list2 = DI()->notorm->live_shut
                ->select('uid')
                ->where('liveuid=? and showid!=0', $uid)
                ->fetchAll();
            DI()->notorm->live_shut->where('liveuid=? and showid!=0', $uid)
                ->delete();

            foreach($list2 as $k => $v){
                DI()->redis->hDel($uid . 'shutup', $v['uid']);
            }

            /* 游戏处理 */
//            $game  = DI()->notorm->game
//                ->select("*")
//                ->where('stream=? and liveuid=? and state=?', $stream, $uid,
//                    "0")
//                ->fetchOne();
//            $total = [];
//            if($game){
//                $total = DI()->notorm->gamerecord
//                    ->select("uid,sum(coin_1 + coin_2 + coin_3 + coin_4 + coin_5 + coin_6) as total")
//                    ->where('gameid=?', $game['id'])
//                    ->group('uid')
//                    ->fetchAll();
//                foreach($total as $k => $v){
//                    DI()->notorm->user
//                        ->where('id = ?', $v['uid'])
//                        ->update(['coin' => new NotORM_Literal("coin + {$v['total']}")]);
//
//                    $insert = [
//                        "type"      => '1',
//                        "action"    => '20',
//                        "uid"       => $v['uid'],
//                        "touid"     => $v['uid'],
//                        "giftid"    => $game['id'],
//                        "giftcount" => 1,
//                        "totalcoin" => $v['total'],
//                        "showid"    => 0,
//                        "addtime"   => $nowtime,
//                    ];
//                    DI()->notorm->user_coinrecord->insert($insert);
//                }
//
//                DI()->notorm->game
//                    ->where('id = ?', $game['id'])
//                    ->update(['state' => '3', 'endtime' => time()]);
//                $brandToken = $stream . "_" . $game["action"] . "_"
//                    . $game['starttime'] . "_Game";
//                DI()->redis->delete($brandToken);
//            }
            DI()->redis->delete('common:live:black:' . $uid);
            if($type == 99) return 1;
            $count = DI()->notorm->user_attention
                    ->where('touid=?', $uid)
                    ->count() ?? 0;
            if($count && !$flag){
                $model      = new Model_User();
                $time       = time();
                $stream     = $uid . '_' . $time;
                $insertData = [
                    "uid"         => $uid,
                    "showid"      => $time,
                    "title"       => $info['title'] . '波鸭直播',
                    "province"    => $info['province'],
                    "city"        => $info['city'],
                    "stream"      => $stream,
                    "thumb"       => $info['thumb'],
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
                $model->createBlackLive($uid, $insertData);
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

        $stream2 = explode('_', $stream);
        $liveuid = $stream2[0];
//        $starttime = $stream2[1];
        $liveinfo = DI()->notorm->live_record
            ->select("starttime,endtime,nums,votes")
//            ->where('uid=? and starttime=?', $liveuid, $starttime)
            ->where('uid=? and stream=?', $liveuid, $stream)
            ->fetchOne();
        if($liveinfo){
            $cha = $liveinfo['endtime'] - $liveinfo['starttime'];
//            $rs['length'] = ceil($cha / 60) . '分钟';
            $rs['length'] = getSeconds($cha, 1);
            $rs['nums']   = $liveinfo['nums'];
        }
        if($liveinfo['votes']){
            $rs['votes'] = (string)round($liveinfo['votes'] / 100, 2);
        }
        return $rs;
    }

    /* 直播状态 */
    public function checkLive($uid, $liveuid, $stream){

        /* 是否被踢出 */
        $isexist = DI()->notorm->live_kick
            ->select("id")
            ->where('uid=? and stream=?', $uid, $stream)
            ->fetchOne();
        if($isexist){
            return 1008;
        }

        $islive = DI()->notorm->live
            ->select("islive,type,type_val,starttime,is_black")
            ->where('uid=? and stream=?', $liveuid, $stream)
            ->fetchOne();

        if(!$islive || $islive['islive'] == 0){
            return 1005;
        }
        $rs['type']     = $islive['type'];
        $rs['type_val'] = '0';
        $rs['type_msg'] = '';
        $rs['is_black'] = $islive['is_black'];

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
                ->where('uid=? and touid=? and showid=? and action=6 and type=0',
                    $uid, $liveuid, $islive['starttime'])
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
            ->update([
                'coin'        => new NotORM_Literal("coin - {$total}"),
                'consumption' => new NotORM_Literal("consumption + {$total}"),
            ]);
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
            ->update([
                'votes'      => new NotORM_Literal("votes + {$total}"),
                'votestotal' => new NotORM_Literal("votestotal + {$total}"),
            ]);

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
            ->insert([
                "type"      => '0',
                "action"    => $action,
                "uid"       => $uid,
                "touid"     => $liveuid,
                "giftid"    => $giftid,
                "giftcount" => $giftcount,
                "totalcoin" => $total,
                "showid"    => $showid,
                "addtime"   => $addtime,
            ]);

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
                    DI()->notorm->user_zombie->where("uid={$v['uid']}")
                        ->delete();
                    continue;
                }

                $info[] = $userinfo;

                $score = '0.' . ($userinfo['level'] + 100) . '1';
                DI()->redis->zAdd(Common_Cache::LIVE_NOW_NUMS . $stream, $score, $v['uid']);
            }
        }
        return $info;
    }

    /* 礼物列表 */
    public function getGiftList(){

        $rs = DI()->notorm->gift
            ->select("id,type,mark,giftname,needcoin,gifticon,sticker_id,swftime,isplatgift")
            ->where('type!=3')
            ->order("list_order asc,addtime desc")
            ->fetchAll();

        return $rs;
    }

    /* 礼物列表 */
    public function getGiftLists($type){
        $rs = DI()->notorm->gift
            ->select("id,type,mark,giftname,needcoin,gifticon,sticker_id,swftime,isplatgift,anchor_rate")
            ->where('type=' . $type . ' and status=1')
            ->order("list_order asc,addtime desc")
            ->fetchAll();

        return $rs;
    }

    /* 礼物：道具列表 */
    public function getPropgiftList(){

        $rs = DI()->notorm->gift
            ->select("id,type,mark,giftname,needcoin,gifticon,sticker_id,swftime,isplatgift")
            ->where("type=3")
            ->order("list_order asc,addtime desc")
            ->fetchAll();

        return $rs;
    }

    protected function getGiftInfo($giftid){
        $key       = 'Gift:gift_info_' . $giftid;
        $gift_info = getcaches($key);
        if(!$gift_info){
            $gift_info = DI()->notorm->gift->select('*')->where('id = ?',
                $giftid)->fetchOne();
            if($gift_info){
                setcaches($key, $gift_info, 30);
            }
        }
        return $gift_info;
    }

    public function sendGifts($uid, $liveid, $stream, $giftid, $giftcount){
        //礼物信息
        $gift_info  = $this->getGiftInfo($giftid);
        $gift_money = intval($gift_info['needcoin']);

        //提成计算
        $total_money = floor($giftcount * $gift_money); //总价（钻石）
        $live_money  = $total_money * $gift_info['anchor_rate']; //主播（钻石）
        $user_money  = $total_money; //用户（金币）

        $userModel         = new Model_User();
        $familyUserModel   = new Model_FamilyUser();
        $familyModel       = new Model_Family();
        $familyProfitModel = new Model_FamilyProfit();


        //送礼人
        $user_info = $userModel->get($uid, 'consumption,coin,avatar_thumb,user_nicename,have_jackpot');

        //判断余额
        if($user_info['coin'] < $total_money){
            return [10000, '余额不足'];
        }

        //是否有家族
        $familyId     = $familyUserModel->getFamiliId($liveid);
        $family_money = 0;
        if($familyId){
            $family_money = $live_money * $gift_info['family_rate'] / 100; //家族（钻石)
            $familyUserId = $familyModel->get($familyId, 'uid');
        }

        //幸运礼物
        $multiple       = 0;
        $multiple_count = null;
        if($gift_info['type'] == '0'){
            $have_jackpot  = $user_info['have_jackpot'] ? $user_info['have_jackpot'] : 0;
            $jackpot_model = new Domain_JackPot($gift_info, $giftcount, $have_jackpot);
            list($multiple, $multiple_desc) = $jackpot_model->start();
            if($multiple > 0){
                $user_money     -= $multiple * $gift_info['needcoin'];
                $multiple_count = json_encode(array_count_values($multiple_desc));
            }
        }
        $bean = $this->getORM();
        //获取直播数据
        $live_info = $bean
            ->where(['stream' => $stream])
            ->select('uid,hotvotes,net_hotvotes,hot_deadline,hot_updtime,pull,title,stream')
            ->fetchOne();
        $live_data = [];
        //判断是否送给主播
        if($live_info && $live_info['uid'] == $liveid){
            //直播数据准备
            if($gift_info['type'] == 1 && $gift_info['mark'] == 1)//判断热门
            {
                $time         = time();
                $hot_time     = $live_info['hot_updtime'] ?: 0;
                $hot_deadline = $live_info['hot_deadline'] ?: 0;
                $time - $hot_time > 0 && $live_data['hot_updtime'] = $time + 5;
                $live_data['hot_deadline'] = ($time - $hot_deadline > 0) ? $time + 600 : $hot_deadline + 600;
            }
        }

        $live_data['net_hotvotes']
            = new NotORM_Literal("net_hotvotes + {$live_money}");

        $stream2 = explode('_', $stream);
        $showid  = $stream2[1];

        //流水数据
        $vote_data = [];
        $coin_data = [];

        //用户送礼支出数据
        $coin_data[] = [
            'type'      => Model_UserCoinRecord::OUT,
            'action'    => Model_UserCoinRecord::SL,
            'uid'       => $uid,
            'touid'     => $liveid,
            'giftcount' => $giftcount,
            'giftid'    => $giftid,
            'totalcoin' => $total_money,
            'showid'    => $showid,
            'addtime'   => time(),
        ];
        //送礼记录
        $gift_record_data = [
            'gift_id'   => $giftid,
            'giftcount' => $giftcount,
            'uid'       => $uid,
            'touid'     => $liveid,
            'totalcoin' => $total_money,
            'showid'    => $showid,
            'addtime'   => time(),
        ];
        //主播送礼收入数据
        $vote_data[] = [
            'type'    => Model_UserVoteRecord::INCOME,
            'action'  => Model_UserVoteRecord::SHOULI,
            'uid'     => $liveid,
            'fromid'  => $uid,
            'nums'    => $giftcount,
            'total'   => $total_money,
            'showid'  => $showid,
            'votes'   => $live_money,
            'addtime' => time(),
        ];

        //家族分成收入数据
        if(isset($familyUserId) && $familyUserId > 0 && $family_money > 0){
            $vote_data[]  = [
                'type'    => Model_UserVoteRecord::INCOME,
                'action'  => Model_UserVoteRecord::JIAZU,
                'uid'     => $familyUserId,
                'fromid'  => $liveid,
                'nums'    => $giftcount,
                'total'   => $total_money,
                'showid'  => $showid,
                'votes'   => $family_money,
                'addtime' => time(),
            ];
            $familyRecord = [
                "uid"           => $liveid,
                "time"          => date('Y-m-d H:i:s'),
                "addtime"       => time(),
                "profit"        => $family_money,
                "profit_anthor" => $live_money,
                "total"         => $total_money,
                "familyid"      => $familyId,
            ];
        }

        $userCoinModel  = new Model_UserCoinRecord();
        $userVotesModel = new Model_UserVoteRecord();
        $jackpotModel   = new Model_JackpotRecord();
        try{
            //事物
            $bean->queryAll('begin');
            if($live_info && $live_info['uid'] == $liveid){
                //更新直播数据
                if(
                !$bean->where('uid = ?', $liveid)
                    ->update($live_data)
                ){
                    $bean->queryAll('rollback');
                    return [1, '送礼失败1'];
                }
            }

            //幸运礼物中奖判断
            if($multiple > 0){
                //添加中奖记录
                $win_data = [
                    'uid'         => $uid,
                    'gift_id'     => $giftid,
                    'multiple'    => $multiple,
                    'money'       => $multiple * $gift_money,
                    'gift_num'    => $giftcount,
                    'create_time' => time(),
                ];
                //用户中奖流水
                $coin_data[] = [
                    'type'      => Model_UserCoinRecord::INCOME,
                    'action'    => Model_UserCoinRecord::ZJ,
                    'uid'       => $uid,
                    'touid'     => $liveid,
                    'giftcount' => $multiple,
                    'giftid'    => $giftid,
                    'totalcoin' => $multiple * $gift_money,
                    'showid'    => $showid,
                    'addtime'   => time(),
                ];
                if(
                !$jackpotModel->insert($win_data)
                ){
                    $bean->queryAll('rollback');
                    return [99, '送礼失败99'];
                }
            }

            //用户金额更新
            if(
            !DI()->notorm->user
                ->where('id = ? and coin >= ?', $uid, $user_money)
                ->update([
                    'coin'        => new NotORM_Literal("coin - {$user_money}"),
                    'consumption' => new NotORM_Literal("consumption + {$total_money}"),
                    'give_money'  => new NotORM_Literal("give_money + {$total_money}"),
                ])
            ){
                $bean->queryAll('rollback');
                return [10000, '余额不足'];
            }

            //主播金额更新
            if(
            !$userModel->update($liveid, [
                'votes'      => new NotORM_Literal("votes + {$live_money}"),
                'votestotal' => new NotORM_Literal("votestotal + {$live_money}"),
                'gift_money' => new NotORM_Literal("gift_money + {$total_money}"),
            ])
            ){
                $bean->queryAll('rollback');
                return [2, '送礼失败2'];
            }
            $giftModel  = new Model_GiftRecord();
            $giftRecord = $giftModel->insert($gift_record_data);
            //送礼记录
            if(!$giftRecord){
                $bean->queryAll('rollback');
                return [66, '送礼失败'];
            }

            //家族金额更新
            if(isset($familyUserId) && $familyUserId > 0 && $family_money > 0 && isset($familyRecord)){
                if(
                !$userModel->update($familyUserId, [
                    'votes'      => new NotORM_Literal("votes + {$family_money}"),
                    'votestotal' => new NotORM_Literal("votestotal + {$family_money}"),
                ])
                ){
                    $bean->queryAll('rollback');
                    return [3, '送礼失败3-' . $familyUserId . '-' . $family_money,];
                }
                if(!$familyProfitModel->insert($familyRecord)){
                    $bean->queryAll('rollback');
                    return [10, '送礼失败4-' . $familyUserId . '-' . $family_money,];
                }

                if(!$familyModel->update($familyId, [
                    'gift_money' => new NotORM_Literal("gift_money + {$total_money}"),
                ])){
                    $bean->queryAll('rollback');
                    return [3, '送礼失败8-' . $familyUserId . '-' . $family_money,];
                }
            }
            foreach($vote_data as &$v){
                $v['actionid'] = $giftRecord;
                $v['giftid']   = $giftid;
            }
            //钻石流水
            if(
            !$userVotesModel->moreInsert($vote_data)
            ){
                $bean->queryAll('rollback');
                return [7, '送礼失败7'];
            }
            //金币流水
            if(
            !$userCoinModel->moreInsert($coin_data)
            ){
                $bean->queryAll('rollback');
                return [8, '送礼失败8'];
            }
            $bean->queryAll('commit');
        }catch(\Exception $e){
            $bean->queryAll('rollback');
            return [99, '送礼失败' . $e->getMessage()];
        }
        //收礼人
        $live_user_info = DI()->notorm->user
            ->where('id = ?', $liveid)
            ->select('avatar_thumb,user_nicename,votestotal,verify')
            ->fetchOne();
        $level          = getLevelV2($user_info['consumption'] + $total_money);
        $gifttoken      = md5(md5($uid . $liveid . $giftid . $giftcount . $user_money . $showid . time() . rand(100, 999)));
        //pk
        $pkDomain = new Domain_Livepk();
        $pkInfo   = ['isPk' => '0'];
        if($live_info && $live_info['uid'] == $liveid){
            $isPk = $pkDomain->addGiftNums($uid, $liveid, $total_money);
            if($isPk){
                $pkInfo = $pkDomain->getPkInfo($liveid);
            }
        }

        //准备socket数据
        $result         = [
            "uid"            => $uid,
            "liveuid"        => $liveid,
            "giftid"         => $giftid,
            "type"           => $gift_info['type'],
            "mark"           => $gift_info['mark'],
            "giftcount"      => $giftcount,
            "totalcoin"      => $total_money,
            "giftname"       => $gift_info['giftname'],
            "gifticon"       => get_upload_path($gift_info['gifticon']),
            "swftime"        => $gift_info['swftime'],
            "swftype"        => $gift_info['swftype'],
            "swf"            => $gift_info['swf'] ? get_upload_path($gift_info['swf']) : '',
            "level"          => $level,
            "coin"           => $user_info['coin'] - $user_money,
            "votestotal"     => (string)($live_user_info['votestotal'] / 100),
            "gifttoken"      => $gifttoken,
            "isplatgift"     => $gift_info['isplatgift'],
            "sticker_id"     => $gift_info['sticker_id'],
            "othername"      => $live_user_info['user_nicename'],
            "othericon"      => get_upload_path($live_user_info['avatar_thumb']),
            "isluck"         => $multiple > 0 ? 1 : 0,
            "isluckall"      => 1,
            "luckcoin"       => $multiple * $gift_money,
            "lucktimes"      => $multiple,
            "multiple_count" => $multiple_count,
            "isup"           => 0,
            "uplevel"        => 0,
            "upcoin"         => 0,
            "iswin"          => 0,
            "wincoin"        => 0,
            "pkInfos"        => $pkInfo,
            'ranking_no'     => 0,
        ];
        $live_user_info = getUserInfo($liveid);
        if($gift_info['isplatgift'] == 1){
            $result['isplatgift_info']['pull']          = $live_info['pull'];
            $result['isplatgift_info']['title']         = $live_info['title'];
            $result['isplatgift_info']['stream']        = $live_info['stream'];
            $result['isplatgift_info']['goodnum']       = $live_user_info['liang']['name'] ?? 0;
            $result['isplatgift_info']['user_nicename'] = $live_user_info['user_nicename'];
            $result['isplatgift_info']['avatar']        = $live_user_info['avatar_thumb'];
            $result['isplatgift_info']['uid']           = $live_info['uid'];
        }
        if(!empty($live_user_info['remark_info'])){
            $key = Common_Cache::ACTIVE_DAY_WATER . date('Ymd');
            //增加主播收入
            $num = DI()->redis->ZINCRBY($key, $result['totalcoin'], $liveid);
            if($num == $result['totalcoin']){
                $outTime = mktime(23, 59, 59, date('m'), date('d') + 1, date('y'));
                DI()->redis->expire($key, $outTime);
            }
            $ranking_model = new Domain_Ranking();
            $ranking_info  = $ranking_model->liveNo($liveid);
            if($ranking_info['branch'] >= 8000000){
                $result['ranking_no'] = $ranking_info['no'];
            }

            //中秋
            $model = new Domain_Festival();
            $model->setFestivalGift($giftid, $giftcount, $liveid);
        }

        if($familyId){
            $redis      = DI()->redis;
            $day_key    = Common_Cache::ACTIVE_FAMILY_DAY_WATER . date('Ymd');
            $week_start = mktime(23, 59, 59, date("m"), date("d") - (date('w') ?: 7) + 1, date("Y"));
            $week_key   = Common_Cache::ACTIVE_FAMILY_WEEK_WATER . date('Ymd', $week_start);
            //增加家族日流水
            $res_day = $redis->ZINCRBY($day_key, $total_money, $familyId);
            if($res_day == $total_money){
                $day_time = mktime(23, 59, 59, date('m'), date('d') + 1, date('Y'));
                $redis->Expire($day_key, $day_time - time());
            }
            //增加家族周流水
            $res_week = $redis->ZINCRBY($week_key, $total_money, $familyId);
            if($res_week == $total_money){
                $week_time = mktime(23, 59, 59, date("m"), date("d") - (date('w') ?: 7) + 14, date("Y"));
                $redis->Expire($week_key, $week_time - time());
            }
        }
        $startTime = strtotime('2020-10-02 00:00:00');
        $endTime   = strtotime('2020-10-08 24:00:00');
        $now       = time();
        $duck_info = null;
        if($live_user_info['verify']){
            if($now >= $startTime && $now < $endTime){
                if($stream2[0] == $liveid){
                    $duck_info = $this->nationalDay($uid, $liveid, $total_money);
                }else{
                    $this->nationalDay($uid, $liveid, $total_money);
                }
            }
        }
        $result['duck_info'] = $duck_info;
        return [0, $result];
    }

//    /* 赠送礼物 */
//    public function sendGift($uid, $liveuid, $stream, $giftid, $giftcount, $ispack){
//        $start          = hm();
//        $stream2        = explode('_', $stream);
//        $showid         = $stream2[1];
//        $sql_start_time = hm();
//        $res            = DI()->notorm->user->queryAll("call SendGift($uid, $liveuid, $giftid, $giftcount,$showid,@gift_type, @gift_mark, @gift_name, @gift_icon, @gift_swftime, @gift_swftype, @gift_swf, @user_consumption, @user_coin, @gift_isplatgift, @gift_sticker_id, @gift_total_coin, @anchor_total, @family_total,@recver_nickname ,@recver_avatar_thumb, @gift_award_total)");
//        if($res[0]['code'] !== '0'){
//            return [1, $res[0]['msg']];
//        }
//        $res_data     = DI()->notorm->user->queryAll('select @gift_type, @gift_mark, @gift_name, @gift_icon, @gift_swftime,@gift_swftype, @gift_swf, @user_consumption, @user_coin, @gift_isplatgift,@gift_sticker_id, @gift_total_coin, @anchor_total, @family_total,@recver_nickname ,@gift_award_total');
//        $sql_end_time = hm();
//        $sql_time     = $sql_end_time - $sql_start_time;
//        $action       = '1';
//        $key1         = 'LivePK';
//        $ispk         = '0';
//        $pkuid1       = '0';
//        $pkuid2       = '0';
//        $pktotal1     = '0';
//        $pktotal2     = '0';
//        $pkuid        = DI()->redis->hGet($key1, $liveuid);
//        delCache("user:userinfo_" . $uid);
//        delCache("user:userinfo_" . $liveuid);
//        $gifttoken = md5(md5($action . $uid . $liveuid . $giftid . $giftcount . $res_data[0]['@gift_total_coin'] . $showid . time() . rand(100, 999)));
//        $isluck    = '0';
//        $isluckall = '0';
//        $luckcoin  = '0';
//        $lucktimes = '0';
//        $isup      = '0';
//        $uplevel   = '0';
//        $upcoin    = '0';
//        $r_time    = 0;
//        if($res_data[0]['@gift_type'] == 0){
//            $isluck    = 1;
//            $isluckall = 1;
//            $luckcoin  = 1;
//            $lucktimes = $res_data[0]['gift_award_total'] ?? 0;
//            $r_start   = hm();
////            $this->winPrize($giftid, $uid, $giftcount);
//            $r_time = hm() - $r_start;
//        }
//        $level  = getLevelV2($res_data[0]['@user_consumption']);
//        $swf    = $res_data[0]['@gift_swf'] ? get_upload_path($res_data[0]['@gift_swf']) : '';
//        $result = [
//            "uid"        => $uid,
//            "liveuid"    => $liveuid,
//            "giftid"     => $giftid,
//            "type"       => $res_data[0]['@gift_type'],
//            "mark"       => $res_data[0]['@gift_mark'],
//            "giftcount"  => $giftcount,
//            "totalcoin"  => $res_data[0]['@gift_total_coin'],
//            "giftname"   => $res_data[0]['@gift_name'],
//            "gifticon"   => get_upload_path($res_data[0]['@gift_icon']),
//            "swftime"    => $res_data[0]['@gift_swftime'],
//            "swftype"    => $res_data[0]['@gift_swftype'],
//            "swf"        => $swf,
//            "level"      => $level,
//            "coin"       => $res_data[0]['@user_coin'],
//            "votestotal" => $res_data[0]['@user_consumption'],
//            "gifttoken"  => $gifttoken,
//            "isplatgift" => $res_data[0]['@gift_isplatgift'],
//            "sticker_id" => $res_data[0]['@gift_sticker_id'],
//            "othername"  => $res_data[0]['@recver_nickname'],
//            "othericon"  => get_upload_path($res_data[0]['@recver_avatar_thumb']),
//
//            "isluck"    => $isluck,
//            "isluckall" => $isluckall,
//            "luckcoin"  => $luckcoin,
//            "lucktimes" => $lucktimes,
//
//            "isup"    => $isup,
//            "uplevel" => $uplevel,
//            "upcoin"  => $upcoin,
//
//            "iswin"   => 0,
//            "wincoin" => 0,
//
//            "ispk"     => $ispk,
//            "pkuid"    => $pkuid,
//            "pkuid1"   => $pkuid1,
//            "pkuid2"   => $pkuid2,
//            "pktotal1" => $pktotal1,
//            "pktotal2" => $pktotal2,
//        ];
//        $c_time = hm() - $start;
//        $log    = '总耗时约:' . $c_time . ';数据库耗时:' . $sql_time . ';rabbitmq耗时:' . $r_time . ';其他耗时:' . ($c_time - $sql_time - $r_time) . PHP_EOL;
//        file_put_contents(API_ROOT . '/Runtime/test.log', $log, FILE_APPEND);
//        return [0, $result];
//    }

    /* 发送弹幕 */
    public function sendBarrage(
        $uid,
        $liveuid,
        $stream,
        $giftid,
        $giftcount,
        $content
    ){

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
            ->update([
                'coin'        => new NotORM_Literal("coin - {$total}"),
                'consumption' => new NotORM_Literal("consumption + {$total}"),
            ]);
        if(!$ifok){
            /* 余额不足 */
            return 1001;
        }


        /* 更新直播 魅力值 累计魅力值 */
        $istouid = DI()->notorm->user
            ->where('id = ?', $liveuid)
            ->update([
                'votes'      => new NotORM_Literal("votes + {$total}"),
                'votestotal' => new NotORM_Literal("votestotal + {$total}"),
            ]);

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
        $insert = [
            "type"      => $type,
            "action"    => $action,
            "uid"       => $uid,
            "touid"     => $liveuid,
            "giftid"    => $giftid,
            "giftcount" => $giftcount,
            "totalcoin" => $total,
            "showid"    => $showid,
            "addtime"   => $addtime,
        ];
        $isup   = DI()->notorm->user_coinrecord->insert($insert);

        $userinfo2 = DI()->notorm->user
            ->select('consumption,coin')
            ->where('id = ?', $uid)
            ->fetchOne();

        $level = getLevelV2($userinfo2['consumption']);

        /* 清除缓存 */
        delCache(Common_Cache::USERINFO . $uid);
        delCache(Common_Cache::USERINFO . $liveuid);

        $votestotal = $this->getVotes($liveuid);

        $barragetoken = md5(md5($action . $uid . $liveuid . $giftid . $giftcount
            . $total . $showid . $addtime . rand(100, 999)));

        $result = [
            "uid"          => $uid,
            "content"      => $content,
            "giftid"       => $giftid,
            "giftcount"    => $giftcount,
            "totalcoin"    => $total,
            "giftname"     => $giftinfo['giftname'],
            "gifticon"     => $giftinfo['gifticon'],
            "level"        => $level,
            "coin"         => $userinfo2['coin'],
            "votestotal"   => $votestotal,
            "barragetoken" => $barragetoken,
        ];

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
            ->insert([
                "uid"     => $uid,
                "touid"   => $touid,
                'content' => $content,
                'type'    => $type,
                'image'   => $image,
                'addtime' => time(),
            ]);
    }

    /* 主播总映票 */
    public function getVotes($liveuid){
        $userinfo = DI()->notorm->user
            ->select("votestotal")
            ->where('id=?', $liveuid)
            ->fetchOne();
        return (string)round($userinfo['votestotal'] / 100, 2);
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

            $rs = DI()->notorm->live_shut->where('id=?', $isexist['id'])
                ->update([
                    'uid'      => $touid,
                    'liveuid'  => $liveuid,
                    'actionid' => $uid,
                    'showid'   => $showid,
                    'addtime'  => time(),
                ]);

        }else{
            $rs = DI()->notorm->live_shut->insert([
                'uid'      => $touid,
                'liveuid'  => $liveuid,
                'actionid' => $uid,
                'showid'   => $showid,
                'addtime'  => time(),
            ]);
        }


        return $rs;
    }

    /* 踢人 */
    public function kicking($uid, $liveuid, $touid, $stream){

        $isexist = DI()->notorm->live_kick
            ->where('uid=? and stream=? ', $touid, $stream)
            ->fetchOne();
        if($isexist){
            return 1002;
        }

        $rs = DI()->notorm->live_kick->insert([
            'uid'      => $touid,
            'liveuid'  => $liveuid,
            'stream'   => $stream,
            'actionid' => $uid,
            'addtime'  => time(),
        ]);


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

        $is_super = DI()->notorm->user_super
            ->where('uid=? ', $uid)
            ->count();

        if($is_super < 1){
            return 1001;
        }

        if($type == 1){

            /* 禁播列表 */
            $isexist = DI()->notorm->live_ban->where('liveuid=? ', $liveuid)
                ->fetchOne();
            if($isexist){
                return 1002;
            }
            DI()->notorm->live_ban->insert([
                'liveuid' => $liveuid,
                'superid' => $uid,
                'addtime' => time(),
            ]);
        }

        if($type == 2){
            /* 关闭并禁用 */
            DI()->notorm->user->where('id=? ', $liveuid)
                ->update(['user_status' => 0]);
        }


        $info = DI()->notorm->live
            ->select("stream")
            ->where('uid=? and islive="1"', $liveuid)
            ->fetchOne();
        if($info){
            $this->stopRoom($liveuid, $info['stream'], 0);
        }


        return 0;

    }

    /* 获取用户本场贡献 */
    public function getContribut($uid, $liveuid, $showid){
        $sum = DI()->notorm->user_coinrecord
            ->where('action=1 and uid=? and touid=? and showid=? ', $uid,
                $liveuid, $showid)
            ->sum('totalcoin');
        if(!$sum){
            $sum = 0;
        }

        return (string)$sum;
    }

    /* 获取用户本场贡献 */
    public function getContributV2($uid, $liveuid, $showid){
        $sum = DI()->notorm->gift_record
            ->where('uid=? and touid=? and showid=? ', $uid, $liveuid, $showid)
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
    public function getLiveInfo($liveUid){

        $info = DI()->notorm->live
            ->select("uid,title,city,stream,pull,thumb,isvideo,type,type_val,goodnum,anyway,starttime,isshop,game_action,pkuid")
            ->where('uid=? and islive=1', $liveUid)
            ->fetchOne();
        if($info){
            $info = handleLive($info);
        }

        return $info;
    }

    protected function winPrize(Int $giftId, Int $uid, Int $number){
//        $conn_args = array(
//            'host' => '127.0.0.1',
//            'port' => '5672',
//            'login' => 'guest',
//            'password' => 'guest',
//            'vhost'=>'/'
//        );
//        $e_name    = 'win_prize'; //交换机名
////        $q_name    = 'win_prize'; //队列名
//        $k_route   = 'win_prize_1'; //路由key
//
//        //创建连接和channel
//        $conn = new AMQPConnection($conn_args);
//        if (!$conn->connect()) {
//            die("Cannot connect to the broker!\n");
//        }
//        $channel = new AMQPChannel($conn);
//
//        //创建交换机对象
//        $ex = new AMQPExchange($channel);
//        $ex->setName($e_name);
//        date_default_timezone_set("Asia/Shanghai");
//        $message = json_encode(['gift_id'=>$giftId,'uid'=>$uid,'num'=>$number]);
//        //发送消息
//        //$channel->startTransaction(); //开始事务
//        $naem = $ex->publish($message, $k_route);
//        //$channel->commitTransaction(); //提交事务
//
//        $conn->disconnect();
        require_once VENDOR . "autoload.php";
        $con = new \PhpAmqpLib\Connection\AMQPStreamConnection('127.0.0.1',
            '5672', 'guest', 'guest');
//        $con     = new \PhpAmqpLib\Connection\AMQPStreamConnection('127.0.0.1', '5672', 'guest', 'boya1818');
        $channel = $con->channel();
        $channel->queue_declare('win_prize', false, false, false, false);
        $data['gift_id'] = $giftId;
        $data['uid']     = $uid;
        $data['number']  = $number;
        $msg
                         = new \PhpAmqpLib\Message\AMQPMessage(json_encode($data));
        $channel->basic_publish($msg, '', 'win_prize');
        $channel->close();
        $con->close();
        return true;
    }

    public function getFamilyLiveList($liveuid){
        $familiy_model = new Model_Family();
        $list          = $familiy_model->familyUserList($liveuid);
        foreach($list as $k => $v){
            $v             = handleLive($v);
            $distance      = '好像在火星';
            $v['distance'] = $distance;
            $list[$k]      = $v;
        }
        return $list;
    }

    public function getLiveTime($uid){
        $time = $this->getORM()
            ->where([
                'uid'      => $uid,
                'islive'   => 1,
                'is_black' => 0,
            ])
            ->fetchOne('starttime');
        return $time ?: 0;
    }

    public function isLive($uid){
        $sql = "select count(*) a from cmf_live where uid = :uid and islive = 1 and hide = 0 and is_black = 0";
        $res = $this->getORM()->queryAll($sql, [':uid' => $uid]);
        if($res && $res[0]['a'] > 0){
            return true;
        }else{
            return false;
        }
    }

    public function ss(){
        $sql = "select * from cmf_live where islive = 1 and hide = 0 and is_black = 0";
        return $this->getORM()->queryAll($sql);
    }

    public function nationalDay($uid, $liveid, $total_money){
        $total_money = floor($total_money / self::MULTIPlE);
        $redis       = DI()->redis;
        $dayKey      = 'live:duck:' . date('Y-m-d') . ':' . $liveid;
        $allKey      = 'live:duck:' . 'all:' . $liveid;
        $todayTotal  = $redis->INCRBY($dayKey, $total_money);//单个主播今日流水
//        $redis->expire($dayKey, 86400);
        $allTotal = $redis->INCRBY($allKey, $total_money);//单个主播总流水
//        $redis->expire($allKey, 86400);
        $setDayKey = 'live:duck:' . date('Y-m-d');
        $setAllKey = 'live:duck:all';
        $redis->zIncrBy($setDayKey, $total_money, $liveid);//所有主播日流水集合
        $redis->zIncrBy($setAllKey, $total_money, $liveid);//所有主播总流水集合
        $setDayKey1 = 'live:duck:aud:' . date('Y-m-d');
        $setAllKey1 = 'live:duck:aud:all';
        $redis->zIncrBy($setDayKey1, $total_money, $uid);//所有今日观众送礼集合
        $redis->zIncrBy($setAllKey1, $total_money, $uid);//所有观众累计送礼集合
        $todayDuck       = floor($todayTotal * self::MULTIPlE / self::DUCK_NUM) + 1;
        $remain          = $allTotal * self::MULTIPlE % self::DUCK_NUM;
        $blood           = self::DUCK_NUM - $remain;
        $allDuck         = floor($allTotal * self::MULTIPlE / self::DUCK_NUM) + 1;
        $todayBefore     = ($todayTotal - $total_money) * self::MULTIPlE;
        $todayBeforeDuck = floor($todayBefore / self::DUCK_NUM) + 1;
        $allBefore       = ($allTotal - $total_money) * self::MULTIPlE;
        $allBeforeDuck   = floor($allBefore / self::DUCK_NUM) + 1;
        $todayDucks      = $todayDuck - $todayBeforeDuck;
        if($todayDucks){
            $key = 'live:duck:kill:' . date('Y-m-d');
            $redis->zIncrBy($key, $todayDucks, $uid);//所有用户今日绝杀集合
        }
        $allDucks = $allDuck - $allBeforeDuck;
        if($allDucks){
            $key = 'live:duck:kill:all';
            $redis->zIncrBy($key, $allDucks, $uid);//所有用户累计绝杀集合
        }
        return [
            'duck'        => $todayDuck,
            'blood'       => $blood,
            'total_blood' => self::DUCK_NUM,
        ];
    }

    public function duckInfo($liveid){
//        $liveInfo = getUserInfo($liveid);
//        if (!$liveInfo['verify']) {
        return 1001;
//        }
        $dayKey     = 'live:duck:' . date('Y-m-d') . ':' . $liveid;
        $allKey     = 'live:duck:' . 'all:' . $liveid;
        $todayTotal = DI()->redis->get($dayKey);
        $allTotal   = DI()->redis->get($allKey);
        $todayDuck  = floor($todayTotal * self::MULTIPlE / self::DUCK_NUM) + 1;
        $remain     = $allTotal * self::MULTIPlE % self::DUCK_NUM;
        $blood      = self::DUCK_NUM - $remain;
        $now        = time();
        $startTime  = strtotime('2020-10-02 00:00:00');
        $endTime    = strtotime('2020-10-08 24:00:00');
        if($now >= $startTime && $now < $endTime){
            return [
                'duck'        => (string)$todayDuck,
                'blood'       => (string)$blood,
                'total_blood' => (string)self::DUCK_NUM,
            ];
        }
        return [
            'duck'        => '1',
            'blood'       => '20000000',
            'total_blood' => '20000000',
        ];
    }

    public function duckDetail($liveid){
        $result    = [
            'duck'        => '0',
            'blood'       => '20000000',
            'total_blood' => '20000000',
        ];
        $startTime = strtotime('2020-10-02 00:00:00');
        $endTime   = strtotime('2020-10-08 24:00:00');
        $now       = time();
        if($now >= $startTime && $now < $endTime){
            $allKey   = 'live:duck:' . 'all:' . $liveid;
            $allTotal = DI()->redis->get($allKey);
            $allDuck  = floor($allTotal * self::MULTIPlE / self::DUCK_NUM);
            $remain   = $allTotal * self::MULTIPlE % self::DUCK_NUM;
            $blood    = self::DUCK_NUM - $remain;
            $result   = [
                'duck'        => (string)$allDuck,
                'blood'       => (string)$blood,
                'total_blood' => (string)self::DUCK_NUM,
            ];
        }
        return $result;
    }

    public function random($uid){
        $sql = "select uid,pkuid,stream from cmf_live where uid <> :uid and islive = 1 and is_black = 0 and pkuid = 0 order by rand() limit 1";
        return $this->getORM()->queryAll($sql, [':uid' => $uid]);
    }

    public function hotPkList($page, $uid){
        $total = 10;
        $page = $this->paging($page,$total);
        return $this->getORM()
            ->where('islive = 1 and is_black = 0 and uid <> ?', $uid)
            ->select('uid,pkuid,stream')
            ->limit($page, $total)
            ->fetchAll();
    }

    /**
     * 根据流名获取直播信息
     * @param $stream
     * @param $columns
     * @return mixed
     */
    public function getStreamLiveInfo($stream, $columns = '*'){
        return $this->getORM()
            ->where('stream = ? and islive = 1 and is_black = 0', $stream)
            ->select($columns)
            ->fetchOne();
    }

    public function searchPk($page, $key, $uid){
        $total = 10;
        $page  = $this->paging($page, $total);
        $sql   = "select a.uid,a.stream,a.pkuid from cmf_live a left join cmf_user b on a.uid = b.id where a.islive = 1 and is_black = 0 and (a.uid = :keys or b.user_nicename like :keys) and a.uid <> :uid limit :page,:total";
        return $this->getORM()->queryAll($sql, [
            ':uid'   => $uid,
            ':keys'  => $key . '%',
            ':page'  => $page,
            ':total' => $total,
        ]);
    }

    public function clearPkData($uid){
        return $this->getORM()
            ->where(['uid' => $uid])
            ->update([
                'pkuid'    => 0,
                'pkstream' => 0,
            ]);
    }
}
