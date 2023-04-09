<?php

class Model_Linkmic extends PhalApi_Model_NotORM{
    /* 设置连麦开关 */
    public function setMic($uid, $ismic){
        return DI()->notorm->live
            ->where('uid=?', $uid)
            ->update(['ismic' => $ismic]);
    }

    /* 判断主播连麦开关 */
    public function isMic($liveuid){
        $isExist = DI()->notorm->live
            ->select('ismic')
            ->where('uid=?', $liveuid)
            ->fetchOne();
        if(isset($isExist['ismic']) && $isExist['ismic'] > 0){
            return 1;
        }
        return 0;
    }
}
