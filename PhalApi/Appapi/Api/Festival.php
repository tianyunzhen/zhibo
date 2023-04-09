<?php

/**
 * 中秋头框活动（汪林）
 */
class Api_Festival extends PhalApi_Api{

    public function getRules(){
        return [
            'getFestivalPrizeList'     => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'page'  => ['name' => 'page', 'type' => 'int', 'require' => true, 'desc' => '页码'],
            ],
            'getFestivalGiftList'      => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'page'  => ['name' => 'page', 'type' => 'int', 'require' => true, 'desc' => '页码'],
            ],
            'getFestivalGiftUserInfo'  => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
            ],
            'getFestivalPrizeUserInfo' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
            ],
        ];
    }

    /**
     * 获取中奖榜
     * @desc 用于获取中奖榜
     * @return string list.id 用户ID
     * @return string list.userName 用户名
     * @return string list.countMultiple 倍数
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
    public function getFestivalPrizeList(){
        $rs                 = ['code' => 0, 'msg' => '', 'info' => []];
        $page               = $this->page;
        $uid                = $this->uid;
        $token              = $this->token;
        $domain             = new Domain_Festival();
        $rs['info']['list'] = $domain->getFestivalPrizeList($page);
        return $rs;
    }

    /**
     * 获取收礼榜
     * @desc 用于获取收礼榜
     * @return string list.id 用户ID
     * @return string list.userName 用户名
     * @return string list.countNums 数量
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
    public function getFestivalGiftList(){
        $rs                 = ['code' => 0, 'msg' => '', 'info' => []];
        $page               = $this->page;
        $uid                = $this->uid;
        $token              = $this->token;
        $domain             = new Domain_Festival();
        $rs['info']['list'] = $domain->getFestivalGiftList($page, $uid);
        return $rs;
    }

    /**
     * 获取收礼榜个人信息
     * @desc 用于获取收礼榜个人信息
     * @return string info.no 排名
     * @return string info.userName 用户名
     * @return string info.nums 数量
     * @return string info.pic 头像
     * @return string info.is_remark 是否认证 0否 1是
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
    public function getFestivalGiftUserInfo(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = $this->token;
        $domain     = new Domain_Festival();
        $rs['info'] = $domain->getFestivalGiftUserInfo($uid);
        return $rs;
    }

    /**
     * 获取中奖榜个人信息
     * @desc 用于获取中奖榜个人信息
     * @return string info.userName 用户名
     * @return string info.nums 数量
     * @return string info.pic 头像
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
    public function getFestivalPrizeUserInfo(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = $this->token;
        $domain     = new Domain_Festival();
        $rs['info'] = $domain->getFestivalPrizeUserInfo($uid);
        return $rs;
    }

}