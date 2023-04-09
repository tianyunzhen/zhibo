<?php

/**
 * 直播列表
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class DataController extends AdminbaseController
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

    function index()
    {
        $data       = $this->request->param();
        $map        = [];
        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time   = isset($data['end_time']) ? $data['end_time'] : '';

        if ($start_time != "") {
            $map[] = ['starttime', '>=', strtotime($start_time)];
        }

        if ($end_time != "") {
            $map[] = ['starttime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }

        $uid = isset($data['uid']) ? $data['uid'] : '';
        if ($uid != '') {
            $map[] = ['r.uid', '=', $uid];
        }

        $this->configpri = getConfigPri();

        $map[] = ['f.state', '=', 1];
        $lists = Db::name("live_record")
            ->alias('r')
            ->field('r.uid,sum(r.votes) as profit,u.user_nicename,f.familyid,sum(endtime-starttime) as live_length')
            ->join('user u', 'r.uid=u.id')
            ->leftJoin('family_user f', 'r.uid=f.uid')
            ->where($map)
            ->group('r.uid')
            ->paginate(20);

        $lists->each(function ($v, $k) {
            $v['live_length'] = ceil($v['live_length'] / 60);
            $v['profit'] = (string) round($v['profit'] / 100, 2);
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        return $this->fetch();
    }

    function record()
    {
        $config = getConfigPub();

        $data = $this->request->param();
        $map  = [];

        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time   = isset($data['end_time']) ? $data['end_time'] : '';

        if ($start_time != "") {
            $map[] = ['starttime', '>=', strtotime($start_time)];
        }

        if ($end_time != "") {
            $map[] = ['starttime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }

        $uid = isset($data['uid']) ? $data['uid'] : '';
        if ($uid != '') {
            $map[] = ['r.uid', '=', $uid];
        }
//        $map[] = ['f.state', '=', 1];
        $lists = Db::name("live_record")
            ->alias('r')
            ->field('r.id,r.uid,u.user_nicename,showid,stream,r.votes,starttime,endtime,familyid')
            ->join('user u', 'r.uid=u.id')
            ->leftJoin('family_user f', 'r.uid=f.uid')
            ->where($map)
            ->order("id DESC")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $live_length = $v['endtime'] - $v['starttime'];
            $hours = intval($live_length / 3600);
            $v['live_length'] = $hours . ":" . gmstrftime('%M:%S', $live_length);
            $v['votes'] = (string) round($v['votes'] / 100, 2);
            return $v;
        });
        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);
        $this->assign("page", $page);

        return $this->fetch();
    }
}
