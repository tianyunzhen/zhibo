<?php

/**
 * 直播列表
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ListController extends AdminbaseController
{
    protected function getLiveClass()
    {
        $liveclass = Db::name("live_class")->order('list_order asc, id desc')
            ->column('name', 'id');

        $list = [
            '0' => '默认分类',
        ];

        $liveclass = $list + $liveclass;
        return $liveclass;
    }

    protected function getTypes($k = '')
    {
        $type = [
            '0' => '普通房间',
            '1' => '密码房间',
            '2' => '门票房间',
            '3' => '计时房间',
        ];

        if ($k == '') {
            return $type;
        }
        return $type[$k];
    }

    function wealth()
    {
        $where = [
            ['type', '=', 0]
        ];
        $this->commonTimeType($where, 'c');
        $lists = Db::name("user_coinrecord")
            ->alias('c')
            ->join('user u', 'u.id=c.uid')
            ->where($where)
            ->field('c.uid,sum(totalcoin) as total_coin,u.user_nicename')
            ->group('c.uid')
            ->order('total_coin desc')
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['no'] = $k + 1;
            return $v;
        });
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        return $this->fetch();
    }


    function charm()
    {
        $where = [
            ['type', '=', 1]
        ];
        $this->commonTimeType($where, 'v');
        $lists = Db::name("user_voterecord")
            ->alias('v')
            ->join('user u', 'u.id=v.uid')
            ->where($where)
            ->field('v.uid,sum(v.votes) as total_diamond,u.user_nicename')
            ->group('v.uid')
            ->order('total_diamond desc')
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['total_diamond'] = round($v['total_diamond'] / 100, 2);
            $v['no'] = $k + 1;
            return $v;
        });
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        return $this->fetch();
    }

    function union()
    {
        $where = [];
        $this->commonTimeType($where, 'p');
        $lists = Db::name("family_profit")
            ->where($where)
            ->alias('p')
            ->join('family f', 'p.familyid=f.id')
            ->join('user u', 'u.id=f.uid')
            ->field('f.uid,p.familyid,f.name as family_name,sum(p.profit_anthor) as total_profit,u.user_nicename')
            ->group('p.familyid')
            ->order('total_profit desc')
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['total_profit'] = round($v['total_profit'] / 100, 2);
            $v['no'] = $k + 1;
            return $v;
        });
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        return $this->fetch();
    }

    function detail()
    {
        $familyId = $data = $this->request->param('id');
        $lists    = Db::name("family_profit")
            ->alias('p')
            ->join('user u', 'p.uid=u.id')
            ->where(['familyid' => $familyId])
            ->field('p.uid,u.user_nicename,sum(profit_anthor) as total_profit')
            ->group('p.uid')
            ->order('total_profit desc')
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['no'] = $k + 1;
            return $v;
        });
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        return $this->fetch();
    }

    private function commonTimeType(&$where, $tag)
    {
        $whereTime = $tag . '.addtime';
        $params = $this->request->param();
        $timeType = $params['time_type'] ?? 0;
        $startTime = $params['start_time'] ?? 0;
        $endTime = $params['end_time'] ?? 0;
        if ($timeType == 1) {
            $today = date('Y-m-d 00:00:00', time());
            $todayTime = strtotime($today);
            $where[] = [$whereTime, '>', $todayTime];
        }
        if ($timeType == 2) {
            $today = date('Y-m-d 00:00:00', time());
            $todayTime = strtotime($today);
            $where[] = [$whereTime, '<', $todayTime];
            $where[] = [$whereTime, '>', $todayTime - 86400];
        }
        if ($timeType == 3) {
            $sdefaultDate = date("Y-m-d");
            $first = 1;
            $w = date('w', strtotime($sdefaultDate));
            $wStartTime = strtotime("$sdefaultDate -". ($w ? $w - $first : 6) .' days');
            $where[] = [$whereTime, '>', $wStartTime];
        }
        if ($timeType == 4) {
            $monthFirstTime = strtotime(date("Y-m-01 00:00:00",time()));
            $where[] = [$whereTime, '>', $monthFirstTime];
        }
        if ($startTime || $endTime) {
            $where[] = [$whereTime, '>', strtotime($startTime)];
            $where[] = [$whereTime, '<', strtotime($endTime)];
        }
    }
}
