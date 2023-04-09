<?php

/**
 * 背包
 */
class Api_Backpack extends PhalApi_Api{

    public function getRules(){
        return [
            'getBackpack'           => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'desc' => '用户token'],
            ],
            'getBackpackV2'         => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'desc' => '用户token'],
                'type'  => ['name' => 'type', 'type' => 'int', 'min' => 1, 'max' => 2, 'require' => true, 'desc' => '类型 1 坐骑 2 靓号', 'default' => 1],
            ],
            'getBackPackHeadBorder' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'desc' => '用户token'],
                'page'  => ['name' => 'page', 'type' => 'int', 'min' => 1, 'desc' => '页码'],
            ],
            'useHeadBorder'         => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'desc' => '用户token'],
                'head_id' => ['name' => 'head_id', 'type' => 'int', 'min' => 1, 'desc' => '头框ID'],
            ],
        ];
    }

    /**
     * 背包礼物V2(新)
     *
     * @desc 用于 获取背包礼物
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info 坐骑或靓号信息
     * @return string info[0]['id'] 主键id (公用)
     * @return string info[0]['name'] 靓号或坐骑名称
     * @return string info[0]['addtime'] 添加时间（公用）
     * @return string info[0]['status'] 使用状态 （公用）0 未使用 1 已使用
     * @return string info[0]['endtime'] 到期时间 （坐骑）
     * @return string info[0]['thumb'] 图标 （坐骑）
     * @return string msg 提示信息
     */
    public function getBackpackV2(){
        $rs    = ['code' => 0, 'msg' => '', 'info' => []];
        $uid   = checkNull($this->uid);
        $token = checkNull($this->token);
        $type  = checkNull($this->type);
        if($uid < 0 || $token == ''){
            $rs['code'] = 1000;
            $rs['msg']  = '信息错误';
            return $rs;
        }
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain     = new Domain_Backpack();
        $info       = $domain->getBackpackV2($uid, $type);
        $rs['info'] = $info;
        return $rs;
    }

    /**
     * 获取背包头框（WANGLIN）
     *
     * @desc 用于 获取背包礼物
     * @return int code 操作码，0表示成功
     * @return array info.list.title 标题
     * @return array info.list.pic 头框图片
     * @return array info.list.type 类型 1活动 2豪华 3梦幻
     * @return array info.list.id ID
     * @return array info.list.is_use 是否使用 1是 2否
     * @return array info.list.expire 过期时间（时间戳） 0表示永久
     * @return array info.list.create_time 购买时间
     * @return array info.pic 头像
     * @return string msg 提示信息
     */
    public function getBackPackHeadBorder(){
        $rs    = ['code' => 0, 'msg' => '', 'info' => []];
        $uid   = checkNull($this->uid);
        $token = checkNull($this->token);
        $page  = checkNull($this->page);
        if($uid < 0 || $token == ''){
            $rs['code'] = 1000;
            $rs['msg']  = '信息错误';
            return $rs;
        }
        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain = new Domain_Backpack();
        list($list, $pic) = $domain->getBackPackHeadBorder($page, $uid);
        $rs['info']['list'] = $list;
        $rs['info']['pic']  = $pic;
        return $rs;
    }

    /**
     * 头框使用（WANGLIN）
     *
     * @desc 用于 获取背包礼物
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
    public function useHeadBorder(){
        $rs     = ['code' => 0, 'msg' => '', 'info' => []];
        $uid    = checkNull($this->uid);
        $token  = checkNull($this->token);
        $headId = checkNull($this->head_id);
        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_Backpack();
        list($rs['code'], $rs['msg']) = $domain->useHeadBorder($headId, $uid);
        return $rs;
    }
}
