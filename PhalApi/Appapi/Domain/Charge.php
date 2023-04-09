<?php

class Domain_Charge
{
    public function getOrderId($changeid, $orderinfo)
    {
        $rs = [];

        $model = new Model_Charge();
        $rs    = $model->getOrderId($changeid, $orderinfo);

        return $rs;
    }

    public function record($uid, $page)
    {
        $page_total = 20;
        $page       = ($page - 1) * $page_total;
        return DI()->notorm->charge_user
            ->select('addtime,status,money')
            ->where('uid = ?', $uid)
            ->limit($page, $page_total)
            ->order('id desc')
            ->fetchAll();
    }

}
