<?php

class Domain_Level
{
    public function index($uid)
    {
        $rs = [];

        $model = new Model_Level();
        $rs    = $model->index($uid);

        return $rs;
    }
}
