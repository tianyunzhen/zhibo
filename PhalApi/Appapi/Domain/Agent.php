<?php

class Domain_Agent
{
    public function getCode($uid)
    {
        $rs = [];

        $model = new Model_Agent();
        $rs    = $model->getCode($uid);

        return $rs;
    }

    public function getShareImage($slideId)
    {
        $rs = [];

        $model = new Model_Agent();
        $rs    = $model->getShareImage($slideId);

        return $rs;
    }
}
