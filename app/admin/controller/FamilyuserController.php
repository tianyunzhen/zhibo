<?php

/**
 * 家族成员
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class FamilyuserController extends AdminbaseController
{

    protected function getState($k = '')
    {
        $status = [
            '1' => '正常',
            '2' => '退出审核中',
            '3' => '已退出',
        ];
        if ($k === '') {
            return $status;
        }

        return isset($status[$k]) ? $status[$k] : '';
    }

    protected function getFamily($k = '')
    {
        $list = Db::name('family')
            ->where('state=2')
            ->order("id desc")
            ->column('*', 'id');

        if ($k === '') {
            return $list;
        }

        return isset($list[$k]) ? $list[$k] : '';
    }

    function index()
    {
        $data  = $this->request->param();
        $familyId = $data['family_id'] ?? 0;
        $uid = $data['uid'] ?? 0;

        $start_time = $data['start_time'] ?? '';
        $end_time   = $data['end_time'] ?? '';
        if (!$familyId) {
            $this->error("请填写家族id");
        }
        $map = [
            ['fu.familyid', '=', $familyId],
        ];
        $sWhere = [
            ['starttime', '>', 0]
        ];
        $tWhere = [];
        if ($start_time) {
            $sWhere[] = ['time', '>=', $start_time];
            $tWhere[] = ['addtime', '>=', strtotime($start_time)];
        }
        if ($end_time) {
            $sWhere[] = ['time', '<=', $end_time];
            $tWhere[] = ['addtime', '<=', strtotime($end_time)];
        }
        if (!$start_time && !$end_time) {
            $sWhere[] = ['time', '=', date('Y-m-d')];
            $tWhere[] = ['addtime', '>=', strtotime(date('Y-m-d 00:00:00'))];
        }

        if ($uid) {
            $map[] = ['fu.uid', '=', $uid];
        }
        $lists = Db::name("family_user")
            ->alias('fu')
            ->field('fu.id,fu.uid,u.user_nicename as nickname,fu.is_admin,fu.addtime')
            ->leftJoin('user u','u.id=fu.uid')
            ->where($map)
            ->group('fu.uid')
            ->paginate(20);
        $lists->each(function ($v, $k) use($sWhere, $tWhere) {
            $sWhere[] = ['uid', '=', $v['uid']];
            $live_length = Db::name("live_record")->where($sWhere)->field('sum(endtime-starttime) as live_length')->select()->toArray();
            $live_length = $live_length[0]['live_length'] ?? 0;
            $tWhere[] = ['touid', '=', $v['uid']];
            $v['total_profit'] = Db::name("gift_record")->where($tWhere)->sum('totalcoin') ?? 0;
            $hours = intval($live_length / 3600);
            $v['live_length'] = $hours . ":" . gmstrftime('%M:%S', $live_length);
            return $v;
        });
        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);
        $this->assign("state", $this->getState());
        $this->assign("family_id", $familyId);

        return $this->fetch();
    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');

        $rs = DB::name('family_user')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $action = "删除家族成员：{$id}";
        setAdminLog($action);
        $this->success("踢除成功！", url('familyuser/index'));
    }

    function add()
    {
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();

            $uid = $data['uid'];

            if ($uid == "") {
                $this->error("请填写用户ID");
            }

            $isexist = DB::name('user')->where(["id" => $uid, "user_type" => 2])
                ->value('id');
            if (!$isexist) {
                $this->error("该用户不存在");
            }

            $isfamily = DB::name('family')->where(["uid" => $uid])->find();
            if ($isfamily) {
                $this->error("该用户已是家族长");
            }

            $isexist = DB::name('family_user')->where(["uid" => $uid])->find();
            if ($isexist) {
                $this->error('该用户已申请家族');
            }


            $familyid = $data['familyid'];
            if ($familyid == "") {
                $this->error("请填写家族ID");
            }
            $family = DB::name("family")->where(["id" => $familyid])->find();
            if (!$family) {
                $this->error('该家族不存在');
            }

            if ($family['state'] != 2) {
                $this->error('该家族未通过审核');
            }

            $data['state']   = 2;
            $data['addtime'] = time();
            $data['uptime']  = time();

            $id = DB::name('family_user')->insertGetId($data);
            if (!$id) {
                $this->error("添加失败！");
            }

            $action = "添加家族成员：{$uid}";
            setAdminLog($action);
            $this->success("添加成功！");
        }

    }


    function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        $data = Db::name('family_user')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }

        $userinfo = getUserInfo($data['uid']);

        $family = Db::name("family")->field("name,divide_family")
            ->where(["id" => $data['familyid']])->find();

        $this->assign('data', $data);
        $this->assign('family', $family);
        $this->assign('userinfo', $userinfo);
        $this->assign('state', $this->getState());
        return $this->fetch();

    }

    function editPost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();

            $data['uptime'] = time();

            $rs = DB::name('family_user')->update($data);
            if ($rs === false) {
                $this->error("修改失败！");
            }

            $action = "修改家族成员信息：{$data['uid']}";
            setAdminLog($action);

            $this->success("修改成功！");
        }
    }

    function outIndex()
    {
        $data  = $this->request->param();
        $map   = [];
        $map[] = ['fu.state', '=', 2];
        $uid = isset($data['uid']) ? $data['uid'] : 0;
        if ($uid) {
            $map[] = ['fu.uid', '=', $uid];
        }
        $familyID = isset($data['familyid']) ? $data['familyid'] : 0;
        if ($familyID) {
            $map[] = ['fu.familyid', '=', $familyID];
        }
        $lists = Db::name("family_user")
            ->alias('fu')
            ->field('fu.id,f.id as family_id,fu.uid,u1.user_nicename as applier,f.uid as family_admin_id,u2.user_nicename as family_admin,f.name,fu.addtime,fu.out_time')
            ->leftJoin('family f','fu.familyid=f.id')
            ->leftJoin('user u1', 'fu.uid=u1.id')
            ->leftJoin('user u2', 'f.uid=u2.id')
            ->where($map)
            ->paginate(20);

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        return $this->fetch();
    }

    function out()
    {
        $id = $this->request->param('id', 0, 'intval');
        $state = $this->request->param('state', 0, 'intval');
        if ($state == 3)  {
            $result = Db::name("family_user")
                ->where(['id' => $id, 'state' => 2])
                ->update(['state' => 3]);
        } else {
            $result = Db::name("family_user")
                ->where(['id' => $id, 'state' => 2])
                ->update(['state' => $state]);
        }
        if ($result === false) {
            $this->error("操作失败！", url('familyuser/outindex'));
        }
        $this->success("操作成功！", url('familyuser/outindex'));
    }

}