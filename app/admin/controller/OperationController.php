<?php

/**
 * 用户反馈
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class OperationController extends AdminbaseController
{
    function index()
    {
        $lists = Db::name("operation_user")
            ->alias('p')
            ->field('p.id,p.admin_id,u.user_login,p.operator,p.addtime')
            ->join('user u', 'p.admin_id=u.id')
            ->order("addtime desc")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            return $v;
        });

        $page = $lists->render();
        $this->assign('lists', $lists);
        $this->assign("page", $page);
        return $this->fetch();
    }

    function add()
    {
        $users = Db::name("user")->field('id,user_login')->where(['user_type' => 1])->select();
        $this->assign('users', $users);
        return $this->fetch();
    }

    function addPost()
    {
       $data = $this->request->param();
       $uid = $data['uid'] ?? 0;
       if (!$uid) {
           $this->error("请选择管理用户！");
       }
       $have = Db::name("operation_user")->where(['admin_id' => $uid])->find();
       if ($have) {
           $this->error("该用户已添加！");
       }
        $adminid   = cmf_get_current_admin_id();
        $admininfo = Db::name("user")->where(["id" => $adminid])
            ->value("user_login");
        $inData = [
            'admin_id' => $uid,
            'operator' => $admininfo,
            'addtime' => time()
        ];
       $result = Db::name('operation_user')->insert($inData);
       if (!$result) {
           $this->error("添加失败！");
       }
        $this->success("操作成功！", url('operation/index'));
    }

}
