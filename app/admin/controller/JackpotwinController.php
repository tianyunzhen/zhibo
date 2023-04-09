<?php

/**
 * 奖池设置
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class JackpotwinController extends AdminbaseController
{
    function indexV2()
    {
        $giftId = $this->request->param('giftid', 0, 'intval');
        $lists  = Db::name("gift")
            ->where(['id' => $giftId])->value('info');
        $lists  = json_decode($lists, true);
        $totalTimes = 0;
        $newList = [];
        foreach ($lists as $k =>$v) {
            $newList[$k] = array_count_values($v);
            $totalTimes += array_sum($v);
        }
        $this->assign('lists', $newList);
        $this->assign('gift_id', $giftId);
        $this->assign('total_times', $totalTimes);

        return $this->fetch('index_new');

    }

    function editPostV2()
    {
        if ($this->request->isPost()) {
            $params = $this->request->param();
            $times  = $params['info'];
            $num    = $params['num'];
            $tem    = '';
            foreach ($times as $k => $time) {
                $tem .= str_repeat($time . ',', (int)$num[$k]);
            }
            $tem    = rtrim($tem, ',');
            $info   = explode(',', $tem);
            $update = Db::name("gift")
                ->where(['id' => $params['id']])
                ->update(['info' => json_encode($info)]);
            if ($update === false) {
                $this->error("信息错误");
            }
            $this->success("修改成功！", url('jackpotwin/index'));
        }
    }

    function index()
    {
        $giftId = $this->request->param('giftid', 0, 'intval');
        $lists  = Db::name("gift")
            ->where(['id' => $giftId])->field('max_num,info')->find();
        $max_num = $lists['max_num'];
        $lists  = json_decode($lists['info'], true);
//        $result = array_count_values($lists);
//        $totalTimes = 0;
//        foreach ($result as $k => $v) {
//            $totalTimes += $k * $v;
//        }

        $arr = [];
        foreach ($lists as $k => $v) {
            $arr[$k] = implode(',', $v);
        }
        $this->assign('lists', $arr);
        $this->assign('gift_id', $giftId);
        $this->assign('max_num', $max_num);

        return $this->fetch();

    }

//    function editPost()
//    {
//        if ($this->request->isPost()) {
//            $params = $this->request->param();
//            $times  = $params['info'];
//            $num    = $params['num'];
//            $tem    = '';
//            foreach ($times as $k => $time) {
//                $tem .= str_repeat($time . ',', (int)$num[$k]);
//            }
//            $tem    = rtrim($tem, ',');
//            $info   = explode(',', $tem);
//            $update = Db::name("gift")
//                ->where(['id' => $params['id']])
//                ->update(['info' => json_encode($info)]);
//            if ($update === false) {
//                $this->error("信息错误");
//            }
//            $this->success("修改成功！", url('jackpotwin/index'));
//        }
//    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $params = $this->request->param();
            $times  = $params['info'];
            $num    = $params['num'];
            $max = $params['max_num'];
            $arr = [];
            $true_max = explode('-', $times[count($times) - 1])[1];
            if ($true_max != $max) {
                $this->error("奖池周期有误！");
            }
           foreach ($times as $k => $time) {
               $arr[$time] = explode(',', $num[$k]);
           }
            $update = Db::name("gift")
                ->where(['id' => $params['id']])
                ->update(['info' => json_encode($arr), 'max_num' => $max]);
            if ($update === false) {
                $this->error("信息错误");
            }
            $giftId = $params['id'];
            $arrPool = getKeys("jackPot:jack_pool_" . $giftId . "_*");
            delcache("jackPot:jack_pot_jsq_$giftId");
            foreach ($arrPool as $value) {
                delcache($value);
            }
            $this->success("修改成功！", url('jackpotwin/index'));
        }
    }
}
