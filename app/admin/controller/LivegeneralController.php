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
use think\db\Query;

class LivegeneralController extends AdminBaseController
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

    public function index()
    {
        $map        = [];
        $data       = $this->request->param();
        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time   = isset($data['end_time']) ? $data['end_time'] : '';
        $uid = isset($data['uid']) ? $data['uid'] : '';
        if ($uid != '') {
            $map[] = ['l.uid', '=', $uid];
        }
        if ($start_time != "") {
            $map[] = ['time', '>=', $start_time];
        }
        if ($end_time != "") {
            $map[] = ['time', '<=', $end_time];
        }
        if (!$start_time && !$end_time) {
            $map[] = ['time', '=', date('Y-m-d')];
        }
        $list = Db::name("live_record")
            ->alias('l')
            ->leftJoin('family_user u', 'l.uid=u.uid')
            ->leftJoin('family f', 'u.familyid=f.id')
            ->field('time,l.uid,sum(votes) as profit,sum(gift_count) as total_gift,sum(endtime-starttime) as length,f.uid as family_admin')
            ->where($map)
            ->group("time,l.uid")
            ->order('time desc')
            ->paginate(20);
        $list->each(function ($v, $k) {
            $v['length'] = round($v['length'] / 3600, 2) . "h";
            $v['withdraw'] = 0;
            $v['daily_water'] = 0;
            $v['exchange'] = 0;
            $v['profit'] = (string) round($v['profit'] / 100, 2);
            return $v;
        });
        $list->appends($data);
        $page = $list->render();
        $this->assign("page", $page);
        $this->assign('lists', $list);
        return $this->fetch();
    }
}
