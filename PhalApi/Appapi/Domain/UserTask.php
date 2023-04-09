<?php

class Domain_UserTask
{

    public function isSignIn($uid)
    {
        $rs    = false;
        $model = new Model_UserTask();
        $rs    = $model->isSignIn($uid);

        return $rs;
    }

    public function userSign($uid)
    {
        $rs    = false;
        $model = new Model_UserTask();
        $rs    = $model->userSign($uid);

        return $rs;
    }
}
