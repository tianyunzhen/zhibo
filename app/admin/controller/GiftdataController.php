<?php

/**
 * 礼物统计
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class GiftdataController extends AdminbaseController
{
    protected function getTypes($k = '')
    {
        $type = [
            '0' => '普通礼物',
            '1' => '豪华礼物',
        ];
        if ($k == '') {
            return $type;
        }
        return isset($type[$k]) ? $type[$k] : '';
    }

    protected function getMark($k = '')
    {
        $mark = [
            '0' => '普通',
            '1' => '热门',
            '2' => '守护',
            '3' => '幸运',
        ];
        if ($k == '') {
            return $mark;
        }
        return isset($mark[$k]) ? $mark[$k] : '';
    }

    function index()
    {
//        $lists = Db::name("gift")
//            ->alias('g')
//            ->leftJoin('gift_record r', 'g.id=r.gift_id')
//            ->leftJoin('jackpot_record j', 'j.gift_id=g.id')
//            ->field('g.id,sum(giftcount) as send_num,sum(totalcoin) as spend_coin,type,needcoin,sum(money) as win_prize,giftname,sum(multiple) as total_multiple')
//            ->group('g.id')
//            ->paginate(20);
////    	var_dump($lists);die;
//        $lists->each(function ($v, $k) {
//            $win_man      = Db::name('jackpot_record')
////                ->field('uid,count(*) as win_man')
//                ->where(['gift_id' => $v['id']])
//                ->group('uid')->count();
//            $v['win_man'] = $win_man ?? 0;
//            return $v;
//        });
        $lists = [];
//        $page = $lists->render();

        $this->assign('lists', $lists);

//        $this->assign("page", $page);

        $this->assign("type", $this->getTypes());

        return $this->fetch();
    }

}
