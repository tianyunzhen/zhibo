<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;

class ChannelController extends AdminBaseController
{
    protected function getVerify($k = '')
    {
        $verify = [
            '0' => '未认证',
            '1' => '认证通过',
        ];
        if ($k === '') {
            return $verify;
        }

        return isset($verify[$k]) ? $verify[$k] : '';
    }

    public function default()
    {
        $map        = [];
        $data       = $this->request->param();
        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time   = isset($data['end_time']) ? $data['end_time'] : '';
        $channel_name = $data['channel_name'] ?? '';
        $type = $data['type'] ?? 0;
        if ($channel_name) {
            $map[] = ['channel_name', '=', $channel_name];
        }
        if ($type) {
            $map[] = ['type', '=', $type];
        }
        if ($start_time != "") {
            $map[] = ['activate_time', '>=', strtotime($start_time)];
        }

        if ($end_time != "") {
            $map[] = ['activate_time', '<=', strtotime($end_time) + 60 * 60 * 24];
        }
        $list = Db::name("app_device")
            ->field("channel_name,count(if(register_time>0,1,null)) as add_num, count(*) as install_num,group_concat(user_id) as user_ids")
            ->where($map)
            ->group("channel_name")
            ->select()->toArray();
        foreach ($list as &$v) {
            $idArr = array_unique(array_filter(explode(',', $v['user_ids'])));
            $v['active_num'] = 0;
            $v['pay_num'] = Db::name("charge_user")->whereIn('uid', $idArr)->group('uid')->count() ?? 0;
            $v['remark_num'] = Db::name("user_remark")->whereIn('uid', $idArr)->group('uid')->count() ?? 0;
            $v['add_percent'] = ($v['install_num'] ? $v['add_num'] * 100 / $v['install_num'] : 0) . '%';
            $v['pay_percent'] = ($v['add_num'] ? $v['pay_num'] * 100 / $v['add_num'] : 0) . '%';;
            $v['gmv'] =  Db::name("charge_user")->whereIn('uid', $idArr)->sum('money') ?? 0;
            $v['arpu'] = $v['add_num'] ? $v['gmv']  / $v['add_num'] : 0;
            $v['arppu'] = $v['pay_num'] ? $v['gmv']  / $v['pay_num'] : 0;
            $v['ltv'] = 0;
        }
        $channels = Db::name("app_device")->field('channel_name')->group('channel_name')->select()->toArray();
        $channels = array_column($channels, 'channel_name');
        $this->assign('list', $list);
        $this->assign('nowtime', time());
        $this->assign('channels', $channels);
        // 渲染模板输出
        return $this->fetch('index');
    }
}
