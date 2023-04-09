<?php

/**
 * 管理员手动充值记录
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ManualController extends AdminbaseController
{
    protected function getReason($k = '')
    {
        $reason = [
            '1' => '运营扶持',
            '2' => '主播奖励',
            '3' => '家族长奖励',
            '4' => '充值返点',
            '5' => '内部测试',
            '6' => '其他',
        ];
        if ($k === '') {
            return $reason;
        }
        return isset($reason[$k]) ? $reason[$k] : '';
    }

    function index()
    {
        $data = $this->request->param();
        $map  = [];

        $start_time = $data['start_time'] ?? 0;
        $end_time   = $data['end_time'] ?? 0;
        $type  = $data['type'] ?? 0;

        if ($start_time) {
            $map[] = ['a.addtime', '>=', strtotime($start_time)];
        }

        if ($end_time) {
            $map[] = ['a.addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }

        if ($type) {
            $map[] = ['a.type', '=', $type];
        }
        $uid = $data['uid'] ?? 0;
        if ($uid) {
            $map[] = ['touid', '=', $uid];
        }
        $lists = Db::name("charge_admin")
            ->alias('a')
            ->field('touid,a.coin,a.type,a.reason,a.remark,a.addtime,a.admin,u.user_nicename,a.s_type')
            ->join('user u', 'a.touid=u.id')
            ->where($map)
            ->order("a.addtime desc")
            ->paginate(20);
        $coin = 0;
        $uidArr = [];
        $lists->each(function ($v, $k) use (&$coin, &$uidArr) {
            if ($v['type'] == 2) {
                $v['coin'] = round($v['coin'] / 100, 2);
            }
            $uidArr[] = $v['touid'];
            $coin += $v['coin'];
            return $v;
        });
        $num = count(array_unique($uidArr));
        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        $this->assign('reason', $this->getReason());
        $this->assign('coin', $coin);
        $this->assign('num', $num);

        return $this->fetch();
    }

    function add()
    {
        $this->assign('reason', $this->getReason());
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $title = $data['title'] ?? '';
            $content = $data['content'] ?? '';
            unset($data['title'], $data['content']);
            $touid = $data['touid'] ?? 0;
            $s_type = $data['s_type'] ?? 0;
            $type = $data['type'] ?? 0;
            if (!$touid) {
                $this->error("请填写用户ID");
            }
            if (!$s_type) {
                $this->error("请选择充值类型");
            }
            if (!$type) {
                $this->error("请选择类型");
            }
            $user = Db::name("user")->where(["id" => $touid])->field("id,coin,votes")->find();
            if (!$user) {
                $this->error("会员不存在，请更正");
            }
            $coin = $data['coin'] ?? 0;
            if (!$coin) {
                $this->error("请填写充值点数");
            }
            $adminid   = cmf_get_current_admin_id();
            $admininfo = Db::name("user")->where(["id" => $adminid])->value("user_login");
            $data['admin'] = $admininfo;
            $ip            = get_client_ip(0, true);

            $data['ip'] = ip2long($ip);

            $data['addtime'] = time();
            Db::startTrans();
            try {
                if ($type == 2) {
                    $data['coin'] = $coin * 100;
                }
                $id = DB::name('charge_admin')->insertGetId($data);
                if ($type == 1) {
                    $str = 'coin';
                    $coinData = [
                        "uid"     => $touid,
                        'totalcoin' => $coin,
                        'addtime' =>  time(),
                    ];
                }
                if ($type == 2) {
                    $str = 'votes';
                    $coin = $coin * 100;
                    $voterData = [
                        "uid"     => $touid,
                        'total' => $coin,
                        'votes' => $coin,
                        'addtime' =>  time(),
                        'actionid' => $id
                    ];
                }
                if ($s_type == 1) {
                    $coinData['action'] = 8;
                    $coinData['type'] = 1;
                    $voterData['action'] = 7;
                    $voterData['type'] = 1;
                    Db::name("user")->where(["id" => $touid])->setInc($str, $coin);
                }
                if ($s_type == 2) {
                    if ($coin > $user[$str]) {
                        $this->error("用户余额不足！");
                    }
                    $coinData['action'] = 10;
                    $coinData['type'] = 0;
                    $voterData['action'] = 8;
                    $voterData['type'] = 1;
                    Db::name("user")->where(["id" => $touid])->setDec($str, $coin);
                }
                if ($type == 1) {
                    DB::name("user_coinrecord")->insert($coinData);
                }
                if ($type == 2) {
                    DB::name("user_voterecord")->insert($voterData);
                }
                Db::commit();
                if ($content) {
                    $insertData = [
                        'uid' => $touid,
                        'title' => $title,
                        'content' => $content,
                        'type' => 1,
                        'addtime' => time(),
                    ];
                    DB::name('message')->insert($insertData);
                }
            } catch (\Exception $e) {
                Db::rollback();
                var_dump($e->getMessage());die;
                $this->error("充值失败！");
            }
            $this->success("充值成功！");
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

        $xlsName = "手动充值记录";
        $xlsData = Db::name("charge_admin")
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

        $action = "导出手动充值记录：" . Db::name("charge_admin")->getLastSql();
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

}
