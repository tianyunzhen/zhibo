<?php

/**
 * 活动奖励管理
 */

namespace app\admin\controller;

use app\common\Message;
use cmf\controller\AdminBaseController;
use think\Db;

class RankingfamilyController extends AdminbaseController
{
    protected function getType($k = '')
    {
        $type = [
            '2' => '家族日',
            '3' => '家族周',
        ];
        if ($k == '') {
            return $type;
        }
        return isset($type[$k]) ? $type[$k] : '';
    }

    protected function getState($k = '')
    {
        $status = [
            '1' => '已发放',
            '2' => '未发放',
            '3' => '未达成',
            '4' => '不予发放',
        ];
        if ($k === '') {
            return $status;
        }
        return isset($status[$k]) ? $status[$k] : '';
    }

    function index()
    {
        $data  = $this->request->param();
        $map = [];
        $state = $data['status'] ?? 0;
        if ($state) {
            $map[] = ['r.status', '=', $state];
        }
        $type = $data['type'] ?? 2;
        if ($type) {
            $map[] = ['r.type', '=', $type];
        }
        $start_time = $data['start_time'] ?? '';
        $end_time = $data['end_time'] ?? '';
        if ($type == 2) {
            $periods = strtotime(date("Y-m-d 00:00:00",strtotime("-1 day")));
        }
        if ($type == 3) {
            $periods = strtotime(date('Y-m-d 00:00:00', strtotime('-1 monday')));
        }
        if (!$start_time && !$end_time) {
            $map[] = ['periods', '=', $periods];
        } else {
            if ($start_time) {
                $map[] = ['periods', '>=', strtotime($start_time)];
            }
            if ($end_time) {
                $map[] = ['periods', '<=', strtotime($end_time)];
            }
        }

        $lists = Db::name("ranking_user")
            ->field('r.id,r.family_id,f.uid,u.user_nicename,f.name as family_name,no,r.money,r.periods,r.type,water,r.status,mans,periods,gear,r.upd_time')
            ->alias('r')
            ->leftjoin('family f', 'r.family_id=f.id')
            ->leftJoin('user u', 'f.uid=u.id')
            ->where($map)
            ->order("water DESC")
            ->paginate(20);
        $totalCoin = Db::name("ranking_user")
                ->alias('r')
                ->where($map)->sum('money') ?? 0;
        $lists->each(function ($v, $k) {
            if (!$v['gear']) {
                $v['gear'] = '未上榜';
            }
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);
        $this->assign("page", $page);
        $this->assign("status", $this->getState());
        $this->assign("type", $this->getType());
        $this->assign("total_coin", $totalCoin);
        return $this->fetch();
    }

    /** 发放 */
    function grant()
    {
        $id = $this->request->param('id', 0, 'intval');
        $type = $this->request->param('type', 0, 'intval');
        if (!$type || !$id) {
            $this->error("操作不当！");
        }
        if ($type == 2) {
            $data = [
                'status' => 4,
                'upd_time' => time()
            ];
            DB::name('ranking_user')->where("id={$id}")->update($data);
            $this->success("操作成功！", url('rankingfamily/index'), '', 1);
        }
        $ranking = DB::name('ranking_user')
            ->alias('r')
            ->leftJoin('family f', 'r.family_id=f.id')
            ->where("r.id={$id}")
            ->field('f.uid,money')
            ->find();
        if (!$ranking) {
            $this->error("奖励不存在！");
        }
        Db::startTrans();
        try {
            DB::name('user')->where(['id' => $ranking['uid']])->setInc('coin', $ranking['money']);
            $data = [
                'status' => 1,
                'upd_time' => time()
            ];
            DB::name('ranking_user')->where("id={$id}")->update($data);
            $inserData = [
                'uid' => $ranking['uid'],
                'type' => 1,
                'action' => 9,
                'totalcoin' => $ranking['money'],
                'addtime' => time()
            ];
            Db::name('user_coinrecord')->insert($inserData);
            Db::commit();
            Message::addMsg('排位赛奖励', "（家族长奖励）波鸭官方打款" . $ranking['money'] . "丫币已到账", $ranking['uid']);
            $this->success("发放成功！", url('rankingfamily/index'), '', 1);
        } catch (\Exception $e) {
            Db::rollback();
            if ($e->getCode() || $e->getMessage()) {
                $this->error("发放失败！", url('rankingfamily/index'), '', 1);
            }
        }
    }
}