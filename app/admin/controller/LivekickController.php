<?php

/**
 * 踢人列表
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LivekickController extends AdminbaseController
{
    function index()
    {
        $data = $this->request->param();
        $map  = [];

        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time   = isset($data['end_time']) ? $data['end_time'] : '';

        if ($start_time != "") {
            $map[] = ['addtime', '>=', strtotime($start_time)];
        }

        if ($end_time != "") {
            $map[] = ['addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }

        $uid = isset($data['uid']) ? $data['uid'] : '';
        if ($uid != '') {
            $lianguid = getLianguser($uid);
            if ($lianguid) {
                $map[] = [
                    'uid|liveuid|actionid',
                    ['=', $uid],
                    ['in', $lianguid],
                    'or',
                ];
            } else {
                $map[] = ['uid|liveuid|actionid', '=', $uid];
            }
        }

        $lists = Db::name("live_kick")
            ->where($map)
            ->order("addtime DESC")
            ->paginate(20);

        $lists->each(function ($v, $k) {
            $v['uidinfo']    = getUserInfo($v['uid']);
            $v['liveinfo']   = getUserInfo($v['liveuid']);
            $v['actioninfo'] = getUserInfo($v['actionid']);
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        return $this->fetch();
    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');

        $rs = DB::name('live_kick')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }

        $this->success("删除成功！", url("livekick/index"));
    }

}
