<?php

/**
 * 礼物
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class GiftController extends AdminbaseController
{
    protected function getTypes($k = '')
    {
        $type = [
            '0' => '普通礼物',
            '1' => '豪华礼物',
        ];
        if ($k == '') {
            return $type;
        }
        return isset($type[$k]) ? $type[$k] : '';
    }

    protected function getMark($k = '')
    {
        $mark = [
            '0' => '普通',
            '1' => '热门',
            '2' => '守护',
            '3' => '幸运',
        ];
        if ($k == '') {
            return $mark;
        }
        return isset($mark[$k]) ? $mark[$k] : '';
    }

    protected function getJackpot($k = '')
    {
        $mark = [
            '0' => '50',
            '1' => '100',
            '2' => '守护',
            '3' => '幸运',
        ];
        if ($k == '') {
            return $mark;
        }
        return isset($mark[$k]) ? $mark[$k] : '';
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

    protected function getPlat($k = '')
    {
        $status = [
            '0' => '都不飘',
            '1' => '全站飘',
            '2' => '本直播间飘',
        ];
        if ($k == '') {
            return $status;
        }
        return isset($status[$k]) ? $status[$k] : '';
    }

    function index()
    {
        $lists = Db::name("gift")
            ->where('type!=2')
            ->order("list_order asc,id desc")
            ->paginate(20);

        $lists->each(function ($v, $k) {
            $v['gifticon'] = get_upload_path($v['gifticon']);
            $v['swf']      = get_upload_path($v['swf']);
            return $v;
        });

        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        $this->assign("type", $this->getTypes());
        $this->assign("mark", $this->getMark());
        $this->assign("swftype", $this->getSwftype());
        $this->assign("status", $this->getStatus());

        return $this->fetch();
    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        $on = Db::name('gift')
            ->field('type')
            ->where("id={$id}")->find();
        $rs = DB::name('gift')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }

        $action = "删除礼物：{$id}";
        setAdminLog($action);
        $key = 'gift:getGiftList_' . $on['type'];
        delcache($key);
        $this->success("删除成功！");
    }

    /* 全站飘屏 */
    function plat()
    {

        $id         = $this->request->param('id', 0, 'intval');
        $isplatgift = $this->request->param('isplatgift', 0, 'intval');

        $rs = DB::name('gift')->where("id={$id}")
            ->update(['isplatgift' => $isplatgift]);
        if (!$rs) {
            $this->error("操作失败！");
        }
        $action = "修改礼物：{$id}";
        setAdminLog($action);
        $this->success("操作成功！");
    }

    //排序
    public function listOrder()
    {
        $model = DB::name('gift');
        parent::listOrders($model);

        $action = "更新礼物排序";
        setAdminLog($action);
        $this->success("排序更新成功！");
    }

    function add()
    {
        $this->assign("type", $this->getTypes());
        $this->assign("mark", $this->getMark());
        $this->assign("swftype", $this->getSwftype());
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();

            $giftname = $data['giftname'];
            if ($giftname == '') {
                $this->error('请输入名称');
            } else {
                $check = Db::name('gift')->where("giftname='{$giftname}'")
                    ->find();
                if ($check) {
                    $this->error('名称已存在');
                }
            }
            $needcoin = $data['needcoin'];
            $gifticon = $data['gifticon'];

            if ($needcoin == '') {
                $this->error('请输入价格');
            }

            if ($gifticon == '') {
                $this->error('请上传图片');
            }

            $swftype     = $data['swftype'];
            $data['swf'] = $data['gif'];
            if ($swftype == 1) {
                $data['swf'] = $data['svga'];
            }

            if ($data['type'] == 1 && $data['swf'] == '') {
                $this->error('请上传动画效果');
            }
            if ($data['type'] == 1) {
                $data['isplatgift'] = 1;
            }
            $data['addtime'] = time();
            $data['status'] = 0;
            $info = [10,20,30,40,50];
            $data['info'] = json_encode($info);
            unset($data['gif']);
            unset($data['svga']);

            $id = DB::name('gift')->insertGetId($data);
            if (!$id) {
                $this->error("添加失败！");
            }

            $action = "添加礼物：{$id}";
            setAdminLog($action);
            $this->success("添加成功！");
        }
    }

    function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        $data = Db::name('gift')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }

        $this->assign("type", $this->getTypes());
        $this->assign("mark", $this->getMark());
        $this->assign("swftype", $this->getSwftype());
        $this->assign("isplatgift", $this->getPlat());

        $this->assign('data', $data);
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $id       = $data['id'];
            $giftname = $data['giftname'];
            $on = Db::name('gift')
                ->field('type')
                ->where("id={$id}")->find();
            if (!$on) {
                $this->error('礼物不存在');
            }
            if ($giftname == '') {
                $this->error('请输入名称');
            } else {
                $check = Db::name('gift')
                    ->where("giftname='{$giftname}' and id!={$id}")->find();
                if ($check) {
                    $this->error('名称已存在');
                }
            }
            $needcoin = $data['needcoin'];
            $gifticon = $data['gifticon'];
            if ($needcoin == '') {
                $this->error('请输入价格');
            }
            if ($gifticon == '') {
                $this->error('请上传图片');
            }
            $swftype     = $data['swftype'];
            $data['swf'] = $data['gif'];
            if ($swftype == 1) {
                $data['swf'] = $data['svga'];
            }
            if ($data['type'] == 1 && $data['swf'] == '') {
                $this->error('请上传动画效果');
            }
            unset($data['gif']);
            unset($data['svga']);

            if ($data['type'] == 1) {
                $data['isplatgift'] = 1;
            }

            $rs = DB::name('gift')->update($data);
            if ($rs === false) {
                $this->error("修改失败！");
            }
            if (empty($data['anchor_rate']) || $data['anchor_rate'] > 100) {
                $this->error('抽成比例错误');
            }
            if (empty($data['family_rate']) || $data['family_rate'] > 100) {
                $this->error('抽成比例错误');
            }
            $action = "修改礼物：{$data['id']}";
            setAdminLog($action);
            $key = 'gift:getGiftList_' . $on['type'];
            delcache($key);
            $this->success("修改成功！");
        }
    }

    /* 上下架 */
    function on()
    {
        $id     = $this->request->param('id', 0, 'intval');
        $status = $this->request->param('status', 1, 'intval');
        $on = Db::name('gift')
            ->field('type')
            ->where("id={$id}")->find();
        $rs = DB::name('gift')->where("id={$id}")
            ->update(['status' => $status]);
        if (!$rs) {
            $this->error("操作失败！");
        }

        $action = "修改礼物：{$id}";
        setAdminLog($action);
        $key = 'gift:getGiftList_' . $on['type'];
        delcache($key);
        $this->success("操作成功！");
    }
}
