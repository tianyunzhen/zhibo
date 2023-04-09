<?php

/**
 * 直播列表
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LiveingController extends AdminbaseController
{
    const SP = 1000000;
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
        $map      = [
            ['islive', '=', 1],
            ['is_black', '=', 0]
        ];
        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time   = isset($data['end_time']) ? $data['end_time'] : '';

        if ($start_time) {
            $map[] = ['starttime', '>=', strtotime($start_time)];
        }

        if ($end_time) {
            $map[] = ['starttime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }

        $uid = isset($data['uid']) ? $data['uid'] : '';
        if ($uid) {
            $map[] = ['l.uid', '=', $uid];
        }

        $this->configpri = getConfigPri();
        $lists = Db::name("live")
            ->alias('l')
            ->field('l.uid,l.thumb,showid,f.name as family_name,f.id as family_id,
            u.user_nicename,starttime,deviceinfo,isvideo,live_weight,net_hotvotes,(cast(net_hotvotes as signed) + live_weight) as s,stream')
            ->join('user u', 'l.uid=u.id')
            ->leftJoin('family_user fu', 'fu.uid=l.uid')
            ->leftJoin('family f', 'fu.familyid=f.id')
            ->where($map)
            ->order("s DESC")->paginate(20);

        $lists->each(function ($v, $k) {
            $v['thumb'] = get_upload_path($v['thumb']);
            /* 打赏观众 */
            $v['total_nums'] =  sCard("live:sendGift:" . $v['stream'] . "_users") ?? 0;
            /* 累计观众人数 */
            $v['nums'] =  sCard("live:audience:info_" . $v['stream']) ?? 0;
            //当前观众
            $v['live_nums'] = zSize("live:user_now:" . $v['stream']) ?? 0;
            $tem = $v['net_hotvotes'] + $v['live_weight'];
            $v['p'] = (string) round($tem / self::SP, 2);
            $v['net_hotvotes'] = (string) round($v['net_hotvotes'] /  self::SP, 2);
            $v['live_weight'] = (string) round($v['live_weight'] /  self::SP, 2);
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

    function weight()
    {
        $uid = $this->request->param('uid', 0, 'intval');
        $this->assign('uid', $uid);
        return $this->fetch();
    }

    function addWeight()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $uid = $data['uid'] ?? 0;
            $live_weight = $data['live_weight'] ?? 0;
            if (!$uid || !$live_weight) {
                $this->error("缺少必要参数！");
            }
            $update = Db::name('user')->where(['id' => $uid])->update(['live_weight' => $live_weight * self::SP]);
            if ($update === false) {
                $this->error("操作失败！");
            }
            $this->success("操作成功！");
        }
    }

}
