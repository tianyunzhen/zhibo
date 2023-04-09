<?php

/**
 * 靓号管理
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class WordsController extends AdminbaseController
{

    protected function getStatus($k = '')
    {
        $status = [
            '1' => '正常',
            '2' => '停用',
        ];

        if ($k == '') {
            return $status;
        }

        return $status[$k];
    }

    function index()
    {
        $data = $this->request->param();
        $status = $data['status'] ?? 0;
        $map = [];
        if ($status) {
            $map[] = ['status', '=', $status];
        }
        $lists = Db::name("sensitive")
            ->where($map)
            ->order("id DESC")
            ->paginate(20);

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        $this->assign('status', $this->getStatus());

        return $this->fetch();
    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        $rs   = DB::name('sensitive')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $this->success("删除成功！", url("words/index"));
    }

    function add()
    {
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();

            $name = $data['name'] ?? '';
            $data['add_time'] = time();
            if (!$name) {
                $this->error("敏感词不能为空");
            }

            $id = DB::name('sensitive')->insertGetId($data);
            if (!$id) {
                $this->error("添加失败！");
            }
            $this->success("添加成功！", url("words/index"));
        }
    }

    function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        $data = Db::name('sensitive')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }
        $this->assign('data', $data);
        $this->assign('status', $this->getStatus());
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $name = $data['name'];
            if ($name == "") {
                $this->error("敏感词不能为空");
            }
            $rs = DB::name('sensitive')->update($data);
            if ($rs === false) {
                $this->error("修改失败！", url("words/index"));
            }
            $this->success("修改成功！", url("words/index"));
        }
    }

}
