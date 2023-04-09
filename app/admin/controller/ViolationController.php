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

use app\common\Message;
use cmf\controller\AdminBaseController;
use think\Db;

class ViolationController extends AdminBaseController
{
    public function index()
    {
        $content = hook_one('user_admin_index_view');

        if (!empty($content)) {
            return $content;
        }
        $data  = $this->request->param();
        $map = [
            ['user_type', '=', 2]
        ];

        $keyword = $data['keyword'] ?? '';
        if ($keyword) {
            $map[] = ['user_login|user_nicename', 'like', '%' . $keyword . '%'];
        }
        $uid = $data['uid'] ?? 0;
        if ($uid) {
            $map[] = ['id', '=', $uid];
        }
        $list = Db::name("user")
            ->field('id,avatar,user_nicename,signature')
            ->where($map)
            ->order("id desc")
            ->paginate(20);
        $list->each(function ($v, $k) {
            $v['avatar'] = get_upload_path($v['avatar']);
            return $v;
        });

        $list->appends($data);
        // 获取分页显示
        $page = $list->render();
        $this->assign('lists', $list);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $rs = DB::name('user')->update($data);
            if ($rs === false) {
                $this->error("修改失败！");
            }
            $action = "修改会员信息：{$data['id']}";
            setAdminLog($action);
            Message::addMsg('违规提醒', '用户资料涉嫌违规，请及时修改', $data['id']);
            $this->success("修改成功！");
        }
    }
}
