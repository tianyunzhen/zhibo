<?php

/**
 * 直播记录
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LiverecordController extends AdminbaseController
{
    function index()
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

        if (!$start_time && !$end_time) {
            $map[] = ['time', '=', date('Y-m-d')];
        }
        $uid = isset($data['uid']) ? $data['uid'] : '';
        if ($uid != '') {
            $map[] = ['uid', '=', $uid];
        }

        $lists = Db::name("live_record")
            ->alias('l')
            ->field('uid,u.user_nicename,stream,starttime,endtime,live_nums,gift_sender_num,l.votes')
            ->join('user u', 'l.uid=u.id')
            ->where($map)
            ->order("l.id DESC")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $where = [
                ['touid', '=', $v['uid']],
                ['addtime', '>=', $v['starttime']],
                ['addtime', '<=', $v['endtime']],
            ];
            $v['new_fans_num'] = Db::name("user_attention")
                ->where($where)->count() ?? 0;
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
        $this->assign("config", $config);

        return $this->fetch();
    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');

        $rs = DB::name('live_record')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }

        $this->success("删除成功！", url("liverecord/index"));
    }

}
