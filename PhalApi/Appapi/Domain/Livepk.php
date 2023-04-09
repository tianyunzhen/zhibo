<?php

class Domain_Livepk{
    protected $pkGiftCache     = Common_Cache::PK_GIFT;
    protected $pkTimeCache     = Common_Cache::PK_TIMES;
    protected $pkUserCache     = Common_Cache::PK_USER;
    protected $pkGiftUserCache = Common_Cache::PK_GIFT_USER;

    public function getLiveList($uid, $where, $p){
        $model = new Model_Livepk();
        return $model->getLiveList($uid, $where, $p);
    }

    public function checkLive($stream){
        $model = new Model_Live();
        return $model->getStreamLiveInfo($stream, 'pull,pkuid');
    }

    public function changeLive($uid, $pkuid, $type){
        $model = new Model_Livepk();
        return $model->changeLive($uid, $pkuid, $type);
    }

    /**
     * 开始pk
     * @param $uid
     * @param $pkUid
     * @return float|int
     */
    public function setPk($uid, $pkUid){
        //增加pk数据
        $pkTime         = time();
        $punishmentTime = $pkTime + 60 * 5;
        $endTime        = $punishmentTime + 60 * 2;
        $pkData         = [
            'pk_time'         => $pkTime,
            'punishment_time' => $punishmentTime,
            'create_time'     => $pkTime,
            'end_time'        => $endTime,
            'update_time'     => $pkTime,
        ];
        $pkModel        = new Model_Livepk();
        $pkUserModel    = new Model_LivePkUser();

        $uidPkInfo = $pkUserModel->getNowUidInfo($uid);
        if($uidPkInfo){
            $pkModel->delete($uidPkInfo['pk_id']);
            $pkUserModel->delete($uidPkInfo['id']);
        }
        $pkUidInfo = $pkUserModel->getNowUidInfo($pkUid);
        if($pkUidInfo){
            $pkModel->delete($pkUidInfo['pk_id']);
            $pkUserModel->delete($pkUidInfo['id']);
        }
        try{
            $pkModel->startBegin();
            $pkId = $pkModel->insert($pkData);
            if(!$pkId){
                $pkModel->rollbacks();
            }
            $pkUserData[] = [
                'uid'         => $uid,
                'pk_id'       => $pkId,
                'create_time' => $pkTime,
                'update_time' => $pkTime,
            ];

            $pkUserData[] = [
                'uid'         => $pkUid,
                'pk_id'       => $pkId,
                'create_time' => $pkTime,
                'update_time' => $pkTime,
            ];
            if(!$pkUserModel->moreInsert($pkUserData)){
                $pkModel->rollbacks();
            }
        }catch(\Exception $e){
            $pkModel->rollbacks();
            return;
        }
        $pkModel->commits();

        //增加缓存
        $redis = DI()->redis;
        $redis->hSet($this->pkUserCache, $uid, $pkUid);
        $redis->hSet($this->pkUserCache, $pkUid, $uid);

        $redis->hSet($this->pkGiftCache, $uid, 0);
        $redis->hSet($this->pkGiftCache, $pkUid, 0);

        $pkData['pkId'] = $pkId;
        $timesData      = json_encode($pkData);
        $redis->hSet($this->pkTimeCache, $uid, $timesData);
        $redis->hSet($this->pkTimeCache, $pkUid, $timesData);
        return $punishmentTime - $pkTime + 2;
//        $key1 = 'LivePK';
//        $key2 = 'LivePK_gift';
//
//        DI()->redis->hSet($key1, $uid, $pkUid);
//        DI()->redis->hSet($key1, $pkUid, $uid);
//
//        DI()->redis->hSet($key2, $uid, 0);
//        DI()->redis->hSet($key2, $pkUid, 0);
//
//
//        $nowtime = time();
//        $key3    = 'LivePK_timer';
//
//        DI()->redis->hSet($key3, $uid, $nowtime);
    }

    /**
     * pk关注列表
     * @param $uid
     * @param $page
     * @return array
     */
    public function followPkList($uid, $page){
        $liveModel = new Model_UserAttention();
        $res       = $liveModel->getFollowList($uid, $page);
        return $this->pkDesc($res);
    }

    /**
     * pk用户列表详情
     * @param $res
     * @return array
     */
    protected function pkDesc($res){
        $livePkUserDomain = new Domain_LivePkUser();
        $list             = [];
        foreach($res as $l => $v){
            $userInfo = getUserInfo($v['uid']);
            //获取pk记录
            list($pkCountsNums, $winning) = $livePkUserDomain->getUserPkDesc($v['uid']);
            //获取直播信息
            $list[] = [
                'userName'     => $userInfo['user_nicename'],
                'sex'          => $userInfo['sex'],
                'headPic'      => $userInfo['avatar_thumb'],
                'remark_info'  => $userInfo['remark_info'],
                'pkCountsNums' => $pkCountsNums,
                'winning'      => $winning,
                'charmLevel'   => $userInfo['level_anchor_thumb'],
                'isPk'         => $v['pkuid'],
                'stream'       => $v['stream'],
                'id'           => $v['uid'],
            ];
        }
        return $list;
    }

    /**
     * 随机pk
     * @param $uid
     * @return array
     */
    public function randomPkList($uid){
        $liveModel        = new Model_Live();
        $livePkUserDomain = new Domain_LivePkUser();
        $res              = $liveModel->random($uid);
        if(!$res){
            return (object)[];
        }
        $userInfo = getUserInfo($res[0]['uid']);
        //获取pk记录
        list($pkCountsNums, $winning) = $livePkUserDomain->getUserPkDesc($res[0]['uid']);
        return [
            'userName'     => $userInfo['user_nicename'],
            'sex'          => $userInfo['sex'],
            'headPic'      => $userInfo['avatar_thumb'],
            'remark_info'  => $userInfo['remark_info'],
            'pkCountsNums' => $pkCountsNums,
            'winning'      => $winning,
            'charmLevel'   => $userInfo['level_anchor_thumb'],
            'isPk'         => $res[0]['pkuid'],
            'stream'       => $res[0]['stream'],
            'id'           => $res[0]['uid'],
        ];
    }

    /**
     * 热门pk列表
     * @param $page
     * @param $uid
     * @return array
     */
    public function hotPkList($page, $uid){
        $liveModel = new Model_Live();
        $res       = $liveModel->hotPkList($page, $uid);
        return $this->pkDesc($res);
    }

    /**
     * PK中的主播列表
     * @param $page
     * @return array
     */
    public function pkConductList($page){
        $livePkModel     = new Model_Livepk();
        $pkListData      = $livePkModel->pkConductList($page);
        $livePkUserModel = new Model_LivePkUser();
        $liveModel       = new Model_Live();
        $pkList          = [];
        foreach($pkListData as $k => $v){
            $liveInfo       = [];
            $pkUserListData = $livePkUserModel->pkUserList($v['id']);
            foreach($pkUserListData as $kk => $vv){
                $liveInfo[] = array_merge($liveModel->getLiveInfo($vv['uid']), $vv);
            }
            $pkList[] = $liveInfo;
        }
        return $pkList;
    }

    /**
     * 获取pk信息
     * @param $uid
     * @return array|bool
     */
    public function getPkPull($uid){
        $redis    = DI()->redis;
        $pkUserId = $redis->hget($this->pkUserCache, $uid);
        if($pkUserId){
            $liveModel      = new Model_Live();
            $pkUserLiveInfo = $liveModel->getLiveInfo($pkUserId);
            return $pkUserLiveInfo['pull'];
        }else{
            return false;
        }
    }

    /**
     * 获取pk倒计时
     * @param $uid
     * @return array
     */
    public function getPkTime($uid){
        $redis      = DI()->redis;
        $pkTimeData = $redis->hget($this->pkTimeCache, $uid);
        if($pkTimeData){
            $pkTimeData = json_decode($pkTimeData, true);
            if($pkTimeData['punishment_time'] - time() > 0){
                //pk中
                return ['1', $pkTimeData['punishment_time'] - time()];
            }elseif($pkTimeData['end_time'] - time() > 0){
                //惩罚中
                return ['2', $pkTimeData['end_time'] - time()];
            }else{
                //结束
                return ['3', 0];
            }
        }else{
            return ['0', ''];
        }
    }

    /**
     * 排行榜用户
     * @param $uid
     * @param $pkUserId
     * @return array
     */
    public function getGiftUserNo($uid, $pkUserId){
        $redis = DI()->redis;
        $list1 = $redis->ZREVRANGE($this->pkGiftUserCache . $uid, 0, 2, true);
        $user1 = $this->userDes($list1);
        $list2 = $redis->ZREVRANGE($this->pkGiftUserCache . $pkUserId, 0, 2, true);
        $user2 = $this->userDes($list2);
        return [$user1, $user2];
    }

    /**
     * 用户详情
     * @param $list
     * @return array
     */
    protected function userDes($list){
        $data = [];
        foreach($list as $k => $v){
            $userInfo = getUserInfo($k);
            $data[]   = [
                'id'           => $k,
                'avatar_thumb' => $userInfo['avatar_thumb'],
            ];
        }
        return $data;
    }

    /**
     * 获取送礼金额
     * @param $uid
     * @param $pkUserId
     * @return array
     */
    public function getTotalMoney($uid, $pkUserId){
        $redis  = DI()->redis;
        $total1 = $redis->hget($this->pkGiftCache, $uid) ?: 0;
        $total2 = $redis->hget($this->pkGiftCache, $pkUserId) ?: 0;
        return [$total1, $total2];
    }

    /**
     * 获取pk对象ID
     * @param $uid
     * @return bool
     */
    public function getPkUserId($uid){
        $redis    = DI()->redis;
        $pkUserId = $redis->hget($this->pkUserCache, $uid);
        if($pkUserId){
            return $pkUserId;
        }else{
            return false;
        }
    }

    /**
     * pk金额维护
     * @param $uid
     * @param $liveUid
     * @param $money
     * @return bool
     */
    public function addGiftNums($uid, $liveUid, $money){
        $redis      = DI()->redis;
        $pkTimeData = $redis->hget($this->pkTimeCache, $liveUid);
        if($pkTimeData){
            $pkTimeData = json_decode($pkTimeData, true);
            if($pkTimeData['punishment_time'] >= time()){
                //增加送礼金额
                $redis->HINCRBYFLOAT($this->pkGiftCache, $liveUid, $money);
                $res = $redis->ZINCRBY($this->pkGiftUserCache . $liveUid, $money, $uid);
                if($res == $money){
                    $redis->Expire($this->pkGiftUserCache . $liveUid, 600);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * 获取pk信息
     * @param $uid
     * @return array
     */
    public function getPkInfo($uid){
        $pkId = $this->getPkUserId($uid);
        if($pkId){
            list($user1, $user2) = $this->getGiftUserNo($uid, $pkId);
            list($total1, $total2) = $this->getTotalMoney($uid, $pkId);
            return [
                "pkuid1"     => $uid,
                "isPk"       => '1',
                "pkuid2"     => $pkId,
                "pktotal1"   => $total1,
                "pktotal2"   => $total2,
                "rank_user1" => $user1,
                "rank_user2" => $user2,
            ];
        }
        return ["isPk" => '0'];
    }

    /**
     * 删除pk缓存
     * @param $uid
     * @param $pkUid
     * @return bool
     */
    public function clearCache($uid, $pkUid){
        $redis = DI()->redis;
        $redis->hdel($this->pkUserCache, $uid);
        $redis->hdel($this->pkUserCache, $pkUid);
        $redis->hdel($this->pkTimeCache, $uid);
        $redis->hdel($this->pkTimeCache, $pkUid);
        $redis->hdel($this->pkGiftCache, $uid);
        $redis->hdel($this->pkGiftCache, $pkUid);
        $redis->del($this->pkGiftUserCache . $uid);
        $redis->del($this->pkGiftUserCache . $pkUid);
        $redis->del(Common_Cache::USER_PK_WINNING . $uid);
        $redis->del(Common_Cache::USER_PK_WINNING . $pkUid);

        return true;
    }

    /**
     * 结束pk
     * @param $uid
     * @return bool
     */
    public function endPK($uid){
        $pkUid = $this->getPkUserId($uid);
        if($pkUid){
            $liveModel   = new Model_Live();
            $livePkModel = new Model_Livepk();
            //清除直播表pk数据
            $liveModel->clearPkData($uid);
            $liveModel->clearPkData($pkUid);
            $pkTimeData = DI()->redis->hget($this->pkTimeCache, $uid);
            $pkId       = json_decode($pkTimeData, true)['pkId'];
            $livePkModel->update($pkId, ['end_time' => time()]);
            //清除缓存
            $this->clearCache($uid, $pkUid);
            return true;
        }
    }

    /**
     * 搜索
     * @param $key
     * @param $page
     * @param $uid
     * @return array
     */
    public function search($key, $page, $uid){
        $model = new Model_Live();
        $res   = $model->searchPk($page, $key, $uid);
        return $this->pkDesc($res);
    }

    /**
     * 获取时间
     * @param $uid
     * @return mixed
     */
    public function pkStatus($uid){
        list($status, $time) = $this->getPkTime($uid);
        $data['status'] = $status;
        $data['time']   = $time;
        $data['pkUid']  = $this->getPkUserId($uid);
        $data['uid']    = $uid;
        $data['win']    = 0;
        if($status != '1'){
            list($a, $b) = $this->getTotalMoney($uid, $data['pkUid']);
            if($a > $b){
                $data['win']       = $uid;
                $dataOne['status'] = 1;
                $dataTwo['status'] = 2;
            }elseif($a < $b){
                $data['win']       = $data['pkUid'];
                $dataOne['status'] = 2;
                $dataTwo['status'] = 1;
            }else{
                $data['win']       = 0;
                $dataOne['status'] = 3;
                $dataTwo['status'] = 3;
            }

            $dataOne['update_time'] = time();
            $dataTwo['update_time'] = time();
            //更新数据
            $dataOne['gift_money'] = $a;
            $dataTwo['gift_money'] = $b;
            $livePkUserModel       = new Model_LivePkUser();
            $pkTimeData            = DI()->redis->hget($this->pkTimeCache, $uid);
            $pkId                  = json_decode($pkTimeData, true)['pkId'];

            $livePkUserModel->startBegin();
            try{
                if(!$livePkUserModel->saveMoney($uid, $pkId, $dataOne)){
                    $livePkUserModel->rollbacks();
                    return false;
                }
                if(!$livePkUserModel->saveMoney($data['pkUid'], $pkId, $dataTwo)){
                    $livePkUserModel->rollbacks();
                    return false;
                }
            }catch(\Exception $e){
                $livePkUserModel->rollbacks();
                return false;
            }
            $livePkUserModel->commits();
        }

        return $data;
    }

    /**
     * 获取胜率
     * @param $uid
     * @return array
     */
    public
    function winningNums($uid){
        $userInfo         = getUserInfo($uid);
        $livePkUserDomain = new Domain_LivePkUser();
        //获取pk记录
        list($pkCountsNums, $winning) = $livePkUserDomain->getUserPkDesc($uid);
        //获取直播信息
        return [
            'userName'     => $userInfo['user_nicename'],
            'sex'          => $userInfo['sex'],
            'headPic'      => $userInfo['avatar_thumb'],
            'remark_info'  => $userInfo['remark_info'],
            'pkCountsNums' => $pkCountsNums,
            'winning'      => $winning,
            'charmLevel'   => $userInfo['level_anchor_thumb'],
            'level_thumb'  => $userInfo['level_thumb'],
            'id'           => $userInfo['id'],
        ];
    }
}
