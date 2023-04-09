<?php

/**
 * 坐骑管理
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ActivityController extends AdminbaseController
{
    protected function getStatus($k = '')
    {
        $status = [
            '0' => '关闭',
            '1' => '开启',
        ];
        if ($k == '') {
            return $status;
        }
        return isset($status[$k]) ? $status[$k] : '';
    }

    function index()
    {

        $lists = Db::name("activity")
            ->order("list_order asc")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['banner'] = get_upload_path($v['banner']);
            return $v;
        });
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        $this->assign("status", $this->getStatus());

        return $this->fetch();
    }

    function del()
    {

        $id = $this->request->param('id', 0, 'intval');

        $rs = DB::name('activity')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }

        $action = "删除活动：{$id}";
        setAdminLog($action);

        $this->resetcache();
        $this->success("删除成功！");

    }

    //排序
    public function listOrder()
    {

        $model = DB::name('activity');
        parent::listOrders($model);

        $action = "更新活动排序";
        setAdminLog($action);

        $this->resetcache();
        $this->success("排序更新成功！");

    }

    function add()
    {
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            if (!$data['name']) {
                $this->error("请填写活动名称");
            }
            if (!$data['banner']) {
                $this->error("请填写banner图");
            }
            if (!$data['link']) {
                $this->error("请填写活动链接");
            }
            if (!$data['start_time'] || !$data['end_time']) {
                $this->error("请填写活动时间");
            }
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time']   = strtotime($data['end_time']);
            $id                 = DB::name('activity')->insertGetId($data);
            if (!$id) {
                $this->error("添加失败！");
            }
            $action = "添加活动：{$id}";
            setAdminLog($action);
            $this->resetcache();
            $this->success("添加成功！");
        }
    }

    function edit()
    {
        $id   = $this->request->param('id', 0, 'intval');
        $data = Db::name('activity')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }
        $this->assign("status", $this->getStatus());
        $this->assign('data', $data);
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            if (!$data['name']) {
                $this->error("请填写活动名称");
            }
            if (!$data['banner']) {
                $this->error("请填写banner图");
            }

            if (!$data['link']) {
                $this->error("请填写活动链接");
            }
            if (!$data['start_time'] || !$data['end_time']) {
                $this->error("请填写活动时间");
            }
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time']   = strtotime($data['end_time']);
            $rs                 = DB::name('activity')->update($data);
            if ($rs === false) {
                $this->error("修改失败！");
            }
            $action = "修改活动：{$data['id']}";
            setAdminLog($action);
            $this->resetcache();
            $this->success("修改成功！", url('activity/index'));
        }
    }

    function resetcache()
    {
        $key           = 'admin:activity';
        $activity_list = DB::name("activity")->order("list_order asc")
            ->select();
        if ($activity_list) {
            setcaches($key, $activity_list);
        }
        return 1;
    }

    /* 上下架 */
    function on()
    {
        $id     = $this->request->param('id', 0, 'intval');
        $status = $this->request->param('status', 1, 'intval');

        $rs = DB::name('activity')->where("id={$id}")
            ->update(['status' => $status]);
        if (!$rs) {
            $this->error("操作失败！");
        }
        $action = "修改活动：{$id}";
        setAdminLog($action);
        $this->resetcache();
        $this->success("操作成功！");
    }
}
