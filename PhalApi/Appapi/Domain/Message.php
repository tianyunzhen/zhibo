<?php

class Domain_Message
{
    public function getList($uid, $p)
    {
        $rs = [];

        $model = new Model_Message();
        $rs    = $model->getList($uid, $p);

        return $rs;
    }

    public function getUnreadNum($uid)
    {
        $rs = [];

        $model = new Model_Message();
        list($push, $attention) = $model->getUnreadNum($uid);

        return [$push, $attention];
    }

    public function readMessage($uid, $table)
    {
        $rs = [];

        $model = new Model_Message();
        $rs    = $model->readMessage($uid, $table);

        return $rs;
    }

}
