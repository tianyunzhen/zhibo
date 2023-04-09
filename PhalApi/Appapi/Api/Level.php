<?php

/**
 * 等级信息（H5）
 */
class Api_Level extends PhalApi_Api
{

    public function getRules()
    {
        return [
            'index'  => [
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
//				'type' => array('name' => 'token', 'type' => 'string',  'require' => true, 'desc' => '用户Token'),
            ],
            'system' => [
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
            ],
        ];
    }

    /**
     * 用户等级信息
     *
     * @desc 用于 获取等级信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info['user_info'] 用户信息
     * @return string info['user_info']['avatar'] 用户头像
     * @return string info['user_info']['consumption'] 财富值
     * @return string info['user_info']['votestotal'] 魅力值
     * @return array info['wealth_info'] 财富等级信息
     * @return string info['wealth_info']['levelid'] 财富等级id
     * @return string info['wealth_info']['levelname'] 财富等级名称
     * @return string info['wealth_info']['level_up'] 当前等级所需财富值
     * @return string info['wealth_info']['thumb'] 等级标识
     * @return string info['wealth_info']['colour'] 等级颜色
     * @return string info['wealth_info']['thumb_mark'] 等级角标
     * @return string info['wealth_info']['bg'] 等级背景
     * @return string info['wealth_info']['progress_bar'] 等级进度条比例
     * @return string info['wealth_info']['upgrade_lack'] 升级到上一级所缺财富值
     * @return array info['charm_info'] 魅力等级信息
     * @return string info['charm_info']['levelid'] 魅力等级id
     * @return string info['charm_info']['levelname'] 魅力等级名称
     * @return string info['charm_info']['level_up'] 当前等级所需魅力值
     * @return string info['charm_info']['thumb'] 等级标识
     * @return string info['charm_info']['colour'] 等级颜色
     * @return string info['charm_info']['thumb_mark'] 等级角标
     * @return string info['charm_info']['bg'] 等级背景
     * @return string info['charm_info']['progress_bar'] 等级进度条比例
     * @return string info['charm_info']['upgrade_lack'] 升级到上一级所缺魅力值
     * @return string msg 提示信息
     */
    public function index()
    {
        $rs  = ['code' => 0, 'msg' => '', 'info' => []];
        $di  = DI()->notorm;
        $uid = (int)checkNull($this->uid);
//        $token = checkNull($this->token);
//        $checkToken = checkToken($uid,$token);
//        if($checkToken == 700){
//            $rs['code'] = 700;
//            $rs['msg'] = '您的登陆状态失效或账号已被禁用';
//            return $rs;
//        }
//        $domain = new Domain_Level();
        $userinfo           = $di->user->select("avatar,consumption,votestotal")
            ->where(["id" => $uid])->fetchOne();
        $userinfo['avatar'] = get_upload_path($userinfo['avatar']);

        /* 财富等级 */
        $levelinfo   = $di->level->where("'{$userinfo['consumption']}'>=level_up")
            ->order("levelid desc")->fetchOne();
        if (!$levelinfo) {
            $levelinfo = $di->level->order("levelid asc")->fetchOne();
        }
        $upLevelInfo = $di->level->where("level_up>?", $levelinfo['level_up'])
            ->select('level_up,thumb')->order("levelid asc")->fetchOne();
        $cha =  $upLevelInfo['level_up'] - $userinfo['consumption'];
        if ($cha > 0) {
            $tem = $upLevelInfo['level_up'] - $levelinfo['level_up'];
            if ($tem > 0) {
                $baifen = floor(($userinfo['consumption'] - $levelinfo['level_up'])
                    / $tem * 100);
            } else {
                $baifen = '0';
            }
        } else {
            $cha = 0;
            $baifen = 100;
        }
        $levelinfo['thumb']          = get_upload_path($levelinfo['thumb']);
        $levelinfo['bg']             = get_upload_path($levelinfo['bg']);
        $levelinfo['progress_bar']   = $baifen;
        $levelinfo['upgrade_lack']   = $cha;
        $levelinfo['up_level_thumb'] = get_upload_path($upLevelInfo['thumb']);
        unset($levelinfo['addtime'], $levelinfo['id']);
        $votestotal = floor($userinfo['votestotal'] / 100);//真实钻石
        /* 主播等级 */
        $levelinfo_a
                       = $di->level_anchor->where("'{$votestotal}'>=level_up")
            ->order("levelid desc")->fetchOne();
        if (!$levelinfo_a) {
            $levelinfo_a = $di->level_anchor->order("levelid asc")->fetchOne();
        }
        $upLevelInfo_a = $di->level_anchor->where("level_up>?", $levelinfo_a['level_up'])
            ->select('level_up,thumb')->order("levelid asc")->fetchOne();
        $cha_a = $upLevelInfo_a['level_up'] - $votestotal;
        if ($cha_a > 0) {
            $tem_a = $upLevelInfo_a['level_up'] - $levelinfo_a['level_up'];
            if ($levelinfo_a['level_up'] > 0) {
                $baifen_a = floor(($votestotal - $levelinfo_a['level_up'])
                    / $tem_a * 100);
            } else {
                $baifen_a = '0';
            }
        } else {
            $cha_a = 0;
            $baifen_a = 100;
        }
        $levelinfo_a['thumb']        = get_upload_path($levelinfo_a['thumb']);
        $levelinfo_a['bg']           = get_upload_path($levelinfo_a['bg']);
        $levelinfo_a['progress_bar'] = $baifen_a;
        $levelinfo_a['upgrade_lack'] = $cha_a;
        $levelinfo_a['up_level_thumb']
                                     = get_upload_path($upLevelInfo_a['thumb']);
        unset($levelinfo_a['addtime'], $levelinfo_a['id']);
        $data       = [
            'user_info'   => $userinfo,
            'wealth_info' => $levelinfo,
            'charm_info'  => $levelinfo_a,
        ];
        $rs['info'] = $data;
        return $rs;
    }

    /**
     * 等级系统
     *
     * @desc 用于获取财富等级及魅力等级体系
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info['wealth_level'] 财富等级信息
     * @return string info['wealth_level']['levelid'] 等级id
     * @return string info['wealth_level']['consumption'] 等级名称
     * @return string info['wealth_level']['level_up'] 所需经验值
     * @return string info['wealth_level']['thumb'] 图标
     * @return array info['charm_level'] 魅力等级体系信息
     * @return string info['charm_level']['levelid'] 等级id
     * @return string info['charm_level']['consumption'] 等级名称
     * @return string info['charm_level']['level_up'] 所需经验值
     * @return string info['charm_level']['thumb'] 图标
     * @return string msg 提示信息
     */
    public function system()
    {
        $rs  = ['code' => 0, 'msg' => '', 'info' => []];
        $di  = DI()->notorm;
        $uid = (int)checkNull($this->uid);
//        $token = checkNull($this->token);
//        $checkToken = checkToken($uid,$token);
//        if($checkToken == 700){
//            $rs['code'] = 700;
//            $rs['msg'] = '您的登陆状态失效或账号已被禁用';
//            return $rs;
//        }
        $str    = 'levelid,levelname,level_up,thumb';
        $wealth = $di->level->select($str)->fetchAll();
        foreach ($wealth as &$v) {
            $v['thumb'] = get_upload_path($v['thumb']);
        }
        $charm = $di->level_anchor->select($str)->fetchAll();
        foreach ($charm as &$v) {
            $v['thumb'] = get_upload_path($v['thumb']);
        }
        $rs['info'] = [
            'wealth_level' => $wealth,
            'charm_level'  => $charm,
        ];
        return $rs;
    }
}
