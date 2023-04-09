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

class IplimitController extends AdminBaseController
{
    public function index()
    {
        $data  = $this->request->param();
        $map   = [];
        $ip = $data['ip'] ?? '';
        if ($ip) {
            $map[] = ['ip', '=', $ip];
        }
        $list = Db::name("limit_ip")
            ->alias('l')
            ->join('user u', 'l.uid=u.id')
            ->field('l.id,ip,user_nicename,mobile,create_time,coin,l.status,l.addtime,l.operator')
            ->where($map)
            ->order("addtime desc")
            ->paginate(20);
        $list->appends($data);
        // 获取分页显示
        $page = $list->render();
        $this->assign('lists', $list);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }

    function addPost()
    {
        $data = $this->request->param();
        $ip   = $data['ip'] ?? '';
        if (!$ip) {
            $this->error("IP地址不能为！");
        }
        $adminid    = cmf_get_current_admin_id();
        $admininfo  = Db::name("user")->where(["id" => $adminid])
            ->value("user_login");
        $limitUsers = Db::name("user")
            ->where(["last_login_ip" => $ip, 'user_type' => 2])
            ->field('id')
            ->select()
            ->toArray();
        $limitUsers = array_column($limitUsers, 'id');
        $insertData = [];
        foreach ($limitUsers as $v) {
            $have = Db::name("limit_ip")
                ->where(['uid' => $v, 'ip' => $ip])
                ->find();
            if ($have) {
                if ($have['status'] == 0) {
                    Db::name("limit_ip")
                        ->where(['id' => $have['id']])
                        ->update(['status' => 1]);
                }
                continue;
            }
            $tem = [
                'ip'       => $ip,
                'uid'      => $v,
                'addtime'  => time(),
                'operator' => $admininfo,
            ];
            $insertData[] = $tem;
        }
        Db::name("limit_ip")->insertAll($insertData);
        $action = "封禁IP操作：{$ip}";
        setAdminLog($action);
        $this->success("操作成功！", url('user/AdminIndex/index'));
    }

    function switch()
    {
        $data = $this->request->param();
        $id = $data['id'] ?? 0;
        $uid = $data['uid'] ?? 0;
        $upData = [
            'status' => $data['status']
        ];
        if ($id) {
            $result = Db::name("limit_ip")->where(['id' => $id])->update($upData);
        }
        if ($uid) {
            $result = Db::name("limit_ip")->where(['uid' => $uid])->update($upData);
        }
        if ($result === false) {
            $this->error("操作失败！");
        }
        $this->success("操作成功！", url('user/AdminIndex/index'));
    }
}
