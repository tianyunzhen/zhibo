<?php

/**
 * 用户反馈
 */

namespace app\admin\controller;

use app\common\Message;
use cmf\controller\AdminBaseController;
use think\Db;

class RemarkController extends AdminbaseController
{
    function index()
    {
        $lists = Db::name("remark")
            ->order("addtime desc")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['icon'] = get_upload_path($v['icon']);
            return $v;
        });

        $page = $lists->render();
        $this->assign('lists', $lists);
        $this->assign("page", $page);
        return $this->fetch();
    }

    function add()
    {
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $level = $data['level'] ?? '';
            $name = $data['name'] ?? '';
            $icon = $data['icon'] ?? '';
            if (!$level || !$name || !$icon) {
                $this->error("缺少必要参数！");
            }
            $data['addtime'] = time();
            $result    = Db::name('remark')->insert($data);
            if (!$result) {
                $this->error("添加失败！");
            }
            $this->success("操作成功！", url('remark/index'));
        }
    }

    function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        $data = Db::name('remark')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }
        $data['icon'] = get_upload_path($data['icon']);
        $this->assign('data', $data);
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $result = Db::name('remark')->update($data);
            if (!$result) {
                $this->error("信息错误");
            }
            $this->success("操作成功！", url('remark/index'));
        }
    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        $rs = DB::name('remark')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $this->success("删除成功！");
    }

    function addUser()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $uid = $data['uid'] ?? 0;
            $remarkId = $data['remark_id'] ?? 0;
            if (!$uid || !$remarkId) {
                $this->error("缺少必要参数！");
            }
            $auth = Db::name('user_auth')->where(['uid' => $uid, 'status' => 2])->count();
            if (!$auth) {
                $this->error("主播未实名！");
            }
            $remark = Db::name('remark')->where(['id' => $remarkId])->find();
            if (!$remark) {
                $this->error("标记不存在！");
            }
            $have = Db::name('user_remark')->where(['uid' => $uid])->find();
            if ($have) {
                $update = Db::name('user_remark')->where(['uid' => $uid])->update(['remark_id' => $remarkId]);
                if ($update === false) {
                    $this->error("设置失败！");
                }
            } else {
                $data['addtime'] = time();
                $result = Db::name('user_remark')->insert($data);
                Db::name('user')->where(['id' => $uid])->update(['verify' => 1]);
                if (!$result) {
                    $this->error("设置失败！");
                }
            }
            Message::addMsg('主播标识', "恭喜您成为" . $remark['auth_desc'], $uid);
            $this->success("操作成功！");
        }
    }

    function delUser()
    {
        $data = $this->request->param();
        $uid = $data['uid'] ?? 0;
        if (!$uid) {
            $this->error("缺少必要参数！");
        }
        Db::name('user_remark')->where(['uid' => $uid])->delete();
        Db::name('user')->where(['id' => $uid])->update(['verify' => 0]);
        $key = 'active:day_water:' . date('Ymd');
        zRem($key, $uid);
        $this->success("操作成功！", url('user/adminIndex/index'));
    }
}
