<?php

/**
 * 家族
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class FamilyController extends AdminbaseController
{

    protected function getState($k = '')
    {
        $status = [
            '1' => '未审核',
            '2' => '审核通过',
            '3' => '审核失败',
        ];
        if ($k === '') {
            return $status;
        }

        return isset($status[$k]) ? $status[$k] : '';
    }

    function index()
    {

        $data  = $this->request->param();
        $map   = [];
        $map[] = ['state', '=', 2];

        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time   = isset($data['end_time']) ? $data['end_time'] : '';

        if ($start_time != "") {
            $map[] = ['addtime', '>=', strtotime($start_time)];
        }

        if ($end_time != "") {
            $map[] = ['addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }

        $state = isset($data['state']) ? $data['state'] : '';
        if ($state != '') {
            $map[] = ['state', '=', $state];
        }

        $uid = isset($data['uid']) ? $data['uid'] : 0;
        if ($uid != '') {
            $map[] = ['uid', '=', $uid];
        }

        $keyword = isset($data['keyword']) ? $data['keyword'] : '';
        if ($keyword != '') {
            $map[] = ['name', 'like', '%' . $keyword . '%'];
        }
        $lists = Db::name("family")
            ->field('f.id,f.name,f.uid,u.user_nicename,state,f.addtime,u1.user_nicename as operator,f.relieve_time,f.disable')
            ->alias('f')
            ->join('user u', 'f.uid=u.id')
            ->leftJoin('user u1', 'f.operator_id=u1.id')
            ->where($map)
            ->order("addtime DESC")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['member'] = Db::name('family_user')
                    ->where(['familyid' => $v['id'], 'state' => 1])->count() ?? 0;
             $totalProfit = Db::name('family_profit')
                ->field('sum(profit_anthor) as profit_anthor,sum(profit) as profit_family')
                ->where(['familyid' => $v['id']])->select()->toArray();
            $v['anthor_profit'] = round(($totalProfit[0]['profit_anthor'] ?? 0) / 100, 2);
            $v['family_profit'] = round(($totalProfit[0]['profit_family'] ?? 0) / 100, 2);
            $v['family_water'] = $v['anthor_profit'];
            $familyUser = Db::name("family_user")
                ->where(['familyid' => $v['id'], 'state' => 1])->field('uid')->select()->toArray();
            $userIds = array_column($familyUser, 'uid');
            $liveRecord = Db::name("live_record")
                ->whereIn('uid', $userIds)->field('sum(endtime-starttime) as live_length')->select()->toArray();
            $live_length = $liveRecord[0]['live_length'] ?? 0;
            if ($live_length) {
                $hours = intval($live_length / 3600);
                $time1 = $hours . ":" . gmstrftime('%M:%S', $live_length);
                $v['live_length'] = $time1;
            } else {
                $v['live_length'] = "00:00:00";
            }
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);
        $this->assign("state", $this->getState());

        return $this->fetch();
    }

    /** 解约 */
    function disable()
    {
        $id = $this->request->param('id', 0, 'intval');

        $rs = DB::name('family')->where("id={$id}")->update(['state' => 4, 'disable' => 1]);
        DB::name('family_user')->where(['familyid' => $id])->delete();
        DB::name('family_code')->where(['family_id' => $id])->delete();
        if ($rs === false) {
            $this->error("解约失败！", url('family/index'));
        }

        $action = "解约家族：{$id}";
        setAdminLog($action);

        $this->success("解约成功！", url('family/index'));

    }

    /** 恢复 */
    function enable()
    {
        $id = $this->request->param('id', 0, 'intval');

        $rs = DB::name('family')->where("id={$id}")->update(['disable' => 0, 'relieve_time' => 0]);
        if ($rs === false) {
            $this->error("恢复失败！", url('family/index'));
        }

        $action = "恢复家族：{$id}";
        setAdminLog($action);

        $this->success("恢复成功！", url('family/index'));

    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');

        $rs = DB::name('family')->where("id={$id}")->update(['state' => 4]);
        if ($rs === false) {
            $this->error("删除失败！", url('family/index'));
        }

        DB::name("family_profit")->where(["familyid" => $id])->delete();

        $data = [
            'state'         => 3,//退出
            'signout'       => 2,
            'signout_istip' => 2,
        ];
        DB::name("family_user")->where(["familyid" => $id])->update($data);
        DB::name('family_code')->where(['family_id' => $id])->delete();

        $action = "删除家族：{$id}";
        setAdminLog($action);

        $this->success("删除成功！", url('family/index'));

    }

    function profit()
    {
        $data = $this->request->param();
        $uid  = $this->request->param('uid', 0, 'intval');

        $map = [];

        $ufamilyinfo = DB::name("family_user")->where(["uid" => $uid])->find();
        if ($ufamilyinfo) {
            $map['uid'] = $uid;
        } else {
            $familyinfo      = DB::name("family")->where(["uid" => $uid])
                ->find();
            $map['familyid'] = $familyinfo['id'];
        }


        $lists = Db::name("family_profit")
            ->where($map)
            ->order("id DESC")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['userinfo'] = getUserInfo($v['uid']);
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        $total_family = Db::name("family_profit")->where($map)->sum("profit");
        if (!$total_family) {
            $total_family = 0;
        }
        $this->assign('total_family', $total_family);

        $total_anthor = Db::name("family_profit")->where($map)
            ->sum("profit_anthor");
        if (!$total_anthor) {
            $total_anthor = 0;
        }
        $this->assign('total_anthor', $total_anthor);
        return $this->fetch();
    }


    function cash()
    {
        $data = $this->request->param();
        $uid  = $this->request->param('uid', 0, 'intval');

        $map   = [];
        $map[] = ['uid', '=', $uid];

        $ufamilyinfo = DB::name("family_user")->where(["uid" => $uid])->find();
        if ($ufamilyinfo) {
            $map[] = ['addtime', '>', $ufamilyinfo['addtime']];
        }


        $lists = Db::name("cash_record")
            ->where($map)
            ->order("id DESC")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['userinfo'] = getUserInfo($v['uid']);
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        $total = Db::name("cash_record")->where('status=1')->where($map)
            ->sum("money");
        if (!$total) {
            $total = 0;
        }
        $this->assign('total', $total);

        $this->assign("state", $this->getState());

        return $this->fetch();

    }


    function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        $data = Db::name('family')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }
        $data['userinfo'] = getUserInfo($data['uid']);
        if ($data['operator_id']) {
            $data['operator'] = Db::name('user')
                ->where("id={$data['operator_id']}")
                ->value('user_nicename') ?? '暂无';
        } else {
            $data['operator'] = "暂无";
        }
        if (!$data['badge']) {
            $data['badge'] = $data['userinfo']['avatar'];
        }
        $this->assign('data', $data);

        $this->assign("state", $this->getState());

        return $this->fetch();

    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $data          = $this->request->param();
            $data['istip'] = 1;

            $rs = DB::name('family')->update($data);
            if ($rs === false) {
                $this->error("修改失败！");
            }
            $codeCount = Db::name("family_code")
                ->where(['family_id' => $data['id']])->count();
            if (0 && $data['state'] == 2) {
                $familyAdminExist = Db::name("family_user")
                    ->where(['familyid' => $data['id'], 'is_admin' => 1])
                    ->find();
                if (!$familyAdminExist) {
                    $familyAdminId = Db::name("family")
                        ->where(['id' => $data['id']])->value('uid');
                    Db::name('family_user')->insert(
                        [
                            'familyid' => $data['id'],
                            'uid'      => $familyAdminId,
                            'is_admin' => 1,
                            'addtime'  => time(),
                        ]
                    );
                }
                if (!$codeCount) {
                    $insertData = [];
                    for ($i = 0; $i < 500; $i++) {
                        $tem = [
                            'family_id' => $data['id'],
                            'addtime'   => time(),
                        ];
                        do {
                            $invite_code        = getRandomString(8);
                            $codeExist          = Db::name("family_code")
                                ->where(['invite_code' => $invite_code])
                                ->find();
                            $tem['invite_code'] = $invite_code;
                        } while ($codeExist);
                        $insertData[] = $tem;
                    }
                    Db::name("family_code")->insertAll($insertData);
                }
            }

            $action = "修改家族信息：{$data['id']}";
            setAdminLog($action);

            $this->success("修改成功！", url('family/index'));

        }
    }


}