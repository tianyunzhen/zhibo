<?php

/**
 * 支付
 */
class Api_Test extends PhalApi_Api{

    public function getRules(){
        return [
            'see' => [
                'gid' => ['name' => 'gid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'],
            ],
        ];
    }

    public function youXiaZhuBo(){
        $param = getcaches('1111');
        $arr   = array_count_values(json_decode($param, true));
        $arrS  = [];
        $a     = [];
        foreach($arr as $k => $v){
            $explode = explode('_', $k);
            if($v >= 4){

                $fId = $explode[0];
                if($fId == 185){
                    $a[] = $explode[1];
                }
                if(isset($arrS[$fId])){
                    $arrS[$fId] += 1;
                }else{
                    $arrS[$fId] = 1;
                }
            }
        }
        var_dump($a);
        die;
//        die;
        set_time_limit(0);
        $time             = "2020-09-27 00:00:00";
        $familyModel      = new Model_FamilyUser();
        $liveRecordDomain = new Domain_LiveRecord();
        $userArr          = [];
//        for($i = 1; $i <= 7; $i++){
        $times = strtotime($time);
//            $times            = strtotime("+{$i} day", strtotime($time));
        $startTime        = strtotime(date('Y-m-d 00:00:00', $times));
        $endTime          = strtotime(date('Y-m-d 23:59:59', $times));
        $elasticGiftModel = new Elast_GiftRecord();
        $data             = $elasticGiftModel->glamourListTest($startTime, $endTime);
        foreach($data as $k => $v){
            if($v['sum_total']['value'] >= 10000000){
                $familyId = $familyModel->getFamiliId($v['key']);
                if($familyId){
                    $liveTime = $liveRecordDomain->userLiveTimes($v['key'], $startTime);
                    if($liveTime >= 10800){
                        $userArr[] = $familyId . '_' . $v['key'];
                    }
                }
            }
        }
//        }
        $cacheValue = getcaches('1111');
        if($cacheValue){
            $cacheValue = json_decode($cacheValue, true);
            $userArr    = array_merge($userArr, $cacheValue);
        }
        setcaches('1111', json_encode($userArr));
        die;
    }

    public function rankingUser()
    {
//        $model = new Domain_Ranking();
//        $info  = $model->timingUserDay();
//         var_dump($info);die;
    }

    public function tt()
    {
        getUserInfo(2017881);
        $domain = new Domain_Livepk();
        $domain->endPK(2017692);
    }


}
