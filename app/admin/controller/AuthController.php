<?php

/**
 * 认证
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use app\common\Jpush;
use app\common\Message;
use think\Config;
use think\Db;

class AuthController extends AdminbaseController
{
    protected function getStatus($k = '')
    {
        $status = [
            '1' => '未审核',
            '2' => '审核成功',
            '3' => '审核失败',
        ];
        if ($k === '') {
            return $status;
        }
        return isset($status[$k]) ? $status[$k] : '';
    }

    function index()
    {
        $data = $this->request->param();
        $map  = [];
        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time   = isset($data['end_time']) ? $data['end_time'] : '';
        if ($start_time != "") {
            $map[] = ['addtime', '>=', strtotime($start_time)];
        }
        if ($end_time != "") {
            $map[] = ['addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }
        $status = isset($data['status']) ? $data['status'] : '';
        if ($status != '') {
            $map[] = ['status', '=', $status];
        } else {
            $map[] = ['status', '=', 1];
        }
        $uid = isset($data['uid']) ? $data['uid'] : '';
        if ($uid != '') {
            $map[] = ['uid', '=', $uid];
        }
        $keyword = isset($data['keyword']) ? $data['keyword'] : '';
        if ($keyword != '') {
            $map[] = ['real_name|mobile', 'like', '%' . $keyword . '%'];
        }
        $lists = Db::name("user_auth")
            ->where($map)
            ->order("addtime DESC")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['userinfo'] = getUserInfo($v['uid']);

            $v['mobile']   = m_s($v['mobile']);
            if (!$v['mobile']) {
                $v['mobile'] = $v['userinfo']['mobile'];
            }
            $v['car_no']   = m_s($v['car_no']);
            return $v;
        });
        $lists->appends($data);
        $page = $lists->render();
        $this->assign('lists', $lists);
        $this->assign("page", $page);
        $this->assign('status', $this->getStatus());
        return $this->fetch();
    }

    function del()
    {
        $uid = $this->request->param('uid', 0, 'intval');
        $rs = DB::name('user_auth')->where("uid={$uid}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }
        $action = "删除会员认证信息：{$uid}";
        setAdminLog($action);
        $this->success("删除成功！");
    }

    function edit()
    {
        $uid = $this->request->param('uid', 0, 'intval');
        $data = Db::name('user_auth')
            ->where("uid={$uid}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }
        $data['userinfo'] = getUserInfo($data['uid']);
        $data['mobile']   = m_s($data['mobile']);
        $data['car_no']   = m_s($data['car_no']);
        $data['front_view'] = get_upload_path($data['front_view']);
        $data['back_view'] = get_upload_path($data['back_view']);
        $data['handset_view'] = get_upload_path($data['handset_view']);
        $this->assign('status', $this->getStatus());
        $this->assign('data', $data);
        return $this->fetch();
    }

    function editPost()
    {
        $data = $this->request->param();
        $status = $data['status'];
        $uid    = $data['uid'];
        if ($status == '0') {
            $this->success("修改成功！");
        }
        $data['uptime'] = time();
        $rs = DB::name('user_auth')->update($data);
        if ($rs === false) {
            $this->error("修改失败！");
        }
//            $push = new Jpush($uid);
        if ($status == '3') {
            $title = '认证不通过';
//                $push->sendAlias($title, Jpush::DHJB);
            Message::addMsg($title,Jpush::DHJB, $uid);
            $action = "修改会员认证信息：{$uid} - 拒绝";
        } else {
            if ($status == '2') {
                $title = '认证通过';
                DB::name('user')->where(['id' => $uid])->update(['is_auth' => 1]);
//                    $push->sendAlias('认证通过', Jpush::TXSQSB);
                Message::addMsg($title,Jpush::TXSQSB, $uid);
                $action = "修改会员认证信息：{$uid} - 同意";
//                $url = Config::get('database.live') . "&uid=$uid";
//                Get($url);
            } else {
                $action = "修改会员认证信息：{$uid} - 暂不处理";
            }
        }
        setAdminLog($action);
        $this->success("修改成功！", url('auth/index'));
    }

    function swithch()
    {
            $data = $this->request->param();
            $status = $data['status'];
            $uid    = $data['uid'];
            if ($status == '0') {
                $this->success("修改成功！");
            }
            $data['uptime'] = time();
            $rs = DB::name('user_auth')->update($data);
            if ($rs === false) {
                $this->error("修改失败！");
            }
//            $push = new Jpush($uid);
            if ($status == '3') {
                $title = '认证不通过';
//                $push->sendAlias($title, Jpush::DHJB);
                Message::addMsg($title,Jpush::DHJB, $uid);
                $action = "修改会员认证信息：{$uid} - 拒绝";
            } else {
                if ($status == '2') {
                    DB::name('user')->update(['is_auth' => 1]);
                    $title = '认证通过';
//                    $push->sendAlias('认证通过', Jpush::TXSQSB);
                    Message::addMsg($title,Jpush::TXSQSB, $uid);
                    $action = "修改会员认证信息：{$uid} - 同意";
                } else {
                    $action = "修改会员认证信息：{$uid} - 暂不处理";
                }
            }
            setAdminLog($action);
            $this->success("修改成功！", url('auth/index'));
    }


}
