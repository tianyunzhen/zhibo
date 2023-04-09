<?php

namespace app\admin\controller;

use think\Db;
use cmf\controller\AdminBaseController;

class AdvideoController extends AdminBaseController
{
    public function index()
    {
        $lists = Db::name("video")
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
            $id = DB::name('video')->insertGetId($data);
            if (!$id) {
                $this->error("添加失败！");
            }
            $action = "添加广告视频：{$id}";
            setAdminLog($action);
//            $this->resetcache();
            $this->success("添加成功！");

        }
    }

    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');
        $rs = DB::name('video')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $action = "删除广告视频：{$id}";
        setAdminLog($action);
        // $this->resetcache();
        $this->success("删除成功！", url("notice/index"));
    }

}




