<?php
/* Redis链接 */
function connectionRedis(){
    $REDIS_HOST = DI()->config->get('app.REDIS_HOST');
    $REDIS_AUTH = DI()->config->get('app.REDIS_AUTH');
    $REDIS_PORT = DI()->config->get('app.REDIS_PORT');
    $redis      = new Redis();
    $redis->pconnect($REDIS_HOST, $REDIS_PORT);
    $redis->auth($REDIS_AUTH);
    return $redis;
}

/* 设置缓存 可自定义时间*/
function setcaches($key, $info, $time = 0){
    $res = DI()->redis->set($key, json_encode($info));
    if($res){
        if($time > 0){
            DI()->redis->expire($key, $time);
        }
    }else{
        file_put_contents(API_ROOT . '/Runtime/redis.log', $info);
    }
    return 1;
}

/* 获取缓存 不判断后台设置 */
function getcaches($key){
    $isexist = DI()->redis->Get($key);
    return json_decode($isexist, true);
}

/* 删除缓存 */
function delcache($key){
    $isexist = DI()->redis->del($key);
    return 1;
}

/* 密码检查 */
function passcheck($user_pass){
    /* 必须包含字母、数字 */
    $preg = '/^(?=.*[A-Za-z])(?=.*[0-9])[a-zA-Z0-9~!@&%#_]{6,20}$/';
    $isok = preg_match($preg, $user_pass);
    if($isok){
        return 1;
    }
    return 0;
}

/* 检验手机号 */
function checkMobile($mobile){
    $ismobile = preg_match("/^1[3|4|5|6|7|8|9]\d{9}$/", $mobile);
    if($ismobile){
        return 1;
    }else{
        return 0;
    }
}

/* 随机数 */
function random($length = 6, $numeric = 0){
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if($numeric){
        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    }else{
        $hash  = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max   = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++){
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}

function Post($curlPost, $url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}

function xml_to_array($xml){
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if(preg_match_all($reg, $xml, $matches)){
        $count = count($matches[0]);
        for($i = 0; $i < $count; $i++){
            $subxml = $matches[2][$i];
            $key    = $matches[1][$i];
            if(preg_match($reg, $subxml)){
                $arr[$key] = xml_to_array($subxml);
            }else{
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}

/* 发送验证码 -- 容联云 */
function sendCode($mobile, $code){

    $rs = ['code' => 0, 'msg' => '', 'info' => []];

    $config = getConfigPri();

    if(!$config['sendcode_switch']){
        $rs['code'] = 667;
        $rs['msg']  = $code;
        return $rs;
    }

    require_once API_ROOT . '/sdk/ronglianyun/CCPRestSDK.php';

    //主帐号
    $accountSid = $config['ccp_sid'];
    //主帐号Token
    $accountToken = $config['ccp_token'];
    //应用Id
    $appId = $config['ccp_appid'];
    //请求地址，格式如下，不需要写https://
    $serverIP = 'app.cloopen.com';
    //请求端口
    $serverPort = '8883';
    //REST版本号
    $softVersion = '2013-12-26';

//        $tempId=$config['ccp_tempid'];
    $tempId = 616356;

    file_put_contents(API_ROOT . '/Runtime/sendCode_ccp_' . date('Y-m-d')
        . '.txt',
        date('Y-m-d H:i:s') . ' 提交参数信息 post_data: accountSid:' . $accountSid
        . ";accountToken:{$accountToken};appId:{$appId};tempId:{$tempId}\r\n",
        FILE_APPEND);

    $rest = new REST($serverIP, $serverPort, $softVersion);
    $rest->setAccount($accountSid, $accountToken);
    $rest->setAppId($appId);

    $datas   = [];
    $datas[] = $code;

    $result = $rest->sendTemplateSMS($mobile, $datas, $tempId);
    file_put_contents(API_ROOT . '/Runtime/sendCode_ccp_' . date('Y-m-d')
        . '.txt',
        date('Y-m-d H:i:s') . ' 提交参数信息 result:' . json_encode($result) . "\r\n",
        FILE_APPEND);

    if($result == null){
        $rs['code'] = 1002;
        $rs['msg']  = "获取失败";
        return $rs;
    }
    if($result->statusCode != 0){
        //echo "error code :" . $result->statusCode . "<br>";
        //echo "error msg :" . $result->statusMsg . "<br>";
        //TODO 添加错误处理逻辑
        $rs['code'] = 1002;
        //$rs['msg']=$gets['SubmitResult']['msg'];
        $rs['msg'] = "获取失败";
        return $rs;
    }
    $content = $code;
    setSendcode(['type' => '1', 'account' => $mobile, 'content' => $content]);

    return $rs;
}

/* curl get请求 */
function curl_get($url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);  // 从证书中检查SSL加密算法是否存在
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}

/* 检测文件后缀 */
function checkExt($filename){
    $config = ["jpg", "png", "jpeg"];
    $ext    = pathinfo(strip_tags($filename), PATHINFO_EXTENSION);

    return empty($config) ? true : in_array(strtolower($ext), $config);
}

/* 密码加密 */
function setPass($pass){
    $authcode = 'rCt52pF2cnnKNB3Hkp';
    $pass     = "###" . md5(md5($authcode . $pass));
    return $pass;
}

/* 去除NULL 判断空处理 主要针对字符串类型*/
function checkNull($checkstr){
    $checkstr = trim($checkstr);
    $checkstr = urldecode($checkstr);
    if(get_magic_quotes_gpc() == 0){
        $checkstr = addslashes($checkstr);
    }
    //$checkstr=htmlspecialchars($checkstr);
    //$checkstr=filterEmoji($checkstr);
    if(strstr($checkstr, 'null') || (!$checkstr && $checkstr != 0)){
        $str = '';
    }else{
        $str = $checkstr;
    }
    return $str;
}

/* 公共配置 */
function getConfigPub(){
    $key    = Common_Cache::PUB_CONFIG;
    $config = getcaches($key);
    if(!$config){
        $config = DI()->notorm->option
            ->select('option_value')
            ->where("option_name='site_info'")
            ->fetchOne();
        $config = json_decode($config['option_value'], true);
        if($config){
            setcaches($key, $config);
        }

    }
    if(isset($config['live_time_coin'])){
        if(is_array($config['live_time_coin'])){

        }elseif($config['live_time_coin']){
            $config['live_time_coin'] = preg_split('/,|，/',
                $config['live_time_coin']);
        }else{
            $config['live_time_coin'] = [];
        }
    }else{
        $config['live_time_coin'] = [];
    }

    if(isset($config['login_type'])){
        if(is_array($config['login_type'])){

        }elseif($config['login_type']){
            $config['login_type'] = preg_split('/,|，/', $config['login_type']);
        }else{
            $config['login_type'] = [];
        }
    }else{
        $config['login_type'] = [];
    }

    if(isset($config['share_type'])){
        if(is_array($config['share_type'])){

        }elseif($config['share_type']){
            $config['share_type'] = preg_split('/,|，/', $config['share_type']);
        }else{
            $config['share_type'] = [];
        }
    }else{
        $config['share_type'] = [];
    }

    if(isset($config['live_type'])){
        if(is_array($config['live_type'])){

        }elseif($config['live_type']){
            $live_type = preg_split('/,|，/', $config['live_type']);
            foreach($live_type as $k => $v){
                $live_type[$k] = preg_split('/;|；/', $v);
            }
            $config['live_type'] = $live_type;
        }else{
            $config['live_type'] = [];
        }
    }else{
        $config['live_type'] = [];
    }

    return $config;
}

/* 私密配置 */
function getConfigPri(){
    $key    = Common_Cache::SYSCONFIG;
    $config = getcaches($key);
    if(!$config){
        $config = DI()->notorm->option
            ->select('option_value')
            ->where("option_name='configpri'")
            ->fetchOne();
        $config = json_decode($config['option_value'], true);
        if($config){
            setcaches($key, $config);
        }

    }

    if(isset($config['game_switch'])){
        if(is_array($config['game_switch'])){

        }elseif($config['game_switch']){
            $config['game_switch'] = preg_split('/,|，/',
                $config['game_switch']);
        }else{
            $config['game_switch'] = [];
        }
    }else{
        $config['game_switch'] = [];
    }


    return $config;
}

/**
 * 返回带协议的域名
 */
function get_host(){
    $config = getConfigPub();
    return $config['site'];
}

/**
 * 转化数据库保存的文件路径，为可以访问的url
 */
function get_upload_path($file){
    if($file == ''){
        return $file;
    }
    if(strpos($file, "http") === 0){
//            echo 1;
        return html_entity_decode($file);
    }elseif(strpos($file, "/") === 0){
//            echo 2;
        $filepath = get_host() . $file;
        return html_entity_decode($filepath);
    }else{
//            echo 3;
        $space_host = DI()->config->get('app.Qiniu.space_host');
        $filepath   = $space_host . "/" . $file;
        return html_entity_decode($filepath);
    }
}

/* 判断是否关注 */
function isAttention($uid, $touid){
    $isexist = DI()->notorm->user_attention
        ->select("*")
        ->where('uid=? and touid=?', $uid, $touid)
        ->fetchOne();
    if($isexist){
        return '1';
    }
    return '0';
}

/* 是否黑名单 */
function isBlack($uid, $touid){
    $isexist = DI()->notorm->user_black
        ->select("*")
        ->where('uid=? and touid=?', $uid, $touid)
        ->fetchOne();
    if($isexist){
        return '1';
    }
    return '0';
}

/* 判断权限 */
function isAdmin($uid, $liveuid){
    if($uid == $liveuid){
        return 50;
    }
    $isuper = isSuper($uid);
    if($isuper){
        return 60;
    }
    $isexist = DI()->notorm->live_manager
        ->select("*")
        ->where('uid=? and liveuid=?', $uid, $liveuid)
        ->fetchOne();
    if($isexist){
        return 40;
    }
    return 30;
}

/* 判断账号是否超管 */
function isSuper($uid){
    $isexist = DI()->notorm->user_super
        ->select("*")
        ->where('uid=?', $uid)
        ->fetchOne();
    if($isexist){
        return 1;
    }
    return 0;
}

/* 判断token */
function checkToken($uid, $token){
    $userinfo = getcaches(Common_Cache::USERTOKEN . $uid);

    if(!$userinfo){
        $userinfo = DI()->notorm->user_token
            ->select('token,expire_time')
            ->where('user_id = ?', $uid)
            ->fetchOne();

        if($userinfo){
            setcaches(Common_Cache::USERTOKEN . $uid, $userinfo, 3600);
        }

    }

    if(!$userinfo || $userinfo['token'] != $token
        || $userinfo['expire_time'] < time()){
        return 700;
    }

    /* 是否禁用、拉黑 */
    $info = DI()->notorm->user
        ->select('user_status,end_bantime')
        ->where('id=? and user_type="2"', $uid)
        ->fetchOne();
    if(!$info || $info['user_status'] == 0 || $info['end_bantime'] > time()){
        return 700;
    }
    return 0;

}

/* 用户基本信息 */
function getUserInfo($uid, $type = 0){
    $info = getcaches(Common_Cache::USERINFO . $uid);
    if(!$info){
        $info = DI()->notorm->user
            ->select('id,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,province,city,birthday,user_status,issuper,location,verify,is_auth,display_id,coin,mobile,sex_modifiable,agent_money,is_agent,is_near')
            ->where('id=? and user_type="2"', $uid)
            ->fetchOne();
        if($info){

        }elseif($type == 1){
            return $info;

        }else{
            $info['id']            = $uid;
            $info['user_nicename'] = '用户不存在';
            $info['avatar']        = '/default.jpg';
            $info['avatar_thumb']  = '/default_thumb.jpg';
            $info['sex']           = '0';
            $info['signature']     = '';
            $info['consumption']   = '0';
            $info['votestotal']    = '0';
            $info['province']      = '';
            $info['city']          = '';
            $info['birthday']      = '';
            $info['issuper']       = '0';
        }
        if($info){
            setcaches(Common_Cache::USERINFO . $uid, $info, 300);
        }

    }
    if($info){
        $info['level']        = getLevelV2($info['consumption']);
        $info['level_anchor'] = getLevelAnchorV2($info['votestotal']);
        $info['avatar']       = get_upload_path($info['avatar']);
        $info['avatar_thumb'] = get_upload_path($info['avatar_thumb']);

        /** @var 财富等级和魅力等级 $thumb */
        $thumb                      = getLevelThumb($info['level']);
        $info['level_thumb']        = get_upload_path($thumb['thumb']);
        $anchor_thumb               = getLevelThumb($info['level_anchor'], 'level_anchor');
        $info['level_anchor_thumb'] = get_upload_path($anchor_thumb['thumb']);

        $info['vip']   = getUserVip($uid);
        $info['liang'] = getUserLiang($uid);
        if($info['birthday']){
            $info['birthday'] = date('Y-m-d', $info['birthday']);
        }else{
            $info['birthday'] = '';
        }
        //金牌主播
        $info['remark_info'] = getRemarkInfo($uid);
        $userHeadBorderInfo  = getHeadBorder($uid);
        if($userHeadBorderInfo){
            $info['head_border'] = [
                'pic' => $userHeadBorderInfo['pic'],
            ];
        }else{
            $info['head_border'] = $userHeadBorderInfo;
        }

        if($info['is_near'] == 2){
            $info['city'] = '好像在火星';
        }
    }
    return $info;
}

/* 会员等级 */
function getLevelList(){
    $key = Common_Cache::LEVEL;
    delcache($key);
    $level = getcaches($key);
    if(!$level){
        $level = DI()->notorm->level
            ->select("*")
            ->order("id asc")
            ->fetchAll();
        if($level){
            setcaches($key, $level, 3600);
        }

    }
    foreach($level as $k => $v){
        $v['thumb']      = get_upload_path($v['thumb']);
        $v['thumb_mark'] = get_upload_path($v['thumb_mark']);
        $v['bg']         = get_upload_path($v['bg']);
        if($v['colour']){
            $v['colour'] = '#' . $v['colour'];
        }else{
            $v['colour'] = '#ffdd00';
        }
        $level[$k] = $v;
    }
    return $level;
}

function getLevelV2($consumption){
    return DI()->notorm->level->where('level_up<=?', (int)$consumption)
            ->order("levelid desc")->fetchOne('levelid') ?? 0;
}

function getLevelAnchorV2($votestotal){
    $votestotal = floor($votestotal / 100);
    return DI()->notorm->level_anchor->where('level_up<=?', (int)$votestotal)
            ->order("levelid desc")->fetchOne('levelid') ?? 0;
}

/* 主播等级 */
function getLevelAnchorList(){
    $key   = Common_Cache::LEVEL_ANCHOR;
    $level = getcaches($key);
    if(!$level){
        $level = DI()->notorm->level_anchor
            ->select("*")
            ->order("level_up asc")
            ->fetchAll();
        if($level){
            setcaches($key, $level, 3600 * 24);
        }
    }
    foreach($level as $k => $v){
        $v['thumb']      = get_upload_path($v['thumb']);
        $v['thumb_mark'] = get_upload_path($v['thumb_mark']);
        $v['bg']         = get_upload_path($v['bg']);
        $level[$k]       = $v;
    }
    return $level;
}

/* 统计 直播 */
function getLives($uid){
    /* 直播中 */
    $count1 = DI()->notorm->live
        ->where('uid=? and islive="1"', $uid)
        ->count();
    /* 回放 */
    $count2 = DI()->notorm->live_record
        ->where('uid=? ', $uid)
        ->count();
    return $count1 + $count2;
}

/* 统计 关注 */
function getFollows($uid){
    $count = DI()->notorm->user_attention
        ->where('uid=? ', $uid)
        ->count();
    return $count;
}

/* 统计 粉丝 */
function getFans($uid){
    $count = DI()->notorm->user_attention
        ->where('touid=? ', $uid)
        ->count();
    return $count;
}

/**
 * @desc 根据两点间的经纬度计算距离
 *
 * @param float $lat1
 * @param float $lng1
 * @param float $lat2
 * @param float $lng2
 *
 * @return string
 */
function getDistance($lat1, $lng1, $lat2, $lng2){
    $earthRadius = 6371000; //近似地球半径 单位 米
    /*
		   Convert these degrees to radians
		   to work with the formula
		 */
    $lat1 = ($lat1 * pi()) / 180;
    $lng1 = ($lng1 * pi()) / 180;

    $lat2 = ($lat2 * pi()) / 180;
    $lng2 = ($lng2 * pi()) / 180;


    $calcLongitude      = $lng2 - $lng1;
    $calcLatitude       = $lat2 - $lat1;
    $stepOne            = pow(sin($calcLatitude / 2), 2) + cos($lat1)
        * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo            = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;
    // 换算成公里（km)
    $distance = $calculatedDistance / 1000;

    // update by 吴晓平 2020年07月06日 10:37:57   根据需求超过50公里则显示50公里以外，其他距离精确到小数点后一位（四舍五入）
    if($distance >= 50){
        $rs = 50;
    }else{
        // 精确到小位点后一位
        $rs = round($distance, 1);
    }

    return $rs;
}

/* 是否认证 */
function isAuth($uid){
    $status = DI()->notorm->user_auth
        ->select("status")
        ->where('uid=?', $uid)
        ->fetchOne();
    if($status && $status['status'] == 2){
        return 1;
    }
    return 0;
}

/* 是否认证 */
function authStatus($uid){
    $status = DI()->notorm->user_auth
        ->select("status")
        ->where('uid=?', $uid)
        ->fetchOne();
    if($status){
        return $status;
    }
    return 0;
}

/* 时间差计算 */
function datetime($time){
    $cha = time() - $time;
    $iz  = floor($cha / 60);
    $hz  = floor($iz / 60);
    $dz  = floor($hz / 24);
    /* 秒 */
    $s = $cha % 60;
    /* 分 */
    $i = floor($iz % 60);
    /* 时 */
    $h = floor($hz / 24);
    /* 天 */

    if($cha < 60){
        return $cha . '秒前';
    }elseif($iz < 60){
        return $iz . '分钟前';
    }elseif($hz < 24){
        return $hz . '小时' . $i . '分钟前';
    }elseif($dz < 30){
        return $dz . '天前';
    }else{
        return date("Y-m-d", $time);
    }
}

/* 时长格式化 */
function getSeconds($cha, $type = 0){
    $iz = floor($cha / 60);
    $hz = floor($iz / 60);
    $dz = floor($hz / 24);
    /* 秒 */
    $s = $cha % 60;
    /* 分 */
    $i = floor($iz % 60);
    /* 时 */
    $h = floor($hz / 24);
    /* 天 */

    if($type == 1){
        if($s < 10){
            $s = '0' . $s;
        }
        if($i < 10){
            $i = '0' . $i;
        }

        if($h < 10){
            $h = '0' . $h;
        }

        if($hz < 10){
            $hz = '0' . $hz;
        }
        return $hz . ':' . $i . ':' . $s;
    }


    if($cha < 60){
        return $cha . '秒';
    }elseif($iz < 60){
        return $iz . '分钟' . $s . '秒';
    }elseif($hz < 24){
        return $hz . '小时' . $i . '分钟' . $s . '秒';
    }elseif($dz < 30){
        return $dz . '天' . $h . '小时' . $i . '分钟' . $s . '秒';
    }
}

/* 数字格式化 */
function NumberFormat($num){
    if($num < 10000){

    }elseif($num < 1000000){
        $num = round($num / 10000, 2) . '万';
    }elseif($num < 100000000){
        $num = round($num / 10000, 1) . '万';
    }elseif($num < 10000000000){
        $num = round($num / 100000000, 2) . '亿';
    }else{
        $num = round($num / 100000000, 1) . '亿';
    }
    return $num;
}

/**
 * @desc 获取推拉流地址
 *
 * @param string $host   协议，如:http、rtmp
 * @param string $stream 流名,如有则包含 .flv、.m3u8
 * @param int    $type   类型，0表示播流，1表示推流
 */
function PrivateKeyA($host, $stream, $type){
    $configpri  = getConfigPri();
    $cdn_switch = $configpri['cdn_switch'];
    //$cdn_switch=3;
    switch($cdn_switch){
    case '1':
        $url = PrivateKey_ali($host, $stream, $type);
        break;
    case '2':
        $url = PrivateKey_tx($host, $stream, $type);
        break;
    case '3':
        $url = PrivateKey_qn($host, $stream, $type);
        break;
    case '4':
        $url = PrivateKey_ws($host, $stream, $type);
        break;
    case '5':
        $url = PrivateKey_wy($host, $stream, $type);
        break;
    case '6':
        $url = PrivateKey_ady($host, $stream, $type);
        break;
    }
    return $url;
}

/**
 * @desc 阿里云直播A类鉴权
 *
 * @param string $host   协议，如:http、rtmp
 * @param string $stream 流名,如有则包含 .flv、.m3u8
 * @param int    $type   类型，0表示播流，1表示推流
 */
function PrivateKey_ali($host, $stream, $type){
    $configpri   = getConfigPri();
    $push        = $configpri['push_url'];
    $pull        = $configpri['pull_url'];
    $key_push    = $configpri['auth_key_push'];
    $length_push = $configpri['auth_length_push'];
    $key_pull    = $configpri['auth_key_pull'];
    $length_pull = $configpri['auth_length_pull'];

    if($type == 1){
        $domain = $host . '://' . $push;
        $time   = time() + $length_push;
    }else{
        $domain = $host . '://' . $pull;
        $time   = time() + $length_pull;
    }

    $filename = "/5showcam/" . $stream;

    if($type == 1){
        if($key_push != ''){
            $sstring  = $filename . "-" . $time . "-0-0-" . $key_push;
            $md5      = md5($sstring);
            $auth_key = "auth_key=" . $time . "-0-0-" . $md5;
        }
        if($auth_key){
            $auth_key = '?' . $auth_key;
        }
        $url = $domain . $filename . $auth_key;
    }else{
        if($key_pull != ''){
            $sstring  = $filename . "-" . $time . "-0-0-" . $key_pull;
            $md5      = md5($sstring);
            $auth_key = "auth_key=" . $time . "-0-0-" . $md5;
        }
        if($auth_key){
            $auth_key = '?' . $auth_key;
        }
        $url = $domain . $filename . $auth_key;
    }

    return $url;
}

/**
 * @desc 腾讯云推拉流地址
 *
 * @param string $host   协议，如:http、rtmp
 * @param string $stream 流名,如有则包含 .flv、.m3u8
 * @param int    $type   类型，0表示播流，1表示推流
 */
function PrivateKey_tx($host, $stream, $type){
    $configpri    = getConfigPri();
    $bizid        = $configpri['tx_bizid'];
    $push_url_key = $configpri['tx_push_key'];
    $push         = $configpri['tx_push'];
    $pull         = $configpri['tx_pull'];
    $stream_a     = explode('.', $stream);
    $streamKey    = $stream_a[0];
//		$ext = $stream_a[1];

    //$live_code = $bizid . "_" .$streamKey;
    $live_code = $streamKey;
    $now_time  = time() + 3 * 60 * 60;
    $txTime    = dechex($now_time);

    $txSecret = md5($push_url_key . $live_code . $txTime);
    $safe_url = "&txSecret=" . $txSecret . "&txTime=" . $txTime;

    if($type == 1){
        //$push_url = "rtmp://" . $bizid . ".livepush2.myqcloud.com/live/" .  $live_code . "?bizid=" . $bizid . "&record=flv" .$safe_url;	可录像
        $url = "rtmp://{$push}/live/" . $live_code . "?bizid=" . $bizid . ""
            . $safe_url;
    }else{
        $url = "http://{$pull}/live/" . $live_code . ".flv";
        //添加防盗链
//            $configpri['tx_pull_key'] = "12345678";
//			if (isset($configpri['tx_pull_key']) && $configpri['tx_pull_key']) {
//                $txTime = dechex(time());
////                $txTime = strtoupper($txTime);
////                var_dump($configpri['tx_pull_key'] . $live_code . $txTime);
//                $txSecret = md5($configpri['tx_pull_key'] . $live_code . $txTime);
//                $url .= "?txSecret=$txSecret&txTime=$txTime";
//            }
    }

    return $url;
}

/**
 * @desc 七牛云直播
 *
 * @param string $host   协议，如:http、rtmp
 * @param string $stream 流名,如有则包含 .flv、.m3u8
 * @param int    $type   类型，0表示播流，1表示推流
 */
function PrivateKey_qn($host, $stream, $type){

    require_once API_ROOT . '/../sdk/qiniucdn/Pili_v2.php';

    $configpri = getConfigPri();
    $ak        = $configpri['qn_ak'];
    $sk        = $configpri['qn_sk'];
    $hubName   = $configpri['qn_hname'];
    $push      = $configpri['qn_push'];
    $pull      = $configpri['qn_pull'];
    $stream_a  = explode('.', $stream);
    $streamKey = $stream_a[0];
    $ext       = $stream_a[1];

    if($type == 1){
        $time = time() + 60 * 60 * 10;
        //RTMP 推流地址
        $url = \Qiniu\Pili\RTMPPublishURL($push, $hubName, $streamKey, $time,
            $ak, $sk);
    }else{
        if($ext == 'flv'){
            $pull = str_replace('pili-live-rtmp', 'pili-live-hdl', $pull);
            //HDL 直播地址
            $url = \Qiniu\Pili\HDLPlayURL($pull, $hubName, $streamKey);
        }elseif($ext == 'm3u8'){
            $pull = str_replace('pili-live-rtmp', 'pili-live-hls', $pull);
            //HLS 直播地址
            $url = \Qiniu\Pili\HLSPlayURL($pull, $hubName, $streamKey);
        }else{
            //RTMP 直播放址
            $url = \Qiniu\Pili\RTMPPlayURL($pull, $hubName, $streamKey);
        }
    }

    return $url;
}

/**
 * @desc 网宿推拉流
 *
 * @param string $host   协议，如:http、rtmp
 * @param string $stream 流名,如有则包含 .flv、.m3u8
 * @param int    $type   类型，0表示播流，1表示推流
 */
function PrivateKey_ws($host, $stream, $type){
    $configpri = getConfigPri();
    if($type == 1){
        $domain = $host . '://' . $configpri['ws_push'];
        //$time=time() +60*60*10;
    }else{
        $domain = $host . '://' . $configpri['ws_pull'];
        //$time=time() - 60*30 + $configpri['auth_length'];
    }

    $filename = "/" . $configpri['ws_apn'] . "/" . $stream;

    $url = $domain . $filename;

    return $url;
}

/**网易cdn获取拉流地址**/
function PrivateKey_wy($host, $stream, $type){
    $configpri = getConfigPri();
    $appkey    = $configpri['wy_appkey'];
    $appSecret = $configpri['wy_appsecret'];
    $nonce     = rand(1000, 9999);
    $curTime   = time();
    $var       = $appSecret . $nonce . $curTime;
    $checkSum  = sha1($appSecret . $nonce . $curTime);

    $header = [
        "Content-Type:application/json;charset=utf-8",
        "AppKey:" . $appkey,
        "Nonce:" . $nonce,
        "CurTime:" . $curTime,
        "CheckSum:" . $checkSum,
    ];
    if($type == 1){
        $url      = 'https://vcloud.163.com/app/channel/create';
        $paramarr = [
            "name" => $stream,
            "type" => 0,
        ];
    }else{
        $url      = 'https://vcloud.163.com/app/address';
        $paramarr = [
            "cid" => $stream,
        ];
    }
    $paramarr = json_encode($paramarr);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $paramarr);
    $data = curl_exec($curl);
    curl_close($curl);
    $rs = json_decode($data, 1);
    return $rs;
}

/**
 * @desc 奥点云推拉流
 *
 * @param string $host   协议，如:http、rtmp
 * @param string $stream 流名,如有则包含 .flv、.m3u8
 * @param int    $type   类型，0表示播流，1表示推流
 */
function PrivateKey_ady($host, $stream, $type){
    $configpri = getConfigPri();
    $stream_a  = explode('.', $stream);
    $streamKey = $stream_a[0];
    $ext       = $stream_a[1];

    if($type == 1){
        $domain = $host . '://' . $configpri['ady_push'];
        //$time=time() +60*60*10;
        $filename = "/" . $configpri['ady_apn'] . '/' . $stream;
        $url      = $domain . $filename;
    }else{
        if($ext == 'm3u8'){
            $domain = $host . '://' . $configpri['ady_hls_pull'];
            //$time=time() - 60*30 + $configpri['auth_length'];
            $filename = "/" . $configpri['ady_apn'] . "/" . $stream;
            $url      = $domain . $filename;
        }else{
            $domain = $host . '://' . $configpri['ady_pull'];
            //$time=time() - 60*30 + $configpri['auth_length'];
            $filename = "/" . $configpri['ady_apn'] . "/" . $stream;
            $url      = $domain . $filename;
        }
    }

    return $url;
}

/* 游戏类型 */
function getGame($action){
    $game_action = [
        '0' => '',
        '1' => '智勇三张',
        '2' => '海盗船长',
        '3' => '转盘',
        '4' => '开心牛仔',
        '5' => '二八贝',
    ];

    return isset($game_action[$action]) ? $game_action[$action] : '';
}

/* 获取用户VIP */
function getUserVip($uid){
    $rs      = [
        'type' => '0',
    ];
    $nowtime = time();
    $key     = 'vip_' . $uid;
    $isexist = getcaches($key);
    if(!$isexist){
        $isexist = DI()->notorm->vip_user
            ->select("*")
            ->where('uid=?', $uid)
            ->fetchOne();
        if($isexist){
            setcaches($key, $isexist, 300);
        }
    }

    if($isexist){
        if($isexist['endtime'] <= $nowtime){
            return $rs;
        }
        $rs['type'] = '1';
    }

    return $rs;
}

/* 获取用户坐骑 */
function getUserCar($uid){
    $key = Common_Cache::USERCAR . $uid;
    $rs  = getcaches($key);
    if(!$rs){
        $rs    = [
            'id'      => '0',
            'swf'     => '',
            'swftime' => '0',
            'words'   => '',
            'swftype' => 1,
        ];
        $model = new Model_CarUser();
        $info  = $model->getUserUseCar($uid);
        if($info){
            $rs       = [
                'id'      => $info['id'],
                'swf'     => $info['swf'],
                'swftime' => $info['swftime'],
                'words'   => $info['words'],
                'swftype' => $info['swftype'],
            ];
            $end_time = $info['endtime'] - time();
            setcaches($key, $rs, $end_time);
        }
    }

    return $rs;
}

/* 获取用户靓号 */
function getUserLiang($uid){
    $rs      = [
        'name' => '0',
    ];
    $nowtime = time();
    $key     = Common_Cache::USERLIANG . $uid;
    $isexist = getcaches($key);
    if(!$isexist){
        $isexist = DI()->notorm->liang
            ->select("*")
            ->where("uid=? and state=1 and (end_time=0 or end_time>{$nowtime})", $uid)
            ->fetchOne();
        if($isexist){
            $time = $isexist['end_time'] - time();
            setcaches($key, $isexist, $time);
        }
    }
    if($isexist){
        $rs['name'] = $isexist['name'];
    }

    return $rs;
}

/* ip限定 */
function ip_limit(){
    $configpri = getConfigPri();
    if($configpri['iplimit_switch'] == 0){
        return 0;
    }
    $date = date("Ymd");
    $ip   = ip2long($_SERVER["REMOTE_ADDR"]);

    $isexist = DI()->notorm->getcode_limit_ip
        ->select('ip,date,times')
        ->where(' ip=? ', $ip)
        ->fetchOne();
    if(!$isexist){
        $data    = [
            "ip"    => $ip,
            "date"  => $date,
            "times" => 1,
        ];
        $isexist = DI()->notorm->getcode_limit_ip->insert($data);
        return 0;
    }elseif($date == $isexist['date']
        && $isexist['times'] >= $configpri['iplimit_times']
    ){
        return 1;
    }else{
        if($date == $isexist['date']){
            $isexist = DI()->notorm->getcode_limit_ip
                ->where(' ip=? ', $ip)
                ->update(['times' => new NotORM_Literal("times + 1 ")]);
            return 0;
        }else{
            $isexist = DI()->notorm->getcode_limit_ip
                ->where(' ip=? ', $ip)
                ->update(['date' => $date, 'times' => 1]);
            return 0;
        }
    }
}

/* 验证码记录 */
function setSendcode($data){
    if($data){
        $data['addtime'] = time();
        DI()->notorm->sendcode->insert($data);
    }
}

/* 检测用户是否存在 */
function checkUser($where){
    if($where == ''){
        return 0;
    }

    $isexist = DI()->notorm->user->where($where)->fetchOne();

    if($isexist){
        return 1;
    }

    return 0;
}

/* 直播分类 */
function getLiveClass(){
    $key  = "getLiveClass";
    $list = getcaches($key);
    if(!$list){
        $list = DI()->notorm->live_class
            ->select("*")
            ->order("list_order asc,id desc")
            ->fetchAll();
        if($list){
            setcaches($key, $list);
        }

    }

    foreach($list as $k => $v){
        $v['thumb'] = get_upload_path($v['thumb']);
        $list[$k]   = $v;
    }
    return $list;

}

/* 校验签名 */
function checkSign($data, $sign){
    $key = DI()->config->get('app.sign_key');
    $str = '';
    ksort($data);
    foreach($data as $k => $v){
        $str .= $k . '=' . $v . '&';
    }
    $str     .= $key;
    $newsign = md5($str);

    if($sign == $newsign){
        return 1;
    }
    return 0;
}

/* 用户退出，注销PUSH */
function userLogout($uid){
    $list = DI()->notorm->user_pushid
        ->where('uid=?', $uid)
        ->delete();
    return 1;
}

/*距离格式化*/
function distanceFormat($distance){
    if($distance < 1000){
        return $distance . '米';
    }else{

        if(floor($distance / 10) < 10){
            return number_format($distance / 10, 1);  //保留一位小数，会四舍五入
        }else{
            return ">10千米";
        }
    }
}

/* 视频是否点赞 */
function ifLike($uid, $videoid){
    $like = DI()->notorm->video_like
        ->select("id")
        ->where("uid='{$uid}' and videoid='{$videoid}'")
        ->fetchOne();
    if($like){
        return 1;
    }else{
        return 0;
    }
}

/* 视频是否踩 */
function ifStep($uid, $videoid){
    $like = DI()->notorm->video_step
        ->select("id")
        ->where("uid='{$uid}' and videoid='{$videoid}'")
        ->fetchOne();
    if($like){
        return 1;
    }else{
        return 0;
    }
}

/* 拉黑视频名单 */
function getVideoBlack($uid){
    $videoids = ['0'];
    $list     = DI()->notorm->video_black
        ->select("videoid")
        ->where("uid='{$uid}'")
        ->fetchAll();
    if($list){
        $videoids = array_column($list, 'videoid');
    }

    $videoids_s = implode(",", $videoids);

    return $videoids_s;
}

/* 生成二维码 */

function scerweima($url = ''){

    $key = md5($url);

    //生成二维码图片
    $filename2 = '/upload/qr/' . $key . '.png';
    $filename  = API_ROOT . '/../public/upload/qr/' . $key . '.png';

    if(!file_exists($filename)){
        require_once API_ROOT . '/../sdk/phpqrcode/phpqrcode.php';

        $value = $url;                    //二维码内容

        $errorCorrectionLevel = 'L';    //容错级别
        $matrixPointSize
                              = 6.2068965517241379310344827586207;            //生成图片大小

        //生成二维码图片
        \QRcode::png($value, $filename, $errorCorrectionLevel, $matrixPointSize,
            2);
    }

    return $filename2;
}

/* 奖池信息 */
function getJackpotInfo(){
    $jackpotinfo = DI()->notorm->jackpot->where("id = 1 ")->fetchOne();
    return $jackpotinfo;
}

/* 奖池配置 */
function getJackpotSet(){
    $key    = 'jackpotset';
    $config = getcaches($key);
    if(!$config){
        $config = DI()->notorm->option
            ->select('option_value')
            ->where("option_name='jackpot'")
            ->fetchOne();
        $config = json_decode($config['option_value'], true);
        if($config){
            setcaches($key, $config);
        }

    }
    return $config;
}

/* 视频数据处理 */
function handleVideo($uid, $v){

    $userinfo = getUserInfo($v['uid']);
    if(!$userinfo){
        $userinfo['user_nicename'] = "已删除";
    }

    $v['userinfo'] = $userinfo;
    $v['datetime'] = datetime($v['addtime']);
    $v['addtime']  = date('Y-m-d H:i:s', $v['addtime']);
    $v['comments'] = NumberFormat($v['comments']);
    $v['likes']    = NumberFormat($v['likes']);
    $v['steps']    = NumberFormat($v['steps']);

    $v['islike']   = '0';
    $v['isstep']   = '0';
    $v['isattent'] = '0';

    if($uid > 0){
        $v['islike'] = (string)ifLike($uid, $v['id']);
        $v['isstep'] = (string)ifStep($uid, $v['id']);
    }

    if($uid > 0 && $uid != $v['uid']){
        $v['isattent'] = (string)isAttention($uid, $v['uid']);
    }

    $v['thumb']   = get_upload_path($v['thumb']);
    $v['thumb_s'] = get_upload_path($v['thumb_s']);
    $v['href']    = get_upload_path($v['href']);
    $v['href_w']  = get_upload_path($v['href_w']);

    $v['ad_url'] = get_upload_path($v['ad_url']);

    if($v['ad_endtime'] < time()){
        $v['ad_url'] = '';
    }

    /* 商品 */
    $goodsinfo = (object)[];
    if($v['goodsid'] > 0){
        $goodsinfo = DI()->notorm->shop_goods
            ->select("type,name,href,thumb,old_price,price,des")
            ->where('id=? and status=1', $v['goodsid'])
            ->fetchOne();
        if($goodsinfo){
            $goodsinfo['thumb'] = get_upload_path($goodsinfo['thumb']);
        }else{
            $v['goodsid'] = '0';
            $goodsinfo    = (object)[];
        }
    }
    $v['goodsinfo'] = $goodsinfo;

    unset($v['ad_endtime']);
    unset($v['orderno']);
    unset($v['isdel']);
    unset($v['show_val']);
    unset($v['status']);
    unset($v['xiajia_reason']);
    unset($v['nopass_time']);
    unset($v['watch_ok']);

    return $v;
}

//账号是否禁用
function isBan($uid){

    $result = DI()->notorm->user->where("end_bantime>? and id=?", time(), $uid)
        ->fetchOne();
    if($result){
        return 0;
    }

    return 1;
}

/* 时长格式化 */
function getBanSeconds($cha, $type = 0){
    $iz = floor($cha / 60);
    $hz = floor($iz / 60);
    $dz = floor($hz / 24);
    /* 秒 */
    $s = $cha % 60;
    /* 分 */
    $i = floor($iz % 60);
    /* 时 */
    $h = floor($hz / 24);
    /* 天 */

    if($type == 1){
        if($s < 10){
            $s = '0' . $s;
        }
        if($i < 10){
            $i = '0' . $i;
        }

        if($h < 10){
            $h = '0' . $h;
        }

        if($hz < 10){
            $hz = '0' . $hz;
        }
        return $hz . ':' . $i . ':' . $s;
    }


    if($cha < 60){
        return $cha . '秒';
    }elseif($iz < 60){
        return $iz . '分钟' . $s . '秒';
    }elseif($hz < 24){
        return $hz . '小时' . $i . '分钟';
    }elseif($dz < 30){
        return $dz . '天' . $h . '小时';
    }
}

/* 过滤：敏感词 */
function sensitiveField($field){
    if($field){
        $configpri = getConfigPri();

        $sensitive_words = $configpri['sensitive_words'];

        $sensitive = explode(",", $sensitive_words);
        $replace   = [];
        $preg      = [];

        foreach($sensitive as $k => $v){
            if($v != ''){
                if(strstr($field, $v) !== false){
                    return 1001;
                }
            }else{
                unset($sensitive[$k]);
            }
        }
    }
    return 1;
}

/* 视频分类 */
function getVideoClass(){
    $key  = "live:getVideoClass";
    $list = getcaches($key);
    if(!$list){
        $list = DI()->notorm->video_class
            ->select("*")
            ->order("list_order asc,id desc")
            ->fetchAll();
        /*   foreach($list as $k=>$v){
                $list[$k]['thumb']=get_upload_path($v['thumb']);
            } */
        setcaches($key, $list);
    }
    return $list;

}

/* 处理直播信息 */
function handleLive($v){

    $configpri = getConfigPri();
    $stream    = explode('_', $v['stream']);
    $show_id   = $stream[1];
    $nums      = DI()->redis->zCard(Common_Cache::LIVE_NOW_NUMS . $v['stream']);
    $js_nums   = DI()->redis->get(Common_Cache::CORPSE_OVER_FLOW . $v['stream']) ?: 0;
    $v['nums'] = (string)($nums + $js_nums);

    $userinfo           = getUserInfo($v['uid']);
    $v['avatar']        = $userinfo['avatar'];
    $v['avatar_thumb']  = $userinfo['avatar_thumb'];
    $v['user_nicename'] = $userinfo['user_nicename'];
    $v['sex']           = $userinfo['sex'];

    $v['remark_info'] = $userinfo['remark_info'];
    /** @var 获取财富等级和魅力等级 $thumb */
    $v['level']              = $userinfo['level'];
    $v['level_thumb']        = $userinfo['level_thumb'];
    $v['level_anchor']       = $userinfo['level_anchor'];
    $v['level_anchor_thumb'] = $userinfo['level_anchor_thumb'];
    if(!$v['thumb']){
        $v['thumb'] = $v['avatar'];
    }
    if($v['isvideo'] == 0 && $configpri['cdn_switch'] != 5){
//            $v['pull']=PrivateKeyA('rtmp',$v['stream'],0);
    }

    if($v['type'] == 1){
        $v['type_val'] = '';
    }
    $v['game']    = getGame($v['game_action']);
    $v['thumb']   = get_upload_path($v['thumb']);
    $v['goodnum'] = $userinfo['liang']['name'];
    return $v;
}

/* 用户手机号 */
function getUserLogin($uid){
    //    $key = 'userLogin_' . $uid;
    //    $info = getcaches($key);
    $info = '';
    if(!$info){
        $info       = DI()->notorm->user
            ->select('user_login')
            ->where('id=?', $uid)
            ->fetchOne();
        $user_login = $info['user_login'];
        //        if ($info) {
        //            setcaches($key, $info['user_login']);
        //        }
    }
    return $user_login;
}

/**
 * 获取等级图标
 *
 * @param $id
 * @param $table
 *
 * @return mixed
 */
function getLevelThumb($id, $table = 'level'){
    return DI()->notorm->$table->select('thumb,thumb_mark')
        ->where(['levelid' => $id])->fetchOne();
}

/**
 * 姓名身份证认证
 *
 * @param $name
 * @param $id_card
 */
function checkIdCard(String $name, String $id_card){
    $host    = "https://eid.shumaidata.com";
    $path    = "/eid/check";
    $method  = "POST";
    $appcode = "31558c9062934bebab7d010bf52b611c";
    $headers = [];
    array_push($headers, "Authorization:APPCODE " . $appcode);
    $querys = "idcard={$id_card}&name={$name}";
    $url    = $host . $path . "?" . $querys;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //设定返回信息中是否包含响应信息头，启用时会将头文件的信息作为数据流输出，true 表示输出信息头, false表示不输出信息头
    //如果需要将字符串转成json，请将 CURLOPT_HEADER 设置成 false
    curl_setopt($curl, CURLOPT_HEADER, false);
    if(1 == strpos("$" . $host, "https://")){
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    $res = curl_exec($curl);
//    DI()->logger->info("快速实名认证", $res);
    if(!$res){
        return [99, '请求失败', ''];
    }
    $res = json_decode($res, true);
    return [$res['code'], $res['message'], $res['result']];
}

/**
 * 用户是否在直播
 *
 * @param $touid
 *
 * @return int
 */
function isLive($touid){
    $live = DI()->notorm->live
        ->select('title,stream,pull,isvideo,anyway,is_black')
        ->where('uid=? and islive=1', $touid)
        ->fetchOne();
    return $live;
}

function hm(){
    return microtime(true) * 1000;
    list($msec, $sec) = explode(' ', microtime());
    $msectime = (float)sprintf('%.0f',
        (floatval($msec) + floatval($sec)) * 1000);
    return $msectime;
}

function getUserLimitIp($uid){
    return DI()->notorm->limit_ip
        ->where(['uid' => $uid, 'status' => 1, 'ip' => $_SERVER['REMOTE_ADDR']])
        ->fetchOne() ? true : false;
}

function getRemarkInfo($uid){
    $userRemarkModel = new Model_UserRemark();
    $userRemarkInfo  = $userRemarkModel->getUserRemark($uid);
    if($userRemarkInfo){
        $userRemarkInfo['icon'] = get_upload_path($userRemarkInfo['icon']);
    }else{
        $userRemarkInfo = null;
    }
    return $userRemarkInfo;
}

function getUserInfoDuck($uid)
{
    $key = "live:duck:user_info:$uid";
    $userInfo = getcaches($key);
    if (!$userInfo) {
        $userInfo = DI()->notorm->user->where(['id' => $uid])->select('id,user_nicename,avatar')->fetchOne();
        setcaches($key, $userInfo, 86400);
    }
    return $userInfo;
}

function getHeadBorder($uid){
    $key     = Common_Cache::HEADER . $uid;
    $keyData = getcaches($key);
    if(!$keyData){
        $userHeadBorderModel = new Model_HeadBorderUser();
        $userHeadBorderInfo  = $userHeadBorderModel->getUserHeadBorder($uid);
        if(isset($userHeadBorderInfo[0]) && $userHeadBorderInfo[0]){
            $userHeadBorderInfo[0]['pic'] = get_upload_path($userHeadBorderInfo[0]['pic']);
            $userHeadBorderInfo           = $userHeadBorderInfo[0];
            $time                         = $userHeadBorderInfo['expire'] > 0 ? $userHeadBorderInfo['expire'] - time() : 259200;
            setcaches($key, json_encode($userHeadBorderInfo), $time);
        }else{
            $userHeadBorderInfo = null;
        }
    }else{
        $userHeadBorderInfo = json_decode($keyData, true);
    }

    return $userHeadBorderInfo;
}