<?php

class Domain_Login
{

    public function userLogin($user_login, $user_pass)
    {
        $rs = [];

        $model = new Model_Login();
        $rs    = $model->userLogin($user_login, $user_pass);
        return $rs;
    }

    public function userReg($user_login, $user_pass, $source)
    {
        $rs    = [];
        $model = new Model_Login();
        $rs    = $model->userReg($user_login, $user_pass, $source);

        return $rs;
    }

    public function userFindPass($user_login, $user_pass)
    {
        $rs    = [];
        $model = new Model_Login();
        $rs    = $model->userFindPass($user_login, $user_pass);

        return $rs;
    }

    public function userLoginByThird(
        $openid,
        $type,
        $nickname,
        $avatar,
        $source
    ) {
        $rs = [];

        $model = new Model_Login();
        $rs    = $model->userLoginByThird($openid, $type, $nickname, $avatar,
            $source);

        return $rs;
    }

    public function upUserPush($uid, $pushid)
    {
        $rs = [];

        $model = new Model_Login();
        $rs    = $model->upUserPush($uid, $pushid);

        return $rs;
    }

    public function getUserban($user_login)
    {
        $rs = [];

        $model = new Model_Login();
        $rs    = $model->getUserban($user_login);

        return $rs;
    }

    public function getThirdUserban($openid, $type)
    {
        $rs = [];

        $model = new Model_Login();
        $rs    = $model->getThirdUserban($openid, $type);

        return $rs;
    }

    public function flashLogin($user_login, $pass, $source)
    {
        $rs = [];

        $model = new Model_Login();
        $rs    = $model->flashLogin($user_login, $pass, $source);
        return $rs;
    }
}
