<?php

/**
 * 排位赛规则
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class RankingController extends AdminbaseController
{
    protected function getType($k = '')
    {
        $type = [
            '1' => '主播',
            '2' => '家族日',
            '3' => '家族周',
        ];
        if ($k == '') {
            return $type;
        }
        return isset($type[$k]) ? $type[$k] : '';
    }

    function index()
    {
        $lists = Db::name("ranking_level")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            return $v;
        });
        $page = $lists->render();

        $this->assign('lists', $lists);
        $this->assign("type", $this->getType());
        $this->assign("page", $page);

        return $this->fetch();
    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');

        $rs = DB::name('ranking_level')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $action = "删除排位赛规则：{$id}";
        setAdminLog($action);
        $this->success("删除成功！");

    }

    function add()
    {
        $this->assign("type", $this->getType());
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            if (!$data['title']) {
                $this->error("请填写活动名称");
            }
            if (!$data['type']) {
                $this->error("请填写banner图");
            }
            if (!$data['no']) {
                $this->error("请填写活动链接");
            }
            if (!$data['money']) {
                $this->error("请填写活动时间");
            }
            if (!$data['min']) {
                $this->error("请填写活动时间");
            }
            if (!$data['max']) {
                $this->error("请填写活动时间");
            }
            $data['add_time']   = time();
            if ($data['type'] != 1) {
                $data['money'] = $data['money'] * 100;//钻石放大100
            }
            $id = DB::name('ranking_level')->insertGetId($data);
            if (!$id) {
                $this->error("添加失败！");
            }
            $action = "添加排位赛规则：{$id}";
            setAdminLog($action);
            $this->success("添加成功！");
        }
    }

    function edit()
    {
        $id   = $this->request->param('id', 0, 'intval');
        $data = Db::name('ranking_level')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }
        if ($data['type'] != 1) {
            $data['money'] = (string) round($data['money'] / 100, 2);
        }
        $this->assign("type", $this->getType());
        $this->assign('data', $data);
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            if (!$data['title']) {
                $this->error("请填写活动名称");
            }
            if (!$data['type']) {
                $this->error("请填写banner图");
            }
            if (!$data['no']) {
                $this->error("请填写活动链接");
            }
            if (!$data['money']) {
                $this->error("请填写活动时间");
            }
            if (!$data['min']) {
                $this->error("请填写活动链接");
            }
            if (!$data['max']) {
                $this->error("请填写活动时间");
            }
            if ($data['type'] != 1) {
                $data['money'] = $data['money'] * 100;//钻石放大100
            }
            $data['up_time'] = time();
            $rs = DB::name('ranking_level')->update($data);
            if ($rs === false) {
                $this->error("修改失败！");
            }
            $action = "修改活动：{$data['id']}";
            setAdminLog($action);
            $this->success("修改成功！", url('ranking/index'));
        }
    }
}
