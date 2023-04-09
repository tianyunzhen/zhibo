<?php

/**
 * 活动奖励管理
 */

namespace app\admin\controller;

use app\common\Message;
use cmf\controller\AdminBaseController;
use think\Db;

class RankinguserController extends AdminbaseController
{
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

    protected function getRemark()
    {
         return Db::name("remark")
            ->field('id,auth_desc')
            ->select()->toArray();
    }


    function index()
    {
        $data  = $this->request->param();
        $start_time = $data['start_time'] ?? '';
        $end_time = $data['end_time'] ?? '';
        $map = [
            ['type', 'in', [1,4]],
        ];
        if (!$start_time && !$end_time) {
            $periods = strtotime(date("Y-m-d 00:00:00", strtotime("-1 day")));
            $map[] = ['periods', '=', $periods];
        } else {
            if ($start_time) {
                $map[] = ['periods', '>=', strtotime($start_time)];
            }
            if ($end_time) {
                $map[] = ['periods', '<=', strtotime($end_time)];
            }
        }

        $state = $data['status'] ?? 0;
        if ($state) {
            $map[] = ['status', '=', $state];
        }
        $uid = $data['uid'] ?? 0;
        if ($uid) {
            $map[] = ['r.uid', '=', $uid];
        }
        $remarkId = $data['remark_id'] ?? 0;
        if ($remarkId) {
            $map[] = ['remark_id', '=', $remarkId];
        }
        $lists = Db::name("ranking_user")
            ->field('r.id,r.uid,u.user_nicename,no,r.money,water,times,r.status,periods,gear,r.type,upd_time,auth_desc')
            ->alias('r')
            ->leftJoin('user u', 'r.uid=u.id')
            ->leftJoin('user_remark ur', 'r.uid=ur.uid')
            ->leftJoin('remark m', 'ur.remark_id=m.id')
            ->where($map)
            ->order("water DESC")
            ->paginate(20);
        $totalCoin = Db::name("ranking_user")
                ->alias('r')
                ->leftJoin('user_remark ur', 'r.uid=ur.uid')
                ->where($map)->sum('money') ?? 0;
        $taskMap = $map;
        $taskMap[] = ['type', '=', 4];
        $totalTask = Db::name("ranking_user")
                ->alias('r')
                ->leftJoin('user_remark ur', 'r.uid=ur.uid')
                ->where($taskMap)->sum('money') ?? 0;
        $totalTaskPerson = Db::name("ranking_user")
                ->alias('r')
                ->leftJoin('user_remark ur', 'r.uid=ur.uid')
                ->where($taskMap)->field('r.uid')->select()->toArray() ?? [];
        $totalTaskPerson = count(array_unique(array_column($totalTaskPerson, 'uid')));
        $totalTask = (string) round($totalTask / 100, 2);
        $totalCoin = (string) round($totalCoin / 100, 2);
        $lists->each(function ($v, $k) {
            $v['hours'] = (string) round($v['times'] / 3600, 2);
            $hours = intval( $v['times'] / 3600);
            $v['money'] = (string) round($v['money'] / 100, 2);
            $v['times'] = $hours . ":" . gmstrftime('%M:%S',  $v['times']);
            if ($v['type'] == 4) {
                $v['gear'] = '任务奖励';
            }
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);
        $this->assign("page", $page);
        $this->assign("status", $this->getState());
        $this->assign("total_coin", $totalCoin);
        $this->assign("total_task", $totalTask);
        $this->assign("total_task_person", $totalTaskPerson);
        $this->assign('remark', $this->getRemark());

        return $this->fetch();
    }

    /** 发放 */
    function grant()
    {
        $id = $this->request->param('id', 0, 'intval');
        $ranking = DB::name('ranking_user')->where("id={$id}")->field('uid,money')->find();
        if (!$ranking) {
            $this->error("奖励不存在！");
        }
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
            $this->success("操作成功！", url('rankinguser/index'), '', 1);
        }
        Db::startTrans();
        try {
            DB::name('user')->where(['id' => $ranking['uid']])->setInc('votes', $ranking['money']);
            $data = [
                'status' => 1,
                'upd_time' => time()
            ];
            DB::name('ranking_user')->where("id={$id}")->update($data);
            $inserData = [
                'uid' => $ranking['uid'],
                'type' => 1,
                'action' => 6,
                'votes' => $ranking['money'],
                'addtime' => time()
            ];
            Db::name('user_voterecord')->insert($inserData);
            Db::commit();
            $actualMoney = (string) round($ranking['money'] / 100, 2);
            Message::addMsg('排位赛奖励', "（主播扶持）波鸭官方打款" . $actualMoney . "丫粮已到账", $ranking['uid']);
            $this->success("发放成功！", url('rankinguser/index'), '', 1);
        } catch (\Exception $e) {
            Db::rollback();
            if ($e->getCode() || $e->getMessage()) {
                $this->error("发放失败！", url('rankinguser/index'), '', 1);
            }
        }
    }
}