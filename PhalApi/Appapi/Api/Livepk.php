<?php

/**
 * 主播PK
 */
class Api_Livepk extends PhalApi_Api{

    public function getRules(){
        return [
            'getLiveList' => [
                'uid' => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'p'   => ['name' => 'p', 'type' => 'int', 'default' => 1, 'desc' => '页码',],
            ],
            'search'      => [
                'uid'  => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'key'  => ['name' => 'key', 'type' => 'string', 'require' => true, 'desc' => '关键词',],
                'page' => ['name' => 'page', 'type' => 'int', 'default' => 1, 'desc' => '页码',],
            ],
            'checkLive'   => [
                'stream'     => ['name' => 'stream', 'type' => 'string', 'require' => true, 'desc' => '连麦主播流名',],
                'uid_stream' => ['name' => 'uid_stream', 'type' => 'string', 'require' => true, 'desc' => '当前主播流名',],
            ],

            'changeLive' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'pkuid' => ['name' => 'pkuid', 'type' => 'int', 'require' => true, 'desc' => '连麦主播ID',],
                'type'  => ['name' => 'type', 'type' => 'int', 'require' => true, 'desc' => '标识',],
                'sign'  => ['name' => 'sign', 'type' => 'string', 'require' => true, 'desc' => '签名',],
            ],

            'setPK' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'pkuid' => ['name' => 'pkuid', 'type' => 'int', 'desc' => '连麦主播ID'],
            ],

            'endPK'         => [
                'uid' => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID',],
            ],
            'pkList'        => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'desc' => 'token'],
                'type'  => ['name' => 'type', 'type' => 'int', 'desc' => '类型  1关注 2热门'],
                'page'  => ['name' => 'page', 'type' => 'int', 'desc' => '页码'],
            ],
            'randomPk'      => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'desc' => 'token'],
            ],
            'pkConductList' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'desc' => 'token'],
                'page'  => ['name' => 'page', 'type' => 'int', 'desc' => '页码'],
            ],
            'pkStatus'      => [
                'uid' => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
            ],
            'winningNums'   => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'touid' => ['name' => 'touid', 'type' => 'int', 'desc' => '查询的用户ID'],
                'token' => ['name' => 'token', 'type' => 'int', 'desc' => 'token'],
            ],
        ];
    }

    /**
     * 直播用户
     *
     * @desc 用于 获取直播中的用户
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[].uid 主播ID
     * @return string info[].pkuid PK对象ID，0表示未连麦
     * @return string msg 提示信息
     */
    public function getLiveList(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid = checkNull($this->uid);
        $p   = checkNull($this->p);
        if(!$p){
            $p = 1;
        }

        $where = "uid!={$uid}";

        $domain = new Domain_Livepk();
        $list   = $domain->getLiveList($uid, $where, $p);

        foreach($list as $k => $v){
            $userinfo          = getUserInfo($v['uid']);
            $v['level']        = $userinfo['level'];
            $v['level_anchor'] = $userinfo['level_anchor'];
            $v['sex']          = $userinfo['sex'];
            $list[$k]          = $v;
        }

        $rs['info'] = $list;
        return $rs;
    }

    /**
     * 搜索直播用户
     *
     * @desc 用于搜索直播中用户
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[].uid 主播ID
     * @return string info[].pkuid PK对象ID，0表示未连麦
     * @return string msg 提示信息
     */
    public function search(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid  = checkNull($this->uid);
        $key  = checkNull($this->key);
        $page = checkNull($this->page);

        $domain     = new Domain_Livepk();
        $rs['info'] = $domain->search($key, $page, $uid);
        return $rs;
    }

    /**
     * 检测是否直播中
     *
     * @desc 用于检测要连麦主播是否直播中
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function checkLive(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $stream     = checkNull($this->stream);
        $uid_stream = checkNull($this->uid_stream);

        $domain = new Domain_Livepk();
        //获取对方直播信息
        $info = $domain->checkLive($stream);

        if(!$info){
            $rs['code'] = 1001;
            $rs['msg']  = '对方已关播';
            return $rs;
        }

        if($info['pkuid'] > 0){
            $rs['code'] = 1002;
            $rs['msg']  = '对方忙碌中';
            return $rs;
        }

        $configpri = getConfigPri();

        $live_sdk = $configpri['live_sdk'];  //live_sdk  0表示金山SDK 1表示腾讯SDK
        if($live_sdk == 1){
            $myInfo   = $domain->checkLive($uid_stream);
            $play_url = $myInfo['pull'];
        }else{
            if($configpri['cdn_switch'] == 5){
                $liveinfo = DI()->notorm->live
                    ->select('pull')
                    ->where('stream=?', $uid_stream)
                    ->fetchOne();

                $play_url = $liveinfo['pull'];
            }else{
                $play_url = PrivateKeyA('rtmp', $uid_stream, 0);
            }
        }

        $info = [
            "pull" => $play_url,
        ];

        $rs['info'][0] = $info;

        return $rs;
    }


    /**
     * 修改直播信息
     *
     * @desc 用于连麦成功后更新数据库信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function changeLive(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = $this->uid;
        $pkuid = checkNull($this->pkuid);

        $type = checkNull($this->type);
        $sign = checkNull($this->sign);

        $checkdata = [
            'uid'   => $uid,
            'pkuid' => $pkuid,
            'type'  => $type,
        ];

        $issign = checkSign($checkdata, $sign);

        if(!$issign){
            $rs['code'] = 1001;
            $rs['msg']  = '签名错误';
            return $rs;
        }

        $domain = new Domain_Livepk();
        $info   = $domain->changeLive($uid, $pkuid, $type);

        if($type == 0){

            $key1 = 'LivePK';
            $key2 = 'LivePK_gift';
            $key3 = 'LivePK_timer';
            $key4 = 'LiveConnect';
            $key5 = 'LiveConnect_pull';

            DI()->redis->hDel($key1, $uid);
            DI()->redis->hDel($key1, $pkuid);

            DI()->redis->hDel($key2, $uid);
            DI()->redis->hDel($key2, $pkuid);

            DI()->redis->hDel($key3, $uid);
            DI()->redis->hDel($key3, $pkuid);

            DI()->redis->hDel($key4, $uid);
            DI()->redis->hDel($key4, $pkuid);

            DI()->redis->hDel($key5, $uid);
            DI()->redis->hDel($key5, $pkuid);

        }else{
            $key4 = 'LiveConnect';
            DI()->redis->hSet($key4, $uid, $pkuid);
            DI()->redis->hSet($key4, $pkuid, $uid);

        }

        return $rs;
    }

    /**
     * PK开始
     *
     * @desc 用于PK开始处理业务
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function setPK(){
        $rs                  = ['code' => 0, 'msg' => '', 'info' => []];
        $uid                 = $this->uid;
        $pkuid               = checkNull($this->pkuid);
        $livePkDomain        = new Domain_Livepk();
        $rs['info']['times'] = $livePkDomain->setPk($uid, $pkuid);
        return $rs;
    }

    /**
     * PK结束
     *
     * @desc 用于PK结束处理业务
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function endPK(){
        $uid    = $this->uid;
        $domain = new Domain_Livepk();
        return $domain->endPK($uid);
    }

    /**
     * 获取pk状态
     *
     * @desc 用于获取pk状态
     * @return int code 操作码，0表示成功
     * @return array info.status 1pk中 2惩罚中 3结束
     * @return array info.times 倒计时（秒）
     * @return string msg 提示信息
     */
    public function pkStatus(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $domain     = new Domain_Livepk();
        $rs['info'] = $domain->pkStatus($uid);
        return $rs;
    }

    /**
     * PK列表
     *
     * @desc 用于获取pk列表
     * @return int info.userName 昵称
     * @return int info.sex 性别
     * @return int info.headPic 头像
     * @return int info.isRemark 是否加V
     * @return int info.pkCountsNums pk场数
     * @return int info.winning 胜率
     * @return int info.charmLevel ，魅力等级图标
     * @return int info.isPk pk状态 0未pk  大于0pk中
     * @return array info
     * @return string msg 提示信息
     */
    public function pkList(){
        $rs     = ['code' => 0, 'msg' => '', 'info' => []];
        $uid    = $this->uid;
        $token  = $this->token;
        $page   = $this->page;
        $type   = $this->type;
        $domain = new Domain_Livepk();
        if($type == 1){
            $rs['info'] = $domain->followPkList($uid, $page);
        }else{
            $rs['info'] = $domain->hotPkList($page, $uid);
        }
        return $rs;
    }

    /**
     * 随机PK
     *
     * @desc 用于获取随机pk
     * @return int info.userName 昵称
     * @return int info.sex 性别
     * @return int info.headPic 头像
     * @return int info.isRemark 是否加V
     * @return int info.pkCountsNums pk场数
     * @return int info.winning 胜率
     * @return int info.charmLevel ，魅力等级图标
     * @return array info
     * @return string msg 提示信息
     */
    public function randomPk(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = $this->token;
        $domain     = new Domain_Livepk();
        $rs['info'] = $domain->randomPkList($uid);
        return $rs;
    }

    /**
     * pk中的直播列表
     *
     * @desc 用于获取pk中的直播列表
     * @return array info
     * @return string msg 提示信息
     */
    public function pkConductList(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = $this->token;
        $page       = $this->page;
        $domain     = new Domain_Livepk();
        $rs['info']['list'] = $domain->pkConductList($page);
        return $rs;
    }

    /**
     * 获取胜率
     *
     * @desc 用于获取胜率
     * @return int info.userName 昵称
     * @return int info.sex 性别
     * @return int info.headPic 头像
     * @return int info.isRemark 是否加V
     * @return int info.pkCountsNums pk场数
     * @return int info.winning 胜率
     * @return int info.charmLevel ，魅力等级图标
     * @return int info.level_thumb ，财富等级图标
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
    public function winningNums(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = $this->uid;
        $token      = $this->token;
        $toUid      = $this->touid;
        $domain     = new Domain_Livepk();
        $rs['info'] = $domain->winningNums($toUid);
        return $rs;
    }
}
