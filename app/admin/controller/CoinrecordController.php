<?php

/**
 * 消费记录
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class CoinrecordController extends AdminbaseController
{

    protected function getTypes($k = '')
    {
        $type = [
            '0' => '支出',
            '1' => '收入',
        ];
        if ($k === '') {
            return $type;
        }

        return isset($type[$k]) ? $type[$k] : '';
    }

    protected function getAction($k = '')
    {
        $action = [
            '1'  => '官方直充',
            '2'  => '代理直充',
            '3'  => '任务',
            '4'  => '兑换',
            '5'  => '中奖',
            '6'  => '送礼',
            '7'  => '买靓号',
            '8'  => '手动充值',
        ];
        if ($k === '') {
            return $action;
        }

        return isset($action[$k]) ? $action[$k] : '未知';
    }

    function index()
    {
        $data = $this->request->param();
        $map  = [];
        $start_time = $data['start_time'] ?? '';
        $end_time   = $data['end_time'] ?? '';

        if ($start_time) {
            $map[] = ['c.addtime', '>=', strtotime($start_time)];
        }

        if ($end_time) {
            $map[] = ['c.addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }

        if (!$start_time && !$end_time) {
            $today = strtotime(date('Y-m-d 00:00:00', time()));
            $map[] = ['c.addtime', '>=', $today];
        }

        if (isset($data['type'])) {
            $map[] = ['c.type', '=', $data['type']];
        }

        $action = $data['action'] ?? 0;
        if ($action) {
            $map[] = ['c.action', '=', $action];
        }

        $uid = $data['uid'] ?? 0;
        if ($uid) {
            $map[] = ['uid|touid', '=', $uid];
        }
        $giftId = $data['giftid'] ?? 0;
        if ($giftId) {
            $map[] = ['giftid', '=', $giftId];
        }
        $lists = Db::name("user_coinrecord")
            ->alias('c')
            ->field('c.id,c.type,c.action,g.giftname,uid,touid,c.totalcoin,c.addtime')
            ->leftJoin('gift g', 'c.giftid=g.id')
            ->where($map)
            ->order('c.addtime desc')
            ->paginate(20);
        $in = $out =0;
        $lists->each(function ($v, $k) use(&$in, &$out){
            if ($v['type']) {
                $in += $v['totalcoin'];
            } else {
                $out += $v['totalcoin'];
            }
            $v['remain_coin'] = 0;
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        $this->assign('action', $this->getAction());
        $this->assign('type', $this->getTypes());
        $this->assign('gifts', getGiftList());
        $this->assign('in', $in);
        $this->assign('out', $out);

        return $this->fetch();

    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        $rs = DB::name('user_coinrecord')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $this->success("删除成功！");

    }
}
