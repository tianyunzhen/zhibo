<?php

class Domain_Shops{

    public function getLiang($page = 0, $type_id){
        $display_num = 30;
        $model       = new Model_Shops();
        $res         = $model->getLiang($page, $display_num, $type_id);
        return $res;
    }

    public function getCard($page = 0, $type_id, $uid){
        $display_num    = 10;
        $model          = new Model_Shops();
        $res            = $model->getCar($page, $display_num, $type_id);
        $car_user_model = new Model_CarUser();
        $byCarList      = $car_user_model->getCarUserList($uid,
            'carid,addtime');
        $now_time       = mktime(00, 00, 00, date('m'), 1, date('Y'));
        foreach($res as $k => &$v){
            $v['is_by'] = 2;
            $v['thumb'] = get_upload_path($v['thumb']);
            foreach($byCarList as $kk => $vv){
                if($vv['carid'] == $v['id']){
                    if($vv['addtime'] > $now_time){
                        $v['is_by'] = 1;
                    }
                }
            }
        }
        return $res;
    }

    public function byCar($car_id, $uid){
        $car_model = new Model_Car();
        //获取座驾信息
        $car_info = $car_model->get($car_id, '*');
        if(!$car_info){
            return [1, '座驾不存在', ''];
        }
        //获取充值金额
        $charge_user_model = new Model_ChargeUser();
        $charge_row        = $charge_user_model->nowMonthMoney($uid);
        $count_money       = $charge_row[0]['counts'] ?? 0;
        if($count_money < $car_info['needcoin']){
            return [1, '本月充值未满' . $car_info['needcoin']];
        }
        //判断本月是否领取
        $car_user_model = new Model_CarUser();
        $car_user_info  = $car_user_model->getCarUserInfo($uid, $car_id,
            'id,updtime,endtime');
        if(
            isset($car_user_info['updtime'])
            && date('Ymd', $car_user_info['updtime']) == date('Ymd')
        ){
            return [1, '本月已领取', ''];
        }
        //领取
        $datas            = [];
        $datas['updtime'] = time();
        if($car_user_info){
            $datas['endtime'] = ($car_user_info['endtime'] > time())
                ? $car_user_info['endtime'] + $car_info['expire'] * 86400
                : time() + $car_info['expire'] * 86400;
            $where['uid']     = $uid;
            $where['carid']   = $car_id;
            if(
            !$car_user_model->update($car_user_info['id'], $datas)
            ){
                return [1, '领取失败', ''];
            }
        }else{
            $datas['endtime'] = time() + $car_info['expire'] * 86400;
            $datas['uid']     = $uid;
            $datas['carid']   = $car_id;
            $datas['status']  = 0;
            $datas['updtime'] = time();
            $datas['addtime'] = time();

            if(!$car_user_model->insertData($datas)){
                return [1, '领取失败', ''];
            }
        }
//        Domain_Msg::addMsg('购买座驾',printf(Common_JPush::GMZJ,$car_info['name']),$uid);
        return [0, '领取成功'];
    }

    public function byLiang($uid, $liangid){
        //获取靓号信息
        $liang_model = new Model_Liang();
        $liang_info  = $liang_model->getInfo($liangid);
        if(!$liang_info){
            return [1, '靓号不存在'];
        }
        if($liang_info['end_time'] > time()
            || ($liang_info['uid']
                && $liang_info['end_time'] < 1)
        ){
            return [1, '该靓号已被购买'];
        }
        //获取个人账户
        $user_model = new Model_User();
        $user_money = $user_model->getUserInfo($uid, 'coin');
        if(!$user_money){
            return [1, '账号不存在'];
        }
        //判断余额
        if($user_money['coin'] < $liang_info['coin']){
            return [1, '余额不足'];
        }
        //更新数据
        try{
            $user_coinrecord_data['type']      = Model_UserCoinRecord::OUT;
            $user_coinrecord_data['action']    = Model_UserCoinRecord::LH;
            $user_coinrecord_data['uid']       = $uid;
            $user_coinrecord_data['totalcoin'] = intval($liang_info['coin']);
            $user_coinrecord_data['addtime']   = time();

            $liang_data['end_time'] = ($liang_info['expire'] > 0) ? (time()
                + $liang_info['expire'] * 86400) : 0;
            $liang_data['uid']      = $uid;
            $liang_data['buytime']  = time();

            DI()->notorm->user->queryAll('begin');
            if(
            !$user_model->decCoin($uid, intval($liang_info['coin']))
            ){
                DI()->notorm->user->queryAll('rollback');
                return [1, '更新余额失败'];
            }
            $user_coinrecord_model = new Model_UserCoinRecord();
            if(
            !$user_coinrecord_model->insert($user_coinrecord_data)
            ){
                DI()->notorm->user->queryAll('rollback');
                return [1, '添加流水失败'];
            }
            if(
            !$liang_model->update($liangid, $liang_data)
            ){
                DI()->notorm->user->queryAll('rollback');
                return [1, '购买失败'];
            }
        }catch(\Exception $e){
            DI()->notorm->user->queryAll('rollback');
        }
        DI()->notorm->user->queryAll('commit');
//        Domain_Msg::addMsg('购买靓号',printf(Common_JPush::GMLH,$liang_info['name']),$uid);
        return [0, '购买成功'];
    }

    public function nowMonthMoney($uid){
        $charge_user_model = new Model_ChargeUser();
        $charge_row        = $charge_user_model->nowMonthMoney($uid);
        return $charge_row[0]['counts'] ?? 0;
    }

    /**
     * 获取头框列表
     * @param $page
     * @param $type
     * @param $uid
     * @return NotORM_Result
     */
    public function getHeadPic($page, $type, $uid){
        $model         = new Model_HeadBorder();
        $columns       = 'id,title,pic,price,type,overdue';
        $res           = $model->getList($type, $page, $columns);
        $headUserModel = new Model_HeadBorderUser();
        foreach($res as $k => &$v){
            $is           = $headUserModel->getHeadDes($uid, $v['id'], 'expire');
            $v['is_have'] = '2';
            $v['pic']     = get_upload_path($v['pic']);
            if($is){
                if($is['expire'] == 0 || ($is['expire'] > 0 && $is['expire'] > time())){
                    $is['is_have'] = '1';
                }
            }
        }
        $userInfo = getUserInfo($uid);
        return [$res, $userInfo['avatar_thumb']];
    }

    public function shopHeadBorder($headId, $uid){
        $model = new Model_HeadBorder();
        $des   = $model->get($headId, 'price,type,is_up,overdue');
        if(!$des){
            return [1, '商品不存在'];
        }
        if($des['is_up'] == Model_HeadBorder::LOWER){
            return [2, '商品已下架'];
        }
        if($des['type'] == Model_HeadBorder::ACTIVE){
            return [3, '活动商品无法直接购买哦'];
        }

        $userHeadModel = new Model_HeadBorderUser();
        $userHeadInfo  = $userHeadModel->getHeadDes($uid, $headId, 'id,expire');

        if($userHeadInfo['expire'] === 0){
            return [4, '您已拥有，请勿重复购买'];
        }

        $user_model  = new Model_User();
        $userBalance = $user_model->get($uid, 'coin');
        $userBalance = $userBalance['coin'] ?? 0;

        if($userBalance < $des['price']){
            return [4, '余额不足'];
        }

        $overdue = $des['overdue'] * 86400;
        if($overdue == 0){
            $userHeadData['expire'] = 0;
        }elseif($userHeadInfo && ($userHeadInfo['expire'] > 0 && $userHeadInfo['expire'] > time())){
            $userHeadData['expire'] = $userHeadInfo['expire'] + $overdue;
        }else{
            $userHeadData['expire'] = time() + $overdue;
        }
        $userHeadData['update_time'] = time();
        $bean                        = DI()->notorm->head_border_user;
        try{
            $bean->queryAll('begin');
            if(!isset($userHeadInfo['id'])){
                $userHeadData['uid']         = $uid;
                $userHeadData['head_id']     = $headId;
                $userHeadData['create_time'] = $userHeadData['update_time'];
                $insertId                    = $userHeadModel->insert($userHeadData);
                if(!$insertId){
                    $bean->queryAll('rollback');
                    return [5, '购买失败'];
                }
            }else{
                if(!$userHeadModel->update($userHeadInfo['id'], $userHeadData)){
                    $bean->queryAll('rollback');
                    return [5, '购买失败'];
                }
                $insertId = $userHeadInfo['id'];
            }

            $data      = [
                'type'      => Model_UserCoinRecord::OUT,
                'action'    => Model_UserCoinRecord::TOUKAUNG,
                'uid'       => $uid,
                'touid'     => $uid,
                'giftid'    => $headId,
                'giftcount' => 1,
                'showid'    => $insertId,
                'addtime'   => time(),
                'totalcoin' => $des['price'],
                'mark'      => 0,
            ];
            $coinModel = new Domain_UserCoin();
            list($code, $msg) = $coinModel->updateCoin($data);
            if($code > 0){
                $bean->queryAll('rollback');
                return [6, $msg];
            }
        }catch(\Exception $e){
            $bean->queryAll('rollback');
            return [99, $e->getMessage()];
        }
        $bean->queryAll('commit');
        return [0, '购买成功'];
    }
}
