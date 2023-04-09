<?php

/**
 * 家族信息（H5）
 */
class Api_Family extends PhalApi_Api{
    public function getRules(){
        return [
            'create'          => [
                'uid'      => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'    => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户Token',
                ],
                'name'     => [
                    'name'    => 'name',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '家族名称',
                ],
                'wechat'   => [
                    'name'    => 'wechat',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '微信号',
                ],
                'mobile'   => [
                    'name'    => 'mobile',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '手机号',
                ],
                'size'     => [
                    'name' => 'size',
                    'type' => 'int',
                    'desc' => '家族规模',
                ],
                'platform' => [
                    'name' => 'platform',
                    'type' => 'string',
                    'desc' => '合作平台',
                ],
            ],
            'info'            => [
                'uid'         => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'       => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户Token',
                ],
                'invite_code' => [
                    'name'    => 'invite_code',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '邀请码',
                ],
            ],
            'join'            => [
                'uid'         => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'       => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户Token',
                ],
                'invite_code' => [
                    'name'    => 'invite_code',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '邀请码',
                ],
            ],
            'management'      => [
                'uid'        => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'      => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'],
                'start_time' => [
                    'name' => 'start_time',
                    'type' => 'string',
                    'desc' => '开始日期',
                ],
                'end_time'   => [
                    'name' => 'end_time',
                    'type' => 'string',
                    'desc' => '结束日期',
                ],
            ],
            'anchors'         => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token' => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户Token',
                ],
                'page'  => [
                    'name'    => 'page',
                    'type'    => 'int',
                    'desc'    => '页码',
                    'default' => 1,
                ],
            ],
            'anchorIndex'     => [
                'uid'        => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'      => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'],
                'anchor_id'  => [
                    'name'    => 'anchor_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '主播uid',
                ],
                'start_time' => [
                    'name' => 'start_time',
                    'type' => 'string',
                    'desc' => '开始日期',
                ],
                'end_time'   => [
                    'name' => 'end_time',
                    'type' => 'string',
                    'desc' => '结束日期',
                ],
            ],
            'kickOut'         => [
                'uid'       => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'     => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户Token',
                ],
                'anchor_id' => [
                    'name'    => 'anchor_id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '主播uid',
                ],
            ],
            'userFamilyLists' => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token' => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户Token',
                ],
                'page'  => [
                    'name'    => 'page',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '页码',
                ],
                'type'  => [
                    'name'    => 'type',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '1日，2周，3月',
                ],
            ],
            'getInviteCode'   => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'],
            ],
            'liveDetail'            => [
                'uid'         => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'       => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户Token',
                ],
                'day' => [
                    'name'    => 'day',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '日期',
                ],
                'page' => [
                    'name'    => 'page',
                    'type'    => 'int',
                    'default' => 1,
                    'desc'    => '页数',
                ],
            ],
        ];
    }

    /**
     * 家族入驻申请
     *
     * @desc 用于申请创建家族
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string list[0].msg 成功提示信息
     * @return string msg 提示信息
     */
    public function create(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $di         = DI()->notorm;
        $uid        = (int)checkNull($this->uid);
        $token      = checkNull($this->token);
        $name       = checkNull($this->name);
        $wechat     = checkNull($this->wechat);
        $mobile     = checkNull($this->mobile);
        $size       = checkNull($this->size);
        $platform   = checkNull($this->platform);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $familyExist = $di->family->where(['uid' => $uid])->fetchOne();
        //是否为家族长
        if($familyExist){
            if($familyExist['state'] == 1){
                $rs['code'] = 702;
                $rs['msg']  = '审核中';
                return $rs;
            }
            if($familyExist['state'] == 2){
                $rs['code'] = 703;
                $rs['msg']  = '已有家族';
                return $rs;
            }
//            if ($familyExist['state'] == 3) {
//                $di->family->where(['uid' => $uid])->delete();
//            }
        }
        //是否为某家族成员
        $where      = ['uid = ?' => $uid, 'state <> ?' => 3];
        $familyUser = $di->family_user->where($where)
            ->fetchOne();
        if($familyUser){
            $rs['code'] = 703;
            $rs['msg']  = '已有家族';
            return $rs;
        }
        $data   = [
            'uid'      => $uid,
            'name'     => $name,
            'wechat'   => $wechat,
            'mobile'   => $mobile,
            'size'     => $size,
            'platform' => $platform,
            'addtime'  => time(),
        ];
        $result = $di->family->insert($data);
        if(!$result){
            $rs['code'] = 703;
            $rs['msg']  = '提交失败';
            return $rs;
        }
        return $rs;
    }

    /**
     * 家族信息
     *
     * @desc 用于获取家族信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info['uid'] 家族长id
     * @return string info['name'] 家族名称
     * @return string info['user_nickname'] 家族长昵称
     * @return string info['avatar'] 家族长头像
     * @return string info['avatar_thumb'] 家族长头像小图
     * @return string msg 提示信息
     */
    public function info(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $token      = $this->token;
        $uid        = $this->uid;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $invite_code = $this->invite_code;
        $info        = DI()->notorm->family_code
            ->where(['invite_code' => $invite_code])
            ->select('family_id')
            ->fetchOne();
        if(!$info){
            $rs['code'] = 703;
            $rs['msg']  = '家族不存在';
        }
        $family = DI()->notorm->family
            ->select('uid,name')
            ->where(['id' => $info['family_id'], 'state' => 2])
            ->fetchOne();
        if(!$family){
            $rs['code'] = 703;
            $rs['msg']  = '家族不存在';
        }
        $person = DI()->notorm->user
            ->select('user_nicename,avatar,avatar_thumb')
            ->where(['id' => $family['uid']])
            ->fetchOne();

        $rs['info'] = [
            'uid'           => $family['uid'],
            'name'          => $family['name'],
            'user_nickname' => $person['user_nicename'],
            'avatar'        => get_upload_path($person['avatar']),
            'avatar_thumb'  => get_upload_path($person['avatar_thumb']),
        ];
        return $rs;
    }

    /**
     * 加入家族
     *
     * @desc 用于申请加入家族
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function join(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $di         = DI()->notorm;
        $uid        = (int)checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $isJoin = $di->family_user->where(['uid' => $uid])
            ->fetchOne();//家族长也在家族成员表中
        if($isJoin){
            if($isJoin['state'] != 3){
                $rs['code'] = 701;
                $rs['msg']  = '您已加入过家族';
                return $rs;
            }
//            $di->family_user->where(['uid' => $uid])->delete();
        }
        $invite_code = checkNull($this->invite_code);
        $where       = ['invite_code' => $invite_code];
        $familyExist = $di->family_code->select('family_id,state')
            ->where($where)->fetchOne();
        if(!$familyExist || $familyExist['state'] == 1){
            $rs['code'] = 702;
            $rs['msg']  = '邀请码无效';
            return $rs;
        }
        $family = $di->family->select('id,state,disable')
            ->where(['id' => $familyExist['family_id']])->fetchOne();
        if(!$family || $family['state'] != 2 || $family['disable'] != 0){
            $rs['code'] = 702;
            $rs['msg']  = '家族不存在或已被禁用';
            return $rs;
        }
        $data   = [
            'uid'      => $uid,
            'familyid' => $familyExist['family_id'],
            'addtime'  => time(),
        ];
        $result = $di->family_user->insert($data);
        $di->family_code->where($where)->update(['state' => 1]);
        if(!$result){
            $rs['code'] = 702;
            $rs['msg']  = '加入失败';
            return $rs;
        }
        return $rs;
    }

    /**
     * 家族主播列表
     *
     * @desc 用于获取家族主播列表
     * @return int code 操作码，0表示成功
     * @return array info['list'] 家族主播列表
     * @return string info['list'][0]['id'] 序列id
     * @return string info['list'][0]['anchor_id'] 主播uid
     * @return string info['list'][0]['user_nickname'] 主播昵称
     * @return string info['list'][0]['avatar'] 主播头像
     * @return string info['list'][0]['avatar_thumb'] 主播小头像
     * @return string info['list'][0]['addtime'] 加入时间 (日期格式)
     * @return string info['total_page'] 总页数
     * @return string msg 提示信息
     */
    public function anchors(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $di         = DI()->notorm;
        $uid        = (int)checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        $page       = (int)checkNull($this->page);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $family = $di->family->where(['uid' => $uid, 'state' => 2])
            ->select('id')->fetchOne();
        if(!$family){
            $rs['code'] = 703;
            $rs['msg']  = '你旗下没有家族';
            return $rs;
        }
        $where       = ['familyid = ?' => $family['id'], 'state <> ?' => 3];
        $query       = $di->family_user
            ->where($where)
            ->select('id,uid as anchor_id,addtime,is_admin')->order('addtime desc');
        $pnum        = 20;
        $count       = ceil($query->count() / $pnum);
        $start       = ($page - 1) * $pnum;
        $familyUsers = $query->limit($start, $pnum)->fetchAll();
        foreach($familyUsers as &$familyUser){
            $familyUser['can_kickout'] = 1;
            if($familyUser['is_admin']){
                $familyUser['can_kickout'] = 0;
            }
            $userInfo                    = getUserInfo($familyUser['anchor_id']);
            $familyUser['user_nickname'] = $userInfo['user_nicename'];
            $familyUser['avatar']        = $userInfo['avatar'];
            $familyUser['avatar_thumb']  = $userInfo['avatar_thumb'];
            $familyUser['addtime']       = date('Y-m-d H:i:s',
                $familyUser['addtime']);
        }
        $rs['info']['list']       = $familyUsers;
        $rs['info']['total_page'] = $count;
        return $rs;
    }

    /**
     * 获取邀请码
     *
     * @desc 用于获取家族邀请码
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info['invite_code'] 邀请码
     * @return string msg 提示信息
     */
    public function getInviteCode(){
        $rs        = ['code' => 0, 'msg' => '', 'info' => []];
        $token     = $this->token;
        $uid       = $this->uid;
        $family_id = DI()->notorm->family
            ->where(['uid' => $uid, 'state' => 2, 'disable' => 0])
            ->select('id')
            ->fetchOne();;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        if(!$family_id){
            $rs['code'] = 700;
            $rs['msg']  = '你不在任何家族';
            return $rs;
        }
        $code = DI()->notorm->family_code
            ->where(['family_id' => $family_id['id'], 'state' => 0])
            ->select('id,invite_code')
            ->fetchOne();
        if(!$code){
            $rs['code'] = 700;
            $rs['msg']  = '邀请码已用完';
            return $rs;
        }
        DI()->notorm->family_code->where(['id' => $code['id']])->update(['state' => 2]);
        $rs['info']['invite_code'] = $code['invite_code'];
        return $rs;
    }

    /**
     * 退出家族
     *
     * @desc 用于家族长踢人或主播主动退出家族
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string list[0].msg 成功提示信息
     * @return string msg 提示信息
     */
    public function kickOut(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $token      = $this->token;
        $uid        = $this->uid;
        $anchor_id  = $this->anchor_id;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        if($uid !== $anchor_id){
            $family = DI()->notorm->family
                ->select('id')
                ->where(['uid' => $uid, 'state' => 2])
                ->fetchOne();
            if(!$family){
                $rs['code'] = 701;
                $rs['msg']  = '家族不存在';
                return $rs;
            }
        }
        $result = DI()->notorm->family_user
            ->where(['uid' => $anchor_id, 'state' => 1])
            ->update(['state' => 2, 'out_time' => time()]);
        if($result === false){
            $rs['code'] = 702;
            $rs['msg']  = '操作失败';
            return $rs;
        }
        return $rs;
    }

    /**
     * 家族魅力榜
     *
     * @desc 用于获取家族魅力榜*
     * @return int code 操作码，0表示成功
     * @return string info['list'] 榜单列表
     * @return string info['list'][0]['uid'] 用户id
     * @return string info['list'][0]['user_nicename'] 用户昵称
     * @return string info['list'][0]['votes'] 总收入
     * @return string info['list'][0]['avatar_thumb'] 头像
     * @return string info['list'][0]['level'] 财富等级
     * @return string info['list'][0]['level_thumb'] 财富等级图标
     * @return string info['list'][0]['level_anchor'] 魅力等级
     * @return string info['list'][0]['level_anchor_thumb'] 魅力等级图标
     * @return string info['list'][0]['verify'] 是否加V认证 0否 1是
     * @return string info['list']['my_info'] 我的数据
     * @return string info['list']['my_info']['uid'] 用户id
     * @return string info['list']['my_info']['user_nicename'] 昵称
     * @return string info['list']['my_info']['votes'] 总收入
     * @return string info['list']['my_info']['avatar_thumb'] 头像
     * @return string info['list']['my_info']['level'] 财富等级
     * @return string info['list']['my_info']['level_thumb'] 财富等级图标
     * @return string info['list']['my_info']['level_anchor'] 魅力等级
     * @return string info['list']['my_info']['level_anchor_thumb'] 魅力等级图标
     * @return string info['list']['my_info']['verify'] 是否加V认证 0否 1是
     * @return string list[0].msg 成功提示信息
     * @return string msg 提示信息
     */

    public function userFamilyLists(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $token      = $this->token;
        $uid        = $this->uid;
        $type       = $this->type;
        $page       = $this->page;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $do_main    = new Domain_Family();
        $rs['info'] = $do_main->userFamilyList($uid, $type, $page);
        return $rs;
    }

    /**
     * 家族管理
     *
     * @desc 用于家族长获取家族管理信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info['total'] 总数据
     * @return string info['total']['id'] 家族id
     * @return string info['total']['anchor_id'] 主播uid
     * @return string info['total']['name'] 家族名
     * @return string info['total']['nickname'] 主播昵称
     * @return string info['total']['avatar'] 主播头像
     * @return string info['total']['avatar_thumb'] 主播小头像
     * @return string info['total']['total_anchor_count'] 主播数量
     * @return string info['total']['total_live_length'] 直播时长
     * @return string info['total']['total_anchor_profit'] 主播收益
     * @return string info['total']['total_family_profit'] 家族提成
     * @return array info['time_total'] 时间段数据
     * @return string info['time_total']['live_count'] 开播人数
     * @return string info['time_total']['live_length'] 直播时长
     * @return string info['time_total']['anchor_profit'] 主播收益
     * @return string info['time_total']['family_profit'] 家族收益
     * @return array info['daily_detail'] 每日明细
     * @return string info['daily_detail'][0]['day'] 日期
     * @return string info['daily_detail'][0]['live_count'] 开播人数
     * @return string info['daily_detail'][0]['live_length'] 直播时长
     * @return string info['daily_detail'][0]['anchor_profit'] 主播收益
     * @return string info['daily_detail'][0]['family_profit'] 家族提成
     * @return string msg 提示信息
     */
    public function management(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $token      = checkNull($this->token);
        $uid        = checkNull($this->uid);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $start_time = checkNull($this->start_time);
        $end_time   = checkNull($this->end_time);
        $result     = Domain_Family::managementV2($uid, $start_time, $end_time);
        if($result == 801){
            $rs['code'] = $result;
            $rs['msg']  = '没有找到你的家族，请先加入';
            return $rs;
        }
        $rs['info'] = $result;
        return $rs;
    }

    /**
     * 家族主播详情
     *
     * @desc 用于获取家族主播信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info['total'] 总数据
     * @return string info['total']['id'] 家族id
     * @return string info['total']['anchor_id'] 主播uid
     * @return string info['total']['family_name'] 家族名
     * @return string info['total']['nickname'] 主播昵称
     * @return string info['total']['avatar'] 主播头像
     * @return string info['total']['total_live_length'] 直播时长
     * @return string info['total']['total_live_times'] 开播次数
     * @return string info['total']['total_anchor_profit'] 主播收益
     * @return string info['total']['total_family_profit'] 家族收益
     * @return array info['total']['remark_info'] 官方认证信息（若未认证为空数组）
     * @return string info['total']['remark_info']['name'] 名称
     * @return string info['total']['remark_info']['icon'] 图标
     * @return string info['total']['remark_info']['auth_desc'] 描述
     * @return string info['total']['remark_info']['addtime'] 认证时间(时间戳)
     * @return string info['time_total'] 时间段数据
     * @return string info['time_total']['live_times'] 开播次数
     * @return string info['time_total']['live_length'] 直播时长
     * @return string info['time_total']['anchor_profit'] 主播收益
     * @return string info['time_total']['family_profit'] 家族收益
     * @return array  info['daily_detail'] 每日明细
     * @return string info['daily_detail'][0]['day'] 日期
     * @return string info['daily_detail'][0]['live_times'] 开播次数
     * @return string info['daily_detail'][0]['live_length'] 直播时长
     * @return string info['daily_detail'][0]['anchor_profit'] 主播收益
     * @return string info['daily_detail'][0]['family_profit'] 家族提成
     * @return string msg 提示信息
     */
    public function anchorIndex(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $token      = checkNull($this->token);
        $uid        = checkNull($this->uid);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $anchor_id  = checkNull($this->anchor_id);
        $start_time = checkNull($this->start_time);
        $end_time   = checkNull($this->end_time);
        $result     = Domain_Family::anchorIndexV2($anchor_id, $start_time, $end_time, 1);
        if($result == 10009) {
            $rs['code'] = $result;
            $rs['msg']  = '未加入过家族';
            return $rs;
        }
        $rs['info'] = $result;
        return $rs;
    }

    /**
     * 直播详情
     *
     * @desc 用于获取指定日期下家族主播直播记录
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info['live_count'] 开播人数
     * @return string info['live_length'] 直播时长
     * @return string info['anchor_profit'] 主播收益
     * @return string info['family_profit'] 家族提成
     * @return array  info['day_live'] 每日明细
     * @return string info['day_live'][0]['uid'] 主播id
     * @return string info['day_live'][0]['user_nicename'] 主播昵称
     * @return string info['day_live'][0]['live_length'] 直播时长
     * @return string info['day_live'][0]['anchor_profit'] 主播收益
     * @return string info['day_live'][0]['family_profit'] 家族提成
     * @return string msg 提示信息
     */
    public function liveDetail()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];
        $token = checkNull($this->token);
        $uid  = checkNull($this->uid);
        $checkToken = checkToken($uid, $token);
        $page = checkNull($this->page);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $day   = checkNull($this->day);
        $result = Domain_Family::liveDetail($uid, $day, $page);
        if ($result == 801) {
            $rs['code'] = $result;
            $rs['msg']  = '您不是家族长';
            return $rs;
        }
        $rs['info'] = $result;
        return $rs;
    }
}
