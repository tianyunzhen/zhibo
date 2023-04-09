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

use app\common\Search;
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
class AdminIndexController extends AdminBaseController
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
//        $mongo = Db::connect('mongodb');
//        var_dump($mongo);die;
//        $search = new Search();
//        $body = [
//            'query' => [
//                'match' => [
//                    'family_id' => 5
//                ]
//            ],
//            'from' => 0,
//            'size' => 10
//        ];
//        $result = $search->index('cmf_family_code', $body);
        $content = hook_one('user_admin_index_view');

        if (!empty($content)) {
            return $content;
        }

        $data  = $this->request->param();
        $map   = [];
        $map[] = ['user_type', '=', 2];

        $start_time = isset($data['start_time']) ? $data['start_time'] : '';
        $end_time   = isset($data['end_time']) ? $data['end_time'] : '';

        if ($start_time != "") {
            $map[] = ['create_time', '>=', strtotime($start_time)];
        }

        if ($end_time != "") {
            $map[] = ['create_time', '<=', strtotime($end_time) + 60 * 60 * 24];
        }

        $iszombie = isset($data['iszombie']) ? $data['iszombie'] : '';
        if ($iszombie != '') {
            $map[] = ['iszombie', '=', $iszombie];
        }

        $isban = isset($data['isban']) ? $data['isban'] : '';
        if ($isban != '') {
            if ($isban == 1) {
                $map[] = ['user_status', '=', 0];
            } else {
                $map[] = ['user_status', '<>', 0];
            }

        }

        $source = isset($data['source']) ? $data['source'] : '';
        if ($source != '') {
            $map[] = ['source', '=', $source];
        }

        $auth = isset($data['is_auth']) ? $data['is_auth'] : '';
        if ($auth != '') {
            $map[] = ['is_auth', '=', $auth];
        }
        $iszombiep = isset($data['iszombiep']) ? $data['iszombiep'] : '';
        if ($iszombiep != '') {
            $map[] = ['iszombiep', '=', $iszombiep];
        }

        $keyword = isset($data['keyword']) ? $data['keyword'] : '';
        if ($keyword != '') {
            $map[] = ['user_login|user_nicename', 'like', '%' . $keyword . '%'];
        }

        $uid = isset($data['uid']) ? $data['uid'] : '';
        if ($uid != '') {
            $map[] = ['id', '=', $uid];
        }

        if (isset($data['is_super'])) {
            $map[] = ['issuper', '=', $data['is_super']];
        }

//        $map[] = ['is_js', '=', 0];
        $map[] = ['user_type', '=', 2];
        $nums = Db::name("user")->where($map)->count();

        $list = Db::name("user")
            ->where($map)
            ->order("id desc")
            ->paginate(20);

        $list->each(function ($v, $k) {
            $v['spend_votes'] = Db::name("user_voterecord")->where(['uid' => $v['id'], 'type' => 0])->sum('votes') ?? 0;
            $v['spend_votes'] = round($v['spend_votes'] / 100,2);
            $v['votes'] = round($v['votes'] / 100, 2);
            $family = Db::name("family_user")->alias('u')->join('family f', 'u.familyid=f.id')
                ->where(['u.uid' => $v['id'], 'u.state' => 1])->field('familyid,name')->find();
            $v['family_name'] = $family['name'] ?? '';
            $v['user_login'] = m_s($v['user_login']);
//            $v['mobile']     = m_s($v['mobile']);
//            $v['user_email'] = m_s($v['user_email']);
//            $v['charge'] = Db::name("charge_user")->where(['uid' => $v['id'], 'status' => 1])->sum('money') ?? 0;
            $v['avatar'] = get_upload_path($v['avatar']);
//            $v['withdrawal'] = Db::name('cash_record')->where(['uid' => $v['id'], 'status' =>1])->sum('money') ?? 0;
            $v['is_limit'] = Db::name("limit_ip")->where(['uid' => $v['id'], 'status' => 1])->find() ? 1 : 0;
            $v['level']        = getLevel($v['consumption']);
            $v['level_anchor'] = getLevelAnchor($v['votestotal']);
            $where = [
                ['uid', '=', $v['id']],
                ['end_time', '>', time()],
            ];
            $v['liang_num'] = Db::name("liang")
                ->where($where)->count();
            $v_time =  Db::name("user_remark")
                ->where(['uid' => $v['id']])->value('addtime') ?? '';
            if (!$v_time) {
                $v['v_time'] = '未加V';
            } else {
                $v['v_time'] = date('Y-m-d H:i:s', $v_time);
            }
            $v['v'] = Db::name("user_remark")
                    ->alias('ur')
                    ->leftJoin('remark r', 'ur.remark_id=r.id')
                    ->where(['uid' => $v['id']])
                    ->value('auth_desc') ?? '';

//            $v['is_super'] =  Db::name("user_super")->where(['uid' => $v['id']])->find() ? 1 : 0;
            $v['is_super'] =  $v['issuper'];
            return $v;
        });

        $list->appends($data);
        // 获取分页显示
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('nowtime', time());

        $this->assign('nums', $nums);
        $this->assign("verify", $this->getVerify());

        // 渲染模板输出
        return $this->fetch();
    }

    function del()
    {

        $id = $this->request->param('id', 0, 'intval');

        $user_login = DB::name('user')->where(["id" => $id, "user_type" => 2])
            ->value('user_login');
        $rs         = DB::name('user')->where(["id" => $id, "user_type" => 2])
            ->delete();
        if (!$rs) {
            $this->error("删除失败！");
        }

        $action = "删除会员：{$id} - {$user_login}";
        setAdminLog($action);

        /* 删除认证 */
        DB::name("user_auth")->where("uid='{$id}'")->delete();
        /* 删除直播记录 */
        DB::name("live_record")->where("uid='{$id}'")->delete();
        /* 删除房间管理员 */
        DB::name("live_manager")->where("uid='{$id}' or liveuid='{$id}'")
            ->delete();
        /*  删除黑名单*/
        DB::name("user_black")->where("uid='{$id}' or touid='{$id}'")->delete();
        /* 删除关注记录 */
        DB::name("user_attention")->where("uid='{$id}' or touid='{$id}'")
            ->delete();
        /* 删除僵尸 */
        DB::name("user_zombie")->where("uid='{$id}'")->delete();
        /* 删除超管 */
        DB::name("user_super")->where("uid='{$id}'")->delete();
        /* 删除会员 */
        DB::name("vip_user")->where("uid='{$id}'")->delete();
        /* 删除分销关系 */
        DB::name("agent")->where("uid='{$id}' or one_uid={$id}")->delete();
        /* 删除分销邀请码 */
        DB::name("agent_code")->where("uid='{$id}'")->delete();
        /* 删除坐骑 */
        DB::name("car_user")->where("uid='{$id}'")->delete();
        /* 删除家族关系 */
        DB::name("family_user")->where("uid='{$id}'")->delete();

        /* 删除推送PUSHID */
        DB::name("user_pushid")->where("uid='{$id}'")->delete();
        /* 删除钱包账号 */
        DB::name("cash_account")->where("uid='{$id}'")->delete();
        /* 删除自己的标签 */
        DB::name("label_user")->where("touid='{$id}'")->delete();


        /* 家族长处理 */
        $isexist = DB::name("family")->field("id")->where("uid={$id}")->find();
        if ($isexist) {
            $data = [
                'state'         => 3,
                'signout'       => 2,
                'signout_istip' => 2,
            ];
            DB::name("family_user")->where("familyid={$isexist['id']}")
                ->update($data);
            DB::name("family_profit")->where("familyid={$isexist['id']}")
                ->delete();
            DB::name("family_profit")->where("id={$isexist['id']}")->delete();
        }

        delcache("user:userinfo_" . $id, "token_" . $id);

        $this->success("删除成功！");

    }

    /* 禁用时间 */
    public function setBan()
    {

        $id       = $this->request->param('id', 0, 'intval');
        $reason   = $this->request->param('reason');
        $ban_long = $this->request->param('ban_long');

        if (!$id) {
            $this->error('数据传入失败！');
        }

        if ($ban_long) {
            $ban_long = strtotime($ban_long);
        } else {
            $ban_long = 0;
        }

        $data = [
            'uid'        => $id,
            'ban_long'   => $ban_long,
            'ban_reason' => $reason,
            'addtime'    => time(),
        ];

        $result = Db::name("user_banrecord")->where(["uid" => $id])
            ->update($data);
        if (!$result) {
            $result = Db::name("user_banrecord")->insert($data);
        }
        if (!$result) {
            $this->error('操作失败！');
        }

        Db::name("user")->where(["id" => $id])
            ->update(['end_bantime' => $ban_long]);

        $action = "禁用会员：{$id}";
        setAdminLog($action);

//        $live = Db::name("live")->field("uid")->where("islive='1'")->select()
//            ->toArray();
//        foreach ($live as $k => $v) {
//            hSet($v['uid'] . 'shutup', $id, 1);
//        }

        $this->success("操作成功！");
    }

    /**
     * 本站用户拉黑
     * @adminMenu(
     *     'name'   => '本站用户拉黑',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户拉黑',
     *     'param'  => ''
     * )
     */
    public function ban()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id) {
            $result = Db::name("user")->where(["id" => $id, "user_type" => 2])
                ->setField('user_status', 0);
            if ($result) {

//                $live = Db::name("live")->field("uid")->where("islive='1'")
//                    ->select()->toArray();
//                foreach ($live as $k => $v) {
//                    hSet($v['uid'] . 'shutup', $id, 1);
//                }

                $action = "禁用会员：{$id}";
                setAdminLog($action);

                $this->success("会员拉黑成功！");
            } else {
                $this->error('会员拉黑失败,会员不存在,或者是管理员！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 本站用户启用
     * @adminMenu(
     *     'name'   => '本站用户启用',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '本站用户启用',
     *     'param'  => ''
     * )
     */
    public function cancelBan()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($id) {
            //Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('user_status', 1);
            //Db::name("user")->where(["id" => $id, "user_type" => 2])->setField('end_bantime', 0);
            Db::name("user")->where(["id" => $id, "user_type" => 2])
                ->update(['user_status' => 1, 'end_bantime' => 0]);

            $action = "启用会员：{$id}";
            setAdminLog($action);

            $this->success("会员启用成功！");
        } else {
            $this->error('数据传入失败！');
        }
    }

    /* 超管 */
    function setsuper()
    {

        $id      = $this->request->param('id', 0, 'intval');
        $issuper = $this->request->param('issuper', 0, 'intval');

        $rs = DB::name('user')->where("id={$id}")->setField('issuper',
            $issuper);
        if (!$rs) {
            $this->error("操作失败！");
        }

        if ($issuper == 1) {
            $action  = "设置超管会员：{$id}";
            $isexist = DB::name("user_super")->where("uid={$id}")->find();
            if (!$isexist) {
                DB::name("user_super")->insert([
                    "uid"     => $id,
                    'addtime' => time(),
                ]);
            }

            hSet('super', $id, '1');
        } else {
            $action = "取消超管会员：{$id}";

            DB::name("user_super")->where("uid='{$id}'")->delete();
            hDel('super', $id);
        }

        setAdminLog($action);

        $this->success("操作成功！");

    }

    /* 热门 */
    function sethot()
    {

        $id    = $this->request->param('id', 0, 'intval');
        $ishot = $this->request->param('ishot', 0, 'intval');

        $rs = DB::name('user')->where("id={$id}")->setField('ishot', $ishot);
        if (!$rs) {
            $this->error("操作失败！");
        }
        DB::name("live")->where(["uid" => $id])->setField('ishot', $ishot);
        if ($ishot == 1) {
            $action = "设置热门会员：{$id}";
        } else {
            $action = "取消热门会员：{$id}";
        }

        setAdminLog($action);

        $this->success("操作成功！");

    }

    /* 推荐 */
    function setrecommend()
    {

        $id          = $this->request->param('id', 0, 'intval');
        $isrecommend = $this->request->param('isrecommend', 0, 'intval');

        $rs = DB::name('user')->where("id={$id}")->setField('isrecommend',
            $isrecommend);
        if (!$rs) {
            $this->error("操作失败！");
        }
        DB::name("live")->where(["uid" => $id])->setField('isrecommend',
            $isrecommend);
        if ($isrecommend == 1) {
            $action = "设置推荐会员：{$id}";
        } else {
            $action = "取消推荐会员：{$id}";
        }

        setAdminLog($action);

        $this->success("操作成功！");

    }

    /* 开启僵尸粉 */
    function setzombie()
    {

        $id       = $this->request->param('id', 0, 'intval');
        $iszombie = $this->request->param('iszombie', 0, 'intval');

        $rs = DB::name('user')->where("id={$id}")->setField('iszombie',
            $iszombie);
        if (!$rs) {
            $this->error("操作失败！");
        }

        if ($iszombie == 1) {
            $action = "开启会员僵尸粉：{$id}";
        } else {
            $action = "关闭会员僵尸粉：{$id}";
        }

        setAdminLog($action);

        $this->success("操作成功！");

    }

    /* 一键开启、关闭僵尸粉 */
    function setzombieall()
    {

        $iszombie = $this->request->param('iszombie', 0, 'intval');

        $rs = DB::name('user')->where('user_type=2')->setField('iszombie',
            $iszombie);
        if (!$rs) {
            $this->error("操作失败！");
        }

        if ($iszombie == 1) {
            $action = "开启全部会员僵尸粉";
        } else {
            $action = "关闭全部会员僵尸粉";
        }

        setAdminLog($action);

        $this->success("操作成功！");

    }

    /* 设置僵尸粉 */
    function setzombiep()
    {

        $id        = $this->request->param('id', 0, 'intval');
        $iszombiep = $this->request->param('iszombiep', 0, 'intval');

        $rs = DB::name('user')->where("id={$id}")->setField('iszombiep',
            $iszombiep);
        if (!$rs) {
            $this->error("操作失败！");
        }

        if ($iszombiep == 1) {
            $action  = "开启僵尸粉会员：{$id}";
            $isexist = DB::name("user_zombie")->where("uid={$id}")->find();
            if (!$isexist) {
                DB::name("user_zombie")->insert(["uid" => $id]);
            }
        } else {
            $action = "关闭僵尸粉会员：{$id}";

            DB::name("user_zombie")->where("uid='{$id}'")->delete();
        }

        setAdminLog($action);

        $this->success("操作成功！");

    }

    /* 批量设置僵尸粉 */
    function setzombiepall()
    {
        $data = $this->request->param();
        $ids  = $data['ids'];
        if (!$ids) {
            $this->error("信息错误！");
        }

        $tids      = join(",", $ids);
        $iszombiep = $this->request->param('iszombiep', 0, 'intval');

        $rs = DB::name('user')->where('id', 'in', $ids)->setField('iszombiep',
            $iszombiep);
        if (!$rs) {
            $this->error("操作失败！");
        }

        if ($iszombiep == 1) {
            $action = "开启僵尸粉会员：{$tids}";
            foreach ($ids as $k => $v) {
                $isexist = DB::name("user_zombie")->where("uid={$v}")->find();
                if (!$isexist) {
                    DB::name("user_zombie")->insert(["uid" => $v]);
                }
            }

        } else {
            $action = "关闭僵尸粉会员：{$tids}";

            DB::name("user_zombie")->where('uid', 'in', $ids)->delete();
        }

        setAdminLog($action);

        $this->success("操作成功！");

    }

    function add()
    {
        return $this->fetch();
    }

    function addPost()
    {
        if ($this->request->isPost()) {

            $data = $this->request->param();

            $user_login = $data['user_login'];

            if ($user_login == "") {
                $this->error("请填写手机号");
            }

            if (!checkMobile($user_login)) {
                $this->error("请填写正确手机号");
            }

            $isexist = DB::name('user')->where(['user_login' => $user_login])
                ->value('id');
            if ($isexist) {
                $this->error("该账号已存在，请更换");
            }

            $user_pass = $data['user_pass'];
            if ($user_pass == "") {
                $this->error("请填写密码");
            }

            if (!passcheck($user_pass)) {
                $this->error("密码为6-20位字母数字组合");
            }

            $data['user_pass'] = cmf_password($user_pass);


            $user_nicename = $data['user_nicename'];
            if ($user_nicename == "") {
                $this->error("请填写昵称");
            }

            $avatar       = $data['avatar'];
            $avatar_thumb = $data['avatar_thumb'];
            if (($avatar == "" || $avatar_thumb == '')
                && ($avatar != ""
                    || $avatar_thumb != '')
            ) {
                $this->error("请同时上传头像 和 头像小图  或 都不上传");
            }

            if ($avatar == '' && $avatar_thumb == '') {
                $data['avatar']       = '/default.jpg';
                $data['avatar_thumb'] = '/default_thumb.jpg';
            }

            $data['user_type']   = 2;
            $data['create_time'] = time();

            $id = DB::name('user')->insertGetId($data);
            if (!$id) {
                $this->error("添加失败！");
            }

            $action = "添加会员：{$id}";
            setAdminLog($action);

            $this->success("添加成功！");

        }
    }

    function edit()
    {

        $id = $this->request->param('id', 0, 'intval');

        $data = Db::name('user')
            ->where("id={$id}")
            ->find();
        if (!$data) {
            $this->error("信息错误");
        }

        $data['user_login'] = m_s($data['user_login']);
        $equipment = Db::name("app_device")->where(['user_id' => $id])->value('model') ?? '';
        if (!$equipment) {
            $data['equipment'] = $data['source'];
        }
        $this->assign('data', $data);
        $this->assign("verify", $this->getVerify());
        return $this->fetch();
    }

    function editPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $user_nicename = $data['user_nicename'];
            if ($user_nicename == "") {
                $this->error("请填写昵称");
            }

            $avatar       = $data['avatar'];
            $avatar_thumb = $data['avatar_thumb'];
            if (($avatar == "" || $avatar_thumb == '')
                && ($avatar != ""
                    || $avatar_thumb != '')
            ) {
                $this->error("请同时上传头像 和 头像小图  或 都不上传");
            }

            if ($avatar == '' && $avatar_thumb == '') {
                $data['avatar']       = '/default.jpg';
                $data['avatar_thumb'] = '/default_thumb.jpg';
            }

            $rs = DB::name('user')->update($data);
            if ($rs === false) {
                $this->error("修改失败！", url('adminIndex/index'), '', 1);
            }

            $action = "修改会员信息：{$data['id']}";
            setAdminLog($action);
            delcache('user:info:user_info_' . $data['id']);

            $this->success("修改成功！", url('adminIndex/index'), '', 1);
        }
    }

    /* 推荐 */
    function verify()
    {
        $this->common('verify');
    }

    /* 直播权重 */
    function liveWeight()
    {
        $id          = $this->request->param('id', 0, 'intval');
        $live_weight = $this->request->param('live_weight', 0, 'intval');

        $rs = DB::name('user')->where("id={$id}")->setField('live_weight',
            $live_weight);
        if (!$rs) {
            $this->error("操作失败！", url('liveing/index'));
        }
        $this->success("操作成功！", url('liveing/index'));
    }

    /* 推荐 */
    function agent()
    {
        $this->common('is_agent');
    }

    function common($field) {
        $id     = $this->request->param('id', 0, 'intval');
        $updateFiled = $this->request->param($field, 0, 'intval');
        $rs = DB::name('user')->where("id={$id}")->setField($field, $updateFiled);
        if (!$rs) {
            $this->error("操作失败！", url('adminIndex/index'));
        }
        delcache('user:info:user_info_' . $id);
        $this->success("操作成功！", url('adminIndex/index'));
    }

    function jackpot() {
        return $this->fetch();
    }

    function addJackpot() {
        $uid = $this->request->param('uid', 0, 'intval');
        $have = Db::name("private_jackpot")->where(["uid" => $uid])
            ->find();
        if ($have) {
            $this->error("该用户已存在！", url('adminIndex/jackpotUser'));
        }
        $adminid   = cmf_get_current_admin_id();
        $admininfo = Db::name("user")->where(["id" => $adminid])
            ->value("user_login");
        $data = [
            'uid' => $uid,
            'operator' => $admininfo,
            'addtime' => time(),
        ];
        DB::name('private_jackpot')->insert($data);
        DB::name('user')->where(['id' => $uid])->update(['have_jackpot' => 1]);
        $this->success("操作成功！", url('adminIndex/jackpotUser'));
    }

    function jackpotUser() {
        $uid = $this->request->param('uid', 0, 'intval');
        $where = [];
        if ($uid) {
            $where = ['uid' => $uid];
        }
        $list = DB::name('private_jackpot')
            ->alias('p')
            ->join('user u', 'p.uid=u.id')
            ->field('p.id,uid,u.user_nicename,p.addtime,operator')
            ->where($where)
            ->order("p.addtime desc")
            ->paginate(20);
        // 获取分页显示
        $page = $list->render();
        $this->assign('lists', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    function delJackpot() {
        $uid = $this->request->param('uid', 0, 'intval');
        DB::name('private_jackpot')->where(['uid' => $uid])->delete();
        DB::name('user')->where(['id' => $uid])->update(['have_jackpot' => 0]);
        $this->success("操作成功！", url('adminIndex/jackpotUser'));
    }

    function remarkList()
    {
        $uid = $this->request->param('uid', 0, 'intval');
        $lists = Db::name("remark")
            ->field('id,name')
            ->order("addtime desc")->select()->toArray();
        $this->assign('lists', $lists);
        $this->assign('uid', $uid);
        return $this->fetch('remark');
    }

    function userLiang()
    {
        $uid = $this->request->param('uid', 0, 'intval');
        $time = time();
        $lists = Db::name("liang")
            ->where("uid = $uid and (end_time > $time or expire = 0)")
            ->field('id,name,buytime,expire,end_time')
            ->order("buytime desc")
            ->select()
            ->toArray();
        foreach ($lists as &$list) {
            $useTime = time() - $list['buytime'];
            $hours = intval($useTime / 3600);
            $list['use_time'] = $hours . ":" . gmstrftime('%M:%S', $useTime);
            if (!$list['expire']) {
                $list['remain_time'] = "永久";
            } else {
                $tem = $list['end_time'] - $time;
                $hours = intval($tem / 3600);
                $list['remain_time'] = $hours . ":" . gmstrftime('%M:%S', $tem);
            }
        }
        $this->assign('lists', $lists);
        return $this->fetch();
    }

    function weight()
    {
        $uid = $this->request->param('uid', 0, 'intval');
        $this->assign('uid', $uid);
        return $this->fetch();
    }

    function addWeight()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $uid = $data['uid'] ?? 0;
            $live_weight = $data['live_weight'] ?? 0;
            if (!$uid || !$live_weight) {
                $this->error("缺少必要参数！");
            }
            $update = Db::name('user')->where(['id' => $uid])->update(['live_weight' => $live_weight]);
            if ($update === false) {
                $this->error("操作失败！");
            }
            $this->success("操作成功！");
        }
    }

    /* 超管 */
    function setsuperV2()
    {
        $id      = $this->request->param('uid', 0, 'intval');
        $type = $this->request->param('type', 0, 'intval');
        if ($type == 1) {
            $action  = "设置超管会员：{$id}";
            $isexist = DB::name("user_super")->where("uid={$id}")->find();
            if (!$isexist) {
                DB::name("user_super")->insert([
                    "uid"     => $id,
                    'addtime' => time(),
                ]);
                DB::name("user")->where("id={$id}")->update([
                    "issuper" => 1,
                ]);
            }
            hSet('super', $id, '1');
        } else {
            $action = "取消超管会员：{$id}";
            DB::name("user_super")->where("uid='{$id}'")->delete();
            DB::name("user")->where("id={$id}")->update([
                "issuper" => 0,
            ]);
            hDel('super', $id);
        }
        setAdminLog($action);
        $this->success("操作成功！", url('adminIndex/index'), '', 1);
    }
}
