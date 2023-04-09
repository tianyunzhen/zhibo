<?php

/**
 * 消费记录
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class VoterController extends AdminbaseController
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
            '1'  => '收礼物',
            '2'  => '提现',
            '3'  => '兑换',
            '4'  => '家族分成',
            '5'  => '提现',
            '6'  => '提现驳回',
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
            $map[] = ['v.addtime', '>=', strtotime($start_time)];
        }

        if ($end_time) {
            $map[] = ['v.addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }

        if (!$start_time && !$end_time) {
            $today = strtotime(date('Y-m-d 00:00:00', time()));
            $map[] = ['v.addtime', '>=', $today];
        }

        if (isset($data['type'])) {
            $map[] = ['v.type', '=', $data['type']];
        }

        $action = $data['action'] ?? 0;
        if ($action) {
            $map[] = ['v.action', '=', $action];
        }

        $uid = $data['uid'] ?? 0;
        if ($uid) {
            $map[] = ['uid|fromid', '=', $uid];
        }

        $giftId = $data['giftid'] ?? 0;
        if ($giftId) {
            $map[] = ['giftid', '=', $giftId];
        }

        $lists = Db::name("user_voterecord")
            ->alias('v')
            ->field('v.id,v.type,v.action,g.giftname,uid,fromid,v.votes,v.addtime')
            ->leftJoin('gift g', 'v.giftid=g.id')
            ->where($map)
            ->order('v.addtime desc')
            ->paginate(20);
        $in = $out =0;
        $lists->each(function ($v, $k) use (&$in, &$out){
            if ($v['type']) {
                $in += $v['votes'];
            } else {
                $out += $v['votes'];
            }
//            $v['remain_voter'] = 0;
            $v['votes'] = (string) round($v['votes'] / 100, 2);
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
