<?php

/**
 * 首页
 */
class Api_Home extends PhalApi_Api{

    public function getRules(){
        return [
            'getHot'            => [
                'p'     => ['name' => 'p', 'type' => 'int', 'default' => '1', 'desc' => '页数'],
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'],
            ],
            'getFollow'         => [
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'],
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'p'     => ['name' => 'p', 'type' => 'int', 'default' => '1', 'desc' => '页数'],
            ],
            'getNew'            => [
                'lng'   => ['name' => 'lng', 'type' => 'string', 'desc' => '经度值'],
                'lat'   => ['name' => 'lat', 'type' => 'string', 'desc' => '纬度值'],
                'p'     => ['name' => 'p', 'type' => 'int', 'default' => '1', 'desc' => '页数'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'],
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
            ],
            'search'            => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => '用户ID'],
                'key'   => ['name' => 'key', 'type' => 'string', 'default' => '', 'desc' => '用户ID'],
                'p'     => ['name' => 'p', 'type' => 'int', 'default' => '1', 'desc' => '页数'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'],
            ],
            'getNearby'         => [
                'lng' => ['name' => 'lng', 'type' => 'string', 'desc' => '经度值'],
                'lat' => ['name' => 'lat', 'type' => 'string', 'desc' => '纬度值'],
                'p'   => ['name' => 'p', 'type' => 'int', 'default' => '1', 'desc' => '页数'],
                //                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'],
                //                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
            ],
            'getNearUser'       => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'lng'   => ['name' => 'lng', 'type' => 'string', 'desc' => '经度值'],
                'lat'   => ['name' => 'lat', 'type' => 'string', 'desc' => '纬度值'],
                'p'     => ['name' => 'p', 'type' => 'int', 'default' => '1', 'desc' => '页数'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'],
            ],
            'getYouLike'        => [
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户Token'],
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
            ],
            'intimateList'      => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'touid' => ['name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '目标用户'],
                'type'  => ['name' => 'type', 'type' => 'string', 'default' => 'day', 'desc' => '参数类型，day表示日榜，week表示周榜，month代表月榜，total代表总榜'],
            ],
            'glamourList'       => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'page'  => ['name' => 'page', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => '页码'],
                'type'  => ['name' => 'type', 'type' => 'int', 'min' => 1, 'max' => 3, 'require' => true, 'desc' => '1日榜，2周榜，3总榜'],
            ],
            'wealthList'        => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'page'  => ['name' => 'page', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => '页码'],
                'type'  => ['name' => 'type', 'type' => 'int', 'min' => 1, 'max' => 3, 'require' => true, 'desc' => '1日榜，2周榜，3总榜'],
            ],
            'exchange'          => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'votes' => ['name' => 'votes', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '钻石数量'],
            ],
            'exchangeRecord'    => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'page'  => ['name' => 'page', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '页码'],
            ],
            'controlPush'       => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
            ],
            'controlPushStatic' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
            ],
            'setDevice'         => [
                'device_id'    => ['name' => 'device_id', 'type' => 'string', 'require' => true, 'desc' => '设备号'],
                'type'         => ['name' => 'type', 'type' => 'int', 'min' => 1, 'max' => 2, 'require' => true, 'desc' => '1苹果 2安卓'],
                'model'        => ['name' => 'model', 'type' => 'string', 'require' => true, 'desc' => '设备型号'],
                'uid'          => ['name' => 'uid', 'type' => 'int', 'desc' => 'uid'],
                'channel_name' => ['name' => 'channel_name', 'type' => 'string', 'require' => true, 'desc' => '渠道名称'],
                'version'      => ['name' => 'version', 'type' => 'string', 'desc' => '版本号'],
            ],
            'setNear'           => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
            ],
            'getNearOff'        => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
            ],
            'liveInfo'          => [
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
            'getConfig'        => [
                'channel_name' => ['name' => 'channel_name', 'type' => 'string', 'desc' => '渠道名', 'default' => ''],
                'uid' => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID', 'default' => 0],
            ],
        ];
    }

    /**
     * 配置信息
     * @desc 用于获取配置信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info[0] 配置信息
     * @return object info[0].guide 引导页
     * @return string info[0].guide.switch 开关，0关1开
     * @return string info[0].guide.type 类型，0图片1视频
     * @return string info[0].guide.time 图片时间
     * @return array  info[0].guide.list
     * @return string info[0].guide.list[].thumb 图片、视频链接
     * @return string info[0].guide.list[].href 页面链接
     * @return string msg 提示信息
     */
    public function getConfig(){
        $rs           = ['code' => 0, 'msg' => '', 'info' => []];
        $channel_name = checkNull($this->channel_name);
        $info         = getConfigPub();
        if($channel_name == 'guanwang'){
//            $info['apk_ver'] = '1.0.4';
        }else{
            $info['apk_ver'] = '1.0.1';
        }
        if ($this->uid) {
            $device = DI()->notorm->app_device
                ->where(['user_id' => $this->uid])
                ->select('channel_name')
                ->order('id desc')
                ->fetchOne();
            if ($device) {
                if ($device['channel_name'] == 'ios_wechat') {
                    $info['ipa_url'] = $info['ipa_wechat_url'] ?? $info['ipa_url'];
                }
            }
        }
        unset($info['site_url']);
        unset($info['site_seo_title']);
        unset($info['site_seo_keywords']);
        unset($info['site_seo_description']);
        unset($info['site_icp']);
        unset($info['site_gwa']);
        unset($info['site_admin_email']);
        unset($info['site_analytics']);
        unset($info['copyright']);
        unset($info['qr_url']);
        unset($info['sina_icon']);
        unset($info['sina_title']);
        unset($info['sina_desc']);
        unset($info['sina_url']);
        unset($info['qq_icon']);
        unset($info['qq_title']);
        unset($info['qq_desc']);
        unset($info['qq_url']);

        $info_pri       = getConfigPri();
        $list           = getLiveClass();
        $videoclasslist = getVideoClass();
        $level          = getLevelList();

        foreach($level as $k => $v){
            unset($v['level_up']);
            unset($v['addtime']);
            unset($v['id']);
            unset($v['levelname']);
            $level[$k] = $v;
        }

        $levelanchor = getLevelAnchorList();

        foreach($levelanchor as $k => $v){
            unset($v['level_up']);
            unset($v['addtime']);
            unset($v['id']);
            unset($v['levelname']);
            $levelanchor[$k] = $v;
        }

        $info['liveclass']  = $list;
        $info['videoclass'] = $videoclasslist;

        $info['level'] = $level;

        $info['levelanchor'] = $levelanchor;

        $info['tximgfolder']    = $info_pri['tximgfolder'];//腾讯云图片存储目录
        $info['txvideofolder']  = $info_pri['txvideofolder'];//腾讯云视频存储目录
        $info['txcloud_appid']  = $info_pri['txcloud_appid'];//腾讯云视频APPID
        $info['txcloud_region'] = $info_pri['txcloud_region'];//腾讯云视频地区
        $info['txcloud_bucket'] = $info_pri['txcloud_bucket'];//腾讯云视频存储桶
        $info['cloudtype']      = $info_pri['cloudtype'];//视频云存储类型
        //$info['qiniu_domain']=$info_pri['qiniu_domain_url'];//七牛云存储空间地址（后台配置）

        $info['qiniu_domain']       = DI()->config->get('app.Qiniu.space_host') . '/';//七牛云存储空间地址（后台配置）
        $info['video_audit_switch'] = $info_pri['video_audit_switch']; //视频审核是否开启

        /* 私信开关 */
        $info['letter_switch'] = $info_pri['letter_switch']; //视频审核是否开启

        /* 引导页 */
        $domain     = new Domain_Guide();
        $guide_info = $domain->getGuide();

        $info['guide'] = $guide_info;

        /* 小程序开关 */
        $info['applets_switch'] = '0';

        /** 敏感词集合*/
        $dirtyarr = [];
        if($info_pri['sensitive_words']){
            $dirtyarr = explode(',', $info_pri['sensitive_words']);
        }
        $info['sensitive_words'] = $dirtyarr;
        //视频水印图片
        $info['video_watermark'] = get_upload_path($info_pri['video_watermark']); //视频审核是否开启

        $info['shopexplain_url'] = $info['site'] . "/portal/page/index?id=38";
        $info['stricker_url']    = $info['site'] . "/portal/page/index?id=39";

        $info['wx_server'][] = 'Boya-kefu01';

        $info['jackpot_multiple'] = '100';

        $rs['info'][0] = $info;

        return $rs;
    }

    /**
     * 获取热门主播
     * @desc 用于获取首页热门主播
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info[0]['slide']
     * @return string info[0]['slide'][].slide_pic 图片
     * @return string info[0]['slide'][].slide_url 链接
     * @return array info[0]['list'] 热门直播列表
     * @return string info[0]['list'][].uid 主播id
     * @return string info[0]['list'][].avatar 主播头像
     * @return string info[0]['list'][].avatar_thumb 头像缩略图
     * @return string info[0]['list'][].user_nicename 直播昵称
     * @return string info[0]['list'][].title 直播标题
     * @return string info[0]['list'][].city 主播位置
     * @return string info[0]['list'][].stream 流名
     * @return string info[0]['list'][].pull 播流地址
     * @return string info[0]['list'][].nums 人数
     * @return string info[0]['list'][].thumb 直播封面
     * @return string info[0]['list'][].level 打赏等级
     * @return string info[0]['list'][].level_thumb 打赏等级标识
     * @return string info[0]['list'][].level_thumb_mark 打赏等级头像角标
     * @return string info[0]['list'][].level_anchor 主播等级
     * @return string info[0]['list'][].level_anchor_thumb 主播等级标识
     * @return string info[0]['list'][].level_anchor_thumb_mark 主播等级头像角标
     * @return string info[0]['list'][].type 直播类型
     * @return string info[0]['list'][].goodnum 靓号
     * @return string info[0]['list'][].verify 加v标识
     * @return string msg 提示信息
     */
    public function getHot(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $token      = checkNull($this->token);
        $uid        = checkNull($this->uid);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_Home();
        $key1   = Common_Cache::LIVE_HOT_PIC;
        $slide  = getcaches($key1);
        if(!$slide){
            $slide = $domain->getSlide(2);
            setcaches($key1, $slide, 300);
        }
        $key2 = Common_Cache::LIVE_HOT . $this->p;
        $list = getcaches($key2);
        if(!$list){
            $list = $domain->getHot($this->p);
            setCaches($key2, $list, 5);
        }
        $rs['info'][0]['slide'] = $slide;
        $rs['info'][0]['list']  = $list;
        return $rs;
    }

    /**
     * 获取关注主播列表
     * @desc 用于获取用户关注的主播的直播列表
     * @return int code 操作码，0表示成功
     * @return string info[0]['title'] 提示标题
     * @return string info[0]['des'] 提示描述
     * @return array info[0]['list'] 直播列表
     * @return string info[0]['list'][].uid 主播id
     * @return string info[0]['list'][].avatar 主播头像
     * @return string info[0]['list'][].avatar_thumb 头像缩略图
     * @return string info[0]['list'][].user_nicename 直播昵称
     * @return string info[0]['list'][].title 直播标题
     * @return string info[0]['list'][].city 主播位置
     * @return string info[0]['list'][].stream 流名
     * @return string info[0]['list'][].pull 播流地址
     * @return string info[0]['list'][].nums 人数
     * @return string info[0]['list'][].thumb 直播封面
     * @return string info[0]['list'][].level_anchor 主播等级
     * @return string info[0]['list'][].type 直播类型
     * @return string info[0]['list'][].goodnum 靓号
     * @return string msg 提示信息
     */
    public function getFollow(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $token      = checkNull($this->token);
        $uid        = checkNull($this->uid);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain        = new Domain_Home();
        $info          = $domain->getFollow($uid, $this->p);
        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * 获取最新主播
     * @desc 用于获取首页最新开播的主播列表
     * @return int code 操作码，0表示成功
     * @return array info 主播列表
     * @return string info[].uid 主播id
     * @return string info[].avatar 主播头像
     * @return string info[].avatar_thumb 头像缩略图
     * @return string info[].user_nicename 直播昵称
     * @return string info[].title 直播标题
     * @return string info[].city 主播位置
     * @return string info[].stream 流名
     * @return string info[].pull 播流地址
     * @return string info[].nums 人数
     * @return string info[].distance 距离
     * @return string info[].thumb 直播封面
     * @return string info[].level_anchor 主播等级
     * @return string info[].type 直播类型
     * @return string info[].goodnum 靓号
     * @return string msg 提示信息
     */
    public function getNew(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $token      = checkNull($this->token);
        $uid        = checkNull($this->uid);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $lng = checkNull($this->lng);
        $lat = checkNull($this->lat);
        $p   = checkNull($this->p);

        if(!$p){
            $p = 1;
        }
        $key  = 'getNew_' . $p;
        $info = getcaches($key);
        if(!$info){
            $domain = new Domain_Home();
            $info   = $domain->getNew($lng, $lat, $p);
            setCaches($key, $info, 5);
        }
        $rs['info'] = $info;
        return $rs;
    }

    /**
     * 搜索
     * @desc 用于首页搜索会员
     * @return int code 操作码，0表示成功
     * @return array info 会员列表
     * @return string info[].id 用户ID
     * @return string info[].user_nicename 用户昵称
     * @return string info[].avatar 头像
     * @return string info[].sex 性别
     * @return string info[].signature 签名
     * @return string info[].level 等级
     * @return string info[].isattention 是否关注，0未关注，1已关注
     * @return string msg 提示信息
     */
    public function search(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $token      = checkNull($this->token);
        $uid        = checkNull($this->uid);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $key = checkNull($this->key);
        $p   = checkNull($this->p);
        if($key == ''){
            $rs['code'] = 1001;
            $rs['msg']  = "请填写关键词";
            return $rs;
        }
        if(!$p){
            $p = 1;
        }
        $domain     = new Domain_Home();
        $info       = $domain->search($uid, $key, $p);
        $rs['info'] = $info;
        return $rs;
    }

    /**
     * 获取附近主播
     * @desc 用于获取附近开播的主播列表
     * @return array code 操作码，0表示成功
     * @return array info 主播列表
     * @return string info[].uid 主播id
     * @return string info[].avatar 主播头像
     * @return string info[].avatar_thumb 头像缩略图
     * @return string info[].user_nicename 直播昵称
     * @return string info[].title 直播标题
     * @return string info[].province 省份
     * @return string info[].city 主播位置
     * @return string info[].stream 流名
     * @return string info[].pull 播流地址
     * @return string info[].nums 人数
     * @return string info[].distance 距离
     * @return string info[].thumb 直播封面
     * @return string info[].level 打赏等级
     * @return string info[].level_thumb 打赏等级标识
     * @return string info[].level_thumb_mark 打赏等级头像角标
     * @return string info[].level_anchor 主播等级
     * @return string info[].level_anchor_thumb 主播等级标识
     * @return string info[].level_anchor_thumb_mark 主播等级头像角标
     * @return string info[].type 直播类型
     * @return string info[].goodnum 靓号
     * @return string msg 提示信息
     */
    public function getNearby(){
        $rs  = ['code' => 0, 'msg' => '', 'info' => []];
        $lng = checkNull($this->lng);
        $lat = checkNull($this->lat);
        $p   = checkNull($this->p);

        // 当前登录用户的经纬度
        if($lng == '' || $lat == ''){
            return $rs;
        }

        // 当前页数
        $p = empty($p) ? 1 : $p;

        $key  = 'getNearby_' . $lng . '_' . $lat . '_' . $p;
        $info = getcaches($key);
        if(!$info){
            $domain = new Domain_Home();
            $info   = $domain->getNearby($lng, $lat, $p);
            setcaches($key, $info, 10);
        }

        $rs['info'] = $info;
        return $rs;
    }

    /**
     * 获取附近人
     * @desc 用于获取附近用户
     * @return int code 操作码，0表示成功
     * @return array info 主播列表
     * @return string info[].id 用户id
     * @return string info[].avatar 主播头像
     * @return string info[].avatar_thumb 头像缩略图
     * @return string info[].user_nicename 用户昵称
     * @return string info[].signature 签名
     * @return string info[].sxi 性别
     * @return string info[].distance 距离
     * @return string info[].verify 是否有加V标识 0 无 1 有
     * @return string info[].is_live 是否在线 0 否 1 是
     * @return string info[].pull 在线用户播流地址
     * @return string info[].location 定位
     * @return int info[].level 用户等级
     * @return string info[].level_thumb 等级标识
     * @return string info[].level_thumb_mark 等级头像角标
     * @return string msg 提示信息
     */
    public function getNearUser(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $lng = checkNull($this->lng);
        $lat = checkNull($this->lat);
        $p   = checkNull($this->p);

        if($lng == ''){
            return $rs;
        }

        if($lat == ''){
            return $rs;
        }

        if(!$p){
            $p = 1;
        }

        $key  = 'Home:getNearUser_' . $lng . '_' . $lat . '_' . $p;
        $info = getcaches($key);
        if(!$info){
            $domain = new Domain_Home();
            $info   = $domain->getNearUser($lng, $lat, $p, $uid);
            setcaches($key, $info, 10);
        }

        $rs['info'] = $info;
        return $rs;
    }

    /**
     * 获取附近模块banner
     * @desc 用于获取附近模块banner
     * @return int code 操作码，0表示成功
     * @return array info 轮播列表
     * @return string info[0]['slide_pic'] 图片
     * @return string info[0]['slide_url'] 链接
     * @return string msg 提示信息
     */
    public function getNearBanner(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $domain     = new Domain_Home();
        $slide      = $domain->getSlide(3);
        $rs['info'] = $slide;
        return $rs;
    }

    /**
     * 猜你喜欢
     * @desc 用于获取猜你喜欢的主播
     * @return int code 操作码，0表示成功
     * @return array info 主播列表
     * @return string info[].id 用户id
     * @return string info[].avatar 主播头像
     * @return string info[].avatar_thumb 头像缩略图
     * @return string info[].user_nicename 用户昵称
     * @return string info[].signature 签名
     * @return string info[].sxi 性别
     * @return string info[].verify 是否有加V标识 0 未认证 1 已认证
     * @return string info[].location 定位
     * @return string msg 提示信息
     */
    public function getYouLike(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain     = new Domain_Home();
        $info       = $domain->getYouLike();
        $rs['info'] = $info;
        return $rs;
    }

    /**
     * 亲密榜
     * @desc 获取亲密榜单
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     * @return array info
     * @return string info[list][0]['uid'] 用户id
     * @return string info[list][0]['user_nicename'] 用户昵称
     * @return string info[list][0]['signature'] 签名
     * @return string info[list][0]['avatar'] 用户头像
     * @return string info[list][0]['avatar_thumb'] 小头像
     * @return string info[list][0]['totalcoin'] 用户贡献钻石数
     * @return string info[list][0]['level'] 财富等级
     * @return string info[list][0]['level_thumb'] 财富等级图标
     * @return string info[list][0]['level_anchor'] 魅力等级
     * @return string info[list][0]['level_anchor_thumb'] 魅力等级图标
     * @return string info[list][0]['isAttention'] 是否关注用户 0否 1是
     * @return string info[list][0]['level_thumb'] 等级图片
     * @return string info[list][0]['verify'] 是否加V认证 0否 1是
     * @return string info[my_data]['uid'] 我的用户id
     * @return string info[my_data]['user_nicename'] 昵称
     * @return string info[my_data]['verify'] 是否加V认证 0否 1是
     * @return string info[my_data]['signature'] 签名
     * @return string info[my_data]['totalcoin'] 我贡献的钻石数
     * @return string info[my_data]['avatar'] 头像
     * @return string info[my_data]['avatar_thumb'] 小头像
     * @return string info[my_data]['level'] 财富等级
     * @return string info[my_data]['level_thumb'] 财富等级图标
     * @return string info[my_data]['level_anchor'] 魅力等级
     * @return string info[my_data]['level_anchor_thumb'] 魅力等级图标
     **/
    public function intimateList(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $touid      = checkNull($this->touid);
        $type       = checkNull($this->type);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_Home();
        list($res, $myRes) = $domain->intimateList($uid, 1, $type, $touid);
        $rs['info']['list']    = $res;
        $rs['info']['my_data'] = $myRes;
        return $rs;
    }

    /**
     * 魅力榜（wanglin）
     * @desc 获取魅力榜
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     * @return array info
     * @return array info.my_info 自己的信息
     * @return array info.排行榜信息 自己的信息
     * @return string votes_sum 金额
     * @return string user_nicename 昵称
     * @return string cf_level 财富图标
     * @return string ml_level 魅力图标
     * @return string head_pic 头像
     * @return string now_three 贡献前三
     * @return string is_vip 是否vip
     * @return string no 排名
     * @return int is_live 是否直播中 0否 大于0是
     **/
    public function glamourList(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $type       = $this->type;
        $page       = $this->page;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain     = new Domain_Home();
        $rs['info'] = $domain->glamourList($type, $uid, $page);
        return $rs;
    }

    /**
     * 财富榜（wanglin）
     * @desc 获取财富榜
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     * @return array info
     * @return array info.my_info 自己的信息
     * @return array info.排行榜信息 自己的信息
     * @return string totalcoin_sum 金额
     * @return string user_nicename 昵称
     * @return string cf_level 财富图标
     * @return string ml_level 魅力图标
     * @return string head_pic 头像
     * @return string is_vip 是否vip
     * @return string no 排名
     * @return int is_live 是否直播中 0否 大于0是
     **/
    public function wealthList(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $type       = $this->type;
        $page       = $this->page;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain     = new Domain_Home();
        $rs['info'] = $domain->wealthList($type, $uid, $page);
        return $rs;
    }

    /**
     * 兑换（wanglin）
     * @desc 用户钻石兑换金币
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     **/
    public function exchange(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $money      = checkNull($this->votes);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_Home();
        list($rs['code'], $rs['msg']) = $domain->exchange($uid, $money);
        return $rs;
    }

    /**
     * 兑换记录（wanglin）
     * @desc 用户获取兑换记录
     * @return int code 操作码 0表示成功
     * @return array info[0].addtime 时间
     * @return array info[0].total 钻石数量
     * @return array info[0].nums 金币数量
     * @return string msg 提示信息
     **/
    public function exchangeRecord(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $page       = checkNull($this->page);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain     = new Domain_Home();
        $rs['info'] = $domain->exchangeRecord($uid, $page);
        return $rs;
    }

    /**
     * 设置主播开播提醒（wanglin）
     * @desc 用户设置主播开播提醒
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     **/
    public function controlPush(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_Home();
        list($rs['code'], $rs['msg']) = $domain->controlPush($uid);
        return $rs;
    }

    /**
     * 获取开播提醒状态（wanglin）
     * @desc 用户获取开播提醒状态
     * @return int code 操作码 0表示成功
     * @return array info[0] 1开启  2关闭
     * @return string msg 提示信息
     **/
    public function controlPushStatic(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain       = new Domain_Home();
        $rs['info'][] = $domain->controlPushStatic($uid);
        return $rs;
    }

    /**
     * 上传设备号（wanglin）
     * @desc 用于上传设备号
     * @return int code 操作码 0表示成功
     * @return array info[0] 1开启  2关闭
     * @return string msg 提示信息
     **/
    public function setDevice(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];
//        $token = checkNull($this->token);
//        $uid  = checkNull($this->uid);
//        $checkToken = checkToken($uid, $token);
//        if($checkToken == 700){
//            $rs['code'] = $checkToken;
//            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
//            return $rs;
//        }
        $data['device_id']    = $this->device_id;
        $data['type']         = $this->type;
        $data['model']        = $this->model;
        $data['user_id']      = $this->uid ?? '0';
        $data['channel_name'] = $this->channel_name;
        $data['version']      = $this->version ?: '';
        $m                    = new Domain_Home();
        if(!$m->setDevice($data)){
            $rs['code'] = 1;
            $rs['msg']  = 'fail';
        }
        return $rs;
    }

    /**
     * 获取app下载地址（wanglin）
     * @desc 用于获取app下载地址
     * @return int code 操作码 0表示成功
     * @return array info[0]['andriod'] 安卓地址
     * @return array info[0]['ios'] 苹果地址
     * @return string msg 提示信息
     **/
    public function getDownApp(){
        $rs = [
            'code' => 0,
            'msg'  => 'ok',
            'info' => [
                'ios'     => 'itms-services://?action=download-manifest&url=https://by.boyaduck.com/down8/manifest.plist',
                'andriod' => 'https://by.boyaduck.com/apk18/boya.apk',

                'ios_1'     => 'itms-services://?action=download-manifest&url=https://by.boyaduck.com/wechat8/manifest.plist',
                'andriod_1' => 'https://by.boyaduck.com/apk18/boya.apk',

                'ios_2'     => 'itms-services://?action=download-manifest&url=https://by.boyaduck.com/wechat8/manifest.plist',
                'andriod_2' => 'https://by.boyaduck.com/apk18/boya.apk',

                'andriod_3' => 'https://by.boyaduck.com/apk18/boya.apk',

                'andriod_4' => 'https://by.boyaduck.com/apk18/boya.apk',
            ],
        ];
        return $rs;
    }

    /**
     * 设置附近的人（wanglin）
     * @desc 用于设置附近的人
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     **/
    public function setNear(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = $this->token;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain  = new Domain_Home();
        $is_near = $domain->setNear($uid);
        if(!$is_near){
            $rs['code'] = 999;
            $rs['msg']  = '设置失败';
        }else{
            delcache(Common_Cache::USERINFO . $uid);
            $rs['info']['is_near'] = $is_near;
        }
        return $rs;
    }

    /**
     * 获取附近的人状态（wanglin）
     * @desc 用于获取附近的人状态
     * @return int code 操作码 0表示成功
     * @return int info.is_near 状态 1开启 2关闭
     * @return string msg 提示信息
     **/
    public function getNearOff(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = $this->token;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain     = new Domain_Home();
        $rs['info'] = $domain->getNearOff($uid);
        return $rs;
    }

    /**
     * 我的直播
     *
     * @desc 用于用户主页我的直播页面
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info['total'] 总数据
     * @return string info['total']['uid'] 家族id
     * @return string info['total']['nickname'] 主播昵称
     * @return string info['total']['avatar'] 主播头像
     * @return string info['total']['total_live_length'] 直播时长
     * @return string info['total']['total_live_times'] 开播次数
     * @return string info['total']['total_anchor_profit'] 主播收益
     * @return array info['total']['remark_info'] 官方认证信息（若未认证为空数组）
     * @return string info['total']['remark_info']['name'] 名称
     * @return string info['total']['remark_info']['icon'] 图标
     * @return string info['total']['remark_info']['auth_desc'] 描述
     * @return string info['total']['remark_info']['addtime'] 认证时间(时间戳)
     * @return string info['time_total'] 时间段数据
     * @return string info['time_total']['live_times'] 开播次数
     * @return string info['time_total']['live_length'] 直播时长
     * @return string info['time_total']['anchor_profit'] 主播收益
     * @return array  info['daily_detail'] 每日明细
     * @return string info['daily_detail'][0]['day'] 日期
     * @return string info['daily_detail'][0]['live_times'] 开播次数
     * @return string info['daily_detail'][0]['live_length'] 直播时长
     * @return string info['daily_detail'][0]['anchor_profit'] 主播收益
     * @return string msg 提示信息
     */
    public function liveInfo(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $token      = checkNull($this->token);
        $uid        = checkNull($this->uid);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $start_time = checkNull($this->start_time);
        $end_time   = checkNull($this->end_time);
        $rs['info'] = Domain_Family::myIndex($uid, $start_time, $end_time);
        return $rs;
    }
}
