<?php

class Domain_Pay{
    const PAY_TYPE = ['ali' => 1, 'wx' => 2];
    public function pay($uid, $pay_id, $pay_type, $type, $toUid, $h5 = false)
    {
        $userModel = new Model_User();
        $userInfo = $userModel->get($uid,'id');
        if(!$userInfo)
        {
            return [1, '用户不存在', ''];
        }
        //查询订单规则
        $order_rules_model = new Model_ChargeRules();
        $rulesInfo         = $order_rules_model->get($pay_id);
        if(!$rulesInfo){
            return [1, '充值失败', ''];
        }
        $money = $rulesInfo['money'];
        if($type == 2){
            $money = $rulesInfo['money_ios'];
        }
        //生产订单数据
        $order_info        = [
            "uid"     => $uid,
            "touid"   => $toUid,
            "money"   => $money,
            "coin"    => $rulesInfo['coin'],
            "orderno" => time() . $uid,
            "type"    => self::PAY_TYPE[$pay_type], //支付宝或微信
            "status"  => 0,
            "addtime" => time(),
        ];
        $charge_user_model = new Model_ChargeUser();
        if(!$charge_user_model->insert($order_info)){
            return [2, '充值失败', ''];
        }
        //调用支付
        if($pay_type == "ali"){
//            $pay_model           = new Api_Lib_Pay_Sum_AliPay();
//            $order_info['title'] = '充值';
            $pay_model = new Api_Lib_Pay_Sum_AliPayV2();
        }else{
            $pay_model = new Api_Lib_Pay_Sum_WxPay();
        }
        if ($pay_type == "wx") {
            $pay_model           = new Api_Lib_Pay_Sum_WxPay();
        }
        list($code, $data, $msg) = $pay_model->start($order_info, $h5);
        return [$code, $msg, $data];
    }

    public function aliNotify($arr){
        $string = '';
        foreach($arr as $k => $v){
            $string .= $k . '=' . $v . '&';
        }
        $string = trim($string, '&');
        unset($arr['service']);
        $pay_model = new Api_Lib_Pay_Sum_AliPay();
        file_put_contents(API_ROOT . '/Runtime/ali.log', $string . '\n\n', FILE_APPEND);
        //验证失败
//        if($pay_model->notify($arr)){
        $order_no   = $arr['out_trade_no'] ?? 0;
        $order_info = DI()->notorm->charge_user
            ->where('orderno = ?', $order_no)
            ->select('id,uid,touid,money,status,coin')
            ->fetchOne();
        if($arr['trade_status'] == 'TRADE_SUCCESS'){
            if($order_info){
                if($order_info['status'] == Model_ChargeUser::WAIT){
//                    $notify_data = json_decode($arr['fund_bill_list'], true);
                    if($order_info['money'] == $arr['total_amount'] && $arr['app_id'] == ''){
                        $charge_data['status']      = Model_ChargeUser::SUCCESS;
                        $charge_data['update_time'] = time();
                        $charge_data['trade_no']    = $arr['trade_no'];

                        $record_data = [
                            'type'      => Model_UserCoinRecord::INCOME,
                            'action'    => Model_UserCoinRecord::GFZC,
                            'uid'       => $order_info['uid'],
                            'touid'     => $order_info['touid'],
                            'giftcount' => $order_info['coin'],
                            'totalcoin' => $order_info['coin'],
                            'addtime'   => time(),
                        ];
                        try{
                            $handle = DI()->notorm->user;
                            $handle->queryAll('begin');
                            if(
                            !DI()->notorm->charge_user
                                ->where('id = ? and status = ?', $order_info['id'], Model_ChargeUser::WAIT)
                                ->update($charge_data)
                            ){
                                $handle->queryAll('rollback');
                                file_put_contents(API_ROOT . '/Runtime/fail.log', '[1]' . $string, FILE_APPEND);
                                exit('fail1');
                            }
                            if(
                            !DI()->notorm->user
                                ->where('id = ?', intval($order_info['uid']))
                                ->update([
                                    'coin' => new NotORM_Literal("coin + {$order_info['coin']}"),
                                ])
                            ){
                                $handle->queryAll('rollback');
                                file_put_contents(API_ROOT
                                    . '/Runtime/fail.log', '[2]' . $string,
                                    FILE_APPEND);
                                exit('fail2');
                            }

                            if(!DI()->notorm->user_coinrecord->insert($record_data)){
                                $handle->queryAll('rollback');
                                file_put_contents(API_ROOT
                                    . '/Runtime/fail.log', '[3]' . $string,
                                    FILE_APPEND);
                                exit('fail3');
                            }
                        }catch(\Exception $e){
                            $handle->queryAll('rollback');
                            exit('fail' . $e->getMessage());
                        }
                        $handle->queryAll('commit');
                        try{
                            $title      = '充值成功';
                            $content    = sprintf(Common_JPush::CZCG,
                                intval($order_info['coin']), '金币');
                            $push_model = new Common_JPush($order_info['uid']);
                        }catch(\Exception $e){

                        }
                        $push_model->sendAlias($title, $content);
                        Domain_Msg::addMsg($title, $content,
                            $order_info['uid']);
                        exit('success');
                    }
                }
            }
        }else{
            $charge_data['status']      = Model_ChargeUser::FAIL;
            $charge_data['update_time'] = time();
            DI()->notorm->charge_user
                ->where('id = ? and status = ?', $order_info['uid'], Model_ChargeUser::WAIT)
                ->update($charge_data);
        }
//        }
        file_put_contents(API_ROOT . '/Runtime/fail.log', '[4]' . $string,
            FILE_APPEND);
        exit('fail5');
    }


    public function wxNotify($arr){
//        exit('success');
        $string = '';
        foreach($arr as $k => $v){
            $string .= $k . '=' . $v . '&';
        }
        $string = trim($string, '&');
        file_put_contents(API_ROOT . '/Runtime/wx.log', $string, FILE_APPEND);
        //验证失败
//        if($pay_model->notify($arr)){
        $order_no   = $arr['out_trade_no'] ?? 0;
        $order_info = DI()->notorm->charge_user
            ->where('orderno = ?', $order_no)
            ->select('id,uid,touid,money,status,coin')
            ->fetchOne();
        if($arr['return_code'] == 'SUCCESS' && ($arr['return_msg'] ?? '') == ''){
            if($order_info){
                if($order_info['status'] == Model_ChargeUser::WAIT){
                    if($order_info['money'] * 100 == $arr['total_fee']){
                        $charge_data['status']      = Model_ChargeUser::SUCCESS;
                        $charge_data['update_time'] = time();
                        $charge_data['trade_no']    = $order_no;
                        $record_data                = [
                            'type'      => Model_UserCoinRecord::INCOME,
                            'action'    => Model_UserCoinRecord::GFZC,
                            'uid'       => $order_info['uid'],
                            'touid'     => $order_info['touid'],
                            'giftcount' => $order_info['coin'],
                            'totalcoin' => $order_info['coin'],
                            'addtime'   => time(),
                        ];
                        try{
                            $handle = DI()->notorm->user;
                            $handle->queryAll('begin');
                            if(
                            !DI()->notorm->charge_user
                                ->where('id = ? and status = ?', $order_info['id'], Model_ChargeUser::WAIT)
                                ->update($charge_data)
                            ){
                                $handle->queryAll('rollback');
                                file_put_contents(API_ROOT . '/Runtime/fail.log', '[1]' . $string, FILE_APPEND);
                                exit('fail1');
                            }
                            if(
                            !DI()->notorm->user
                                ->where('id = ?', intval($order_info['uid']))
                                ->update([
                                    'coin' => new NotORM_Literal("coin + {$order_info['coin']}"),
                                ])
                            ){
                                $handle->queryAll('rollback');
                                file_put_contents(API_ROOT . '/Runtime/fail.log', '[2]' . $string, FILE_APPEND);
                                exit('fail2');
                            }

                            if(!DI()->notorm->user_coinrecord->insert($record_data)){
                                $handle->queryAll('rollback');
                                file_put_contents(API_ROOT . '/Runtime/fail.log', '[3]' . $string, FILE_APPEND);
                                exit('fail3');
                            }
                        }catch(\Exception $e){
                            $handle->queryAll('rollback');
                            exit('fail' . $e->getMessage());
                        }
                        $handle->queryAll('commit');
                        try{
                            $title      = '充值成功';
                            $content    = sprintf(Common_JPush::CZCG, intval($order_info['coin']), '金币');
                            $push_model = new Common_JPush($order_info['uid']);
                        }catch(\Exception $e){

                        }
                        $push_model->sendAlias($title, $content);
                        Domain_Msg::addMsg($title, $content, $order_info['uid']);
                        exit('success');
                    }
                }
            }
        }else{
            $charge_data['status']      = Model_ChargeUser::FAIL;
            $charge_data['update_time'] = time();
            DI()->notorm->charge_user
                ->where('id = ? and status = ?', $order_info['uid'], Model_ChargeUser::WAIT)
                ->update($charge_data);
        }
//        }
        file_put_contents(API_ROOT . '/Runtime/wxPayFail.log', '[4]' . $string,
            FILE_APPEND);
        exit('fail5');
    }
}