<?php

/**
 * 广告管理
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class AdvertController extends AdminbaseController
{
    function index()
    {
        $lists = Db::name("advert")
            ->order("add_time desc")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['pic'] = get_upload_path($v['pic']);
            return $v;
        });

        $page = $lists->render();
        $this->assign('lists', $lists);
        $this->assign("page", $page);
        return $this->fetch();
    }

    function add()
    {
        $package = Db::name("package")->select();
        $this->assign("package", $package);
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $startTime = $data['start_time'] ?? '';
            $endTime = $data['end_time'] ?? '';
            $data['add_time'] = time();
            if ($startTime) {
                $data['start_time'] = strtotime($startTime);
            }
            if ($endTime) {
                $data['end_time'] = strtotime($endTime);
            }
            $result = Db::name('advert')->insert($data);
            if (!$result) {
                $this->error("添加失败！");
            }
            $this->success("操作成功！", url('advert/index'));
        }
    }

    function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        $data = Db::name('advert')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }
        $data['pic'] = get_upload_path($data['pic']);
        $package = Db::name("package")->select();
        $this->assign("package", $package);
        $this->assign('data', $data);
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $startTime = $data['start_time'] ?? '';
            $endTime = $data['end_time'] ?? '';
            $data['add_time'] = time();
            if ($startTime) {
                $data['start_time'] = strtotime($startTime);
            }
            if ($endTime) {
                $data['end_time'] = strtotime($endTime);
            }
            $result = Db::name('advert')->update($data);
            if (!$result) {
                $this->error("信息错误");
            }
            $this->success("操作成功！", url('advert/index'));
        }
    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        $rs = DB::name('advert')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $this->success("删除成功！");
    }
}
