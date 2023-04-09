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

namespace app\user\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use think\db\Query;

/**
 * Class AdminIndexController
 *
 * @package app\user\controller
 *
 * @adminMenuRoot(
 *     'name'   =>'用户管理',
 *     'action' =>'default',
 *     'parent' =>'',
 *     'display'=> true,
 *     'order'  => 10,
 *     'icon'   =>'group',
 *     'remark' =>'用户管理'
 * )
 *
 * @adminMenuRoot(
 *     'name'   =>'用户组',
 *     'action' =>'default1',
 *     'parent' =>'user/AdminIndex/default',
 *     'display'=> true,
 *     'order'  => 10000,
 *     'icon'   =>'',
 *     'remark' =>'用户组'
 * )
 */
class StatisticalController extends AdminBaseController
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

    /**
     * 后台本站用户列表
     * @adminMenu(
     *     'name'   => '本站用户',
     *     'parent' => 'default1',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $map        = [];
        $map[]      = ['user_type', '=', 2];
        $data       = $this->request->param();
        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time   = isset($data['end_time']) ? $data['end_time'] : '';

        if ($start_time != "") {
            $map[] = ['create_time', '>=', strtotime($start_time)];
        }

        if ($end_time != "") {
            $map[] = ['create_time', '<=', strtotime($end_time) + 60 * 60 * 24];
        }
        $list = Db::name("user")
            ->field("FROM_UNIXTIME(create_time,'%Y-%m-%d') as create_date,count(*) as register,count(if(sex=1,1,null)) as male,count(if(sex=2,1,null)) as female")
            ->where($map)
            ->group("FROM_UNIXTIME(create_time,'%Y-%m-%d')")
            ->select()->toArray();
        foreach ($list as &$v) {
            $v['activate']      = 0;
            $v['reg_equipment'] = 0;
            $v['reg_transform'] = 0;
        }
        $total_male   = array_sum(array_column($list, 'male'));
        $total_female = array_sum(array_column($list, 'female'));
        $total_user   = array_sum(array_column($list, 'register'));
        $total        = [
            'have'         => empty($list) ? 0 : 1,
            'start_date'   => $list[0]['create_date'] ?? '未知',
            'end_date'     => $list[count($list) - 1]['create_date'] ?? '未知',
            'total_male'   => $total_male,
            'total_female' => $total_female,
            'total_user'   => $total_user,
        ];
        $this->assign('list', $list);
        $this->assign('nowtime', time());
        $this->assign('total', $total);
        // 渲染模板输出
        return $this->fetch();
    }
}
