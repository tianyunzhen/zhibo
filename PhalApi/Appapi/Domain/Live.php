<?php

class Domain_Live{

    public function checkBan($uid){
        $model = new Model_Live();
        $rs    = $model->checkBan($uid);
        return $rs;
    }

    public function createRoom($uid, $data){
        $model = new Model_Live();
        $rs    = $model->createRoom($uid, $data);
        return $rs;
    }

    public function getFansIds($touid){
        $model = new Model_Live();
        $rs    = $model->getFansIds($touid);
        return $rs;
    }

    public function changeLive($uid, $stream, $status){
        $model = new Model_Live();
        $rs    = $model->changeLive($uid, $stream, $status);
        return $rs;
    }

    public function changeLiveType($uid, $stream, $data){
        $model = new Model_Live();
        $rs    = $model->changeLiveType($uid, $stream, $data);
        return $rs;
    }

    public function stopRoom($uid, $stream, $type = 0){
        $model = new Model_Live();
        $rs    = $model->stopRoom($uid, $stream, $type);
        return $rs;
    }

    public function stopInfo($stream){
        $model = new Model_Live();
        $rs    = $model->stopInfo($stream);
        return $rs;
    }

    public function checkLive($uid, $liveuid, $stream){
        $model = new Model_Live();
        $rs    = $model->checkLive($uid, $liveuid, $stream);
        return $rs;
    }

    public function roomCharge($uid, $liveuid, $stream){
        $model = new Model_Live();
        $rs    = $model->roomCharge($uid, $liveuid, $stream);
        return $rs;
    }

    public function getUserCoin($uid){
        $model = new Model_Live();
        $rs    = $model->getUserCoin($uid);
        return $rs;
    }

    public function isZombie($uid){
        $model = new Model_Live();
        $rs    = $model->isZombie($uid);
        return $rs;
    }

//    public function getZombie($stream, $where){
//        $rs = [];
//
//        $model = new Model_Live();
//        $rs    = $model->getZombie($stream, $where);
//
//        return $rs;
//    }

//    public function getPop($touid){
//        $model = new Model_Live();
//        $rs    = $model->getPop($touid);
//        return $rs;
//    }

    public function getGiftList(){

        $key = 'getGiftList';
        delcache($key);
        $list = getcaches($key);

        if(!$list){
            $model = new Model_Live();
            $list  = $model->getGiftList();
            if($list){
                setcaches($key, $list);
            }
        }

        foreach($list as $k => $v){
            $list[$k]['gifticon'] = get_upload_path($v['gifticon']);
        }

        return $list;
    }

    public function getGiftLists($type){

        $key  = 'gift:getGiftList_' . $type;
        $list = getcaches($key);

        if(!$list){
            $model = new Model_Live();
            $list  = $model->getGiftLists($type);
            if($list){
                setcaches($key, $list, 300);
            }
        }

        foreach($list as $k => $v){
            $list[$k]['gifticon']  = get_upload_path($v['gifticon']);
            $list[$k]['max_money'] = 1000;
        }

        return $list;
    }

    public function getPropgiftList(){

        $key  = 'getPropgiftList';
        $list = getcaches($key);

        if(!$list){
            $model = new Model_Live();
            $list  = $model->getPropgiftList();
            if($list){
                setcaches($key, $list);
            }
        }

        foreach($list as $k => $v){
            $list[$k]['gifticon'] = get_upload_path($v['gifticon']);
        }

        return $list;
    }

    public function sendGift(
        $uid,
        $liveuid,
        $stream,
        $giftid,
        $giftcount,
        $ispack
    ){
        $model = new Model_Live();
        $rs    = $model->sendGifts($uid, $liveuid, $stream, $giftid, $giftcount, $ispack);
        return $rs;
    }

    public function sendBarrage(
        $uid,
        $liveuid,
        $stream,
        $giftid,
        $giftcount,
        $content
    ){
        $model = new Model_Live();
        $rs    = $model->sendBarrage($uid, $liveuid, $stream, $giftid,
            $giftcount, $content);
        return $rs;
    }

    public function setAdmin($liveuid, $touid){
        $model = new Model_Live();
        $rs    = $model->setAdmin($liveuid, $touid);
        return $rs;
    }

    public function getAdminList($liveuid){
        $model = new Model_Live();
        $rs    = $model->getAdminList($liveuid);
        return $rs;
    }

    public function getUserHome($uid, $touid){
        $model = new Model_Live();
        $rs    = $model->getUserHome($uid, $touid);
        return $rs;
    }

    public function getReportClass(){
        $model = new Model_Live();
        $rs    = $model->getReportClass();
        return $rs;
    }

    public function setReport($uid, $touid, $content, $type, $image){
        $model = new Model_Live();
        $rs    = $model->setReport($uid, $touid, $content, $type, $image);
        return $rs;
    }

    public function getVotes($liveuid){
        $model = new Model_Live();
        $rs    = $model->getVotes($liveuid);
        return $rs;
    }

    public function checkShut($uid, $liveuid){
        $model = new Model_Live();
        $rs    = $model->checkShut($uid, $liveuid);
        return $rs;
    }

    public function setShutUp($uid, $liveuid, $touid, $showid){
        $model = new Model_Live();
        $rs    = $model->setShutUp($uid, $liveuid, $touid, $showid);
        return $rs;
    }

    public function kicking($uid, $liveuid, $touid, $stream){
        $model = new Model_Live();
        $rs    = $model->kicking($uid, $liveuid, $touid, $stream);
        return $rs;
    }

    public function superStopRoom($uid, $liveuid, $type){
        $model = new Model_Live();
        $rs    = $model->superStopRoom($uid, $liveuid, $type);
        return $rs;
    }

    public function getContribut($uid, $liveuid, $showid){
        $model = new Model_Live();
        $rs    = $model->getContribut($uid, $liveuid, $showid);
        return $rs;
    }

    public function checkLiveing($uid, $stream){
        $model = new Model_Live();
        $rs    = $model->checkLiveing($uid, $stream);
        return $rs;
    }

    public function getLiveInfo($liveuid){
        $model = new Model_Live();
        $rs    = $model->getLiveInfo($liveuid);
        return $rs;
    }

    public function getFamilyLiveList($liveuid){
        $model = new Model_Live();
        $rs    = $model->getFamilyLiveList($liveuid);
        return $rs;
    }

    public function sendGiftV2(
        $uid,
        $liveuid,
        $stream,
        $giftid,
        $giftcount,
        $ispack
    ){
        $model = new Model_Live();
        $rs    = $model->sendGiftV2($uid, $liveuid, $stream, $giftid,
            $giftcount, $ispack);
        return $rs;
    }

    public function incomeNo($uid, $type, $liveuid){
        switch($type){
        case '2': //周
            $start_time = mktime(0, 0, 0, date('m'),
                date('d') - (date('w') ?: 7) + 1, date('y'));;
            break;
        case '3': //总
            $start_time = mktime(0, 0, 0, date('m'), 1, date('y'));
            break;
        case '1': //日
        default:
            $start_time = mktime(0, 0, 0, date('m'), date('d'), date('y'));
            break;
        }
        $where = '';
        if($type != 3){
            $end_time = time();
            $where    = " and addtime between " . $start_time . " and "
                . $end_time;
        }
//        $key = 'commom:wealthList:' . $type;
//        $glamourListData = getcaches($key);
        if(!isset($glamourListData['list'])){
            $glamourListData['list'] = [];
            //打赏榜
            $record_model = new Model_GiftRecord();
            $res          = $record_model->reward($liveuid, $where);
            foreach($res as $k => $v){
                $num                       = $k + 1;
                $cf_level['level']         = getLevelV2($v['consumption']);
                $ml_level['level_anchor']  = getLevelAnchorV2($v['votestotal']);
                $thumb                     = getLevelThumb($cf_level['level']);
                $cf_level_thumb            = get_upload_path($thumb['thumb']);
                $anchor_thumb
                                           = getLevelThumb($ml_level['level_anchor'],
                    'level_anchor');
                $ml_level_thumb
                                           = get_upload_path($anchor_thumb['thumb']);
                $d                         = [
                    'totalcoin_sum' => $v['totalcoin_sum'],
                    'user_nicename' => $v['user_nicename'],
                    'signature'     => $v['signature'],
                    'cf_level'      => $cf_level_thumb,
                    'ml_level'      => $ml_level_thumb,
                    'head_pic'      => get_upload_path($v['avatar_thumb']),
                    'is_vip'        => getUserVip($v['id'])['type'] ?: '0',
                    'no'            => $num ?? 0,
                    'uid'           => $v['id'],
                ];
                $glamourListData['list'][] = $d;
                if($v['id'] == $uid){
                    $glamourListData['my_info'] = $d;
                }
            }
            if(!isset($glamourListData['my_info'])){
                $user_info                  = $record_model->myReward($liveuid, $uid, $where);
                $cf_level['level']
                                            = getLevelV2($user_info[0]['consumption']);
                $ml_level['level_anchor']
                                            = getLevelAnchorV2($user_info[0]['votestotal']);
                $thumb                      = getLevelThumb($cf_level['level']);
                $cf_level_thumb             = get_upload_path($thumb['thumb']);
                $anchor_thumb
                                            = getLevelThumb($ml_level['level_anchor'],
                    'level_anchor');
                $ml_level_thumb
                                            = get_upload_path($anchor_thumb['thumb']);
                $glamourListData['my_info'] = [
                    'totalcoin_sum' => $user_info[0]['totals'] ?: 0,
                    'user_nicename' => $user_info[0]['user_nicename'],
                    'signature'     => $user_info[0]['signature'],
                    'cf_level'      => $cf_level_thumb,
                    'ml_level'      => $ml_level_thumb,
                    'head_pic'      => get_upload_path($user_info[0]['avatar_thumb']),
                    'is_vip'        => getUserVip($uid)['type'] ?: '0',
                    'no'            => 0,
                    'uid'           => $uid,
                ];
            }
//            if($glamourListData['list']){
//                setcaches($key, $glamourListData, 300);
//            }
        }
        return $glamourListData;
    }

    public function updateLiveTitle($uid, $title){
        $model         = new Model_Live();
        $data['title'] = $title;
        if(!$model->update($uid, $data)){
            return false;
        }else{
            return true;
        }
    }

    public function getZombie($stream){
        $live_audience    = Common_Cache::LIVE_NOW_NUMS . $stream;
        $corpse_insert    = Common_Cache::CORPSE_INSET . $stream;
        $corpse_over_flow = Common_Cache::CORPSE_OVER_FLOW . $stream;
        $redis            = DI()->redis;
        //获取僵尸剩余数量
        $fansNum = $redis->get($corpse_insert) ?: 0;
        if($fansNum > 0){
            //需增加僵尸数量
            $corpse_man_nums = ceil((rand(1, 5) * $fansNum) / 100);
            $userModel       = new Model_User();
            //获取已插入僵尸粉数量
            $corpse_insert_num = $redis->ZCOUNT($live_audience, 1, 1) ?: 0;
            if($corpse_insert_num < 100){
                $total_fans = $corpse_insert_num + $corpse_man_nums;
                if($total_fans > 100){
                    $fansNms = $corpse_man_nums - ($total_fans - 100);
                    //插入僵尸数量
                    $redis->INCRBY($corpse_over_flow, $total_fans - 100);
                }else{
                    $fansNms = $corpse_man_nums;
                }
                //获取需增加的僵尸列表
                $fansNms > 0 && $fansList = $userModel->fans($fansNms);
                if(isset($fansList) && $fansList){
                    $data[] = $live_audience;
                    foreach($fansList as $k => $v){
                        $data[] = 1;
                        $data[] = $v['id'];
                    }
                    if($data[1]){
                        call_user_func_array([DI()->redis, 'zadd'], $data);
                    }
                }
            }else{
                $redis->INCRBY($corpse_over_flow, $corpse_man_nums);
            }
            $redis->DECRBY($corpse_insert, $corpse_man_nums);
        }


        $list      = $this->getLiveUserList($stream);
        $list['s'] = rand(10, 60);
        return [0, $list];
    }

    public function getLiveUserList($stream){
        $data             = [];
        $key              = Common_Cache::LIVE_NOW_NUMS . $stream;
        $corpse_over_flow = Common_Cache::CORPSE_OVER_FLOW . $stream;
        $noMan            = DI()->redis->ZRANGE($key, 0, 100);
        $userModel        = new Model_User();
        $list             = [];
        foreach($noMan as $k => $v){
            $info           = getcaches(Common_Cache::USERINFO . $v);
            $headBorderData = getHeadBorder($v);
            if(!$info){
                $avatar       = $userModel->get($v, 'avatar_thumb');
                $avatar_thumb = get_upload_path($avatar['avatar_thumb']);
            }else{
                $avatar_thumb = get_upload_path($info['avatar_thumb']);
            }
            if($headBorderData){
                $headBorderInfo['pic'] = $headBorderData['pic'];
            }else{
                $headBorderInfo = null;
            }
            $list[] = [
                'id'          => $v,
                'avatar'      => $avatar_thumb,
                'head_border' => $headBorderInfo,
            ];
        }
        $nums          = DI()->redis->ZCARD($key) ?: 0;
        $over_flow_num = DI()->redis->get($corpse_over_flow) ?: 0;
        $data['nums']  = $nums + intval($over_flow_num);
        $data['list']  = $list ?: [];
        return $data;
    }


    public function attackList($type){
        if(1 == $type){
            $setDayKey = 'live:duck:aud:' . date('Y-m-d');
        }else{
            $setDayKey = 'live:duck:aud:all';
        }
        $list = DI()->redis->zReverseRange($setDayKey, 0, 49, true);
        $res  = [];
        foreach($list as $k => $v){
            $user_info = getUserInfoDuck($k);
            $tem       = [
                'uid'           => $k,
                'user_nicename' => $user_info['user_nicename'],
                'avatar'        => get_upload_path($user_info['avatar']),
                'coin'          => $v * 50,
            ];
            $res[]     = $tem;
        }
        return $res;
    }

    public function deadList($type){
        if(1 == $type){
            $setDayKey = 'live:duck:' . date('Y-m-d');
        }else{
            $setDayKey = 'live:duck:all';
        }
        $list = DI()->redis->zReverseRange($setDayKey, 0, 49, true);
        $res  = [];
        foreach($list as $k => $v){
            $user_info = getUserInfoDuck($k);
            $tem       = [
                'uid'           => $k,
                'user_nicename' => $user_info['user_nicename'],
                'avatar'        => get_upload_path($user_info['avatar']),
                'duck'          => floor($v * 50 / 20000000),
            ];
            $res[]     = $tem;
        }
        return $res;
    }

    public function goalList($type){
        if(1 == $type){
            $setDayKey = 'live:duck:kill:' . date('Y-m-d');
        }else{
            $setDayKey = 'live:duck:kill:all';
        }
        $list = DI()->redis->zReverseRange($setDayKey, 0, 49, true);
        $res  = [];
        foreach($list as $k => $v){
            $user_info = getUserInfoDuck($k);
            $tem       = [
                'uid'           => $k,
                'user_nicename' => $user_info['user_nicename'],
                'avatar'        => get_upload_path($user_info['avatar']),
                'duck'          => $v,
            ];
            $res[]     = $tem;
        }
        return $res;
    }

    public function attackListV2($type){
        $model = new Model_GiftRecord();
        $list  = $model->attackListV2($type);
        foreach($list as $v){
            $user_info = getUserInfoDuck($v['uid']);
            $tem       = [
                'uid'           => $v['uid'],
                'user_nicename' => $user_info['user_nicename'],
                'avatar'        => get_upload_path($user_info['avatar']),
                'coin'          => $v['coin'],
            ];
            $res[]     = $tem;
        }
//        setcaches("live:duck:attackList", $res);
        return $res;
    }

    public function deadListV2($type){
        $model = new Model_GiftRecord();
        $list  = $model->deadListV2($type);
        foreach($list as $v){
            $tem   = [
                'uid'           => $v['touid'],
                'user_nicename' => $v['user_nicename'],
                'avatar'        => get_upload_path($v['avatar']),
                'duck'          => floor($v['coin'] / 20000000),
            ];
            $res[] = $tem;
        }
//        setcaches("live:duck:deadList", $res);
        return $res;
    }

    public function goalListV2($type){
        $res = [];
        if(2 == $type){
            $key = 'live:duck:goalList';
            $res = getcaches($key);
        }
        return $res;
    }
}
