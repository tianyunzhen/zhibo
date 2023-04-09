<?php

/**
 * 家族
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use app\common\Jpush;
use app\common\Message;
use think\Db;

class FamilycheckController extends AdminbaseController
{

    protected function getState($k = '')
    {
        $status = [
            '1' => '未审核',
            '2' => '审核通过',
            '3' => '审核不通过',
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
        $start_time = $data['start_time'] ?? '';
        $end_time   = $data['end_time'] ?? '';
        if ($start_time) {
            $map[] = ['addtime', '>=', strtotime($start_time)];
        }
        if ($end_time) {
            $map[] = ['addtime', '<=', strtotime($end_time) + 60 * 60 * 24];
        }
        $state = $data['state'] ?? 0;
        if ($state) {
            $map[] = ['state', '=', $state];
        } else {
            $map[] = ['state', '=', 1];
        }

        $uid = $data['uid'] ?? 0;
        if ($uid) {
            $map[] = ['uid', '=', $uid];
        }
        $keyword = $data['keyword'] ?? '';
        if ($keyword) {
            $map[] = ['name', 'like', '%' . $keyword . '%'];
        }
        $lists = Db::name("family")
            ->where($map)
            ->order("addtime DESC")
            ->paginate(20);
        $lists->each(function ($v, $k) {
            $v['user_nicename'] = getUserInfo($v['uid'])['user_nicename'];
            $v['real_name']     = Db::name("user_auth")
                    ->where(['uid' => $v['uid']])
                    ->value('real_name') ?? '';
            return $v;
        });
        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);
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

        $this->assign('data', $data);

        $this->assign("state", $this->getState());

        return $this->fetch();

    }

    function editPost()
    {
//        if ($this->request->isPost()) {
        $data          = $this->request->param();
        $data['istip'] = 1;

        $rs = DB::name('family')->update($data);
        if ($rs === false) {
            $this->error("修改失败！");
        }
        $familyAdminId = Db::name("family")
            ->where(['id' => $data['id']])->value('uid');
//        $push = new Jpush($familyAdminId);
        $title = "家族申请未通过";
//        $content = Jpush::GMLH;
        if ($data['state'] == 2) {
            $familyAdminExist = Db::name("family_user")
                ->where(['familyid' => $data['id'], 'is_admin' => 1])
                ->find();
            if (!$familyAdminExist) {
                Db::name('family_user')->insert(
                    [
                        'familyid' => $data['id'],
                        'uid'      => $familyAdminId,
                        'is_admin' => 1,
                        'addtime'  => time(),
                    ]
                );
            }
            $codeCount = Db::name("family_code")
                ->where(['family_id' => $data['id']])->count();
            if (!$codeCount) {
                $insertData = [];
                for ($i = 0; $i < 5000; $i++) {
                    $tem = [
                        'family_id' => $data['id'],
                        'addtime'   => time(),
                    ];
                    do {
                        $invite_code        = getRandomString(8);
                        $codeExist          = Db::name("family_code")
                            ->where(['invite_code' => $invite_code])->find();
                        $tem['invite_code'] = $invite_code;
                    } while ($codeExist);
                    $insertData[] = $tem;
                }
                Db::name("family_code")->insertAll($insertData);
            }
            $title = "家族申请通过";
            $content = Jpush::RHCG;
        }
        if (in_array($data['state'], [2, 3])) {
//            $push->sendAlias($title, $content);
            Message::addMsg($title, $content, $familyAdminId);
        }
        $action = "修改家族信息：{$data['id']}";
        setAdminLog($action);
        $this->success("修改成功！", url('familycheck/index'));
//		}
    }

    function add()
    {
        $users = Db::name("operation_user")
            ->alias('o')
            ->join('user u', 'o.admin_id=u.id')
            ->field('admin_id,user_login')
            ->select();
        $this->assign('users', $users);
        return $this->fetch();
    }


    function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $name = $data['name'];
            $operatorId = $data['operator_id'] ?? 0;
            if (!$operatorId) {
                $this->error('没有选择对接运营');
            }
            if (!$name) {
                $this->error('请输入家族名称');
            } else {
                $check = Db::name('family')->where([
                    ['name', '=', $name],
                    ['state', '<>', 4],
                ])->find();
                if ($check) {
                    $this->error('名称已存在');
                }
            }
            $uid = $data['uid'];
            if (!$uid) {
                $this->error('请输入家族长uid');
            } else {
                $have = Db::name('family_user')->where([
                    ['uid', '=', $uid],
                    ['state', '<>', 3],
                ])->find();
                if ($have) {
                    $this->error('该用户已存在家族');
                }
            }
            Db::startTrans();
            try {
                $data['addtime'] = time();
                $data['state'] = 2;
                $id = DB::name('family')->insertGetId($data);
                $newData = [
                    'uid' => $uid,
                    'familyid' => $id,
                    'addtime' => time(),
                    'state' => 1,
                    'is_admin' => 1,
                ];
                DB::name('family_user')->insert($newData);
                $insertData = [];
                $addTime = time();
                for ($i = 0; $i < 500; $i++) {
                    $tem = [
                        'family_id' => $id,
                        'addtime'   => $addTime,
                    ];
                    do {
                        $invite_code        = getRandomString(8);
                        $codeExist          = Db::name("family_code")
                            ->where(['invite_code' => $invite_code])->find();
                        $tem['invite_code'] = $invite_code;
                    } while ($codeExist);
                    $insertData[] = $tem;
                }
                Db::name("family_code")->insertAll($insertData);
                Db::commit();
//                $action = "新增家族申请：{$id}";
//                setAdminLog($action);
                 $this->success("添加成功！");
            } catch (\Exception $e) {
//                var_dump($e->getCode(), $e->getMessage());die;
                Db::rollback();
                $this->error("添加失败！");
            }
        }
    }
}