<?php

/**
 * 靓号管理
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class LiangsaleController extends AdminbaseController
{

    protected function getStatus($k = '')
    {
        $status = [
            '0' => '出售中',
            '1' => '已售',
            '2' => '停售',
        ];

        if ($k == '') {
            return $status;
        }

        return $status[$k];
    }

    function index()
    {
        $data   = $this->request->param();
        $map    = [['uid', '<>', 0]];
        $status = isset($data['status']) ? $data['status'] : '';
        if ($status != '') {
            $map[] = ['status', '=', $status];
        }

        $length = isset($data['length']) ? $data['length'] : '';
        if ($length != '') {
            $map[] = ['length', '=', $length];
        }

        $uid = isset($data['uid']) ? $data['uid'] : '';
        if ($uid != '') {
            $map[] = ['uid', '=', $uid];
        }

        $keyword = isset($data['keyword']) ? $data['keyword'] : '';
        if ($keyword != '') {
            $map[] = ['name', 'like', '%' . $keyword . '%'];
        }

        $lists = Db::name("liang")
            ->where($map)
            ->order("id DESC")
            ->paginate(20);

        $lists->each(function ($v, $k) {
            if ($v['uid'] > 0) {
                $v['userinfo']    = getUserInfo($v['uid']);
                if (!$v['expire']) {
                    $v['remain_time'] = '永久';
                } else {
                    $remain = $v['end_time'] - time();
                    if ($remain <= 0) {
                        $v['remain_time'] = "已过期";
                    } else {
                        $hours = intval( $remain / 3600);
                        $v['remain_time'] = $hours . ":" .gmstrftime('%M:%S', $remain);
                    }
                }
            }
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        $this->assign('status', $this->getStatus());

        $length = Db::name("liang")
            ->field("length")
            ->order("length asc")
            ->group("length")
            ->select();

        $this->assign('length', $length);


        return $this->fetch();

    }

    //排序
    public function listOrder()
    {
        $model = DB::name('liang');
        parent::listOrders($model);

        $action = "修改靓号排序";
        setAdminLog($action);

        $this->success("排序更新成功！");

    }


    function add()
    {
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {

            $configpub = getConfigPub();

            $data = $this->request->param();

            $name = $data['name'];

            if ($name == "") {
                $this->error("靓号不能为空");
            }

            $coin = $data['coin'];
            if ($coin == "") {
                $this->error("请填写所需" . $configpub['name_coin']);
            }

            if (!is_numeric($coin)) {
                $this->error("请确认所需" . $configpub['name_coin']);
            }

            $score = $data['score'];
            if ($score == "") {
                $this->error("请填写所需" . $configpub['name_score']);
            }

            if (!is_numeric($score)) {
                $this->error("请确认所需" . $configpub['name_score']);
            }

            $isexist = DB::name('liang')->where(["name" => $name])->find();

            if ($isexist) {
                $this->error('该靓号已存在');
            }

            $data['length']  = mb_strlen($name);
            $data['addtime'] = time();

            $id = DB::name('liang')->insertGetId($data);
            if (!$id) {
                $this->error("添加失败！");
            }

            $action = "添加靓号：{$id}";
            setAdminLog($action);

            $this->success("添加成功！");

        }
    }

    function edit()
    {
        $id = $this->request->param('id', 0, 'intval');

        $data = Db::name('liang')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }

        $this->assign('data', $data);
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {

            $configpub = getConfigPub();

            $data = $this->request->param();

            $id   = $data['id'];
            $name = $data['name'];

            if ($name == "") {
                $this->error("靓号不能为空");
            }

            $coin = $data['coin'];
            if ($coin == "") {
                $this->error("请填写所需" . $configpub['name_coin']);
            }

            if (!is_numeric($coin)) {
                $this->error("请确认所需" . $configpub['name_coin']);
            }

            $score = $data['score'];
            if ($score == "") {
                $this->error("请填写所需" . $configpub['name_score']);
            }

            if (!is_numeric($score)) {
                $this->error("请确认所需" . $configpub['name_score']);
            }


            $isexist = DB::name('liang')->where([
                ['id', '<>', $id],
                ['name', '=', $name],
            ])->find();

            if ($isexist) {
                $this->error('该靓号已存在');
            }

            $rs = DB::name('liang')->update($data);
            if ($rs === false) {
                $this->error("修改失败！");
            }

            $action = "编辑靓号：{$data['id']}";
            setAdminLog($action);

            $this->success("修改成功！");
        }
    }

    function recycle()
    {
        $id = $this->request->param('id', 0, 'intval');

        $query = Db::name('liang')
            ->where("id={$id}")
            ->find();
        if (!$query) {
            $this->error("信息错误");
        }
        $delete = Db::name('liang')
            ->where("id={$id}")
            ->delete();
        delcache("user:liang:liang_info_" . $query['uid']);
        if ($delete === false) {
            $this->error("操作失败");
        }
        $this->success("修改成功！", url('liangsale/index'));
    }

}
