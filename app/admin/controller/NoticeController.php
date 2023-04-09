<?php
//来来1号店
//QQ：125050230
namespace app\admin\controller;

use think\Db;
use cmf\controller\AdminBaseController;
use app\admin\model\SlideItemModel;

class NoticeController extends AdminBaseController
{

    public function index()
    {
        $lists = Db::name("notice")
            ->where('id<10000')
            ->order("id desc")
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


            $id = DB::name('notice')->insertGetId($data);
            if (!$id) {
                $this->error("添加失败！");
            }

            $action = "添加首页公告：{$id}";
            setAdminLog($action);

//            $this->resetcache();
            $this->success("添加成功！");

        }
    }

    public function delete()
    {
        $id = $this->request->param('id', 0, 'intval');

        $rs = DB::name('notice')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }

        $action = "删除公告：{$id}";
        setAdminLog($action);

        // $this->resetcache();
        $this->success("删除成功！", url("notice/index"));

    }

}