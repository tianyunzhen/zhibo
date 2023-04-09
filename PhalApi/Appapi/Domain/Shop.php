<?php

class Domain_Shop
{
    public function isShop($uid)
    {
        $rs = [];

        $model = new Model_Shop();
        $rs    = $model->isShop($uid);

        return $rs;
    }

    public function getShop($uid)
    {
        $rs = [];

        $model = new Model_Shop();
        $rs    = $model->getShop($uid);

        return $rs;
    }

    public function setGoods($data)
    {

        $rs = ['code' => 0, 'msg' => '操作成功', 'info' => []];

        $isshop = $this->isShop($data['uid']);

        if (!$isshop) {
            $rs['code'] = 1002;
            $rs['msg']  = '店铺未申请或未审核通过';
            return $rs;
        }

        if ($data['type'] != 0) {
            $data['type'] = 1;
        }


        $data['addtime'] = time();

        $configpri = getConfigPri();
        //if($configpri['']){
        $data['status'] = 1;
        //}


        $model = new Model_Shop();
        $res   = $model->setGoods($data);

        return $rs;
    }

    public function upHits($videoid)
    {
        $rs = [];

        $model = new Model_Shop();
        $rs    = $model->upHits($videoid);

        return $rs;
    }

    public function getGoods($where = [])
    {
        $rs = [];

        $model = new Model_Shop();
        $rs    = $model->getGoods($where);

        return $rs;
    }

    public function getRecomment($where = [])
    {
        $rs = [];

        $model = new Model_Shop();
        $rs    = $model->getRecomment($where);

        return $rs;
    }

    public function getGoodsList($where = [], $p)
    {
        $rs = [];

        $model = new Model_Shop();
        $rs    = $model->getGoodsList($where, $p);

        return $rs;
    }

    public function countGoods($where = [])
    {
        $rs = [];

        $model = new Model_Shop();
        $rs    = $model->countGoods($where);

        return $rs;
    }

    public function countSale($liveuid)
    {
        $rs = [];

        $where           = [];
        $where['uid=?']  = $liveuid;
        $where['issale'] = 1;
        $where['status'] = 1;

        $model = new Model_Shop();
        $rs    = $model->countGoods($where);

        return $rs;
    }

    public function setSale($uid, $goodsid, $issale)
    {

        $rs = ['code' => 0, 'msg' => '操作成功', 'info' => []];

        $model = new Model_Shop();

        $where         = [];
        $where['id=?'] = $goodsid;

        $info = $model->getGoods($where);
        if (!$info) {
            $rs['code'] = 1001;
            $rs['msg']  = '商品不存在';
            return $rs;
        }

        if ($info['uid'] != $uid) {
            $rs['code'] = 1003;
            $rs['msg']  = '无权操作';
            return $rs;
        }

        if ($info['status'] == -2) {
            $rs['code'] = 1002;
            $rs['msg']  = '已被管理员下架';
            return $rs;
        }

        if ($info['status'] != 1) {
            $rs['code'] = 1002;
            $rs['msg']  = '商品未审核通过';
            return $rs;
        }

        $issale = $issale ? 1 : 0;
        $data   = [
            'issale' => $issale,
        ];

        $res = $model->upGoods($where, $data);

        return $rs;
    }

    public function upStatus($uid, $goodsid, $status)
    {

        $rs = ['code' => 0, 'msg' => '操作成功', 'info' => []];

        $model = new Model_Shop();

        $where         = [];
        $where['id=?'] = $goodsid;

        $info = $model->getGoods($where);
        if (!$info) {
            $rs['code'] = 1001;
            $rs['msg']  = '商品不存在';
            return $rs;
        }


        if ($info['uid'] != $uid) {
            $rs['code'] = 1003;
            $rs['msg']  = '无权操作';
            return $rs;
        }

        if ($info['status'] == 0) {
            $rs['code'] = 1002;
            $rs['msg']  = '商品审核中，无权操作';
            return $rs;
        }

        if ($info['status'] == 2) {
            $rs['code'] = 1002;
            $rs['msg']  = '商品审核未通过';
            return $rs;
        }

        if ($info['status'] == -2) {
            $rs['code'] = 1002;
            $rs['msg']  = '已被管理员下架';
            return $rs;
        }

        if ($status == 1) {
            $where['status'] = -1;
            $data            = [
                'status' => 1,
            ];
            $info2['status'] = '1';
        } else {
            $where['status'] = 1;
            $data            = [
                'status' => -1,
            ];
            $info2['status'] = '-1';
        }

        $res = $model->upGoods($where, $data);

        $rs['info'][0] = $info2;

        return $rs;
    }

    public function delGoods($uid, $goodsid)
    {

        $rs = ['code' => 0, 'msg' => '操作成功', 'info' => []];

        $model = new Model_Shop();

        $where         = [];
        $where['id=?'] = $goodsid;

        $info = $model->getGoods($where);
        if (!$info) {
            $rs['code'] = 1001;
            $rs['msg']  = '商品不存在';
            return $rs;
        }


        if ($info['uid'] != $uid) {
            $rs['code'] = 1003;
            $rs['msg']  = '无权操作';
            return $rs;
        }
        $where['uid=?'] = $uid;


        $res = $model->delGoods($where);

        return $rs;
    }

}
