<?php

/**
 * 提现
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use app\common\Jpush;
use app\common\Message;
use think\Db;

class CashController extends AdminbaseController
{
    protected function getStatus($k = '')
    {
        $status = [
            '0' => '未处理',
            '1' => '审核成功',
            '2' => '审核失败',
        ];
        if ($k === '') {
            return $status;
        }

        return isset($status[$k]) ? $status[$k] : '';
    }

    protected function getCheckStatus($k = '')
    {
        $status = [
            '1' => '待打款',
            '3' => '打款成功',
            '4' => '打款失败',
        ];
        if ($k === '') {
            return $status;
        }

        return isset($status[$k]) ? $status[$k] : '';
    }

    protected function getTypes($k = '')
    {
        $type = [
            '1' => '支付宝',
            '2' => '微信',
            '3' => '银行卡',
        ];
        if ($k === '') {
            return $type;
        }

        return isset($type[$k]) ? $type[$k] : '';
    }

    function index()
    {
        $data = $this->request->param();
        $map  = [];
        $status = isset($data['status']) ? $data['status'] : '';
        if ($status != '') {
            $map[] = ['r.status', '=', $status];
        } else {
            $map[] = ['r.status', 'in', [0,1,2]];
        }
        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time   = isset($data['end_time']) ? $data['end_time'] : '';
        if ($start_time != "") {
            $map[] = ['r.addtime', '>=', strtotime($start_time)];
        }
        if ($end_time != "") {
            $map[] = ['r.addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }
        $uid = isset($data['uid']) ? $data['uid'] : '';
        if ($uid != '') {
            $map[] = ['r.uid', '=', $uid];
        }
//        $map[] = ['a.status', '=', 2];
        $lists = DB::name("cash_record")
            ->alias('r')
            ->field('r.id,r.uid,money,r.votes,r.trade_no,r.status,r.addtime,r.account,r.remark,u2.user_nicename as review,a.real_name,a.car_no,f.id as family_id,f.name as family_name')
            ->leftJoin('user u', 'u.id=r.uid')
            ->leftJoin('user u2', 'u2.id=r.review_id')
            ->leftJoin('user_auth a', 'r.uid=a.uid')
            ->leftJoin('family_user fu', 'fu.uid=r.uid')
            ->leftJoin('family f', 'f.id=fu.familyid')
            ->where($map)
            ->order('r.addtime desc')
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['votes'] = (string) round($v['votes'] / 100, 2);
            return $v;
        });
        $lists->appends($data);
        $page = $lists->render();
        $this->assign('lists', $lists);
        $this->assign('type', $this->getTypes());
        $this->assign('status', $this->getStatus());
        $this->assign("page", $page);

        return $this->fetch();
    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id) {
            $result = DB::name("cash_record")->delete($id);
            if ($result) {
                $action = "删除提现记录：{$id}";
                setAdminLog($action);
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    function edit()
    {
        $id = $this->request->param('id', 0, 'intval');
        $type = $this->request->param('type', 0, 'intval');
        $data = Db::name('cash_record')
            ->alias('r')
            ->field('r.id,r.uid,u.user_nicename,r.votes,r.money,r.type,r.account,a.real_name,a.car_no,r.trade_no,r.remark,r.status')
            ->leftJoin('user u', 'r.uid=u.id')
            ->leftJoin('user_auth a', 'a.uid=r.uid')
            ->where("r.id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }
        $data['votes'] = (string) round($data['votes'] / 100, 2);
        $this->assign('type', $this->getTypes());
        $this->assign('c_type', $type);
        if ($type == 1) {
            $this->assign('status', $this->getStatus());
        } else {
            $this->assign('status', $this->getCheckStatus());
        }
        $this->assign('data', $data);
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $cType = $data['c_type'];
            unset($data['c_type']);
            $id = $data['id'];
            $cashRecord = DB::name('cash_record')
                ->field('id,uid,money,votes')
                ->where(['id' => $id])->find();
            $uid = $cashRecord['uid'];
            $status = $data['status'];
            $votes = $cashRecord['votes'];
            if ($status == '0') {
                $this->success("修改成功！");
            }
            $data['uptime'] = time();
            $adminid = cmf_get_current_admin_id();
            $data['review_id'] = $adminid;
            Db::startTrans();
            try {
                $action = '';
                DB::name('cash_record')->update($data);
                if ($status == '2' || $status == '4') {
                    DB::name("user")->where(["id" => $uid])->setInc("votes", $votes);
                    $voterData = [
                        "uid"     => $uid,
                        'type'    => 1,
                        'action'  => 5,
                        'actionid'=> $id,
                        'votes' =>  $votes,
                        'addtime' =>  time(),
                    ];
                    DB::name("user_voterecord")->insert($voterData);
                    if ($status == "2") {
                        $action = "修改提现申请状态：{$id} - 拒绝";
                    } else {
                        $action = "修改提现申请状态：{$id} - 打款失败";
                    }
                    $title = "提现申请审核不通过";
                    Message::addMsg($title, Jpush::BDSJ, $uid);
                }
                if ($status == '1') {
                    $action = "修改提现申请状态：{$id} - 同意";
                    $title = "提现申请审核通过";
                    Message::addMsg($title, "恭喜鸭，提现申请成功，[".$cashRecord['money']."]元武装鸭运中！", $uid);
                }
                if ($status == '3') {
                    $action = "修改提现申请状态：{$id} - 已打款";
//                    $title = "提现已打款请及时查收";
//                    Message::addMsg($title, "恭喜鸭，提现已打款成功，[".$cashRecord['money']."]元武装鸭运中！", $uid);
                }
                if($action) {
                    setAdminLog($action);
                }
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error("修改失败！");
            }
            if ($cType == 1) {
                $url = 'cash/index';
            }
            if ($cType == 2) {
                $url = 'cash/check';
            }
            $this->success("修改成功！", url($url));
        }
    }

    function export()
    {
        $data = $this->request->param();
        $map  = [];

        $status = isset($data['status']) ? $data['status'] : '';
        if ($status != '') {
            $map[]        = ['r.status', '=', $status];
        }

        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time   = isset($data['end_time']) ? $data['end_time'] : '';

        if ($start_time != "") {
            $map[] = ['r.addtime', '>=', strtotime($start_time)];
        }

        if ($end_time != "") {
            $map[] = ['r.addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }

        $keyword = isset($data['keyword']) ? $data['keyword'] : '';
        if ($keyword != '') {
            $map[] = ['r.uid|r.orderno|r.trade_no', 'like', "%" . $keyword . "%"];
        }

        $xlsName = "提现";

        $xlsData = DB::name("cash_record")
            ->alias('r')
            ->field('r.id,r.uid,u.user_nicename,r.money,a.real_name,a.car_no,r.votes,r.trade_no,r.status,r.addtime,r.account_bank,r.account')
            ->join('user_auth a', 'r.uid=a.uid')
            ->join('user u', 'r.uid=u.id')
            ->where($map)
            ->order('id desc')
            ->select()
            ->toArray();

        foreach ($xlsData as $k => $v) {
//            $xlsData[$k]['uid'] = $v['uid'];
            $xlsData[$k]['car_no'] = (string) $v['car_no'];
            $xlsData[$k]['money'] = (string) $v['money'];
            $xlsData[$k]['votes'] = (string) round($v['votes'] / 100, 2);
            $xlsData[$k]['user_nicename'] = $v['user_nicename'];
            $xlsData[$k]['addtime']       = date("Y-m-d H:i:s", $v['addtime']);
//            $xlsData[$k]['uptime']        = date("Y-m-d H:i:s", $v['uptime']);
            $xlsData[$k]['status']        = $this->getStatus($v['status']);
        }

        $action = "导出提现记录：" . DB::name("cash_record")->getLastSql();
        setAdminLog($action);
        $cellName = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','K','L'];
        $xlsCell  = [
            ['id', '序号'],
            ['uid', '用户ID'],
            ['user_nicename', '昵称'],
            ['real_name', '真实姓名'],
            ['car_no', '身份证号'],
            ['money', '提现金额'],
            ['votes', '兑换点数'],
            ['trade_no', '第三方支付订单号'],
            ['account_bank', '提现银行'],
            ['account', '提现账户'],
            ['status', '状态'],
            ['addtime', '提交时间'],
        ];
        exportExcel($xlsName, $xlsCell, $xlsData, $cellName);
    }

    function check()
    {
        $data = $this->request->param();
        $map  = [];
        $status = $data['status'] ?? 0;
        if ($status) {
            $map[] = ['r.status', '=', $status];
        } else {
            $map[] = ['r.status', 'in', [1,3,4]];
        }
        $start_time = $data['start_time'] ?? '';
        $end_time   = $data['end_time'] ?? '';
        if ($start_time) {
            $map[] = ['r.addtime', '>=', strtotime($start_time)];
        }
        if ($end_time) {
            $map[] = ['r.addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }
        $uid = $data['uid'] ?? '';
        if ($uid) {
            $map[] = ['r.uid', '=', $uid];
        }
        $lists = DB::name("cash_record")
            ->alias('r')
            ->field('r.id,r.uid,money,r.votes,r.trade_no,r.status,r.addtime,r.account,r.remark,u2.user_nicename as review,a.real_name,a.car_no,f.id as family_id,f.name as family_name')
            ->leftJoin('user u', 'u.id=r.uid')
            ->leftJoin('user u2', 'u2.id=r.review_id')
            ->leftJoin('user_auth a', 'r.uid=a.uid')
            ->leftJoin('family_user fu', 'fu.uid=r.uid')
            ->leftJoin('family f', 'f.id=fu.familyid')
            ->where($map)
            ->order('r.addtime desc')
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['votes'] = (string) round($v['votes'] / 100, 2);
            return $v;
        });
        $lists->appends($data);
        $page = $lists->render();
        $this->assign('lists', $lists);
        $this->assign('type', $this->getTypes());
        $this->assign('status', $this->getCheckStatus());
        $this->assign("page", $page);

        return $this->fetch();
    }
}
