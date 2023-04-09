<?php

/**
 * 相框管理
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class HeadborderController extends AdminbaseController
{

    protected function getTypes($k = '')
    {
        $type = [
            '1' => '活动',
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
            '1' => '上架',
            '2' => '下架',
        ];
        if ($k == '') {
            return $status;
        }
        return isset($status[$k]) ? $status[$k] : '';
    }

    function index()
    {
        $lists = Db::name("head_border")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['pic'] = get_upload_path($v['pic']);
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
        $headUser = DB::name('head_border_user')->where("head_id={$id}")->find();
        if ($headUser) {
            $this->error("该头像有用户在使用！");
        }
        $rs = DB::name('head_border')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $action = "删除相框：{$id}";
        setAdminLog($action);
        $this->success("删除成功！");
    }

    //排序
    public function listOrder()
    {
        $model = DB::name('car');
        parent::listOrders($model);
        $action = "更新头像排序";
        setAdminLog($action);
        $this->resetcache();
        $this->success("排序更新成功！");
    }


    function add()
    {
        $this->assign("type", $this->getTypes());
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $name = $data['title'];
            if ($name == "") {
                $this->error("请填写头像名称");
            }
            $needcoin = $data['price'];
            if ($needcoin == "") {
                $this->error("请填写头像所需丫币");
            }
            if (!is_numeric($needcoin)) {
                $this->error("请确认头像所需丫币");
            }
            $data['create_time'] = time();
            $id = DB::name('head_border')->insertGetId($data);
            if (!$id) {
                $this->error("添加失败！");
            }
            $action = "添加头像：{$id}";
            setAdminLog($action);
            $this->resetcache();
            $this->success("添加成功！", url('headborder/index'));
        }
    }

    function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        $data = Db::name('head_border')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }
        $this->assign("type", $this->getTypes());
        $this->assign('data', $data);
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $configpub = getConfigPub();
            $data      = $this->request->param();
            $name = $data['title'];
            if ($name == "") {
                $this->error("请填写头像名称");
            }
            $needcoin = $data['price'];
            if ($needcoin == "") {
                $this->error("请填写头像所需" . $configpub['name_coin']);
            }
            if (!is_numeric($needcoin)) {
                $this->error("请确认头像所需" . $configpub['name_coin']);
            }
            $rs = DB::name('head_border')->update($data);
            if ($rs === false) {
                $this->error("修改失败！");
            }
            $action = "修改头像：{$data['id']}";
            setAdminLog($action);
            $this->resetcache();
            $this->success("修改成功！", url('headborder/index'));
        }
    }

    function resetcache()
    {
        $key = 'shop:head_border:';
        $car_list = DB::name("head_border")->select();
        if ($car_list) {
            setcaches($key, $car_list);
        }
        return 1;
    }

    /* 上下架 */
    function on()
    {
        $id     = $this->request->param('id', 0, 'intval');
        $status = $this->request->param('is_up', 1, 'intval');
        $rs = DB::name('head_border')->where("id={$id}")->update(['is_up' => $status]);
        if (!$rs) {
            $this->error("操作失败！");
        }
        $action = "修改头像：{$id}";
        setAdminLog($action);
        $this->resetcache();
        $this->success("操作成功！");
    }

    /* 上下架 */
    function addUserPost()
    {
        if ($this->request->isPost()) {
            $data      = $this->request->param();
            $uid = $data['uid'] ?? 0;
            $head_id = $data['head_id'] ?? 0;
            $head = DB::name('head_border')->where(['id' => $head_id])->field('overdue')->find();
            $by_time = $head['overdue'];
            $nowTime = time();
            if ($by_time) {
                $data['expire'] = $nowTime + $by_time * 86400;
            } else {
                $data['expire'] = 0;
            }
            $data['create_time'] = $nowTime;
            if (!$uid || !$head_id) {
                $this->error("参数错误");
            }
            $rs = DB::name('head_border_user')->insert($data);
            if ($rs === false) {
                $this->error("添加失败！");
            }
            $this->success("添加成功！", url('headborder/index'));
        }
    }

    /* 上下架 */
    function addUser()
    {
        $head = DB::name('head_border')
            ->field('id,title')
            ->where(['is_up' => 1])
            ->select()
            ->toArray();
        $this->assign('head', $head);
        return $this->fetch('send');
    }


    function userIndex()
    {
        $data   = $this->request->param();
        $map    = [['uid', '<>', 0]];
        $uid = $data['uid'] ?? 0;
        if ($uid) {
            $map[] = ['uid', '=', $uid];
        }
        $lists = Db::name("head_border_user")
            ->alias('bu')
            ->leftJoin('user u', 'bu.uid=u.id')
            ->leftJoin('head_border hb', 'bu.head_id=hb.id')
            ->field('bu.id,uid,title,user_nicename,bu.create_time,expire,pic')
            ->where($map)
            ->order("id DESC")
            ->paginate(20);

        $lists->each(function ($v, $k) {
            $v['pic'] = get_upload_path($v['pic']);
            if (!$v['expire']) {
                $v['remain_time'] = '永久';
            } else {
                $remain = $v['expire'] - time();
                if ($remain <= 0) {
                    $v['remain_time'] = "已过期";
                } else {
                    $hours = intval( $remain / 3600);
                    $v['remain_time'] = $hours . ":" .gmstrftime('%M:%S', $remain);
                }
            }
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);
        return $this->fetch('user_index');
    }

    function recycle()
    {
        $id = $this->request->param('id', 0, 'intval');
        $query = Db::name('head_border_user')
            ->where("id={$id}")
            ->field('uid')
            ->find();
        if (!$query) {
            $this->error("信息错误");
        }
        $delete = Db::name('head_border_user')
            ->where("id={$id}")
            ->delete();
        if ($delete === false) {
            $this->error("操作失败");
        }
        delcache('user:head_border:head_info_' . $query['uid']);
        $this->success("修改成功！", url('headborder/userIndex'));
    }
}
