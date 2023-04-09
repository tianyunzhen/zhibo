<?php

namespace app\admin\controller;

use think\Db;
use cmf\controller\AdminBaseController;
use app\admin\model\SlideItemModel;

class AddynamicController extends AdminBaseController
{
    public function index()
    {
        $lists = Db::name("dynamic")
            ->where('is_ad=1')
            ->order("id asc")
            ->paginate(20);

        $this->assign('lists', $lists);

        return $this->fetch();

    }

    public function add()
    {
        return $this->fetch();
    }

    public function addpost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();
            $id = DB::name('dynamic')->insertGetId($data);
            if (!$id) {
                $this->error("添加失败！");
            }
            $action = "添加动态广告：{$id}";
            setAdminLog($action);
//            $this->resetcache();
            $this->success("添加成功！");
        }
    }

    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        $rs = DB::name('dynamic')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $action = "删除动态广告：{$id}";
        setAdminLog($action);
        // $this->resetcache();
        $this->success("删除成功！", url("addynamic/index"));

    }

    //动态纯文字广告列表
    public function adtext()
    {
        $lists = Db::name("dynamic")
            ->where('type=4')
            ->order("id asc")
            ->paginate(20);
        $this->assign('lists', $lists);
        return $this->fetch();

    }

    //删除动态纯文字广告
    public function deleteadtext()
    {
        $id = $this->request->param('id', 0, 'intval');

        $rs = DB::name('dynamic')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $action = "删除动态文字广告：{$id}";
        setAdminLog($action);
        // $this->resetcache();
        $this->success("删除成功！", url("addynamic/index"));

    }

    public function addadtext()
    {
        return $this->fetch();
    }

}

