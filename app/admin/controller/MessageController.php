<?php

/**
 * 官方消息管理
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;


class MessageController extends AdminbaseController
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
            $map[] = ['uid', 'like', "%" . $keyword . "%"];
        }

        $lists = DB::name("message")
            ->where($map)
            ->order('id desc')
            ->paginate(20);

        $lists->each(function ($v, $k) {
            return $v;
        });

        $lists->appends($data);
        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

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
            $title = $data['title'];
            $content = $data['content'];
            $touid   = $data['uid'];
            $content = str_replace("\r", "", $content);
            $content = str_replace("\n", "", $content);
            if ($touid) {
                $touid = str_replace("\r", "", $touid);
                $touid = str_replace("\n", "", $touid);
                $touid = preg_replace("/,|，/", ",", $touid);
                if ($content == '') {
                    $this->error('推送内容不能为空');
                }
                $uids  = preg_split('/,|，/', $touid);
                $insertData = [];
                foreach ($uids as $uid) {
                    $tem = [
                        'uid'   => $uid,
                        'title' => $title,
                        'content' => $content,
                        'type' => 1,
                        'addtime' => time(),
                    ];
                    $insertData[] = $tem;
                }
            } else {
                $insertData = [
                    [
                        'title' => $title,
                        'content' => $content,
                        'type' => 2,
                        'addtime' => time(),
                    ],
                ];
            }
            $result = DB::name('message')->insertAll($insertData);
            if (!$result) {
                $this->error("推送失败！");
            }
            $this->success("推送成功！", url('message/index'));
        }

    }

}
