<?php

class Api_Mibai extends PhalApi_Api
{
//来来1号店二开开发-QQ125050230
//直播间 视频间文字滚动广告跳转
    public function gettxturl()
    {
        $rs = ["code" => "1000", "msg" => "suceess"];

        $info     = DI()->notorm->txturl
            ->select("id,txt,url,type,if_uid")
            ->fetchALL();
        $rs[info] = $info;
        return $rs;
    }

    //首页公告
    public function getnotice()
    {
        $rs       = ["code" => "1000", "msg" => "suceess"];
        $info     = DI()->notorm->notice
            ->select("id,txt,url,type,if_uid")
            ->fetchALL();
        $rs[info] = $info;
        return $rs;

    }

    //首页分类
// 提醒一下
//首页的游戏分类区域可以隐藏，只需要把状态码改为1001 即可隐藏
    public function getgameclass()
    {
        $rs = ["code" => "1000", "msg" => "success"];

        $info     = DI()->notorm->gameclass
            ->select("id,name,thumb,url,type,status,if_uid")
            ->fetchALL();
        $rs[info] = $info;

        return $rs;

    }

    public function getuserinfo()
    {
        $rs    = ['code' => '0', 'msg' => '获取成功'];
        $uid   = $_GET['uid'];
        $token = $_GET['token'];

        if (empty($uid)) {
            $rs = ['code' => '1000', 'msg' => '缺少必要的请求参数'];
            return $rs;
        }
        if (empty($token)) {
            $rs = ['code' => '1000', 'msg' => '缺少必要的请求参数'];
            return $rs;
        }


        $user = DI()->notorm->user
            ->where("id=$uid")
            ->select("id,user_login,user_nicename,avatar,last_login_ip,coin,source")
            ->fetchALL();

        $rs[info] = $user;
        return $rs;

    }

    public function getroomgame()
    {
        $rs   = ['code' => '', 'msg' => ''];
        $info = DI()->notorm->roomgame
            ->select("id,pic,url,ifuid")
            ->fetchALL();

        if (!$info) {
            $rs = ['code' => 1000, 'msg' => '暂无游戏列表'];
            return $rs;
        } else {
            $rs[code] = 0;
            $rs[msg]  = 'success';
            $rs[info] = $info;
            return $rs;
        }


    }

}