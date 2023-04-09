<?php

class Domain_Red
{
    public function sendRed($data)
    {
        $rs = [];

        $model = new Model_Red();
        $rs    = $model->sendRed($data);

        return $rs;
    }

    public function getRedList($liveuid, $showid)
    {
        $rs = [];

        $model = new Model_Red();
        $rs    = $model->getRedList($liveuid, $showid);

        return $rs;
    }

    public function robRed($data)
    {
        $rs = [];

        $model = new Model_Red();
        $rs    = $model->robRed($data);

        return $rs;
    }

    public function getRedInfo($redid)
    {
        $rs = [];

        $model = new Model_Red();
        $rs    = $model->getRedInfo($redid);

        return $rs;
    }

    public function getRedRobList($redid)
    {
        $rs = [];

        $model = new Model_Red();
        $rs    = $model->getRedRobList($redid);

        return $rs;
    }

}
