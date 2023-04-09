<?php

/**
 * 直播监控
 */

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class MonitorController extends AdminbaseController
{

    protected function getTypes($k = '')
    {
        $type = [
            '1' => '关闭',
            '2' => '警告',
            '3' => '隐藏',
            '4' => '禁播',
            '5' => '更换封面',
        ];
        if ($k == '') {
            return $type;
        }
        return isset($type[$k]) ? $type[$k] : '';
    }

    function index()
    {
        $config       = getConfigPri();
        $this->config = $config;
        $this->assign('config', $config);
        $map = [
            ['l.islive', '=', 1],
            ['l.isvideo', '=', 0],
            ['is_black', '=', 0],
        ];
        $data  = $this->request->param();
        $keyword = $data['keyword'] ?? '';
        if ($keyword) {
            $map[] = ['user_login|user_nicename', 'like', '%' . $keyword . '%'];
        }
        $uid = $data['uid'] ?? 0;
        if ($uid) {
            $map[] = ['uid', '=', $uid];
        }
        $lists = Db::name("live")
            ->alias('l')
            ->join('user u', 'l.uid=u.id')
            ->field('uid,user_nicename,pull,showid,l.stream')
            ->where($map)
            ->order("starttime desc")
            ->paginate(20);

        $lists->each(function ($v, $k) {
            if ($this->config['cdn_switch'] == 5) {
                $auth_url = $v['pull'];
            } else {
                $auth_url = PrivateKeyA('http', $v['stream'] . '.flv', 0);
            }
            $v['url'] = $auth_url;
            return $v;
        });

        $page = $lists->render();

        $this->assign('lists', $lists);

        $this->assign("page", $page);

        return $this->fetch();
    }

    public function full()
    {
        $uid = $this->request->param('uid', 0, 'intval');

        $where['islive'] = 1;
        $where['uid']    = $uid;

        $live   = Db::name("live")->where($where)->find();
        $config = getConfigPri();

        if ($live['title'] == "") {
            $live['title'] = "直播监控后台";
        }

        if ($config['cdn_switch'] == 5) {
            $pull = $live['pull'];
        } else {
            $pull = urldecode(PrivateKeyA('http', $live['stream'] . '.flv', 0));
        }
        $live['pull'] = $pull;
        $this->assign('config', $config);
        $this->assign('live', $live);

        return $this->fetch();
    }

    public function stopRoom()
    {
        $uid = $this->request->param('uid', 0, 'intval');

        $where['islive'] = 1;
        $where['uid']    = $uid;

        $liveinfo = Db::name("live")
            ->field("uid,showid,starttime,title,province,city,stream,lng,lat,type,type_val,liveclassid")
            ->where($where)->find();

        Db::name("live")->where(" uid='{$uid}'")->delete();

        if ($liveinfo) {
            $liveinfo['endtime'] = time();
            $liveinfo['time']    = date("Y-m-d", $liveinfo['showid']);

            $where2           = [];
            $where2['touid']  = $uid;
            $where2['showid'] = $liveinfo['showid'];

            $votes             = Db::name("user_coinrecord")
                ->where($where2)
                ->sum('totalcoin');
            $liveinfo['votes'] = 0;
            if ($votes) {
                $liveinfo['votes'] = $votes;
            }

            $stream = $liveinfo['stream'];
            $nums   = zSize('user_' . $stream);

            hDel("livelist", $uid);
            delcache($uid . '_zombie');
            delcache($uid . '_zombie_uid');
            delcache('attention_' . $uid);
            delcache('user_' . $stream);


            $liveinfo['nums'] = $nums;

            Db::name("live_record")->insert($liveinfo);

            /* 游戏处理 */
            $where3            = [];
            $where3['state']   = 0;
            $where3['liveuid'] = $uid;
            $where3['stream']  = $stream;

            $game = Db::name("game")
                ->where($where3)
                ->find();
            if ($game) {
                $total = Db::name("gamerecord")
                    ->field("uid,sum(coin_1 + coin_2 + coin_3 + coin_4 + coin_5 + coin_6) as total")
                    ->where(["gameid" => $game['id']])
                    ->group('uid')
                    ->select();
                foreach ($total as $k => $v) {

                    Db::name("user")->where(["id" => $v['uid']])->setInc('coin',
                        $v['total']);

                    delcache('user:userinfo_' . $v['uid']);

                    $insert = ["type"      => '1',
                               "action"    => '20',
                               "uid"       => $v['uid'],
                               "touid"     => $v['uid'],
                               "giftid"    => $game['id'],
                               "giftcount" => 1,
                               "totalcoin" => $v['total'],
                               "addtime"   => $nowtime,
                    ];

                    Db::name("user_coinrecord")->insert($insert);
                }

                Db::name("game")->where(["id" => $game['id']])
                    ->save(['state' => '3', 'endtime' => time()]);
                $brandToken = $stream . "_" . $game["action"] . "_"
                    . $game['starttime'] . "_Game";
                delcache($brandToken);
            }
        }
        $action = "监控 关闭直播间：{$uid}";
        setAdminLog($action);
        $this->success("操作成功！");
    }

    function edit()
    {
        $config       = getConfigPri();
        $this->config = $config;
        $this->assign('config', $config);
        $id   = $this->request->param('id', 0, 'intval');
        $data = Db::name('user')
            ->field('id,user_nicename,avatar')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }
        $this->assign("type", $this->getTypes());
        $this->assign('data', $data);
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isAjax()) {
            $data = $this->request->param();
            $uid  = $data['id'];
            $type = $data['s_type'];
            if ($type == 1) {//关闭
                $where['islive'] = 1;
                $where['uid']    = $uid;
                $liveinfo        = Db::name("live")
                    ->field("uid,showid,starttime,title,province,city,stream,lng,lat,type,type_val,liveclassid")
                    ->where($where)
                    ->find();
                Db::name("live")->where(" uid='{$uid}'")->delete();

                if ($liveinfo) {
                    $liveinfo['endtime'] = time();
                    $liveinfo['time']    = date("Y-m-d", $liveinfo['showid']);

                    $where2           = [];
                    $where2['touid']  = $uid;
                    $where2['showid'] = $liveinfo['showid'];

                    $votes             = Db::name("user_coinrecord")
                        ->where($where2)
                        ->sum('totalcoin');
                    $liveinfo['votes'] = 0;
                    if ($votes) {
                        $liveinfo['votes'] = $votes;
                    }

                    $stream = $liveinfo['stream'];
                    $nums   = zSize('user_' . $stream);

                    hDel("livelist", $uid);
                    delcache($uid . '_zombie');
                    delcache($uid . '_zombie_uid');
                    delcache('attention_' . $uid);
                    delcache('user_' . $stream);

                    $liveinfo['nums'] = $nums;

                    Db::name("live_record")->insert($liveinfo);
                }
                $action = "直播监控操作：{$data['id']}";
                setAdminLog($action);
            }
            //警告
            if ($type == 2) {
                //todo
            }
            //隐藏
            if ($type == 3) {
                $updateData = [
                    'hide' => 1,
                ];
                DB::name('live')->where(['uid' => $uid])->update($updateData);
            }
            //禁播
            if ($type == 4) {
                $insertData = [
                    'liveuid' => $uid,
                    'addtime' => time(),
                ];
                Db::name("live_ban")->insert($insertData);
            }
            //更换封面
            if ($type == 5) {
                $updateData = [
                    'thumb' => $data['thumb'],
                ];
                $result     = DB::name('live')->where(['uid' => $uid])
                    ->update($updateData);
                if ($result === false) {
                    $this->error("操作失败！");
                }
            }
            $this->success("操作成功！");
        }
    }
}

