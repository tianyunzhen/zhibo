<?php

/**
 * 直播间
 */
class Api_Live extends PhalApi_Api{

    public function getRules(){
        return [
            'getSDK'         => [
                'uid' => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
            ],
            'createRoom'     => [
                'uid'         => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'       => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'title'       => ['name' => 'title', 'type' => 'string', 'default' => '', 'desc' => '直播标题 url编码'],
                'province'    => ['name' => 'province', 'type' => 'string', 'default' => '', 'desc' => '省份'],
                'city'        => ['name' => 'city', 'type' => 'string', 'default' => '', 'desc' => '城市'],
                'lng'         => ['name' => 'lng', 'type' => 'string', 'default' => '0', 'desc' => '经度值'],
                'lat'         => ['name' => 'lat', 'type' => 'string', 'default' => '0', 'desc' => '纬度值'],
                'type'        => ['name' => 'type', 'type' => 'int', 'default' => '0', 'desc' => '直播类型，0是一般直播，1是私密直播，2是收费直播，3是计时直播'],
                'type_val'    => ['name' => 'type_val', 'type' => 'string', 'default' => '', 'desc' => '类型值'],
                'anyway'      => ['name' => 'anyway', 'type' => 'int', 'default' => '0', 'desc' => '直播类型 1 PC, 0 app'],
                'liveclassid' => ['name' => 'liveclassid', 'type' => 'int', 'default' => '0', 'desc' => '直播分类ID'],
                'deviceinfo'  => ['name' => 'deviceinfo', 'type' => 'string', 'default' => '', 'desc' => '设备信息'],
                'isshop'      => ['name' => 'isshop', 'type' => 'int', 'default' => '0', 'desc' => '是否开启购物车'],
                'pic_url'     => ['name' => 'pic_url', 'type' => 'string', 'default' => '', 'desc' => '直播封面'],
            ],
            'changeLive'     => [
                'uid'    => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'  => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'stream' => ['name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'],
                'status' => ['name' => 'status', 'type' => 'int', 'require' => true, 'desc' => '直播状态 0关闭 1直播'],
            ],
            'changeLiveType' => [
                'uid'      => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'    => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'stream'   => ['name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'],
                'type'     => ['name' => 'type', 'type' => 'int', 'default' => '0', 'desc' => '直播类型，0是一般直播，1是私密直播，2是收费直播，3是计时直播'],
                'type_val' => ['name' => 'type_val', 'type' => 'string', 'default' => '', 'desc' => '类型值'],
            ],
            'stopRoom'       => [
                'uid'    => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'  => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'stream' => ['name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'],
                'type'   => ['name' => 'type', 'type' => 'int', 'default' => '0', 'desc' => '类型'],
            ],

            'stopInfo' => [
                'stream' => ['name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'],
            ],

            'checkLive' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
                'stream'  => ['name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'],
            ],

            'roomCharge' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
                'stream'  => ['name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'],
            ],
            'timeCharge' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
                'stream'  => ['name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'],
            ],

            'enterRoom' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
                'stream'  => ['name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'],
                'city'    => ['name' => 'city', 'type' => 'string', 'default' => '', 'desc' => '城市'],
            ],

            'showVideo' => [
                'uid'      => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'    => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'touid'    => ['name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '上麦会员ID'],
                'pull_url' => ['name' => 'pull_url', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '连麦用户播流地址'],
            ],

            'getZombie'       => [
                'uid'    => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'stream' => ['name' => 'stream', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '流名'],
            ],
            'getLiveUserList' => [
                'uid'    => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'stream' => ['name' => 'stream', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '流名'],
            ],

            'getUserLists' => [
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
                'stream'  => ['name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'],
                'p'       => ['name' => 'p', 'type' => 'int', 'min' => 1, 'default' => 1, 'desc' => '页数'],
            ],

            'getPop' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
                'touid'   => ['name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'],
            ],

            'getGiftList' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'type'  => ['name' => 'type', 'type' => 'int', 'min' => 0, 'max' => 1, 'require' => true, 'desc' => '礼物类型 0幸运 1奢华'],
            ],

            'sendGift' => [
                'uid'        => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'      => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid'    => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
                'stream'     => ['name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'],
                'giftid'     => ['name' => 'giftid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '礼物ID'],
                'giftcount'  => ['name' => 'giftcount', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '礼物数量'],
                'ispack'     => ['name' => 'ispack', 'type' => 'int', 'default' => '0', 'desc' => '是否背包'],
                'is_sticker' => ['name' => 'is_sticker', 'type' => 'int', 'default' => '0', 'desc' => '是否为贴纸礼物：0：否；1：是'],
            ],

            'sendBarrage' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
                'stream'  => ['name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '流名'],
                'content' => ['name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '弹幕内容'],
            ],

            'setAdmin' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
                'touid'   => ['name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'],
            ],

            'getAdminList' => [
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
            ],

            'setReport' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'touid'   => ['name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'],
                'type'    => ['name' => 'type', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '举报类型(1 恶意骚扰 2 诈骗 3 未成年 4 涉政 5 其他)'],
                'content' => ['name' => 'content', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '举报内容'],
                'image'   => ['name' => 'image', 'type' => 'string', 'default' => '', 'desc' => '图片'],
            ],

            'getVotes' => [
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
            ],

            'setShutUp' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '用户token'],
                'touid'   => ['name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '禁言用户ID'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
                'type'    => ['name' => 'type', 'type' => 'int', 'default' => '0', 'desc' => '禁言类型,0永久，1本场'],
                'stream'  => ['name' => 'stream', 'type' => 'string', 'default' => '0', 'desc' => '流名，0永久'],
            ],

            'kicking' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
                'touid'   => ['name' => 'touid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '对方ID'],
                'stream'  => ['name' => 'stream', 'type' => 'string', 'default' => '0', 'desc' => '流名，0永久'],
            ],

            'superStopRoom' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '会员ID'],
                'token'   => ['name' => 'token', 'require' => true, 'min' => 1, 'desc' => '会员token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '主播ID'],
                'type'    => ['name' => 'type', 'type' => 'int', 'default' => 0, 'desc' => '关播类型 0表示关闭当前直播 1表示禁播，2表示封禁账号'],
            ],
            'searchMusic'   => [
                'key' => ['name' => 'key', 'type' => 'string', 'require' => true, 'desc' => '关键词'],
                'p'   => ['name' => 'p', 'type' => 'int', 'min' => 1, 'default' => 1, 'desc' => '页数'],
            ],

            'getDownurl' => [
                'audio_id' => ['name' => 'audio_id', 'type' => 'int', 'require' => true, 'desc' => '歌曲ID'],
            ],

            'getCoin' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '会员ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '会员token'],
            ],

            'checkLiveing' => [
                'uid'    => ['name' => 'uid', 'type' => 'int', 'desc' => '会员ID'],
                'stream' => ['name' => 'stream', 'type' => 'string', 'desc' => '流名'],
            ],

            'getLiveInfo' => [
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'desc' => '主播ID'],
            ],

            'getCover'          => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '会员ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '会员token'],
            ],
            'getFamilyLiveList' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '会员ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '会员token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '会员token'],
            ],
            'breakPull'         => [
            ],
            'incomeNo'          => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '会员ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '会员token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '主播ID'],
                'type'    => ['name' => 'type', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '1日榜，2周榜，3总榜'],
            ],
            'updateLiveTitle'   => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => 'uid'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => 'token'],
                'title' => ['name' => 'title', 'type' => 'string', 'require' => true, 'desc' => '标题'],
            ],
            'duckInfo'          => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'require' => true, 'desc' => '主播ID'],
            ],
            'attackList'        => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'require' => true, 'desc' => '主播ID'],
                'type'    => ['name' => 'type', 'type' => 'int', 'require' => true, 'min' => 1, 'default' => 1, 'desc' => '1日榜，2总榜'],
            ],
            'goalList'          => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'require' => true, 'desc' => '主播ID'],
                'type'    => ['name' => 'type', 'type' => 'int', 'require' => true, 'min' => 1, 'default' => 1, 'desc' => '1日榜，2总榜'],
            ],
            'deadList'          => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'require' => true, 'desc' => '主播ID'],
                'type'    => ['name' => 'type', 'type' => 'int', 'require' => true, 'min' => 1, 'default' => 1, 'desc' => '1日榜，2总榜'],
            ],
            'duckDetail'        => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'require' => true, 'desc' => '主播ID'],
            ],
            'getUserLiveData'   => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'liveuid' => ['name' => 'liveuid', 'type' => 'int', 'require' => true, 'desc' => '主播ID'],
            ],
        ];
    }

    /**
     * 获取SDK
     * @desc 用于获取SDK类型
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].live_sdk SDK类型，0金山SDK 1腾讯SDK
     * @return object info[0].android 安卓CDN配置
     * @return object info[0].ios IOS CDN配置
     * @return string info[0].isshop 是否有店铺，0否1是
     * @return string msg 提示信息
     */
    public function getSDK(){
        $rs        = ['code' => 0, 'msg' => '', 'info' => []];
        $uid       = checkNull($this->uid);
        $configpri = getConfigPri();

        //$info['live_sdk']=$configpri['live_sdk'];

//        $cdnset = include API_ROOT . '../../Config/cdnset.php';
        $cdnset = DI()->config->get('cdnset');

        $cdnset['live_sdk'] = $configpri['live_sdk'];

        /* 店铺信息 */
        $domain = new Domain_Shop();
        $isshop = $domain->isShop($uid);

        $cdnset['isshop'] = $isshop;
        $rs['info'][0]    = $cdnset;


        return $rs;
    }

    /**
     * 创建开播
     * @desc 用于用户开播生成记录
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].userlist_time 用户列表请求间隔
     * @return string info[0].barrage_fee 弹幕价格
     * @return string info[0].votestotal 主播映票
     * @return string info[0].stream 流名
     * @return string info[0].push 推流地址
     * @return string info[0].pull 播流地址
     * @return string info[0].chatserver socket地址
     * @return array info[0].game_switch 游戏开关
     * @return string info[0].game_switch[][0] 开启的游戏类型
     * @return string info[0].game_bankerid 庄家ID
     * @return string info[0].game_banker_name 庄家昵称
     * @return string info[0].game_banker_avatar 庄家头像
     * @return string info[0].game_banker_coin 庄家余额
     * @return string info[0].game_banker_limit 上庄限额
     * @return object info[0].liang 用户靓号信息
     * @return string info[0].liang.name 号码，0表示无靓号
     * @return object info[0].vip 用户VIP信息
     * @return string info[0].vip.type VIP类型，0表示无VIP，1表示有VIP
     * @return string info[0].guard_nums 守护数量
     * @return string msg 提示信息
     */
    public function createRoom(){
//        checkIdCard('汪林','500102199309144633');
        $rs        = ['code' => 0, 'msg' => '', 'info' => []];
        $uid       = $this->uid;
        $token     = checkNull($this->token);
        $configpub = getConfigPub();
        if($configpub['maintain_switch'] == 1){
            $rs['code'] = 1002;
            $rs['msg']  = $configpub['maintain_tips'];
            return $rs;
        }
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $isban = isBan($uid);
        if(!$isban){
            $rs['code'] = 1001;
            $rs['msg']  = '该账号已被禁用';
            return $rs;
        }

        $domain = new Domain_Live();
        $result = $domain->checkBan($uid);
        if($result){
            $rs['code'] = 1015;
            $rs['msg']  = '已被禁播';
            return $rs;
        }
        $configpri = getConfigPri();
        if($configpri['auth_islimit'] == 1){
            $check_domain = new Domain_User();
            $checkRes     = $check_domain->checkUserPhoneId($uid);

            $isauth = isAuth($uid);
            if(!$isauth){
                $rs['code'] = 1002;
                $rs['msg']  = '请先进行身份认证或等待审核';
                return $rs;
            }
        }
        $userinfo = getUserInfo($uid);

        if($configpri['level_islimit'] == 1){
            if($userinfo['level'] < $configpri['level_limit']){
                $rs['code'] = 1003;
                $rs['msg']  = '等级小于' . $configpri['level_limit'] . '级，不能直播';
                return $rs;
            }
        }

        $nowtime = time();

        $showid      = $nowtime;
        $starttime   = $nowtime;
        $title       = checkNull($this->title);
        $province    = checkNull($this->province);
        $city        = checkNull($this->city);
        $lng         = checkNull($this->lng);
        $lat         = checkNull($this->lat);
        $type        = checkNull($this->type);
        $type_val    = checkNull($this->type_val);
        $anyway      = checkNull($this->anyway);
        $liveclassid = checkNull($this->liveclassid);
        $deviceinfo  = checkNull($this->deviceinfo);
        $isshop      = checkNull($this->isshop);
        $pic_url     = checkNull($this->pic_url);

        if($type == 1 && $type_val == ''){
            $rs['code'] = 1002;
            $rs['msg']  = '密码不能为空';
            return $rs;
        }elseif($type > 1 && $type_val <= 0){
            $rs['code'] = 1002;
            $rs['msg']  = '价格不能小于等于0';
            return $rs;
        }


        $stream = $uid . '_' . $nowtime;
        $wy_cid = '';
        if($configpri['cdn_switch'] == 5){
            $wyinfo = PrivateKeyA('rtmp', $stream, 1);
            $pull   = $wyinfo['ret']["rtmpPullUrl"];
            $wy_cid = $wyinfo['ret']["cid"];
            $push   = $wyinfo['ret']["pushUrl"];
        }else{
            $push = PrivateKeyA('rtmp', $stream, 1);
            $pull = PrivateKeyA('rtmp', $stream, 0);
        }
        if(!$city){
            $city = '好像在火星';
        }
        if(!$lng && $lng != 0){
            $lng = '';
        }
        if(!$lat && $lat != 0){
            $lat = '';
        }
        $thumb = '';
        if(!$pic_url){
            if($_FILES){
                if($_FILES["file"]["error"] > 0){
                    $rs['code'] = 1003;
                    $rs['msg']  = T('failed to upload file with error: {error}', ['error' => $_FILES['file']['error']]);
                    DI()->logger->debug('failed to upload file with error: ' . $_FILES['file']['error']);
                    return $rs;
                }

                if(!checkExt($_FILES["file"]['name'])){
                    $rs['code'] = 1004;
                    $rs['msg']  = '图片仅能上传 jpg,png,jpeg';
                    return $rs;
                }

                $uptype = DI()->config->get('app.uptype');
                if($uptype == 1){
                    //七牛
                    $url = DI()->qiniu->uploadFile($_FILES['file']['tmp_name']);

                    if(!empty($url)){
                        $thumb = $url . '?imageView2/2/w/600/h/600'; //600 X 600
                    }
                }elseif($uptype == 2){
                    //本地上传
                    //设置上传路径 设置方法参考3.2
                    DI()->ucloud->set('save_path', 'thumb/' . date("Ymd"));

                    //新增修改文件名设置上传的文件名称
                    // DI()->ucloud->set('file_name', $this->uid);

                    //上传表单名
                    $res = DI()->ucloud->upfile($_FILES['file']);

                    $files         = '../upload/' . $res['file'];
                    $PhalApi_Image = new Image_Lite();
                    //打开图片
                    $PhalApi_Image->open($files);
                    /**
                     * 可以支持其他类型的缩略图生成，设置包括下列常量或者对应的数字：
                     * IMAGE_THUMB_SCALING      //常量，标识缩略图等比例缩放类型
                     * IMAGE_THUMB_FILLED       //常量，标识缩略图缩放后填充类型
                     * IMAGE_THUMB_CENTER       //常量，标识缩略图居中裁剪类型
                     * IMAGE_THUMB_NORTHWEST    //常量，标识缩略图左上角裁剪类型
                     * IMAGE_THUMB_SOUTHEAST    //常量，标识缩略图右下角裁剪类型
                     * IMAGE_THUMB_FIXED        //常量，标识缩略图固定尺寸缩放类型
                     */
                    $PhalApi_Image->thumb(660, 660, IMAGE_THUMB_SCALING);
                    $PhalApi_Image->save($files);

                    $thumb = $res['url'];
                }

                @unlink($_FILES['file']['tmp_name']);
            }else{
                $domain2 = new Domain_User();
                $thumb   = $domain2->getCover($uid);
            }
        }else{
            $thumb = $pic_url . '?imageView2/2/w/600/h/600';
        }

        /* 主播靓号 */
        $liang   = getUserLiang($uid);
        $goodnum = 0;
        if($liang['name'] != 0){
            $goodnum = $liang['name'];
        }
        $info['liang'] = $liang;

        /* 主播VIP */
        $vip         = getUserVip($uid);
        $info['vip'] = $vip;

        $dataroom = [
            "uid"         => $uid,
            "showid"      => $showid,
            "starttime"   => $starttime,
            "title"       => $title,
            "province"    => $province,
            "city"        => $city,
            "stream"      => $stream,
            "thumb"       => $thumb,
            "pull"        => $pull,
            "lng"         => $lng,
            "lat"         => $lat,
            "type"        => $type,
            "type_val"    => $type_val,
            "goodnum"     => $goodnum,
            "isvideo"     => 0,
            "islive"      => 0,
            "wy_cid"      => $wy_cid,
            "anyway"      => $anyway,
            "liveclassid" => $liveclassid,
            "deviceinfo"  => $deviceinfo,
            "isshop"      => $isshop,
            "hotvotes"    => 0,
            "pkuid"       => 0,
            "pkstream"    => '',
            "banker_coin" => 10000000,
        ];

        $domain = new Domain_Live();
        list($code, $str) = $domain->createRoom($uid, $dataroom);
        if($code !== 0){
            $rs['code'] = 1011;
            $rs['msg']  = '开播失败，请重试';
            return $rs;
        }
        if($str){
            $stream = $str;
        }
        $data    = ['city' => $city];
        $domain2 = new Domain_User();
        $domain2->userUpdate($uid, $data);

        $userinfo['city']     = $city;
        $userinfo['usertype'] = 50;
        $userinfo['sign']     = '0';

        setcaches(Common_Cache::ENTER_ROOM_TOKEN . $token, $userinfo, 43200);

        $votestotal = $domain->getVotes($uid);

        $info['userlist_time'] = $configpri['userlist_time'];
        $info['barrage_fee']   = $configpri['barrage_fee'];
        $info['chatserver']    = $configpri['chatserver'];
//        $info['chatserver'] = 'http://192.168.31.135:19967';

        $info['votestotal'] = $votestotal;
        $info['stream']     = $stream;
        $info['push']       = $push;
        $info['pull']       = $pull;

        /* 游戏配置信息 */
        $info['game_switch']        = $configpri['game_switch'];
        $info['game_bankerid']      = '0';
        $info['game_banker_name']   = '吕布';
        $info['game_banker_avatar'] = '';
        $info['game_banker_coin']   = NumberFormat(10000000);
        $info['game_banker_limit']  = $configpri['game_banker_limit'];
        /* 游戏配置信息 */

        /* 守护数量 */
        $domain_guard       = new Domain_Guard();
        $guard_nums         = $domain_guard->getGuardNums($uid);
        $info['guard_nums'] = $guard_nums;

        /* 腾讯APPID */
        $info['tx_appid'] = $configpri['tx_appid'];

        /* 奖池 */
        $info['jackpot_level'] = '-1';
        $jackpotset            = getJackpotSet();
        if($jackpotset['switch']){
            $jackpotinfo           = getJackpotInfo();
            $info['jackpot_level'] = $jackpotinfo['level'];
        }
//		/** 敏感词集合*/
//		$dirtyarr=array();
//		if($configpri['sensitive_words']){
//            $dirtyarr=explode(',',$configpri['sensitive_words']);
//        }
//		$info['sensitive_words']=$dirtyarr;
        $info['uid']   = $uid;
        $rs['info'][0] = $info;


        /* 清除连麦PK信息 */
        DI()->redis->hset('LiveConnect', $uid, 0);
        DI()->redis->hset('LivePK', $uid, 0);
        DI()->redis->hset('LivePK_gift', $uid, 0);

        return $rs;
    }


    /**
     * 修改直播状态
     * @desc 用于主播修改直播状态
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].msg 成功提示信息
     * @return string msg 提示信息
     */
    public function changeLive(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid    = $this->uid;
        $token  = checkNull($this->token);
        $stream = checkNull($this->stream);
        $status = $this->status;

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain   = new Domain_Live();
        $info     = $domain->changeLive($uid, $stream, $status);
        $userinfo = getUserInfo($uid);

        $anthorinfo = [
            "uid"           => $info['uid'],
            "avatar"        => $info['avatar'],
            "avatar_thumb"  => $info['avatar_thumb'],
            "user_nicename" => $info['user_nicename'],
            "title"         => $info['title'],
            "city"          => $info['city'],
            "stream"        => $info['stream'],
            "pull"          => $info['pull'],
            "thumb"         => $info['thumb'],
            "isvideo"       => '0',
            "type"          => $info['type'],
            "type_val"      => $info['type_val'],
            "game_action"   => '0',
            "goodnum"       => $info['goodnum'],
            "anyway"        => $info['anyway'],
            "nums"          => 0,
            "level_anchor"  => $userinfo['level_anchor'],
            "game"          => '',
        ];
        $title      = '开播啦';
        $content    = '你的好友：' . $anthorinfo['user_nicename'] . '正在直播，邀请你一起';
        $push_model = new Common_JPush();
        $push_model->sendLabel($title, $content, Common_JPush::FOLLOW_PUSH . $uid);
        DI()->redis->Incrby(Common_Cache::CORPSE_INSET . $stream, 30);
        $rs['info'][0]['msg'] = '成功';
        return $rs;
    }

    /**
     * 修改直播类型
     * @desc 用于主播修改直播类型
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].msg 成功提示信息
     * @return string msg 提示信息
     */
    public function changeLiveType(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid    = $this->uid;
        $token  = checkNull($this->token);
        $stream = checkNull($this->stream);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $type     = checkNull($this->type);
        $type_val = checkNull($this->type_val);

        if($type == 1 && $type_val == ''){
            $rs['code'] = 1002;
            $rs['msg']  = '密码不能为空';
            return $rs;
        }elseif($type > 1 && $type_val <= 0){
            $rs['code'] = 1002;
            $rs['msg']  = '价格不能小于等于0';
            return $rs;
        }


        $data = [
            "type"     => $type,
            "type_val" => $type_val,
        ];

        $domain = new Domain_Live();
        $info   = $domain->changeLiveType($uid, $stream, $data);

        $rs['info'][0]['msg'] = '成功';
        return $rs;
    }

    /**
     * 关闭直播
     * @desc 用于用户结束直播
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].msg 成功提示信息
     * @return string msg 提示信息
     */
    public function stopRoom(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        file_put_contents(API_ROOT . '/Runtime/stopRoom_' . date('Y-m-d') . '.txt', date('Y-m-d H:i:s') . ' 提交参数信息 开始:' . "\r\n", FILE_APPEND);
        file_put_contents(API_ROOT . '/Runtime/stopRoom_' . date('Y-m-d') . '.txt', date('Y-m-d H:i:s') . ' 提交参数信息 _REQUEST:' . json_encode($_REQUEST) . "\r\n", FILE_APPEND);
        $uid    = $this->uid;
        $token  = checkNull($this->token);
        $stream = checkNull($this->stream);
        $type   = checkNull($this->type);

        $key     = 'stopRoom_' . $stream;
        $isexist = getcaches($key);
        file_put_contents(API_ROOT . '/Runtime/stopRoom_' . date('Y-m-d') . '.txt', date('Y-m-d H:i:s') . ' 提交参数信息 isexist:' . json_encode($isexist) . "\r\n", FILE_APPEND);
        //if(!$isexist && $type==1){
        if(!$isexist){
            $checkToken = checkToken($uid, $token);
            file_put_contents(API_ROOT . '/Runtime/stopRoom_' . date('Y-m-d') . '.txt', date('Y-m-d H:i:s') . ' 提交参数信息 checkToken:' . json_encode($checkToken) . "\r\n", FILE_APPEND);
            if($checkToken == 700){
                $rs['code'] = $checkToken;
                $rs['msg']  = '您的登陆状态失效或账号已被禁用';
                return $rs;
            }
            setcaches($key, '1', 10);
            //if($type==1){
            $domain = new Domain_Live();
            $info   = $domain->stopRoom($uid, $stream, 0);
            DI()->redis->del(Common_Cache::ENTER_ROOM_TOKEN . $token);//关播删除缓存
            //}
        }

        $rs['info'][0]['msg'] = '关播成功';
        file_put_contents(API_ROOT . '/Runtime/stopRoom_' . date('Y-m-d') . '.txt', date('Y-m-d H:i:s') . ' 提交参数信息 结束:' . "\r\n", FILE_APPEND);

        return $rs;
    }

    /**
     * 直播结束信息
     * @desc 用于直播结束页面信息展示
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].nums 人数
     * @return string info[0].length 时长
     * @return string info[0].votes 映票数
     * @return string msg 提示信息
     */
    public function stopInfo(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $stream = checkNull($this->stream);

        $domain = new Domain_Live();
        $info   = $domain->stopInfo($stream);

        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * 检查直播
     * @desc 用于用户进房间时检查直播
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].type 房间类型
     * @return string info[0].type_val 收费房间价格，默认0
     * @return string info[0].type_msg 提示信息
     * @return string msg 提示信息
     */
    public function checkLive(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = $this->uid;
        $token   = checkNull($this->token);
        $liveuid = $this->liveuid;
        $stream  = checkNull($this->stream);

        $configpub = getConfigPub();
        if($configpub['maintain_switch'] == 1){
            $rs['code'] = 1002;
            $rs['msg']  = $configpub['maintain_tips'];
            return $rs;

        }

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }


        $isban = isBan($uid);
        if(!$isban){
            $rs['code'] = 1001;
            $rs['msg']  = '该账号已被禁用';
            return $rs;
        }


        if($uid == $liveuid){
            $rs['code'] = 1011;
            $rs['msg']  = '不能进入自己的直播间';
            return $rs;
        }


        $domain = new Domain_Live();
        $info   = $domain->checkLive($uid, $liveuid, $stream);

        if($info == 1005){
            $rs['code'] = 1005;
            $rs['msg']  = '直播已结束';
            return $rs;
        }elseif($info == 1007){
            $rs['code'] = 1007;
            $rs['msg']  = '超管不能进入1v1房间';
            return $rs;
        }elseif($info == 1008){
            $rs['code'] = 1004;
            $rs['msg']  = '您已被踢出房间';
            return $rs;
        }


        $configpri = getConfigPri();

        $info['live_sdk'] = $configpri['live_sdk'];
        $info['is_live']  = 1;

        $rs['info'][0] = $info;


        return $rs;
    }

    /**
     * 房间扣费
     * @desc 用于房间扣费
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].coin 用户余额
     * @return string msg 提示信息
     */
    public function roomCharge(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = $this->uid;
        $token   = checkNull($this->token);
        $liveuid = $this->liveuid;
        $stream  = checkNull($this->stream);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain = new Domain_Live();
        $info   = $domain->roomCharge($uid, $liveuid, $stream);

        if($info == 1005){
            $rs['code'] = 1005;
            $rs['msg']  = '直播已结束';
            return $rs;
        }elseif($info == 1006){
            $rs['code'] = 1006;
            $rs['msg']  = '该房间非扣费房间';
            return $rs;
        }elseif($info == 1007){
            $rs['code'] = 1007;
            $rs['msg']  = '房间费用有误';
            return $rs;
        }elseif($info == 1008){
            $rs['code'] = 1008;
            $rs['msg']  = '余额不足';
            return $rs;
        }
        $rs['info'][0]['coin'] = $info['coin'];

        return $rs;
    }

    /**
     * 房间计时扣费
     * @desc 用于房间计时扣费
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].coin 用户余额
     * @return string msg 提示信息
     */
    public function timeCharge(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = $this->uid;
        $token   = checkNull($this->token);
        $liveuid = $this->liveuid;
        $stream  = checkNull($this->stream);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain = new Domain_Live();

        $key   = 'timeCharge_' . $stream . '_' . $uid;
        $cache = getcaches($key);
        if($cache){
            $coin                  = $domain->getUserCoin($uid);
            $rs['info'][0]['coin'] = $coin['coin'];
            return $rs;
        }


        $info = $domain->roomCharge($uid, $liveuid, $stream);

        if($info == 1005){
            $rs['code'] = 1005;
            $rs['msg']  = '直播已结束';
            return $rs;
        }elseif($info == 1006){
            $rs['code'] = 1006;
            $rs['msg']  = '该房间非扣费房间';
            return $rs;
        }elseif($info == 1007){
            $rs['code'] = 1007;
            $rs['msg']  = '房间费用有误';
            return $rs;
        }elseif($info == 1008){
            $rs['code'] = 1008;
            $rs['msg']  = '余额不足';
            return $rs;
        }
        $rs['info'][0]['coin'] = $info['coin'];

        setcaches($key, 1, 50);

        return $rs;
    }


    /**
     * 进入直播间（wanglin）
     * @desc 用于用户进入直播
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].votestotal 直播映票
     * @return string info[0].barrage_fee 弹幕价格
     * @return string info[0].userlist_time 用户列表获取间隔
     * @return string info[0].chatserver socket地址
     * @return string info[0].isattention 是否关注主播，0表示未关注，1表示已关注
     * @return string info[0].nums 房间人数
     * @return string info[0].push_url 推流地址
     * @return string info[0].pull_url 播流地址
     * @return string info[0].linkmic_uid 连麦用户ID，0表示未连麦
     * @return string info[0].linkmic_pull 连麦播流地址
     * @return array info[0].userlists 用户列表
     * @return array info[0].game 押注信息
     * @return array info[0].gamebet 当前用户押注信息
     * @return string info[0].gametime 游戏剩余时间
     * @return string info[0].gameid 游戏记录ID
     * @return string info[0].gameaction 游戏类型，1表示炸金花，2表示牛牛，3表示转盘
     * @return string info[0].game_bankerid 庄家ID
     * @return string info[0].game_banker_name 庄家昵称
     * @return string info[0].game_banker_avatar 庄家头像
     * @return string info[0].game_banker_coin 庄家余额
     * @return string info[0].game_banker_limit 上庄限额
     * @return object info[0].vip 用户VIP信息
     * @return string info[0].vip.type VIP类型，0表示无VIP，1表示普通VIP，2表示至尊VIP
     * @return object info[0].liang 用户靓号信息
     * @return string info[0].liang.name 号码，0表示无靓号
     * @return object info[0].guard 守护信息
     * @return string info[0].guard.type 守护类型，0表示非守护，1表示月守护，2表示年守护
     * @return string info[0].guard.endtime 到期时间
     * @return string info[0].guard_nums 主播守护数量
     * @return object info[0].pkinfo 主播连麦/PK信息
     * @return string info[0].pkinfo.pkuid 连麦用户ID
     * @return string info[0].pkinfo.pkpull 连麦用户播流地址
     * @return string info[0].pkinfo.ifpk 是否PK
     * @return string info[0].pkinfo.pk_time 剩余PK时间（秒）
     * @return string info[0].pkinfo.pk_gift_liveuid 主播PK总额
     * @return string info[0].pkinfo.pk_gift_pkuid 连麦主播PK总额
     * @return string info[0].isred 是否显示红包
     * @return string info[0].family_name 家族名称
     * @return string msg 提示信息
     */
    public function enterRoom(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = $this->uid;
        $token   = checkNull($this->token);
        $liveuid = $this->liveuid;
        $city    = checkNull($this->city);
        $stream  = checkNull($this->stream);
        $redis   = DI()->redis;

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }


        $isban = isBan($uid);
        if(!$isban){
            $rs['code'] = 1001;
            $rs['msg']  = '该账号已被禁用';
            return $rs;
        }


        //是否禁言
        $domain = new Domain_Live();
        $domain->checkShut($uid, $liveuid);
        //个人信息
        $userinfo = getUserInfo($uid);
        //家族
        $family_model            = new Model_Family();
        $familyInfo              = $family_model->userFamilyInfo($liveuid, "b.name");
        $userinfo['family_name'] = $familyInfo[0]['name'] ?? '';
        //坐骑
        $carinfo         = getUserCar($uid);
        $userinfo['car'] = $carinfo;
        //超管
        $superModel = new Model_UserSuper();
        $isSuper    = $superModel->isSuper($uid);
        if($isSuper == 1){
            $redis->hset('super', $userinfo['id'], '1');
        }else{
            $redis->hDel('super', $userinfo['id']);
        }
        //地址
        $data = ['city' => $city];

        $domain2 = new Domain_User();
        $domain2->userUpdate($uid, $data);
        $userinfo['city'] = $city;

        $usertype             = isAdmin($uid, $liveuid);
        $userinfo['usertype'] = $usertype;

        $stream2 = explode('_', $stream);
        $showid  = $stream2[1];

        $contribution = '0';
        if($showid){
            $contribution = $domain->getContribut($uid, $liveuid, $showid);
        }

        $userinfo['contribution'] = $contribution;


        unset($userinfo['issuper']);

        /* 守护 */
        $domain_guard = new Domain_Guard();
        $guard_info   = $domain_guard->getUserGuard($uid, $liveuid);

        $guard_nums             = $domain_guard->getGuardNums($liveuid);
        $userinfo['guard_type'] = $guard_info['type'];
        /* 等级+100 保证等级位置位数相同，最后拼接1 防止末尾出现0 */
        $userinfo['sign'] = $userinfo['contribution'] . '.' . ($userinfo['level'] + 100) . '1';

        setcaches(Common_Cache::ENTER_ROOM_TOKEN . $token, $userinfo, 43200);
        //主播金币
        $domain     = new Domain_Live();
        $votestotal = $domain->getVotes($liveuid);

        //本场观看人数
        $audience_key  = Common_Cache::LIVE_AUDIENCE . $stream;
        $live_now_nums = Common_Cache::LIVE_NOW_NUMS . $stream;
        if(!$redis->SISMEMBER($audience_key, $uid)){
            $redis->SADD($audience_key, $uid);
            $redis->INCRBY(Common_Cache::CORPSE_INSET . $stream, 5);
        }
        if($uid != $liveuid){
            if(!$redis->SISMEMBER($live_now_nums, $uid)){
                //计算送礼数量
                $coinModel = new Model_GiftRecord();
                $branch    = $coinModel->getNowDayCoin($uid, $liveuid);
                if($branch > 0){
                    $branch = -$branch;
                }
                DI()->redis->ZADD($live_now_nums, $branch, $uid);
            }
        }


        /* 用户列表 */
        $user_list = $domain->getLiveUserList($stream);


        /* 用户连麦 */
        $linkMicUid   = '0';
        $linkMicPull  = '';
        $linkCacheKey = Common_Cache::LINK_MIC;
        $showVideo    = DI()->redis->hGet($linkCacheKey, $liveuid);
        if($showVideo){
            $showVideo_a = json_decode($showVideo, true);
            $linkMicUid  = $showVideo_a['uid'];
            $linkMicPull = $this->getPullWithSign($showVideo_a['pull_url']);
        }

        /* pk信息 */
        $pkInfo = ['isPk' => '0','pkuid'=>'0'];

        //pk信息
        $livePkDomain = new Domain_Livepk();
        $livePkInfo   = $livePkDomain->getPkInfo($liveuid);
        if($livePkInfo['isPk'] == '1'){
            list($isPk, $pkTime) = $livePkDomain->getPkTime($liveuid);
            $livePkInfo['isPk']    = $isPk;
            $livePkInfo['pk_time'] = $pkTime;
            $livePkInfo['pkpull']  = $livePkDomain->getPkPull($liveuid);
            $pkInfo                = $livePkInfo;
        }

        $configpri = getConfigPri();

        $game    = [
            "brand"         => [],
            "bet"           => ['0', '0', '0', '0'],
            "time"          => "0",
            "id"            => "0",
            "action"        => "0",
            "bankerid"      => "0",
            "banker_name"   => "吕布",
            "banker_avatar" => "",
            "banker_coin"   => "0",
        ];
        $isLive  = 1;
        $model   = new Model_Live();
        $oldInfo = $model->checkLive($uid, $liveuid, $stream);
        if($oldInfo == 1005){
            $isLive = 0;
        }elseif($oldInfo == 1007){
            $isLive = 0;
        }elseif($oldInfo == 1008){
            $isLive = 0;
        }
        $info                = [
            'votestotal'         => $votestotal ?: 0,
            'barrage_fee'        => $configpri['barrage_fee'],
            'userlist_time'      => $configpri['userlist_time'],
            'chatserver'         => $configpri['chatserver'],
            //                                    'chatserver'         => 'http://192.168.31.135:19967',
            'linkmic_uid'        => $linkMicUid,
            'linkmic_pull'       => $linkMicPull,
            'nums'               => $user_list['nums'],
            'game'               => $game['brand'],
            'gamebet'            => $game['bet'],
            'gametime'           => $game['time'],
            'gameid'             => $game['id'],
            'gameaction'         => $game['action'],
            'game_bankerid'      => $game['bankerid'],
            'game_banker_name'   => $game['banker_name'],
            'game_banker_avatar' => $game['banker_avatar'],
            'game_banker_coin'   => $game['banker_coin'],
            'game_banker_limit'  => $configpri['game_banker_limit'],
            'speak_limit'        => $configpri['speak_limit'],
            'barrage_limit'      => $configpri['barrage_limit'],
            'vip'                => $userinfo['vip'],
            'liang'              => $userinfo['liang'],
            'issuper'            => (string)$isSuper,
            'usertype'           => (string)$usertype,
            'family_name'        => $userinfo['family_name'],
            'is_live'            => $isLive,
            'is_black'           => $oldInfo['is_black'],
        ];
        $info['isattention'] = (string)isAttention($uid, $liveuid);
        $info['userlists']   = $user_list['list'];

        /* 用户余额 */
        $domain2      = new Domain_User();
        $usercoin     = $domain2->getBalance($uid);
        $info['coin'] = $usercoin['coin'];

        /* 守护 */
        $info['guard']      = $guard_info;
        $info['guard_nums'] = $guard_nums;

        /* 主播连麦/PK */
        $info['pkinfo']  = ['isPk' => '0','pkuid'=>'0'];
        $info['pkInfos'] = $pkInfo;

        /* 红包 */
        $key   = 'red_list_' . $stream;
        $nums  = DI()->redis->lLen($key);
        $isred = '0';
        if($nums > 0){
            $isred = '1';
        }
        $info['isred'] = $isred;

        /* 奖池 */
        $info['jackpot_level'] = '-1';
        $jackpotset            = getJackpotSet();
        if($jackpotset['switch']){
            $jackpotinfo           = getJackpotInfo();
            $info['jackpot_level'] = $jackpotinfo['level'];
        }
//        /** 敏感词集合*/
//        $dirtyarr = [];
//        if($configpri['sensitive_words']){
//            $dirtyarr = explode(',', $configpri['sensitive_words']);
//        }
//        $info['sensitive_words'] = $dirtyarr;
        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * 连麦信息
     * @desc 用于主播同意连麦 写入redis
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */

    public function showVideo(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid      = $this->uid;
        $token    = checkNull($this->token);
        $touid    = checkNull($this->touid);
        $pull_url = checkNull($this->pull_url);

        // file_put_contents('./showVideo.txt',date('Y-m-d H:i:s').' 提交参数信息 uid:'.json_encode($uid)."\r\n",FILE_APPEND);
        // file_put_contents('./showVideo.txt',date('Y-m-d H:i:s').' 提交参数信息 token:'.json_encode($token)."\r\n",FILE_APPEND);
        // file_put_contents('./showVideo.txt',date('Y-m-d H:i:s').' 提交参数信息 touid:'.json_encode($touid)."\r\n",FILE_APPEND);
        // file_put_contents('./showVideo.txt',date('Y-m-d H:i:s').' 提交参数信息 pull_url:'.json_encode($pull_url)."\r\n",FILE_APPEND);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $data = [
            'uid'      => $touid,
            'pull_url' => $pull_url,
        ];

        // file_put_contents('./showVideo.txt',date('Y-m-d H:i:s').' 提交参数信息 set:'.json_encode($data)."\r\n",FILE_APPEND);
        $key = Common_Cache::LINK_MIC;
        DI()->redis->hset($key, $uid, json_encode($data));
        return $rs;
    }

    /**
     * 获取最新流地址
     * @desc 用于连麦获取最新流地址
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */

    protected function getPullWithSign($pull){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        if($pull == ''){
            return '';
        }
        $list1       = preg_split('/\?/', $pull);
        $originalUrl = $list1[0];

        $list = preg_split('/\//', $originalUrl);
        $url  = preg_split('/\./', end($list));

        $stream = $url[0];

        $play_url = PrivateKeyA('rtmp', $stream, 0);

        return $play_url;
    }

    /**
     * 获取僵尸粉
     * @desc 用于获取僵尸粉
     * @return int code 操作码，0表示成功
     * @return array info 僵尸粉信息
     * @return string msg 提示信息
     */

    public function getZombie(){
        $rs     = ['code' => 0, 'msg' => '', 'info' => []];
        $stream = checkNull($this->stream);
        $domain = new Domain_Live();
        list($code, $info) = $domain->getZombie($stream);
        if($code > 0){
            $rs['code'] = $code;
            $rs['msg']  = $info;
        }else{
            $rs['info'] = $info;
        }
        return $rs;
    }



//    /**
//     * 获取僵尸粉
//     * @desc 用于获取僵尸粉
//     * @return int code 操作码，0表示成功
//     * @return array info 僵尸粉信息
//     * @return string msg 提示信息
//     */
//
//    public function getZombie(){
//        $rs = ['code' => 0, 'msg' => '', 'info' => []];
//
//        $uid    = $this->uid;
//        $stream = checkNull($this->stream);
//
//        $stream2 = explode('_', $stream);
//        $liveuid = $stream2[0];
//
//
//        $domain = new Domain_Live();
//
//        $iszombie = $domain->isZombie($liveuid);
//
//        if($iszombie == 0){
//            $rs['code'] = 1001;
//            $rs['info'] = '未开启僵尸粉';
//            $rs['msg']  = '未开启僵尸粉';
//            return $rs;
//
//        }
//
//        /* 判断用户是否进入过 */
//        $isvisit = DI()->redis->sIsMember($liveuid . '_zombie_uid', $uid);
//
//        if($isvisit){
//            $rs['code'] = 1003;
//            $rs['info'] = '用户已访问';
//            $rs['msg']  = '用户已访问';
//            return $rs;
//
//        }
//
//        $times = DI()->redis->get($liveuid . '_zombie');
//
//        if($times && $times > 10){
//            $rs['code'] = 1002;
//            $rs['info'] = '次数已满';
//            $rs['msg']  = '次数已满';
//            return $rs;
//        }elseif($times){
//            $times = $times + 1;
//
//        }else{
//            $times = 0;
//        }
//
//        DI()->redis->set($liveuid . '_zombie', $times);
//        DI()->redis->sAdd($liveuid . '_zombie_uid', $uid);
//
//        /* 用户列表 */
//
//        $uidlist = DI()->redis->zRevRange('user_' . $stream, 0, -1);
//
//        $uid = implode(",", $uidlist);
//
//        $where = '0';
//        if($uid){
//            $where .= ',' . $uid;
//        }
//
//        $where                 = str_replace(",,", ',', $where);
//        $where                 = trim($where, ",");
//        $rs['info'][0]['list'] = $domain->getZombie($stream, $where);
//
//        $nums = DI()->redis->zCard('user_' . $stream);
//        if(!$nums){
//            $nums = 0;
//        }
//
//        $rs['info'][0]['nums'] = (string)$nums;
//
//        return $rs;
//    }

    /**
     * 用户列表
     * @desc 用于直播间获取用户列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].userlist 用户列表
     * @return string info[0].nums 房间人数
     * @return string info[0].votestotal 主播映票
     * @return string info[0].guard_type 守护类型
     * @return string msg 提示信息
     */
    public function getUserLists(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $liveuid = $this->liveuid;
        $stream  = checkNull($this->stream);
        $p       = $this->p;

        /* 用户列表 */
        $info = $this->getUserList($liveuid, $stream, $p);

        $rs['info'][0] = $info;

        return $rs;
    }

    protected function getUserList($liveuid, $stream, $p = 1){
        /* 用户列表 */
        $n     = 1;
        $pnum  = 20;
        $start = ($p - 1) * $pnum;

        $domain_guard = new Domain_Guard();

        /* $key="getUserLists_".$stream.'_'.$p;
		$list=getcaches($key);
		if(!$list){  */
        $list = [];

        $uidlist = DI()->redis->zRevRange(Common_Cache::LIVE_NOW_NUMS . $stream, $start, $pnum, true);

        foreach($uidlist as $k => $v){
            $userinfo                 = getUserInfo($k);
            $info                     = explode(".", $v);
            $userinfo['contribution'] = (string)$info[0];

            /* 守护 */
            $guard_info             = $domain_guard->getUserGuard($k, $liveuid);
            $userinfo['guard_type'] = $guard_info['type'];

            $list[] = $userinfo;
        }

        /*     if($list){
                setcaches($key,$list,30);
            }
		} */

        if(!$list){
            $list = [];
        }

        $nums = DI()->redis->zCard(Common_Cache::LIVE_NOW_NUMS . $stream);
        if(!$nums){
            $nums = 0;
        }

        $rs['userlist'] = $list;
        $rs['nums']     = (string)$nums;

        /* 主播信息 */
        $domain           = new Domain_Live();
        $rs['votestotal'] = $domain->getVotes($liveuid);


        return $rs;
    }


    /**
     * 弹窗
     * @desc 用于直播间弹窗信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].consumption 消费总数
     * @return string info[0].votestotal 票总数
     * @return string info[0].follows 关注数
     * @return string info[0].fans 粉丝数
     * @return string info[0].isattention 是否关注，0未关注，1已关注
     * @return string info[0].action 操作显示，0表示自己，30表示普通用户，40表示管理员，501表示主播设置管理员，502表示主播取消管理员，60表示超管管理主播，70表示对方是超管
     * @return object info[0].vip 用户VIP信息
     * @return string info[0].vip.type VIP类型，0表示无VIP，1表示普通VIP，2表示至尊VIP
     * @return object info[0].liang 用户靓号信息
     * @return string info[0].liang.name 号码，0表示无靓号
     * @return array info[0].label 印象标签
     * @return array info[0].label 印象标签
     * @return array info[0].level_anchor 魅力等级
     * @return array info[0].level 财富等级
     * @return array info[0].is_black 是否拉黑 0否 1是
     * @return string msg 提示信息
     */
    public function getPop(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = $this->uid;
        $liveuid = $this->liveuid;
        $touid   = $this->touid;

        $info = getUserInfo($touid);
        if(!$info){
            $rs['code'] = 1002;
            $rs['msg']  = '用户信息不存在';
            return $rs;
        }
        $info['follows']  = getFollows($touid);
        $info['fans']     = getFans($touid);
        $info['is_black'] = isBlack($uid, $touid);

        $info['isattention'] = (string)isAttention($uid, $touid);
        $info['votestotal']  = (string)round($info['votestotal'] / 100, 2);
        if($uid == $touid){
            $info['action'] = '0';
        }else{
            $uid_admin   = isAdmin($uid, $liveuid);
            $touid_admin = isAdmin($touid, $liveuid);

            if($uid_admin == 40 && $touid_admin == 30){
                $info['action'] = '40';
            }elseif($uid_admin == 50 && $touid_admin == 30){
                $info['action'] = '501';
            }elseif($uid_admin == 50 && $touid_admin == 40){
                $info['action'] = '502';
            }elseif($uid_admin == 60 && $touid_admin < 50){
                $info['action'] = '40';
            }elseif($uid_admin == 60 && $touid_admin == 50){
                $info['action'] = '60';
            }elseif($touid_admin == 60){
                $info['action'] = '70';
            }else{
                $info['action'] = '30';
            }

        }
        /* 标签 */
        $labels        = [];
        $info['label'] = $labels;
        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * 礼物列表
     * @desc 用于获取礼物列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].coin 余额
     * @return array info[0].giftlist 礼物列表
     * @return string info[0].giftlist[].id 礼物ID
     * @return string info[0].giftlist[].type 礼物类型
     * @return string info[0].giftlist[].mark 礼物标识
     * @return string info[0].giftlist[].giftname 礼物名称
     * @return string info[0].giftlist[].needcoin 礼物价格和礼物经验
     * @return string info[0].giftlist[].gifticon 礼物图片
     * @return string info[0].giftlist[].max_money 最大中奖金额
     * @return string info[0].giftlist[].anchor_rate 主播分润比
     * @return string msg 提示信息
     */
    public function getGiftList(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = $this->uid;
        $token = checkNull($this->token);
        $type  = $this->type;

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain   = new Domain_Live();
        $giftlist = $domain->getGiftLists($type);
//        $proplist = $domain->getPropgiftList();

        $domain2 = new Domain_User();
        $coin    = $domain2->getBalance($uid);

        $rs['info'][0]['giftlist'] = $giftlist;
        $rs['info'][0]['proplist'] = [];
        $rs['info'][0]['coin']     = $coin['coin'];
        return $rs;
    }

    /**
     * 赠送礼物
     * @desc 用于赠送礼物
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].gifttoken 礼物token
     * @return string info[0].level 用户等级
     * @return string info[0].coin 用户余额
     * @return string msg 提示信息
     */
    public function sendGift(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];
//        return $rs;
        $uid        = $this->uid;
        $token      = $this->token;
        $liveuid    = $this->liveuid;
        $stream     = checkNull($this->stream);
        $giftid     = $this->giftid;
        $giftcount  = $this->giftcount;
        $ispack     = $this->ispack;
        $is_sticker = $this->is_sticker;
//        $uid_arr  = [37207, 37200, 37205, 37206, 37198, 37199];
//        $gift_arr = [20, 25, 28, 62];
//        $uid      = $uid_arr[array_rand($uid_arr, 1)];
//        $stream   = '3e2_' . rand(1000, 999999);
////        $token      = $this->token;
//        $liveuid = 37188;
////        $stream     =
//        $giftid     = $gift_arr[array_rand($gift_arr, 1)];
//        $giftcount  = mt_rand(1000, 9999);
//        $ispack     = 0;
//        $is_sticker = 0;


        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain = new Domain_Live();
        list($code, $result) = $domain->sendGift($uid, $liveuid, $stream, $giftid, $giftcount, $ispack);
        if($code > 0){
            $rs['code'] = $code;
            $rs['msg']  = $result;
            return $rs;
        }
        /** 新增打赏人集合 */
        DI()->redis->sadd("live:sendGift:" . $stream . "_users", $uid);
        $rs['info'][0]['gifttoken'] = $result['gifttoken'];
        $rs['info'][0]['level']     = $result['level'];
        $rs['info'][0]['coin']      = $result['coin'];

        //增加分数
        DI()->redis->ZINCRBY(Common_Cache::LIVE_NOW_NUMS . $stream, -$result['totalcoin'], $uid);
        unset($result['gifttoken']);
        setcaches(Common_Cache::SEND_GIFT_TOKEN . $rs['info'][0]['gifttoken'], $result, 2 * 60);
//        DI()->redis->set(Common_Cache::SEND_GIFT_TOKEN . $rs['info'][0]['gifttoken'], json_encode($result) . PHP_EOL);
        return $rs;
    }

    /**
     * 发送弹幕
     * @desc 用于发送弹幕
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].barragetoken 礼物token
     * @return string info[0].level 用户等级
     * @return string info[0].coin 用户余额
     * @return string msg 提示信息
     */
    public function sendBarrage(){
        $rs        = ['code' => 0, 'msg' => '', 'info' => []];
        $uid       = $this->uid;
        $token     = $this->token;
        $liveuid   = $this->liveuid;
        $stream    = checkNull($this->stream);
        $giftid    = 0;
        $giftcount = 1;

        $content = checkNull($this->content);
        if($content == ''){
            $rs['code'] = 1003;
            $rs['msg']  = '弹幕内容不能为空';
            return $rs;
        }

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain = new Domain_Live();
        $result = $domain->sendBarrage($uid, $liveuid, $stream, $giftid, $giftcount, $content);

        if($result == 1001){
            $rs['code'] = 1001;
            $rs['msg']  = '余额不足';
            return $rs;
        }elseif($result == 1002){
            $rs['code'] = 1002;
            $rs['msg']  = '礼物信息不存在';
            return $rs;
        }

        $rs['info'][0]['barragetoken'] = $result['barragetoken'];
        $rs['info'][0]['level']        = $result['level'];
        $rs['info'][0]['coin']         = $result['coin'];

        unset($result['barragetoken']);

        DI()->redis->set($rs['info'][0]['barragetoken'], json_encode($result));

        return $rs;
    }

    /**
     * 设置/取消管理员
     * @desc 用于设置/取消管理员
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].isadmin 是否是管理员，0表示不是管理员，1表示是管理员
     * @return string msg 提示信息
     */
    public function setAdmin(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = $this->uid;
        $token   = $this->token;
        $liveuid = $this->liveuid;
        $touid   = $this->touid;

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        if($uid != $liveuid){
            $rs['code'] = 1001;
            $rs['msg']  = '你不是该房间主播，无权操作';
            return $rs;
        }

        $domain = new Domain_Live();
        $info   = $domain->setAdmin($liveuid, $touid);

        if($info == 1004){
            $rs['code'] = 1004;
            $rs['msg']  = '最多设置5个管理员';
            return $rs;
        }elseif($info == 1003){
            $rs['code'] = 1003;
            $rs['msg']  = '操作失败，请重试';
            return $rs;
        }

        $rs['info'][0]['isadmin'] = $info;
        return $rs;
    }

    /**
     * 管理员列表
     * @desc 用于获取管理员列表
     * @return int code 操作码，0表示成功
     * @return array info 管理员列表
     * @return array info[0]['list'] 管理员列表
     * @return array info[0]['list'][].userinfo 用户信息
     * @return string info[0]['nums'] 当前人数
     * @return string info[0]['total'] 总数
     * @return string msg 提示信息
     */
    public function getAdminList(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $domain = new Domain_Live();
        $info   = $domain->getAdminList($this->liveuid);

        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * 举报类型
     * @desc 用于获取举报类型
     * @return int code 操作码，0表示成功
     * @return array info 列表
     * @return string info[].name 类型名称
     * @return string msg 提示信息
     */
    public function getReportClass(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $domain = new Domain_Live();
        $info   = $domain->getReportClass();


        $rs['info'] = $info;
        return $rs;
    }


    /**
     * 用户举报
     * @desc 用于用户举报
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].msg 举报成功
     * @return string msg 提示信息
     */
    public function setReport(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid        = $this->uid;
        $token      = checkNull($this->token);
        $touid      = $this->touid;
        $content    = checkNull($this->content);
        $type       = checkNull($this->type);
        $image      = checkNull($this->image);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        if(!$content){
            $rs['code'] = 1001;
            $rs['msg']  = '举报内容不能为空';
            return $rs;
        }

        if(mb_strlen($touid) > 200){
            $rs['code'] = 1002;
            $rs['msg']  = '账号长度不能超过200个字符';
            return $rs;
        }

        $domain = new Domain_Live();
        $info   = $domain->setReport($uid, $touid, $content, $type, $image);
        if($info === false){
            $rs['code'] = 1002;
            $rs['msg']  = '举报失败，请重试';
            return $rs;
        }
        Domain_Msg::addMsg('举报成功', Common_JPush::TJJB, $this->uid);
        $rs['info'][0]['msg'] = "举报成功";
        return $rs;
    }

    /**
     * 主播映票
     * @desc 用于获取主播映票
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].votestotal 用户总数
     * @return string msg 提示信息
     */
    public function getVotes(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $domain = new Domain_Live();
        $info   = $domain->getVotes($this->liveuid);

        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * 禁言
     * @desc 用于 禁言操作
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */

    public function setShutUp(){
        $rs = ['code' => 0, 'msg' => '禁言成功', 'info' => []];

        $uid     = $this->uid;
        $token   = $this->token;
        $liveuid = $this->liveuid;
        $touid   = $this->touid;
        $type    = $this->type;
        $stream  = $this->stream;

        //file_put_contents('./setShutUp.txt',date('Y-m-d H:i:s').' 提交参数信息 request:'.json_encode($_REQUEST)."\r\n",FILE_APPEND);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = 'token已过期，请重新登陆';
            return $rs;
        }

        $uidtype = isAdmin($uid, $liveuid);

        if($uidtype == 30){
            $rs["code"] = 1001;
            $rs["msg"]  = '无权操作';
            return $rs;
        }

        $touidtype = isAdmin($touid, $liveuid);

        if($touidtype == 60){
            $rs["code"] = 1001;
            $rs["msg"]  = '对方是超管，不能禁言';
            return $rs;
        }

        if($uidtype == 40){
            if($touidtype == 50){
                $rs["code"] = 1002;
                $rs["msg"]  = '对方是主播，不能禁言';
                return $rs;
            }
            if($touidtype == 40){
                $rs["code"] = 1002;
                $rs["msg"]  = '对方是管理员，不能禁言';
                return $rs;
            }

            /* 守护 */
            $domain_guard = new Domain_Guard();
            $guard_info   = $domain_guard->getUserGuard($touid, $liveuid);

            if($uid != $liveuid && $guard_info && $guard_info['type'] == 2){
                $rs["code"] = 1004;
                $rs["msg"]  = '对方是尊贵守护，不能禁言';
                return $rs;
            }

        }
        $showid = 0;
        if($type == 1 || $stream){
            $showid = 1;
        }
        $domain = new Domain_Live();
        $result = $domain->setShutUp($uid, $liveuid, $touid, $showid);

        if($result == 1002){
            $rs["code"] = 1003;
            $rs["msg"]  = '对方已被禁言';
            return $rs;

        }elseif(!$result){
            $rs["code"] = 1005;
            $rs["msg"]  = '操作失败，请重试';
            return $rs;
        }

        DI()->redis->hSet($liveuid . 'shutup', $touid, 1);

        return $rs;
    }

    /**
     * 踢人
     * @desc 用于直播间踢人
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].msg 踢出成功
     * @return string msg 提示信息
     */
    public function kicking(){
        $rs = ['code' => 0, 'msg' => '踢人成功', 'info' => []];

        $uid     = $this->uid;
        $token   = $this->token;
        $liveuid = $this->liveuid;
        $touid   = $this->touid;
        $stream  = $this->stream;

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $admin_uid = isAdmin($uid, $liveuid);
        if($admin_uid == 30){
            $rs['code'] = 1001;
            $rs['msg']  = '无权操作';
            return $rs;
        }
        $admin_touid = isAdmin($touid, $liveuid);

        if($admin_touid == 60){
            $rs["code"] = 1002;
            $rs["msg"]  = '对方是超管，不能被踢出';
            return $rs;
        }

        if($admin_uid != 60){
            if($admin_touid == 50){
                $rs['code'] = 1001;
                $rs['msg']  = '对方是主播，不能被踢出';
                return $rs;
            }

            if($admin_touid == 40){
                $rs['code'] = 1002;
                $rs['msg']  = '对方是管理员，不能被踢出';
                return $rs;
            }
            /* 守护 */
            $domain_guard = new Domain_Guard();
            $guard_info   = $domain_guard->getUserGuard($touid, $liveuid);

            if($uid != $liveuid && $guard_info && $guard_info['type'] == 2){
                $rs["code"] = 1004;
                $rs["msg"]  = '对方是尊贵守护，不能被踢出';
                return $rs;
            }
        }

        $domain = new Domain_Live();

        $result = $domain->kicking($uid, $liveuid, $touid, $stream);
        if($result == 1002){
            $rs["code"] = 1005;
            $rs["msg"]  = '对方已被踢出';
            return $rs;
        }elseif(!$result){
            $rs["code"] = 1006;
            $rs["msg"]  = '操作失败，请重试';
            return $rs;
        }

        $rs['info'][0]['msg'] = '踢出成功';
        return $rs;
    }

    /**
     * 超管关播
     * @desc 用于超管关播
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].msg 提示信息
     * @return string msg 提示信息
     */

    public function superStopRoom(){

        $rs = ['code' => 0, 'msg' => '关闭成功', 'info' => []];

        $uid     = checkNull($this->uid);
        $token   = checkNull($this->token);
        $liveuid = checkNull($this->liveuid);
        $type    = checkNull($this->type);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }


        $domain = new Domain_Live();

        $result = $domain->superStopRoom($uid, $liveuid, $type);
        if($result == 1001){
            $rs['code']           = 1001;
            $rs['msg']            = '你不是超管，无权操作';
            $rs['info'][0]['msg'] = '你不是超管，无权操作';
            return $rs;
        }elseif($result == 1002){
            $rs['code']           = 1002;
            $rs['msg']            = '该主播已被禁播';
            $rs['info'][0]['msg'] = '该主播已被禁播';
            return $rs;
        }
        $rs['info'][0]['msg'] = '关闭成功';

        return $rs;
    }

    /**
     * 用户余额
     * @desc 用于获取用户余额
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].coin 余额(金币)
     * @return string info[0].coin 余额（钻石）
     * @return string msg 提示信息
     */
    public function getCoin(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = $this->uid;
        $token = checkNull($this->token);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }


        $domain2 = new Domain_User();
        $coin    = $domain2->getBalance($uid);

        $rs['info'][0]['coin']  = $coin['coin'];
        $rs['info'][0]['votes'] = (string)round($coin['votes'] / 100, 2);
        return $rs;
    }

    /**
     * 检测房间状态
     * @desc 用于检测房间状态
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].status 状态 0关1开
     * @return string msg 提示信息
     */
    public function checkLiveing(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid    = checkNull($this->uid);
        $stream = checkNull($this->stream);

        //file_put_contents(API_ROOT.'/Runtime/checkLiveing_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 uid:'.json_encode($uid)."\r\n",FILE_APPEND);
        //file_put_contents(API_ROOT.'/Runtime/checkLiveing_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 stream:'.json_encode($stream)."\r\n",FILE_APPEND);

        $domain2 = new Domain_Live();
        $info    = $domain2->checkLiveing($uid, $stream);

        //file_put_contents(API_ROOT.'/Runtime/checkLiveing_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 info:'.json_encode($info)."\r\n",FILE_APPEND);

        $rs['info'][0]['status'] = $info;
        return $rs;
    }

    /**
     * 获取直播信息
     * @desc 用于个人中心进入直播间获取直播信息
     * @return int code 操作码，0表示成功
     * @return array info  直播信息
     * @return string msg 提示信息
     */
    public function getLiveInfo(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $liveuid = checkNull($this->liveuid);

        if($liveuid < 1){
            $rs['code'] = 1001;
            $rs['msg']  = '参数错误';
            return $rs;
        }


        $domain2 = new Domain_Live();
        $info    = $domain2->getLiveInfo($liveuid);
        if(!$info){
            $rs['code'] = 1002;
            $rs['msg']  = '直播已结束';
            return $rs;
        }

        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * 获取直播封面（wanglin）
     * @desc 用于获取直播封面
     * @return int code 操作码，0表示成功
     * @return array info
     * @return array info.cover  头像
     * @return string msg 提示信息
     */
    public function getCover(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = checkNull($this->uid);
        $token = checkNull($this->token);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain2             = new Domain_User();
        $info                = $domain2->getCover($uid);
        $rs['info']['cover'] = get_upload_path($info);
        return $rs;
    }

    /**
     * 获取直播家族列表
     * @desc 获取直播家族列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function getFamilyLiveList(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = checkNull($this->uid);
        $token   = checkNull($this->token);
        $liveuid = checkNull($this->liveuid);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $do_main    = new Domain_Live();
        $info       = $do_main->getFamilyLiveList($liveuid);
        $rs['info'] = $info;
        return $rs;
    }

    /**
     * 直播断流回调
     * @desc 用户腾讯云直播断流回调
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function breakPull(){
        $rs     = ['code' => 0, 'msg' => '', 'info' => []];
        $params = json_decode(file_get_contents('php://input'), true);
        $stream = $params['stream_id'];
        $uid    = explode('_', $stream);
        $uid    = $uid[0];
        $domain = new Domain_Live();
        $domain->stopRoom($uid, $stream, 1);
        $rs['info'][0]['msg'] = '关播成功';
        return $rs;
    }

    /**
     * 主播打赏榜（wanglin）
     * @desc 获取主播打赏榜
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
     **/
    public function incomeNo(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = $this->token;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $liveuid    = $this->liveuid;
        $type       = $this->type;
        $model      = new Domain_Live();
        $rs['info'] = $model->incomeNo($uid, $type, $liveuid);
        return $rs;
    }

    /**
     * 设置直播标题（wanglin）
     * @desc 用于设置直播标题
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     **/
    public function updateLiveTitle(){
        $rs         = ['code' => 0, 'msg' => '设置成功', 'info' => []];
        $uid        = $this->uid;
        $token      = $this->token;
        $title      = $this->title;
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_Live();
        if(!$domain->updateLiveTitle($uid, $title)){
            $rs['code'] = 1;
            $rs['msg']  = '设置失败';
        }
        return $rs;
    }

    public function getLiveUserList(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $stream     = checkNull($this->stream);
        $domain     = new Domain_Live();
        $rs['info'] = $domain->getLiveUserList($stream);
        return $rs;
    }

    /**
     * 直播间主播当日鸭子情况
     * @desc 获取直播间主播当日鸭子情况
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     * @return array info
     * @return string info['duck'] 鸭子数量
     * @return string info['blood'] 血量
     * @return string info['total_blood'] 总血量
     **/
    public function duckInfo(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $liveuid = checkNull($this->liveuid);
        $model   = new Model_Live();
        $result  = $model->duckInfo($liveuid);
        if(1001 == $result){
            $rs['code'] = $result;
            return $rs;
        }
        $rs['info'] = $result;
        return $rs;
    }

    /**
     * 国庆榜单主播鸭子信息
     * @desc 获取榜单鸭子情况
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     * @return array info
     * @return string info['duck'] 鸭子数量
     * @return string info['blood'] 血量
     * @return string info['total_blood'] 总血量
     **/
    public function duckDetail(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $liveuid    = checkNull($this->liveuid);
        $model      = new Model_Live();
        $rs['info'] = $model->duckDetail($liveuid);
        return $rs;
    }


    /**
     * 国庆鸭子攻击榜
     * @desc 获取鸭子攻击榜
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     * @return array info
     * @return string info[0]['uid'] 用户id
     * @return string info[0]['user_nicename'] 昵称
     * @return string info[0]['coin'] 丫币流水
     * @return string info[0]['no'] 排名
     **/
    public function attackList(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $domain     = new Domain_Live();
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $type = checkNull($this->type);
        if(1 == $type){
            $rs['info'] = [];
        }else{
//            $rs['info'] = $domain->attackListV2($type);
            $rs['info'] = getcaches('live:duck:attackList');
        }
        return $rs;
    }

    /**
     * 国庆鸭子阵亡榜
     * @desc 获取鸭子阵亡榜
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     * @return array info
     * @return string info[0]['uid'] 用户id
     * @return string info[0]['user_nicename'] 昵称
     * @return string info[0]['duck'] 鸭子数量
     * @return string info[0]['no'] 排名
     **/
    public function deadList(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $domain     = new Domain_Live();
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $type = checkNull($this->type);
        if(1 == $type){
            $rs['info'] = [];
        }else{
//            $rs['info'] = $domain->deadListV2($type);
            $rs['info'] = getcaches('live:duck:deadList');
        }
        return $rs;
    }

    /**
     * 国庆鸭子绝杀榜
     * @desc 获取鸭子绝杀榜
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     * @return array info
     * @return string info[0]['uid'] 用户id
     * @return string info[0]['user_nicename'] 昵称
     * @return string info[0]['duck'] 鸭子数量
     * @return string info[0]['no'] 排名
     **/
    public function goalList(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $domain     = new Domain_Live();
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $type       = checkNull($this->type);
        $rs['info'] = $domain->goalListV2($type);
        return $rs;
    }

    /**
     * 获取直播间信息
     * @desc 获取直播间信息
     * @return int code 操作码 0表示成功
     * @return string msg 提示信息
     * @return array info
     **/
    public function getUserLiveData(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $domain     = new Domain_Live();
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $liveUid    = $this->liveuid;
        $rs['info'] = $domain->getLiveInfo($liveUid) ?: (object)[];
        return $rs;
    }
}
