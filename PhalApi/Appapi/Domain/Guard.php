<?php

class Domain_Guard
{
    public function getGuardList($data)
    {
        $rs = [];

        $model = new Model_Guard();
        $rs    = $model->getGuardList($data);

        return $rs;
    }

    public function getList()
    {
        $rs = [];

        $model = new Model_Guard();
        $rs    = $model->getList();

        return $rs;
    }

    public function getUserGuard($uid, $liveuid)
    {
        $rs = [];

        $model = new Model_Guard();
        $rs    = $model->getUserGuard($uid, $liveuid);

        return $rs;
    }

    public function getGuardNums($liveuid)
    {
        $rs = [];

        $model = new Model_Guard();
        $rs    = $model->getGuardNums($liveuid);

        return $rs;
    }

}
