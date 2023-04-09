<?php

class Domain_Backpack{
    public function getBackpack($uid){

        $model  = new Model_Backpack();
        $result = $model->getBackpack($uid);
    }

    public function addBackpack($uid, $giftid, $nums){
        $rs = [];

        $model = new Model_Backpack();
        $rs    = $model->addBackpack($uid, $giftid, $nums);

        return $rs;
    }

    public function reduceBackpack($uid, $giftid, $nums){
        $rs = [];

        $model = new Model_Backpack();
        $rs    = $model->reduceBackpack($uid, $giftid, $nums);

        return $rs;
    }

    public function getBackpackV2($uid, $type){

        $model = new Model_Backpack();
        $rs    = $model->getBackpackV2($uid, $type);
        return $rs;
    }

    public function EquipmentSwitch($id, $uid, $type, $status){

        $model = new Model_Backpack();
        $rs    = $model->equipmentSwitch($id, $uid, $type, $status);
        return $rs;
    }

    public function getBackPackHeadBorder($page, $uid){
        $model = new Model_HeadBorderUser();
        $list  = $model->getUserHeadBorderList($page, $uid);
        foreach($list as $k => &$v){
            $v['pic']    = get_upload_path($v['pic']);
            $v['expire'] = ($v['expire'] > 0) ? $this->Sec2Time($v['expire'] - time()) : '永久';
        }
        $userInfo = getUserInfo($uid);
        return [$list, $userInfo['avatar_thumb']];
    }

    public function useHeadBorder($id, $uid){
        $model = new Model_HeadBorderUser();
        $info  = $model->get($id, 'uid,is_use,expire');
        if(!$info){
            return [1, '头框不存在'];
        }
        if($info['uid'] != $uid){
            return [2, '头框不存在'];
        }
        if($info['expire'] > 0 && $info['expire'] < time()){
            return [3, '头框已过期'];
        }
        $bean = DI()->notorm->head_border_user;
        if($model->uninstallAll($uid) === false){
            $bean->queryAll('rollback');
            return [4, '操作失败'];
        }
        if($info['is_use'] == 2){
            if($model->useHeadBorder($id, $uid) === false){
                $bean->queryAll('rollback');
                return [5, '操作失败'];
            }
        }
        $key = Common_Cache::HEADER . $uid;
        delcache($key);
        $bean->queryAll('commit');
        return [0, '操作成功'];
    }

    public function Sec2Time($time){
        if(is_numeric($time)){
            $value = [
                "years"   => 0, "days" => 0, "hours" => 0,
                "minutes" => 0, "seconds" => 0,
            ];
            if($time >= 31556926){
                $value["years"] = floor($time / 31556926);
                return $value["years"] . '年';
            }
            if($time >= 86400){
                $value["days"] = floor($time / 86400);
                return $value["days"] . '天';
            }
            if($time >= 3600){
                $value["hours"] = floor($time / 3600);
                return $value["hours"] . '小时';
            }
            if($time >= 60){
                $value["minutes"] = floor($time / 60);
                return $value["minutes"] . '分';
            }
            return floor($time) . '秒';
//            $value["seconds"] = floor($time);
//            //return (array) $value;
//            $t=$value["years"] ."年". $value["days"] ."天"." ". $value["hours"] ."小时". $value["minutes"] ."分".$value["seconds"]."秒";
//            Return $t;

        }else{
            return (bool)false;
        }
    }
}
