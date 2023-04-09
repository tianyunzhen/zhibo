<?php

/**
 * 商城(WANGLIN)
 */
class Api_Shops extends PhalApi_Api{

    public function getRules(){
        return [
            'getLiangType'   => [
                'uid'   => ['name' => 'uid', 'require' => true, 'type' => 'int', 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'require' => true, 'type' => 'int', 'desc' => '用户token'],
            ],
            'getCarType'     => [
                'uid'   => ['name' => 'uid', 'require' => true, 'type' => 'int', 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'require' => true, 'type' => 'int', 'desc' => '用户token'],
            ],
            'getLiang'       => [
                'uid'     => ['name' => 'uid', 'require' => true, 'type' => 'int', 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'require' => true, 'type' => 'int', 'desc' => '用户token'],
                'type_id' => ['name' => 'type_id', 'require' => true, 'type' => 'int', 'desc' => '类型ID 1推荐，2极品，3豹子，4情侣'],
                'page'    => ['name' => 'page', 'require' => true, 'type' => 'int', 'desc' => '页码'],
            ],
            'getCar'         => [
                'uid'     => ['name' => 'uid', 'require' => true, 'type' => 'int', 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'require' => true, 'type' => 'int', 'desc' => '用户token'],
                'type_id' => ['name' => 'type_id', 'require' => true, 'type' => 'int', 'desc' => '类型ID 1福利，2豪华，3梦幻'],
                'page'    => ['name' => 'page', 'require' => true, 'type' => 'int', 'desc' => '页码'],
            ],
            'byCar'          => [
                'uid'    => ['name' => 'uid', 'require' => true, 'type' => 'int', 'desc' => '用户ID'],
                'token'  => ['name' => 'token', 'require' => true, 'type' => 'int', 'desc' => '用户token'],
                'car_id' => ['name' => 'car_id', 'require' => true, 'type' => 'int', 'desc' => '座驾ID'],
            ],
            'byLiang'        => [
                'uid'      => ['name' => 'uid', 'require' => true, 'type' => 'int', 'desc' => '用户ID'],
                'token'    => ['name' => 'token', 'require' => true, 'type' => 'int', 'desc' => '用户token'],
                'liang_id' => ['name' => 'liang_id', 'require' => true, 'type' => 'int', 'desc' => '靓号ID'],
            ],
            'nowMonthMoney'  => [
                'uid'   => ['name' => 'uid', 'require' => true, 'type' => 'int', 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'require' => true, 'type' => 'int', 'desc' => '用户token'],
            ],
            'getHeadList'    => [
                'uid'   => ['name' => 'uid', 'require' => true, 'type' => 'int', 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'require' => true, 'type' => 'int', 'desc' => '用户token'],
                'type'  => ['name' => 'type', 'require' => true, 'min' => 1, 'max' => 3, 'type' => 'int', 'desc' => '类型ID 1活动，2豪华，3梦幻'],
                'page'  => ['name' => 'page', 'require' => true, 'min' => 1, 'type' => 'int', 'desc' => '页码'],
            ],
            'shopHeadBorder' => [
                'uid'     => ['name' => 'uid', 'require' => true, 'type' => 'int', 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'require' => true, 'type' => 'int', 'desc' => '用户token'],
                'head_id' => ['name' => 'head_id', 'require' => true, 'min' => 1, 'type' => 'int', 'desc' => '头框ID'],
            ],
        ];
    }

    /**
     * 靓号分类列表
     * @desc 用于获取靓号分类列表
     * @return int code 操作码，0表示成功
     * @return array info 分类信息
     * @return string msg 提示信息
     */
    public function getLiangType(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $rs['info'] = [
            '1' => '推荐',
            '2' => '极品',
            '3' => '豹子',
            '4' => '情侣',
        ];
        return $rs;
    }

    /**
     * 座驾分类列表
     * @desc 用于获取座驾分类列表
     * @return int code 操作码，0表示成功
     * @return array info 分类信息
     * @return string msg 提示信息
     */
    public function getCarType(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $rs['info'] = [
            '1' => '福利',
            '2' => '豪华',
            '3' => '梦幻',
        ];
        return $rs;
    }

    /**
     * 获取靓号
     * @desc 用于获取靓号
     * @return int code 操作码，0表示成功
     * @return int info[0].id 靓号ID
     * @return string info[0].name 靓号名称
     * @return int info[0].needcoin 靓号价格
     * @return int info[0].type 靓号类型
     * @return int info[0].expire 靓号有效期（单位：天） 0永久
     * @return string msg 提示信息
     */
    public function getLiang(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $page       = $this->page;
        $type_id    = $this->type_id;
        $model      = new Domain_Shops();
        $rs['info'] = $model->getLiang($page, $type_id);
        return $rs;
    }

    /**
     * 获取座驾
     * @desc 用于获取座驾
     * @return int code 操作码，0表示成功
     * @return int info[0].id 座驾ID
     * @return string info[0].name 座驾名称
     * @return string info[0].thumb 座驾图片
     * @return int info[0].needcoin 座驾价格
     * @return int info[0].expire 座驾有效期（单位：天） 0永久
     * @return int info[0].is_by 是否已领取  1是 2否
     * @return string msg 提示信息
     */
    public function getCar(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $page       = $this->page ?: 1;
        $type_id    = $this->type_id;
        $uid        = $this->uid;
        $model      = new Domain_Shops();
        $rs['info'] = $model->getCard($page, $type_id, $uid);
        return $rs;
    }

    /**
     * 购买座驾
     * @desc 用于获取靓号分类列表
     * @return int code 操作码，0表示成功
     * @return array info 分类信息
     * @return string msg 提示信息
     */
    public function byCar(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $car_id     = $this->car_id;
        $uid        = $this->uid;
        $token      = $this->token;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $mode = new Domain_Shops();
        list($rs['code'], $rs['msg']) = $mode->byCar($car_id, $uid);
        return $rs;
    }

    /**
     * 购买靓号
     * @desc 用于获取靓号分类列表
     * @return int code 操作码，0表示成功
     * @return array info 分类信息
     * @return string msg 提示信息
     */
    public function byLiang(){
        $rs         = ['code' => 1, 'msg' => 'adsfa', 'info' => []];
        $liang_id   = $this->liang_id;
        $uid        = $this->uid;
        $token      = $this->token;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $mode = new Domain_Shops();
        list($rs['code'], $rs['msg']) = $mode->byLiang($uid, $liang_id);
        return $rs;
    }

    /**
     * 获取本月消费
     * @desc 用于获取获取本月消费
     * @return int code 操作码，0表示成功
     * @return array info.money 金额
     * @return string msg 提示信息
     */
    public function nowMonthMoney(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = $this->token;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $mode                = new Domain_Shops();
        $rs['info']['money'] = $mode->nowMonthMoney($uid);
        return $rs;
    }

    /**
     * 获取头框列表
     * @desc 用于获取头框列表
     * @return int code 操作码，0表示成功
     * @return array info.list.id ID
     * @return array info.list.price 价格
     * @return array info.list.title 标题
     * @return array info.list.is_have 是否拥有 1是 2否
     * @return array info.list.pic 图片地址
     * @return array info.list.type 类型 1活动 2豪华 3梦幻
     * @return array info.list.overdue 有效期（单位：天） 0表示永久
     * @return array info.head_pic 头像
     * @return string msg 提示信息
     */
    public function getHeadList(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = $this->token;
        $page       = $this->page;
        $type       = $this->type;
        $checkToken = checkToken($uid, $token);
//        if($checkToken == 700){
//            $rs['code'] = $checkToken;
//            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
//            return $rs;
//        }
        $domain = new Domain_Shops();
        list($list, $headPic) = $domain->getHeadPic($page, $type, $uid);
        $rs['info']['list']     = $list;
        $rs['info']['head_pic'] = $headPic;

        return $rs;
    }

    /**
     * 购买头框
     * * @return string msg 提示信息
     * @return array
     */
    public function shopHeadBorder(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $head_id    = $this->head_id;
        $token      = $this->token;
        $checkToken = checkToken($uid, $token);
//        if($checkToken == 700){
//            $rs['code'] = $checkToken;
//            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
//            return $rs;
//        }
        $domain = new Domain_Shops();
        list($rs['code'], $rs['msg']) = $domain->shopHeadBorder($head_id, $uid);
        return $rs;
    }
}
