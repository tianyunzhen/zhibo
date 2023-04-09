<?php

class Domain_Home{

    public function getSlide($id){
        $rs    = [];
        $model = new Model_Home();
        $rs    = $model->getSlide($id);
        return $rs;
    }

    public function getHot($p){
        $rs = [];

        $model = new Model_Home();
        $rs    = $model->getHot($p);

        return $rs;
    }

    public function getFollow($uid, $p){
        $rs = [];

        $model = new Model_Home();
        $rs    = $model->getFollow($uid, $p);

        return $rs;
    }

    public function getNew($lng, $lat, $p){
        $rs = [];

        $model = new Model_Home();
        $rs    = $model->getNew($lng, $lat, $p);

        return $rs;
    }

    public function search($uid, $key, $p){
        $rs = [];

        $model = new Model_Home();
        $rs    = $model->search($uid, $key, $p);

        return $rs;
    }

    public function getNearby($lng, $lat, $p){
        $rs = [];

        $model = new Model_Home();
        $rs    = $model->getNearby($lng, $lat, $p);

        return $rs;
    }

    public function getNearUser($lng, $lat, $p, $uid){
        $rs = [];

        $model = new Model_Home();
        $rs    = $model->getNearUser($lng, $lat, $p, $uid);

        return $rs;
    }

    public function getYouLike(){
        $rs = [];

        $model = new Model_Home();
        $rs    = $model->getYouLike();

        return $rs;
    }

    public function intimateList($uid, $p, $type, $touid){

        $model = new Model_Home();
        list($result, $myResult) = $model->intimateList($uid, $p, $type, $touid);

        return [$result, $myResult];
    }

    /**
     * 魅力榜
     * @param $type
     * @param $uid
     * @param $page
     * @return mixed
     */
    public function glamourList($type, $uid, $page){
        $res['list']    = [];
        $res['my_info'] = [];
        if($page > 15){
            return $res;
        }
        switch($type){
        case '2': //周
            $start_time = mktime(0, 0, 0, date('m'), date('d') - (date('w') ?: 7) + 1, date('y'));
            $tt         = 300;
            break;
        case '3': //总
            $start_time = 0;
            $tt         = 300;
            break;
        case '1': //日
        default:
            $start_time = mktime(0, 0, 0, date('m'), date('d'), date('y'));
            $tt         = 60;
            break;
        }
        $key         = Common_Cache::GLAMOUR_LIST . $page . '_' . $type;
        $res['list'] = getcaches($key);
        if(!$res['list']){
            $end_time            = time();
            $e_gift_record_model = new Elast_GiftRecord();
            $data                = $e_gift_record_model->glamourList($start_time, $end_time, $page);
            $no                  = ($page - 1) * 10;
            $liveModel           = new Model_Live();

            foreach($data as $k => $v){
                ++$no;
                $user_info = getUserInfo($v['key']);
                $three     = $e_gift_record_model->getThree($start_time, $end_time, $v['key']);
                $threeArr  = [];
                $is_live   = $liveModel->isLive($user_info['id']);
                foreach($three as $kk => $vv){
                    $three_info = getUserInfo($vv['key']);
                    $threeArr[] = $three_info['avatar_thumb'];
                }
                $list          = [
                    'votes_sum'     => $v['sum_total']['value'],
                    'user_nicename' => $user_info['user_nicename'],
                    'signature'     => $user_info['signature'],
                    'uid'           => $user_info['id'],
                    'cf_level'      => $user_info['level_thumb'],
                    'ml_level'      => $user_info['level_anchor_thumb'],
                    'head_pic'      => $user_info['avatar_thumb'],
                    'no'            => $no,
                    'is_vip'        => $user_info['remark_info'] ? 1 : 0,
                    'now_three'     => $threeArr,
                    'is_live'       => $is_live ? '1' : '0',
                    'head_border'   => $user_info['head_border'],
                ];
                $res['list'][] = $list;
            }
            if($res['list']){
                setcaches($key, json_encode($res['list']), $tt);
            }
        }else{
            $res['list'] = json_decode($res['list'], true);
        }
        $res['list'] = $res['list'] ?: [];
        return $res;
    }

    /**
     * 财富榜
     * @param $type
     * @param $uid
     * @param $page
     * @return false|string
     */
    public function wealthList($type, $uid, $page){
        $res['list']    = [];
        $res['my_info'] = [];
        if($page > 15){
            return $res;
        }
        switch($type){
        case '2': //周
            $start_time = mktime(0, 0, 0, date('m'), date('d') - (date('w') ?: 7) + 1, date('y'));
            $tt         = 300;
            break;
        case '3': //总
            $start_time = 0;
            $tt         = 300;
            break;
        case '1': //日
        default:
            $start_time = mktime(0, 0, 0, date('m'), date('d'), date('y'));
            $tt         = 300;
            break;
        }
        $key         = Common_Cache::WEALTH_LIST . $page . '_' . $type;
        $res['list'] = getcaches($key);
        if(!$res['list']){
            $end_time            = time();
            $e_gift_record_model = new Elast_GiftRecord();
            $data                = $e_gift_record_model->wealthList($start_time, $end_time, $page);
            $no                  = ($page - 1) * 10;
            $liveModel           = new Model_Live();
            foreach($data as $k => $v){
                ++$no;
                $user_info     = getUserInfo($v['key']);
                $is_live       = $liveModel->isLive($user_info['id']);
                $list          = [
                    'totalcoin_sum' => $v['sum_total']['value'],
                    'user_nicename' => $user_info['user_nicename'],
                    'uid'           => $user_info['id'],
                    'cf_level'      => $user_info['level_thumb'],
                    'ml_level'      => $user_info['level_anchor_thumb'],
                    'head_pic'      => $user_info['avatar_thumb'],
                    'is_vip'        => $user_info['remark_info'] ? 1 : 0,
                    'no'            => $no,
                    'is_live'       => $is_live ? '1' : '0',
                    'head_border'   => $user_info['head_border'],
                ];
                $res['list'][] = $list;
            }
            if($res['list']){
                setcaches($key, json_encode($res['list']), $tt);
            }
        }else{
            $res['list'] = json_decode($res['list'], true);
        }
        $res['list'] = $res['list'] ?: [];
        return $res;
    }

    public function exchange($uid, $votes){
        $handle = DI()->notorm->user;
        try{
            $newVotes = $votes * 100;
            //更新数据
            $data = [
                'votes' => new NotORM_Literal("votes - {$newVotes}"),
                'coin'  => new NotORM_Literal("coin + {$votes}"),
            ];
            $handle->queryAll('begin');
            if(
            !DI()->notorm->user
                ->where('id = ? and votes >= ?', $uid, $newVotes)
                ->update($data)
            ){
                $handle->queryAll('rollback');
                return [1, '兑换失败'];
            }

            $data  = [
                'action'    => Model_UserCoinRecord::DH,
                'type'      => Model_UserCoinRecord::INCOME,
                'uid'       => $uid,
                'totalcoin' => $votes,
                'addtime'   => time(),
            ];
            $model = new Model_UserCoinRecord;
            if(!$model->insert($data)){
                $handle->queryAll('rollback');
                return [2, '兑换失败'];
            }
            $model = new Model_UserVoteRecord;
            $data  = [
                'action'  => Model_UserVoteRecord::DUIHUAN,
                'type'    => Model_UserVoteRecord::OUT,
                'uid'     => $uid,
                'total'   => $votes,
                'votes'   => $newVotes,
                'addtime' => time(),
            ];
            if(!$model->insert($data)){
                $handle->queryAll('rollback');
                return [3, '兑换失败'];
            }
        }catch(\Exception $e){
            $handle->queryAll('rollback');
            return [4, '兑换失败'];
        }
        $handle->queryAll('commit');
        $push_model = new Common_JPush($uid);
        $push_model->sendAlias('兑换成功', sprintf(Common_JPush::DHJB, intval($votes)));
        Domain_Msg::addMsg('兑换成功', sprintf(Common_JPush::DHJB, intval($votes)), $uid);
        return [0, '兑换成功'];
    }

    public function exchangeRecord($uid, $page){
        $page_total = 20;
        $page       = ($page - 1) * $page_total;
        $type       = Model_UserVoteRecord::OUT;
        $action     = Model_UserVoteRecord::DUIHUAN;
        return DI()->notorm->user_voterecord
            ->select('addtime,total,nums')
            ->where("uid = ? and type = {$type} and action = {$action}", $uid)
            ->limit($page, $page_total)
            ->order('id desc')
            ->fetchAll();
    }

    public function controlPush($uid){
        $is_off = DI()->notorm->user_pushid
            ->where('uid = ?', $uid)
            ->fetchOne();
        //获取关注列表
        $list = DI()->notorm->user_attention
            ->select('touid')
            ->where('uid = ?', $uid)
            ->fetchAll();
        $ids  = array_column($list, 'touid');
        foreach($ids as &$v){
            $v = Common_JPush::FOLLOW_PUSH . $v;
        }
        if($ids){
            $jpush_model = new Common_JPush($uid);
        }

        if($is_off['follow_push'] == 1)//取消
        {
            $data['follow_push'] = 2;
            //更新数据
            if(
            !DI()->notorm->user_pushid->where('uid = ?', $uid)->update($data)
            ){
                return [1, '操作失败'];
            }
            $ids && $jpush_model->removeLabel($ids);
        }else{
            $data['follow_push'] = 1;
            //更新数据
            if(
            !DI()->notorm->user_pushid->where('uid = ?', $uid)->update($data)
            ){
                return [1, '操作失败'];
            }
            $ids && $jpush_model->addLabel($ids);
        }
        return [0, '操作成功'];
    }

    public function controlPushStatic($uid){
        $is_off = DI()->notorm->user_pushid
            ->where('uid = ?', $uid)
            ->select('follow_push')
            ->fetchOne();
        return $is_off['follow_push'] ?? 0;
    }

    public function setDevice($data){
        $model      = new Model_AppDevice();
        $deviceInfo = $model->selectDeviceId($data['device_id'], $data['user_id']);
        if($deviceInfo){
            if($deviceInfo['user_id'] == '0' && $data['user_id'] != '0'){
                $data['register_time'] = time();
            }
            $model->update($deviceInfo['id'], $data);
        }else{
            if($data['user_id'] != '0')
            {
                $data['register_time'] = time();
            }
            $data['activate_time'] = time();
            $model->insert($data);
        }
        return true;
    }

    public function setNear($uid){
        $model   = new Model_User();
        $is_near = $model->get($uid, 'is_near');
        if($is_near['is_near'] == 1){
            $is_near = 2;//关
        }else{
            $is_near = 1;//开
        }
        $update['is_near'] = $is_near;
        if(!$model->update($uid, $update)){
            return false;
        }
        return $is_near;
    }

    public function getNearOff($uid){
        $model = new Model_User();
        return $model->get($uid, 'is_near');
    }
}
