<?php

class Domain_LivePkUser{

    /**
     * 获取PK胜率
     * @param $uid
     * @return array
     */
    public function getUserPkDesc($uid){
        $key = Common_Cache::USER_PK_WINNING . $uid;
        $res = getcaches($key);
        if(!$res){
            $livePkUserModel = new Model_LivePkUser();
            $res             = $livePkUserModel->getPkDesc($uid);
            if($res){
                setcaches($key, json_encode($res), 3600);
            }
        }else{
            $res = json_decode($res,true);
        }
        if($res['count_nums'] == 0){
            return [0, 0];
        }else{
            return [$res['count_nums'], intval($res['win_nums'] / $res['count_nums'] * 100)];
        }
    }
}
