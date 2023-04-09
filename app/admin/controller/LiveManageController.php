<?php

/**
 * 直播列表
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LiveManageController extends AdminbaseController
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
        $data     = $this->request->param();
        $map = [];
        $uid = isset($data['uid']) ? $data['uid'] : '';
        if ($uid) {
            $map[] = ['uid', '=', $uid];
        }
        $lists = Db::name("live_hot")
            ->alias('h')
            ->field('l.uid,u.user_nicename,')
            ->join('user u', 'h.uid=u.id')
            ->where($map)
            ->order("starttime DESC")
            ->paginate(20);

        $lists->each(function ($v, $k) {
            $where = [
                'action' => 6,
                'touid' => $v['uid'],
                'showid' => $v['showid'],
            ];
            /* 本场收益 */
            $totalcoin = Db::name("user_coinrecord")->where($where)
                ->sum('totalcoin') ?? 0;
            /* 打赏观众 */
            $total_nums = Db::name("user_coinrecord")->where($where)
                ->group("uid")->count();
            if (!$total_nums) {
                $total_nums = 0;
            }
            /* 人均打赏 */
            $total_average = 0;
            if ($totalcoin && $total_nums) {
                $total_average = round($totalcoin / $total_nums, 2);
            }
            /* 累计观众人数 */
            $nums = zSize('user_' . $v['stream']);

            $v['totalcoin']     = $totalcoin;
            $v['total_nums']    = $total_nums;
            $v['total_average'] = $total_average;
            $v['nums']          = $nums;
            $v['live_nums']     = 0;//当前观众

            if ($v['isvideo'] == 0 && $this->configpri['cdn_switch'] != 5) {
                $v['pull'] = PrivateKeyA('rtmp', $v['stream'], 0);
            }
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        $this->assign("liveclass", $this->getLiveClass());

        $this->assign("type", $this->getTypes());

        return $this->fetch();
    }

    function del()
    {
        $uid = $this->request->param('uid', 0, 'intval');

        $rs = DB::name('live')->where("uid={$uid}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $this->success("删除成功！", url("liveing/index"));
    }

    function add()
    {
        $this->assign("liveclass", $this->getLiveClass());

        $this->assign("type", $this->getTypes());

        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();

            $nowtime = time();
            $uid     = $data['uid'];

            $userinfo = DB::name('user')->field("ishot,isrecommend")
                ->where(["id" => $uid])->find();
            if (!$userinfo) {
                $this->error('用户不存在');
            }

            $liveinfo = DB::name('live')->field('uid,islive')
                ->where(["uid" => $uid])->find();
            if ($liveinfo['islive'] == 1) {
                $this->error('该用户正在直播');
            }

            $pull        = urldecode($data['pull']);
            $type        = $data['type'];
            $type_val    = $data['type_val'];
            $anyway      = $data['anyway'];
            $liveclassid = $data['liveclassid'];
            $stream      = $uid . '_' . $nowtime;
            $title       = '';

            $data2 = [
                "uid"         => $uid,
                "ishot"       => $userinfo['ishot'],
                "isrecommend" => $userinfo['isrecommend'],

                "showid"      => $nowtime,
                "starttime"   => $nowtime,
                "title"       => $title,
                "province"    => '',
                "city"        => '好像在火星',
                "stream"      => $stream,
                "thumb"       => '',
                "pull"        => $pull,
                "lng"         => '',
                "lat"         => '',
                "type"        => $type,
                "type_val"    => $type_val,
                "isvideo"     => 1,
                "islive"      => 1,
                "anyway"      => $anyway,
                "liveclassid" => $liveclassid,
            ];

            if ($liveinfo) {
                $rs = DB::name('live')->update($data2);
            } else {
                $rs = DB::name('live')->insertGetId($data2);
            }

            if ($rs !== false) {
                $this->error("添加失败！");
            }

            $this->success("添加成功！");
        }
    }

    function edit()
    {
        $uid = $this->request->param('uid', 0, 'intval');

        $data = Db::name('live')
            ->where("uid={$uid}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }

        $this->assign('data', $data);

        $this->assign("liveclass", $this->getLiveClass());

        $this->assign("type", $this->getTypes());

        return $this->fetch();


    }

    function editPost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();

            $data['pull'] = urldecode($data['pull']);

            $rs = DB::name('live')->update($data);
            if ($rs === false) {
                $this->error("修改失败！");
            }

            $this->success("修改成功！");
        }
    }

    function change()
    {
        $uid  = $this->request->param('id', 0, 'intval');
        $data = Db::name('live')
            ->field('uid,thumb')
            ->where("uid={$uid}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }
        $this->assign('data', $data);
        return $this->fetch();
    }

    function changePost()
    {
        $uid    = $this->request->param('uid', 0);
        $thumb  = $this->request->param('thumb', '');
        $update = Db::name('live')
            ->where("uid={$uid}")
            ->update(['thumb' => $thumb]);
        if ($update === false) {
            $this->error("修改失败");
        }
        $this->success("修改成功！", url('liveing/index'));
    }

}
