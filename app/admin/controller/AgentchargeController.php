<?php

/**
 * 代理充值记录
 */

namespace app\admin\controller;

use app\common\Jpush;
use app\common\Message;
use cmf\controller\AdminBaseController;
use think\Db;

class AgentchargeController extends AdminbaseController
{
    function index()
    {
        $data = $this->request->param();
        $map  = [];

        $start_time = $data['start_time'] ?? '';
        $end_time   = $data['end_time'] ?? '';

        if ($start_time) {
            $map[] = ['a.addtime', '>=', strtotime($start_time)];
        }

        if ($end_time) {
            $map[] = ['a.addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }

        $uid = $data['uid'] ?? 0;
        if ($uid) {
            $map[] = ['touid', '=', $uid];
        }

        $lists = Db::name("charge_agent")
            ->alias('a')
            ->field('a.id,a.touid,u.user_nicename,a.admin,a.coin,a.addtime,a.ip,u.agent_money')
            ->join('user u', 'a.touid=u.id')
            ->where($map)
            ->order("a.id desc")
            ->paginate(20);
        $coin = 0;
        $lists->each(function ($v, $k) use (&$coin) {
            $v['ip']       = long2ip($v['ip']);
            $coin += $v['coin'];
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        $this->assign('coin', $coin);

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

            $touid = $data['touid'];

            if ($touid == "") {
                $this->error("请填写代理ID");
            }

            $uid = Db::name("user")->where(["id" => $touid, 'is_agent' => 1])
                ->value("id");
            if (!$uid) {
                $this->error("代理不存在，请更正");

            }

            $coin = $data['coin'];
            if ($coin == "") {
                $this->error("请填写充值点数");
            }

            $adminid   = cmf_get_current_admin_id();
            $admininfo = Db::name("user")->where(["id" => $adminid])
                ->value("user_login");

            $data['admin'] = $admininfo;
            $ip            = get_client_ip(0, true);

            $data['ip'] = ip2long($ip);

            $data['addtime'] = time();

            Db::startTrans();
            try {
                DB::name('charge_agent')->insertGetId($data);
                Db::name("user")->where(["id" => $touid])->setInc("agent_money", $coin);
                $insertData = [
                    "type"      => 1,
                    "action"    => 2,
                    "uid"       => $touid,
                    "touid"     => 0,
                    "totalcoin" => $coin,
                    "addtime"   => time(),
                ];
                DB::name('user_coinrecord')->insert($insertData);
                Db::commit();
                Message::addMsg('代理充值', Jpush::DLCZ . $coin . '丫币', $touid);
            } catch (\Exception $e) {
                Db::rollback();
                $this->error("充值失败！", url('agentcharge/index'));
            }
            $this->success("充值成功！", url('agentcharge/index'));
        }
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
            $map[] = ['touid', '=', $keyword];
        }

        $xlsName = "代理充值记录";
        $xlsData = Db::name("charge_agent")
            ->where($map)
            ->order("id desc")
            ->select()
            ->toArray();

        foreach ($xlsData as $k => $v) {
            $userinfo = getUserInfo($v['touid']);

            $xlsData[$k]['user_nicename'] = $userinfo['user_nicename'] . '('
                . $v['touid'] . ')';
            $xlsData[$k]['addtime']       = date("Y-m-d H:i:s", $v['addtime']);
        }

        $action = "导出代理充值记录：" . Db::name("charge_admin")->getLastSql();
        setAdminLog($action);
        $cellName = ['A', 'B', 'C', 'D', 'E', 'F'];
        $xlsCell  = [
            ['id', '序号'],
            ['admin', '管理员'],
            ['user_nicename', '会员 (账号)(ID)'],
            ['coin', '充值点数'],
            ['ip', 'IP'],
            ['addtime', '时间'],
        ];
        exportExcel($xlsName, $xlsCell, $xlsData, $cellName);
    }


    function detail()
    {
        $data = $this->request->param();
        $uid = $data['uid'] ?? 0;
        $map = [];
        if ($uid) {
            $map[] = ['uid', '=', $uid];
        }
//        $map  = [
//            ['uid', '=', $data['uid']]
//        ];
//
//        $start_time = $data['start_time'] ?? '';
//        $end_time   = $data['end_time'] ?? '';
//
//        if ($start_time) {
//            $map[] = ['t.addtime', '>=', strtotime($start_time)];
//        }
//
//        if ($end_time) {
//            $map[] = ['t.addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
//        }
//
//        $touid = $data['touid'] ?? 0;
//        if ($touid) {
//            $map[] = ['touid', '=', $touid];
//        }

        $lists = Db::name("transfer_record")
            ->alias('t')
            ->join('user u', 't.touid=u.id')
            ->field('t.id,t.touid,u.user_nicename,t.money,t.addtime')
            ->where($map)
            ->order("t.id desc")
            ->paginate(20);

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        return $this->fetch();
    }
}
