<?php

/**
 * 充值记录
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ChargeController extends AdminbaseController
{
    protected function getStatus($k = '')
    {
        $status = [
            '0' => '未支付',
            '1' => '已完成',
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
            '3' => '苹果支付',
            '4' => '支付宝当面付',
        ];
        if ($k === '') {
            return $type;
        }

        return isset($type[$k]) ? $type[$k] : '';
    }

    protected function getAmbient($k = '')
    {
        $ambient = [
            "1" => [
                '0' => 'App',
                '1' => 'PC',
            ],
            "2" => [
                '0' => 'App',
                '1' => '公众号',
                '2' => 'PC',
            ],
            "3" => [
                '0' => '沙盒',
                '1' => '生产',
            ],
            "4" => [
                '0' => 'App',
                '1' => 'PC',
            ],
        ];

        if ($k === '') {
            return $ambient;
        }

        return isset($ambient[$k]) ? $ambient[$k] : '';
    }

    function index()
    {
        $data = $this->request->param();
        $map  = [];

        $start_time = $data['start_time'] ?? '';
        $end_time   = $data['end_time'] ?? '';
        $today = strtotime(date('Y-m-d 00:00:00'));
        if ($start_time) {
            $map[] = ['c.addtime', '>=', strtotime($start_time)];
        }

        if ($end_time) {
            $map[] = ['c.addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }
        if (!$start_time && !$end_time) {
            $map[] = ['c.addtime', '>=', $today];
        }
        $status = isset($data['status']) ? $data['status'] : '';
        if ($status != '') {
            $map[] = ['c.status', '=', $status];
        } else {
            $map[] = ['c.status', '=', 1];
        }
        $uid = $data['uid'] ?? 0;
        if ($uid) {
            $map[] = ['uid', '=', $uid];
        }

        $keyword = isset($data['keyword']) ? $data['keyword'] : '';
        if ($keyword != '') {
            $map[] = ['orderno|trade_no', 'like', '%' . $keyword . '%'];
        }
        $lists = Db::name("charge_user")
            ->alias('c')
            ->field('c.id,c.uid,c.touid,u.user_nicename,c.money,c.coin,orderno,c.type,u.create_time,c.addtime,trade_no,c.status,ambient')
            ->join('user u', 'c.uid=u.id')
            ->where($map)
            ->order("c.id desc")
            ->paginate(20);
        $moneysum = Db::name("charge_user")->where([
                ['status', '=', 1],
                ['addtime', '>', $today]
            ])->sum('money') ?? 0;
        $lists->each(function ($v, $k) use (&$moneysum) {
            if ($v['uid'] != $v['touid']) {
                $v['charge_type'] = '代充'. '('. $v['touid'] . ')';
            } else {
                $v['charge_type'] = "直充";
            }
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        $this->assign('status', $this->getStatus());
        $this->assign('type', $this->getTypes());
        $this->assign('ambient', $this->getAmbient());

        $this->assign('moneysum', $moneysum);

        return $this->fetch();
    }

    function setPay()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id) {
            $result = Db::name("charge_user")->where(["id"     => $id,
                                                      "status" => 0,
            ])->find();
            if ($result) {

                /* 更新会员虚拟币 */
                $coin = $result['coin'] + $result['coin_give'];
                Db::name("user")->where("id='{$result['touid']}'")
                    ->setInc("coin", $coin);
                /* 更新 订单状态 */
                Db::name("charge_user")->where("id='{$result['id']}'")
                    ->update(["status" => 1]);

                $action = "确认充值：{$id}";
                setAdminLog($action);
                $this->success('操作成功');
            } else {
                $this->error('数据传入失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }


    function del()
    {
        $id = $this->request->param('id', 0, 'intval');

        $rs = DB::name('charge_user')->where("id={$id}")->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }

        $action = "删除充值记录：{$id}";
        setAdminLog($action);

        $this->success("删除成功！");

    }

    function export()
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
        }

        $keyword = isset($data['keyword']) ? $data['keyword'] : '';
        if ($keyword != '') {
            $map[] = ['uid|orderno|trade_no', 'like', '%' . $keyword . '%'];
        }

        $xlsName = "充值记录";
        $xlsData = Db::name("charge_user")
            ->field('id,uid,money,coin,coin_give,orderno,type,trade_no,status,addtime')
            ->where($map)
            ->order('id desc')
            ->select()
            ->toArray();
        foreach ($xlsData as $k => $v) {
            $userinfo                     = getUserInfo($v['uid']);
            $xlsData[$k]['user_nicename'] = $userinfo['user_nicename'] . "("
                . $v['uid'] . ")";
            $xlsData[$k]['addtime']       = date("Y-m-d H:i:s", $v['addtime']);
            $xlsData[$k]['type']          = $this->getTypes($v['type']);
            $xlsData[$k]['status']        = $this->getStatus($v['status']);
        }

        $action = "导出充值记录：" . Db::name("charge_user")->getLastSql();
        setAdminLog($action);

        $cellName = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $xlsCell  = [
            ['id', '序号'],
            ['user_nicename', '会员'],
            ['money', '人民币金额'],
            ['coin', '兑换点数'],
            ['coin_give', '赠送点数'],
            ['orderno', '商户订单号'],
            ['type', '支付类型'],
            ['trade_no', '第三方支付订单号'],
            ['status', '订单状态'],
            ['addtime', '提交时间'],
        ];
        exportExcel($xlsName, $xlsCell, $xlsData, $cellName);
    }

}
