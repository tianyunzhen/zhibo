<?php

/**
 * 坐骑管理
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class CarController extends AdminbaseController
{

    protected function getTypes($k = '')
    {
        $type = [
            '1' => '福利',
            '2' => '豪华',
            '3' => '梦幻',
        ];
        if ($k == '') {
            return $type;
        }
        return isset($type[$k]) ? $type[$k] : '';
    }

    protected function getStatus($k = '')
    {
        $status = [
            '0' => '下架',
            '1' => '上架',
        ];
        if ($k == '') {
            return $status;
        }
        return isset($status[$k]) ? $status[$k] : '';
    }

    protected function getSwftype($k = '')
    {
        $swftype = [
            '0' => 'GIF',
            '1' => 'SVGA',
        ];
        if ($k == '') {
            return $swftype;
        }
        return isset($swftype[$k]) ? $swftype[$k] : '';
    }

    function index()
    {
        $lists = Db::name("car")
            ->order("list_order asc")
            ->paginate(20);
        $lists->each(function ($v, $k) {
//            $v['total_buy'] =  Db::name("car_user")->where(['carid' => $v['id']])->count();
            $v['thumb'] = get_upload_path($v['thumb']);
            return $v;
        });
        $page = $lists->render();
        $this->assign('lists', $lists);
        $this->assign("page", $page);
        $this->assign("status", $this->getStatus());
        $this->assign("type", $this->getTypes());
        return $this->fetch();
    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        $rs = DB::name('car')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $action = "删除坐骑：{$id}";
        setAdminLog($action);
        $this->resetcache();
        $this->success("删除成功！");
    }

    //排序
    public function listOrder()
    {
        $model = DB::name('car');
        parent::listOrders($model);
        $action = "更新坐骑排序";
        setAdminLog($action);
        $this->resetcache();
        $this->success("排序更新成功！");
    }


    function add()
    {
        $this->assign("type", $this->getTypes());
        $this->assign("swftype", $this->getSwftype());
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {
            $configpub = getConfigPub();
            $data = $this->request->param();
            $name = $data['name'];
            if ($name == "") {
                $this->error("请填写坐骑名称");
            }
            $needcoin = $data['needcoin'];
            if ($needcoin == "") {
                $this->error("请填写坐骑所需" . $configpub['name_coin']);
            }
            if (!is_numeric($needcoin)) {
                $this->error("请确认坐骑所需" . $configpub['name_coin']);
            }

//            $expire = $data['expire'];
//            if (!$expire) {
//                $this->error("有效时间不能为空");
//            }

            $swftime = $data['swftime'];
            if ($swftime == "") {
                $this->error("请填写动画时长");
            }
            if (!is_numeric($swftime)) {
                $this->error("请确认动画时长");
            }
            $words = $data['words'];
            if ($words == "") {
                $this->error("请填写进场话术");
            }
            $data['addtime'] = time();
            $id = DB::name('car')->insertGetId($data);
            if (!$id) {
                $this->error("添加失败！");
            }
            $action = "添加坐骑：{$id}";
            setAdminLog($action);
            $this->resetcache();
            $this->success("添加成功！");
        }
    }

    function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        $data = Db::name('car')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }
        $this->assign("type", $this->getTypes());
        $this->assign('data', $data);
        $this->assign("swftype", $this->getSwftype());
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $configpub = getConfigPub();
            $data      = $this->request->param();
            $name = $data['name'];
            if ($name == "") {
                $this->error("请填写坐骑名称");
            }
            $needcoin = $data['needcoin'];
            if ($needcoin == "") {
                $this->error("请填写坐骑所需" . $configpub['name_coin']);
            }
            if (!is_numeric($needcoin)) {
                $this->error("请确认坐骑所需" . $configpub['name_coin']);
            }
            $swftime = $data['swftime'];
            if ($swftime == "") {
                $this->error("请填写动画时长");
            }
            if (!is_numeric($swftime)) {
                $this->error("请确认动画时长");
            }
            $words = $data['words'];
            if ($words == "") {
                $this->error("请填写进场话术");
            }
            $rs = DB::name('car')->update($data);
            if ($rs === false) {
                $this->error("修改失败！");
            }
            $action = "修改坐骑：{$data['id']}";
            setAdminLog($action);
            $this->resetcache();
            $this->success("修改成功！", url('car/index'));
        }
    }

    function resetcache()
    {
        $key = 'carinfo';
        $car_list = DB::name("car")->order("list_order asc")->select();
        if ($car_list) {
            setcaches($key, $car_list);
        }
        return 1;
    }

    /* 上下架 */
    function on()
    {
        $id     = $this->request->param('id', 0, 'intval');
        $status = $this->request->param('status', 1, 'intval');
        $rs = DB::name('car')->where("id={$id}")->update(['status' => $status]);
        if (!$rs) {
            $this->error("操作失败！");
        }
        $action = "修改坐骑：{$id}";
        setAdminLog($action);
        $this->resetcache();
        $this->success("操作成功！");
    }
}
