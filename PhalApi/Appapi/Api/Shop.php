<?php

/**
 * 商城
 */
class Api_Shop extends PhalApi_Api
{

    public function getRules()
    {
        return [
            'getApplets' => [
                'id' => ['name' => 'id', 'type' => 'int', 'desc' => '小程序商品ID'],
            ],

            'setGoods' => [
                'uid'       => [
                    'name' => 'uid',
                    'type' => 'int',
                    'desc' => '用户ID',
                ],
                'token'     => [
                    'name' => 'token',
                    'type' => 'string',
                    'desc' => '用户token',
                ],
                'type'      => [
                    'name'    => 'type',
                    'type'    => 'int',
                    'default' => '0',
                    'desc'    => '类型，0淘宝1小程序',
                ],
                'name'      => [
                    'name' => 'name',
                    'type' => 'string',
                    'desc' => '商品名',
                ],
                'href'      => [
                    'name' => 'href',
                    'type' => 'string',
                    'desc' => '商品链接',
                ],
                'thumb'     => [
                    'name' => 'thumb',
                    'type' => 'string',
                    'desc' => '视频图片',
                ],
                'old_price' => [
                    'name' => 'old_price',
                    'type' => 'string',
                    'desc' => '原价',
                ],
                'price'     => [
                    'name' => 'price',
                    'type' => 'string',
                    'desc' => '现价',
                ],
                'des'       => [
                    'name' => 'des',
                    'type' => 'string',
                    'desc' => '描述',
                ],
            ],

            'upHits' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'token' => [
                    'name' => 'token',
                    'type' => 'string',
                    'desc' => '用户token',
                ],
            ],

            'getGoodsList' => [
                'uid'     => [
                    'name' => 'uid',
                    'type' => 'int',
                    'desc' => '用户ID',
                ],
                'token'   => [
                    'name' => 'token',
                    'type' => 'string',
                    'desc' => '用户token',
                ],
                'keyword' => [
                    'name'    => 'keyword',
                    'type'    => 'string',
                    'default' => '',
                    'desc'    => '关键词',
                ],
                'p'       => [
                    'name'    => 'p',
                    'type'    => 'int',
                    'default' => '1',
                    'desc'    => '页码',
                ],
            ],

            'setSale' => [
                'uid'     => [
                    'name' => 'uid',
                    'type' => 'int',
                    'desc' => '用户ID',
                ],
                'token'   => [
                    'name' => 'token',
                    'type' => 'string',
                    'desc' => '用户token',
                ],
                'goodsid' => [
                    'name' => 'goodsid',
                    'type' => 'int',
                    'desc' => '商品ID',
                ],
                'issale'  => [
                    'name' => 'issale',
                    'type' => 'int',
                    'desc' => '在售状态，0否1是',
                ],
            ],

            'getShop' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'touid' => [
                    'name' => 'touid',
                    'type' => 'int',
                    'desc' => '对方ID',
                ],
                'p'     => [
                    'name'    => 'p',
                    'type'    => 'int',
                    'default' => '1',
                    'desc'    => '页码',
                ],
            ],

            'getRecomment' => [
                'touid' => [
                    'name' => 'touid',
                    'type' => 'int',
                    'desc' => '对方ID',
                ],
            ],
            'getSale'      => [
                'uid'     => [
                    'name' => 'uid',
                    'type' => 'int',
                    'desc' => '用户ID',
                ],
                'liveuid' => [
                    'name' => 'liveuid',
                    'type' => 'int',
                    'desc' => '主播ID',
                ],
                'p'       => [
                    'name'    => 'p',
                    'type'    => 'int',
                    'default' => '1',
                    'desc'    => '页码',
                ],
            ],

            'upStatus' => [
                'uid'     => [
                    'name' => 'uid',
                    'type' => 'int',
                    'desc' => '用户ID',
                ],
                'token'   => [
                    'name' => 'token',
                    'type' => 'string',
                    'desc' => '用户token',
                ],
                'goodsid' => [
                    'name' => 'goodsid',
                    'type' => 'int',
                    'desc' => '商品ID',
                ],
                'status'  => [
                    'name' => 'status',
                    'type' => 'int',
                    'desc' => '状态，-1下架1上架',
                ],
            ],

            'delGoods' => [
                'uid'     => [
                    'name' => 'uid',
                    'type' => 'int',
                    'desc' => '用户ID',
                ],
                'token'   => [
                    'name' => 'token',
                    'type' => 'string',
                    'desc' => '用户token',
                ],
                'goodsid' => [
                    'name' => 'goodsid',
                    'type' => 'int',
                    'desc' => '商品ID',
                ],
            ],

            'getShopInfo' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'touid' => [
                    'name' => 'touid',
                    'type' => 'int',
                    'desc' => '对方ID',
                ],
            ],
        ];
    }

    /**
     * 发布商品
     *
     * @desc 用于发布商品
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function setGoods()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid       = checkNull($this->uid);
        $token     = checkNull($this->token);
        $type      = checkNull($this->type);
        $name      = checkNull($this->name);
        $href      = checkNull($this->href);
        $thumb     = checkNull($this->thumb);
        $old_price = floatval(checkNull($this->old_price));
        $price     = floatval(checkNull($this->price));
        $des       = checkNull($this->des);

        if ($uid < 1 || $token == '' || $name == '' || $href == ''
            || $thumb == ''
            || $price < 0
        ) {
            $rs['code'] = 1001;
            $rs['msg']  = '信息错误';
            return $rs;
        }

        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $data = [
            'uid'       => $uid,
            'type'      => $type,
            'name'      => $name,
            'href'      => $href,
            'thumb'     => $thumb,
            'old_price' => $old_price,
            'price'     => $price,
            'des'       => $des,
        ];

        $domain = new Domain_Shop();
        $res    = $domain->setGoods($data);

        return $res;
    }

    /**
     * 查看
     *
     * @desc 用于更新商品查看次数
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    /* public function upHits() {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $uid=checkNull($this->uid);
        $token=checkNull($this->token);
        $videoid=checkNull($this->videoid);

        if($uid<1 || $token=='' || $videoid<1 ){
            $rs['code'] = 1001;
            $rs['msg'] = '信息错误';
            return $rs;
        }

        $checkToken=checkToken($uid,$token);
        if($checkToken==700){
            $rs['code'] = $checkToken;
            $rs['msg'] = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain = new Domain_Shop();
        $info = $domain->upHits($videoid);

        return $rs;
    } */

    /**
     * 我的商品
     *
     * @desc 用于获取商品列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[].name 商品名
     * @return string info[].thumb 商品封面
     * @return string info[].hits 查看次数
     * @return string info[].old_price 原价
     * @return string info[].price 现价
     * @return string info[].des 描述
     * @return string info[].issale 是否在售，0否1是
     * @return string msg 提示信息
     */
    public function getGoodsList()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = checkNull($this->uid);
        $token   = checkNull($this->token);
        $keyword = checkNull($this->keyword);
        $p       = checkNull($this->p);


        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $where           = [];
        $where['uid=?']  = $uid;
        $where['status'] = 1;
        if ($keyword != '') {
            $where['name like ?'] = '%' . $keyword . '%';
        }

        $domain = new Domain_Shop();
        $info   = $domain->getGoodsList($where, $p);

        $rs['info'] = $info;

        return $rs;
    }

    /**
     * 店铺信息
     *
     * @desc 用于获取店铺信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return object info[0].shopinfo 店铺信息
     * @return string info[0].shopinfo.name 店铺名称
     * @return string info[0].shopinfo.thumb 封面
     * @return string info[0].shopinfo.des 描述
     * @return string info[0].nums 商品数量
     * @return array info[0].list 商品列表
     * @return string info[0].list[].videoid 视频ID
     * @return string info[0].list[].name 商品名
     * @return string info[0].list[].thumb 商品封面
     * @return string info[0].list[].hits 查看次数
     * @return string info[0].list[].old_price 原价
     * @return string info[0].list[].price 现价
     * @return string info[0].list[].des 描述
     * @return string msg 提示信息
     */
    public function getShop()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = checkNull($this->uid);
        $touid = checkNull($this->touid);
        $p     = checkNull($this->p);


        $domain = new Domain_Shop();
        $info   = $domain->getShop($touid);

        $where          = [];
        $where['uid=?'] = $touid;

        if ($uid == $touid) {
            //$where['status!=?']=-2;
        } else {
            $where['status'] = 1;
        }


        $list = $domain->getGoodsList($where, $p);

        $nums = $domain->countGoods($where);

        $rs['info'][0]['shopinfo'] = $info;
        $rs['info'][0]['nums']     = $nums;
        $rs['info'][0]['list']     = $list;

        return $rs;
    }

    /**
     * 推荐商品
     *
     * @desc 用于获取推荐商品
     * @return int code 操作码，0表示成功
     * @return array info 商品列表
     * @return string info[].videoid 视频ID
     * @return string info[].name 商品名
     * @return string info[].thumb 商品封面
     * @return string info[].hits 查看次数
     * @return string info[].old_price 原价
     * @return string info[].price 现价
     * @return string info[].des 描述
     * @return string msg 提示信息
     */
    public function getRecomment()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $touid = checkNull($this->touid);


        $where          = [];
        $where['uid=?'] = $touid;

        $where['status'] = 1;
        $domain          = new Domain_Shop();
        $list            = $domain->getRecomment($where);

        $rs['info'] = $list;

        return $rs;
    }


    /**
     * 在售商品
     *
     * @desc 用于用户获取直播间在售商品
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].nums 总数
     * @return array info[0].list 商品列表
     * @return string info[0].list[].name 商品名
     * @return string info[0].list[].thumb 商品封面
     * @return string info[0].list[].hits 查看次数
     * @return string info[0].list[].old_price 原价
     * @return string info[0].list[].price 现价
     * @return string info[0].list[].des 描述
     * @return string msg 提示信息
     */
    public function getSale()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = checkNull($this->uid);
        $liveuid = checkNull($this->liveuid);
        $p       = checkNull($this->p);


        $domain = new Domain_Shop();
        $nums   = $domain->countSale($liveuid);

        $where          = [];
        $where['uid=?'] = $liveuid;

        $where['status'] = 1;
        $where['issale'] = 1;

        $list = $domain->getGoodsList($where, $p);

        $rs['info'][0]['nums'] = $nums;
        $rs['info'][0]['list'] = $list;

        return $rs;
    }

    /**
     * 增删在售商品
     *
     * @desc 用于主播增删在售商品
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function setSale()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = checkNull($this->uid);
        $token   = checkNull($this->token);
        $goodsid = checkNull($this->goodsid);
        $issale  = checkNull($this->issale);

        if ($uid < 0 || $token == '' || $goodsid < 0) {
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


        $domain = new Domain_Shop();

        $res = $domain->setSale($uid, $goodsid, $issale);

        return $res;
    }

    /**
     * 上架/下架商品
     *
     * @desc 用于上架/下架商品
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].status 状态，-1下架1上架
     * @return string msg 提示信息
     */
    public function upStatus()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = checkNull($this->uid);
        $token   = checkNull($this->token);
        $goodsid = checkNull($this->goodsid);
        $status  = checkNull($this->status);

        if ($uid < 0 || $token == '' || $goodsid < 0
            || ($status != -1
                && $status != 1)
        ) {
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


        $domain = new Domain_Shop();

        $res = $domain->upStatus($uid, $goodsid, $status);

        return $res;
    }

    /**
     * 删除商品
     *
     * @desc 用于删除商品
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function delGoods()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = checkNull($this->uid);
        $token   = checkNull($this->token);
        $goodsid = checkNull($this->goodsid);

        if ($uid < 0 || $token == '' || $goodsid < 0) {
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


        $domain = new Domain_Shop();

        $res = $domain->delGoods($uid, $goodsid);

        return $res;
    }

    /**
     * 店铺信息(无商品)
     *
     * @desc 用于获取店铺信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].name 店铺名称
     * @return string info[0].thumb 封面
     * @return string info[0].des 描述
     * @return string info[0].nums 商品数量
     * @return string msg 提示信息
     */
    public function getShopInfo()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = checkNull($this->uid);
        $touid = checkNull($this->touid);

        $domain = new Domain_Shop();
        $info   = $domain->getShop($touid);

        $where          = [];
        $where['uid=?'] = $touid;

        if ($uid == $touid) {
            //$where['status!=?']=-2;
        } else {
            $where['status'] = 1;
        }

        $nums = $domain->countGoods($where);

        $info['nums'] = $nums;
        unset($info['reason']);
        unset($info['status']);
        unset($info['addtime']);
        unset($info['uptime']);
        $rs['info'][0] = $info;

        return $rs;
    }
}
