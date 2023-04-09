<?php

class Domain_Linkmic{

    public function setMic($uid, $ismic){
        $model = new Model_Linkmic();
        return $model->setMic($uid, $ismic);
    }

    public function isMic($liveuid){
        $model = new Model_Linkmic();
        return $model->isMic($liveuid);
    }

}
