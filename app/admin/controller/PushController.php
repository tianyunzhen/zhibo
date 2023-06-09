<?php

/**
 * 推送管理
 */

namespace app\admin\controller;

use app\common\Message;
use cmf\controller\AdminBaseController;
use app\common\Jpush;
use think\Db;


class PushController extends AdminbaseController
{

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

        $keyword = isset($data['keyword']) ? $data['keyword'] : '';
        if ($keyword != '') {
            $map[] = ['touid|adminid', 'like', "%" . $keyword . "%"];
        }

        $lists = DB::name("pushrecord")
            ->where($map)
            ->order('id desc')
            ->paginate(20);

        $lists->each(function ($v, $k) {
            $v['ip'] = long2ip($v['ip']);
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        return $this->fetch();
    }

    function del()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id) {
            $result = DB::name("pushrecord")->delete($id);
            if ($result) {
                $action = "删除推送信息：{$id}";
                setAdminLog($action);

                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    function add()
    {
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();

            $content = $data['content'];
            $touid   = $data['touid'];

            $content = str_replace("\r", "", $content);
            $content = str_replace("\n", "", $content);

            $touid = str_replace("\r", "", $touid);
            $touid = str_replace("\n", "", $touid);
            $touid = preg_replace("/,|，/", ",", $touid);

            if ($content == '') {
                $this->error('推送内容不能为空');
            }

            /* 极光推送 */
//            $configpri     = getConfigPri();
//            $app_key       = $configpri['jpush_key'];
//            $master_secret = $configpri['jpush_secret'];

//            if (!$app_key || !$master_secret) {
//                $this->error('请先设置推送配置');
//            }
//            if ($app_key && $master_secret) {
                if ($touid != '') {
                    $uids  = preg_split('/,|，/', $touid);
                }
                foreach ($uids as $uid) {
                    Message::addMsg('系统消息', $content, $uid);
//                    $push = new Jpush($uid);
//                    $push->sendByRegistrationId('系统消息', $content);
                }
//            }
            $this->success("推送成功！");
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

        $keyword = isset($data['keyword']) ? $data['keyword'] : '';
        if ($keyword != '') {
            $map[] = ['touid|adminid', 'like', "%" . $keyword . "%"];
        }

        $xlsData = DB::name("pushrecord")
            ->where($map)
            ->order('id desc')
            ->select()
            ->toArray();

        foreach ($xlsData as $k => $v) {
            if (!$v['touid']) {
                $xlsData[$k]['touid']   = '所有会员';
                $xlsData[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
            }
        }

        $action = "导出推送信息：" . DB::name("pushrecord")->getLastSql();
        setAdminLog($action);
        $cellName = ['A', 'B', 'C', 'D', 'E', 'F'];
        $xlsCell  = [
            ['id', '序号'],
            ['admin', '管理员'],
            ['ip', 'IP'],
            ['touid', '推送对象'],
            ['content', '推送内容'],
            ['addtime', '提交时间'],
        ];
        exportExcel($xlsName, $xlsCell, $xlsData, $cellName);
    }
}
