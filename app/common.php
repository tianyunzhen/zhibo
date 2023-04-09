<?php

use think\Db;
use cmf\lib\Storage;

// 应用公共文件
error_reporting(E_ALL);
//ini_set('display_errors','On');
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once dirname(__FILE__) . '/redis.php';

/* 去除NULL 判断空处理 主要针对字符串类型*/
function checkNull($checkstr)
{
    $checkstr = urldecode($checkstr);
    $checkstr = htmlspecialchars($checkstr);
    $checkstr = trim($checkstr);

    if (strstr($checkstr, 'null') || (!$checkstr && $checkstr != 0)) {
        $str = '';
    } else {
        $str = $checkstr;
    }
    return $str;
}

/* 去除emoji表情 */
function filterEmoji($str)
{
    $str = preg_replace_callback(
        '/./u',
        function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },
        $str);
    return $str;
}

/* 获取公共配置 */
function getConfigPub()
{
    $key    = 'getConfigPub';
    $config = getcaches($key);
    if (!$config) {
        $config = Db::name("option")
            ->field('option_value')
            ->where("option_name='site_info'")
            ->find();
        $config = json_decode($config['option_value'], true);

        if ($config) {
            setcaches($key, $config);
        }

    }

    if (isset($config['live_time_coin'])) {
        if (is_array($config['live_time_coin'])) {

        } elseif ($config['live_time_coin']) {
            $config['live_time_coin'] = preg_split('/,|，/',
                $config['live_time_coin']);
        } else {
            $config['live_time_coin'] = [];
        }
    } else {
        $config['live_time_coin'] = [];
    }

    if (isset($config['login_type'])) {
        if (is_array($config['login_type'])) {

        } elseif ($config['login_type']) {
            $config['login_type'] = preg_split('/,|，/', $config['login_type']);
        } else {
            $config['login_type'] = [];
        }
    } else {
        $config['login_type'] = [];
    }


    if (isset($config['share_type'])) {
        if (is_array($config['share_type'])) {

        } elseif ($config['share_type']) {
            $config['share_type'] = preg_split('/,|，/', $config['share_type']);
        } else {
            $config['share_type'] = [];
        }
    } else {
        $config['share_type'] = [];
    }

    if (isset($config['live_type'])) {
        if (is_array($config['live_type'])) {

        } elseif ($config['live_type']) {
            $live_type = preg_split('/,|，/', $config['live_type']);
            foreach ($live_type as $k => $v) {
                $live_type[$k] = preg_split('/;|；/', $v);
            }
            $config['live_type'] = $live_type;
        } else {
            $config['live_type'] = [];
        }
    } else {
        $config['live_type'] = [];
    }

    return $config;
}

/* 获取私密配置 */
function getConfigPri()
{
    $key = 'getConfigPri';
    delcache($key);
    $config = getcaches($key);
    if (!$config) {
        $config = Db::name("option")
            ->field('option_value')
            ->where("option_name='configpri'")
            ->find();
        $config = json_decode($config['option_value'], true);
        if ($config) {
            setcaches($key, $config);
        }

    }

    if (isset($config['game_switch'])) {
        if (is_array($config['game_switch'])) {

        } elseif ($config['game_switch']) {
            $config['game_switch'] = preg_split('/,|，/',
                $config['game_switch']);
        } else {
            $config['game_switch'] = [];
        }
    } else {
        $config['game_switch'] = [];
    }

    return $config;
}

/**
 * 转化数据库保存的文件路径，为可以访问的url
 */
function get_upload_path($file)
{
    if ($file == '') {
        return $file;
    }
    if (strpos($file, "http") === 0) {
        return $file;
    } elseif (strpos($file, "/") === 0) {
        $configpub = getConfigPub();
        $filepath  = $configpub['site'] . $file;
        return $filepath;
    } else {
        $style   = '';
        $storage = Storage::instance();
//            var_dump($storage->getImageUrl($file, $style));die;
        return $storage->getImageUrl($file, $style);
    }
}

/* 获取等级 */
function getLevelList()
{
    $key   = 'level';
    $level = getcaches($key);
    if (!$level) {
        $level = Db::name("level")->order("level_up asc")->select();
        if ($level) {
            setcaches($key, $level);
        }
    }

    foreach ($level as $k => $v) {
        $v['thumb']      = get_upload_path($v['thumb']);
        $v['thumb_mark'] = get_upload_path($v['thumb_mark']);
        $v['bg']         = get_upload_path($v['bg']);
        if ($v['colour']) {
            $v['colour'] = '#' . $v['colour'];
        } else {
            $v['colour'] = '#ffdd00';
        }
        $level[$k] = $v;
    }

    return $level;
}

function getLevel($experience)
{
    $level_a = 1;
    $levelid = 1;

    $level = getLevelList();

    foreach ($level as $k => $v) {
        if ($v['level_up'] >= $experience) {
            $levelid = $v['levelid'];
            break;
        } else {
            $level_a = $v['levelid'];
        }
    }
    $levelid = $levelid < $level_a ? $level_a : $levelid;

    return (string)$levelid;
}

/* 主播等级 */
function getLevelAnchorList()
{
    $key   = 'levelanchor';
    $level = getcaches($key);
    if (!$level) {
        $level = Db::name("level_anchor")->order("level_up asc")->select();
        if ($level) {
            setcaches($key, $level);
        }
    }

    foreach ($level as $k => $v) {
        $v['thumb']      = get_upload_path($v['thumb']);
        $v['thumb_mark'] = get_upload_path($v['thumb_mark']);
        $v['bg']         = get_upload_path($v['bg']);
        $level[$k]       = $v;
    }

    return $level;
}

function getLevelAnchor($experience)
{
    $levelid = 1;
    $level_a = 1;
    $level   = getLevelAnchorList();

    foreach ($level as $k => $v) {
        if ($v['level_up'] >= $experience) {
            $levelid = $v['levelid'];
            break;
        } else {
            $level_a = $v['levelid'];
        }
    }
    $levelid = $levelid < $level_a ? $level_a : $levelid;

    return $levelid;
}

/* 判断是否关注 */
function isAttention($uid, $touid)
{
    $where['uid']   = $uid;
    $where['touid'] = $touid;
    $id             = Db::name("user_attention")->where($where)->find();
    if ($id) {
        return 1;
    } else {
        return 0;
    }
}

/*判断是否拉黑*/
function isBlack($uid, $touid)
{
    $where['uid']   = $uid;
    $where['touid'] = $touid;
    $isexist        = Db::name("user_black")->where($where)->find();
    if ($isexist) {
        return 1;
    } else {
        return 0;
    }
}

/* 关注人数 */
function getFollownums($uid)
{
    $where['uid'] = $uid;
    return Db::name("user_attention")->where($where)->count();
}

/* 粉丝人数 */
function getFansnums($uid)
{
    $where['touid'] = $uid;
    return Db::name("user_attention")->where($where)->count();
}

/* 用户基本信息 */
function getUserInfo($uid)
{
    $where['id'] = $uid;
    $info        = Db::name("user")
        ->field("id,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,province,city,birthday,issuper,mobile,live_weight,create_time")
        ->where($where)->find();
    if (!$info) {
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
        $info['mobile']        = '';
    }

    if ($info) {
        $info['avatar']       = get_upload_path($info['avatar']);
        $info['avatar_thumb'] = get_upload_path($info['avatar_thumb']);
        $info['level']        = getLevel($info['consumption']);
        $info['level_anchor'] = getLevelAnchor($info['votestotal']);

        $info['vip']    = getUserVip($uid);
        $info['liang']  = getUserLiang($uid);
        $info['mobile'] = $info['mobile'];

        if ($info['birthday']) {
            $info['birthday'] = date('Y-m-d', $info['birthday']);
        } else {
            $info['birthday'] = '';
        }

    }

    return $info;
}

/*获取收到礼物数量(tsd) 以及送出的礼物数量（tsc） */
function getgif($uid)
{

    $count = Db::query('select sum(case when touid=' . $uid
        . ' then 1 else 0 end) as tsd,sum(case when uid=' . $uid
        . ' then 1 else 0 end) as tsc from cmf_user_coinrecord');
    return $count;
}

/* 用户信息 含有私密信息 */
function getUserPrivateInfo($uid)
{
    $where['id'] = $uid;
    $info        = Db::name("user")
        ->field('id,user_login,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,province,city,coin,votes,birthday,issuper')
        ->where($where)->find();
    if ($info) {
        $info['lighttime']    = "0";
        $info['light']        = 0;
        $info['level']        = getLevel($info['consumption']);
        $info['level_anchor'] = getLevelAnchor($info['votestotal']);
        $info['avatar']       = get_upload_path($info['avatar']);
        $info['avatar_thumb'] = get_upload_path($info['avatar_thumb']);

        $info['vip']   = getUserVip($uid);
        $info['liang'] = getUserLiang($uid);

        if ($info['birthday']) {
            $info['birthday'] = date('Y-m-d', $info['birthday']);
        } else {
            $info['birthday'] = '';
        }
    }
    return $info;
}

/* 用户信息 含有私密信息 */
function getUserToken($uid)
{
    $where['user_id'] = $uid;
    $info             = Db::name("user_token")->field('token')->where($where)
        ->find();
    if (!$info) {
        return '';
    }
    return $info['token'];
}

/* 房间管理员 */
function getIsAdmin($uid, $showid)
{
    if ($uid == $showid) {
        return 50;
    }
    $isuper = isSuper($uid);
    if ($isuper) {
        return 60;
    }
    $where['uid']     = $uid;
    $where['liveuid'] = $showid;
    $id               = Db::name("live_manager")->where($where)->find();

    if ($id) {
        return 40;
    }
    return 30;
}

/*判断token是否过期*/
function checkToken($uid, $token)
{
    if (!$uid || !$token) {
        session('uid', null);
        session('token', null);
        session('user', null);
        cookie('uid', null);
        cookie('token', null);
        return 700;
    }

    $key      = "token_" . $uid;
    $userinfo = getcaches($key);
    if (!$userinfo) {
        $where['user_id'] = $uid;
        $userinfo         = Db::name("user_token")->field('token,expire_time')
            ->where($where)->find();
        if ($userinfo) {
            setcaches($key, $userinfo);
        }
    }

    if (!$userinfo || $userinfo['token'] != $token
        || $userinfo['expire_time'] < time()
    ) {
        session('uid', null);
        session('token', null);
        session('user', null);
        cookie('uid', null);
        cookie('token', null);
        return 700;
    } else {
        return 0;
    }
}

/*前台个人中心判断是否登录*/
function LogIn()
{
    $uid = session("uid");
    if ($uid <= 0) {
        header("Location: /");
        exit;
    }
}

/* 判断账号是否超管 */
function isSuper($uid)
{
    $where['uid'] = $uid;
    $isexist      = Db::name("user_super")->where($where)->find();
    if ($isexist) {
        return 1;
    }
    return 0;
}

/* 判断账号是被禁用 */
function isBanBF($uid)
{
    $where['id'] = $uid;
    $status      = Db::name("user")->field("user_status")->where($where)
        ->find();
    if (!$status || $status['user_status'] == 0) {
        return 0;
    }
    return 1;
}

/* 过滤关键词 */
function filterField($field)
{
    $configpri = getConfigPri();

    $sensitive_field = $configpri['sensitive_field'];

    $sensitive = explode(",", $sensitive_field);
    $replace   = [];
    $preg      = [];
    foreach ($sensitive as $k => $v) {
        if ($v) {
            $re  = '';
            $num = mb_strlen($v);
            for ($i = 0; $i < $num; $i++) {
                $re .= '*';
            }
            $replace[$k] = $re;
            $preg[$k]    = '/' . $v . '/';
        } else {
            unset($sensitive[$k]);
        }
    }

    return preg_replace($preg, $replace, $field);
}

/* 检验手机号 */
function checkMobile($mobile)
{
    $ismobile = preg_match("/^1[3|4|5|6|7|8|9]\d{9}$/", $mobile);
    if ($ismobile) {
        return 1;
    } else {
        return 0;
    }
}


/*直播间判断是否开启僵尸粉*/
function isZombie($uid)
{
    $where['id'] = $uid;
    $userinfo    = Db::name("user")->field("iszombie")->where($where)->find();
    if (!$userinfo) {
        return 0;
    }
    return $userinfo['iszombie'];
}

/* 时间差计算 */
function datetime($time)
{
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

    if ($cha < 60) {
        return $cha . '秒前';
    } elseif ($iz < 60) {
        return $iz . '分钟前';
    } elseif ($hz < 24) {
        return $hz . '小时' . $i . '分钟前';
    } elseif ($dz < 30) {
        return $dz . '天前';
    } else {
        return date("Y-m-d", $time);
    }
}

/* 时长格式化 */
function getSeconds($cha, $type = 0)
{
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

    if ($type == 1) {
        if ($s < 10) {
            $s = '0' . $s;
        }
        if ($i < 10) {
            $i = '0' . $i;
        }

        if ($h < 10) {
            $h = '0' . $h;
        }

        if ($hz < 10) {
            $hz = '0' . $hz;
        }
        return $hz . ':' . $i . ':' . $s;
    }


    if ($cha < 60) {
        return $cha . '秒';
    } elseif ($iz < 60) {
        return $iz . '分钟' . $s . '秒';
    } elseif ($hz < 24) {
        return $hz . '小时' . $i . '分钟' . $s . '秒';
    } elseif ($dz < 30) {
        return $dz . '天' . $h . '小时' . $i . '分钟' . $s . '秒';
    }
}

/*判断该用户是否已经认证*/
function auth($uid)
{
    $where['uid'] = $uid;
    $user_auth    = Db::name("user_auth")->field('uid,status')->where($where)
        ->find();
    if ($user_auth) {
        return $user_auth["status"];
    }

    return 3;

}

/* 获取指定长度的随机字符串 */
function random($length = 6, $numeric = 0)
{
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if ($numeric) {
        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash  = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max   = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}


/* 发送验证码 */
function sendCode_huyi($mobile, $code)
{
    $rs     = [];
    $config = getConfigPri();

    if (!$config['sendcode_switch']) {
        $rs['code'] = 667;
        $rs['msg']  = '123456';
        return $rs;
    }
    /* 互亿无线 */
    $target  = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
    $content = "您的验证码是：" . $code . "。请不要把验证码泄露给其他人。";

    $post_data = "account=" . $config['ihuyi_account'] . "&password="
        . $config['ihuyi_ps'] . "&mobile=" . $mobile . "&content="
        . rawurlencode($content);
    //密码可以使用明文密码或使用32位MD5加密
    $gets = xml_to_array(Post($post_data, $target));
    file_put_contents(CMF_ROOT . 'data/sendCode_' . date('Y-m-d') . '.txt',
        date('Y-m-d H:i:s') . ' 提交参数信息 gets:' . json_encode($gets) . "\r\n",
        FILE_APPEND);

    if ($gets['SubmitResult']['code'] == 2) {
        setSendcode([
            'type'    => '1',
            'account' => $mobile,
            'content' => $content,
        ]);
        $rs['code'] = 0;
    } else {
        $rs['code'] = 1002;
        $rs['msg']  = $gets['SubmitResult']['msg'];
    }
    return $rs;
}

function Post($curlPost, $url)
{
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

function xml_to_array($xml)
{
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if (preg_match_all($reg, $xml, $matches)) {
        $count = count($matches[0]);
        for ($i = 0; $i < $count; $i++) {
            $subxml = $matches[2][$i];
            $key    = $matches[1][$i];
            if (preg_match($reg, $subxml)) {
                $arr[$key] = xml_to_array($subxml);
            } else {
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}

/* 发送验证码 */

/* 发送验证码 -- 容联云 */
function sendCode($mobile, $code)
{

    $rs = ['code' => 0, 'msg' => '', 'info' => []];

    $config = getConfigPri();

//        if(!$config['sendcode_switch']){
//            $rs['code']=667;
//			$rs['msg']='1234';
//            return $rs;
//        }

    require_once CMF_ROOT . 'sdk/ronglianyun/CCPRestSDK.php';

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

    $tempId = $config['ccp_tempid'];

    file_put_contents(CMF_ROOT . 'data/runtime/sendCode_ccp_' . date('Y-m-d')
        . '.txt',
        date('Y-m-d H:i:s') . ' 提交参数信息 post_data: accountSid:' . $accountSid
        . ";accountToken:{$accountToken};appId:{$appId};tempId:{$tempId}\r\n",
        FILE_APPEND);

    $rest = new \REST($serverIP, $serverPort, $softVersion);
    $rest->setAccount($accountSid, $accountToken);
    $rest->setAppId($appId);

    $datas   = [];
    $datas[] = $code;

    $result = $rest->sendTemplateSMS($mobile, $datas, $tempId);
    file_put_contents(CMF_ROOT . 'data/runtime/sendCode_ccp_' . date('Y-m-d')
        . '.txt',
        date('Y-m-d H:i:s') . ' 提交参数信息 result:' . json_encode($result) . "\r\n",
        FILE_APPEND);

    if ($result == null) {
        $rs['code'] = 1002;
        $rs['msg']  = "获取失败";
        return $rs;
    }
    if ($result->statusCode != 0) {
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

/**导出Excel 表格
 *
 * @param $expTitle     名称
 * @param $expCellName  参数
 * @param $expTableData 内容
 *
 * @throws \PHPExcel_Exception
 * @throws \PHPExcel_Reader_Exception
 */
function exportExcel($expTitle, $expCellName, $expTableData, $cellName)
{
    //$xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
    $xlsTitle = $expTitle;//文件名称
    $fileName = $xlsTitle . '_' . date('YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
    $cellNum  = count($expCellName);
    $dataNum  = count($expTableData);

    $path = CMF_ROOT . 'sdk/PHPExcel/';
    require_once($path . "PHPExcel.php");

    $objPHPExcel = new \PHPExcel();
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    for ($i = 0; $i < $cellNum; $i++) {
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '1',
            $expCellName[$i][1]);
    }
    for ($i = 0; $i < $dataNum; $i++) {
        for ($j = 0; $j < $cellNum; $j++) {
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i
                    + 2), filterEmoji($expTableData[$i][$expCellName[$j][0]]));
        }
    }
    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'
        . $xlsTitle . '.xls"');
    header("Content-Disposition:attachment;filename={$fileName}.xls");//attachment新窗口打印inline本窗口打印
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,
        'Excel5');//Excel5为xls格式，excel2007为xlsx格式
    $objWriter->save('php://output');
    exit;
}

/* 密码检查 */
function passcheck($user_pass)
{
    /* 必须包含字母、数字 */
    $preg = '/^(?=.*[A-Za-z])(?=.*[0-9])[a-zA-Z0-9~!@&%#_]{6,20}$/';
    $isok = preg_match($preg, $user_pass);
    if ($isok) {
        return 1;
    }
    return 0;
}

/**
 * @desc 获取推拉流地址
 *
 * @param string $host   协议，如:http、rtmp
 * @param string $stream 流名,如有则包含 .flv、.m3u8
 * @param int    $type   类型，0表示播流，1表示推流
 */
function PrivateKeyA($host, $stream, $type)
{
    $configpri  = getConfigPri();
    $cdn_switch = $configpri['cdn_switch'];
    //$cdn_switch=3;
    switch ($cdn_switch) {
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
function PrivateKey_ali($host, $stream, $type)
{
    $configpri   = getConfigPri();
    $push        = $configpri['push_url'];
    $pull        = $configpri['pull_url'];
    $key_push    = $configpri['auth_key_push'];
    $length_push = $configpri['auth_length_push'];
    $key_pull    = $configpri['auth_key_pull'];
    $length_pull = $configpri['auth_length_pull'];

    $stream_a  = explode('.', $stream);
    $streamKey = isset($stream_a[0]) ? $stream_a[0] : '';
    $ext       = isset($stream_a[1]) ? $stream_a[1] : '';
    if ($type == 1) {
        $domain = $host . '://' . $push;
        $time   = time() + $length_push;
    } else {
        $domain = $host . '://' . $pull;
        $time   = time() + $length_pull;
    }

    $filename = "/5showcam/" . $stream;

    if ($type == 1) {
        if ($key_push != '') {
            $sstring  = $filename . "-" . $time . "-0-0-" . $key_push;
            $md5      = md5($sstring);
            $auth_key = "auth_key=" . $time . "-0-0-" . $md5;
        }
        if ($auth_key) {
            $auth_key = '?' . $auth_key;
        }
        //$domain.$filename.'?vhost='.$configpri['pull_url'].$auth_key;
        $url = [
            'cdn'    => $domain . '/5showcam',
            'stream' => $stream . $auth_key,
        ];
    } else {
        if ($key_pull != '') {
            $sstring  = $filename . "-" . $time . "-0-0-" . $key_pull;
            $md5      = md5($sstring);
            $auth_key = "auth_key=" . $time . "-0-0-" . $md5;
        }
        if ($auth_key) {
            $auth_key = '?' . $auth_key;
        }
        $url = $domain . $filename . $auth_key;

        $configpub = getConfigPub();

        if (strstr($configpub['site'], 'https')) {
            $url = str_replace('http:', 'https:', $url);
        }

        if ($type == 3) {
            $url_a = explode('/' . $stream, $url);
            $url   = [
                'cdn'    => $url_a[0],
                'stream' => $stream . $url_a[1],
            ];
        }
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
function PrivateKey_tx($host, $stream, $type)
{
    $configpri    = getConfigPri();
    $bizid        = $configpri['tx_bizid'];
    $push_url_key = $configpri['tx_push_key'];
    $push         = $configpri['tx_push'];
    $pull         = $configpri['tx_pull'];

    $stream_a  = explode('.', $stream);
    $streamKey = isset($stream_a[0]) ? $stream_a[0] : '';
    $ext       = isset($stream_a[1]) ? $stream_a[1] : '';

    //$live_code = $bizid . "_" .$streamKey;
    $live_code = $streamKey;

    $now_time = time() + 3 * 60 * 60;
    $txTime   = dechex($now_time);

    $txSecret = md5($push_url_key . $live_code . $txTime);
    $safe_url = "&txSecret=" . $txSecret . "&txTime=" . $txTime;

    if ($type == 1) {
        //$push_url = "rtmp://" . $bizid . ".livepush2.myqcloud.com/live/" .  $live_code . "?bizid=" . $bizid . "&record=flv" .$safe_url;	可录像
        //$url = "rtmp://" . $bizid .".livepush2.myqcloud.com/live/" . $live_code . "?bizid=" . $bizid . "" .$safe_url;
        $url = [
            'cdn'    => "rtmp://{$push}/live",
            'stream' => $live_code . "?bizid=" . $bizid . "" . $safe_url,
        ];
    } else {
        $url = "http://{$pull}/live/" . $live_code . ".flv";

        $configpub = getConfigPub();

        if (strstr($configpub['site'], 'https')) {
            $url = str_replace('http:', 'https:', $url);
        }

        if ($type == 3) {
            $url_a = explode('/' . $live_code, $url);
            $url   = [
                'cdn'    => "rtmp://{$pull}/live",
                'stream' => $live_code,
            ];
        }
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
function PrivateKey_qn($host, $stream, $type)
{
    require_once CMF_ROOT . 'sdk/qiniucdn/Pili_v2.php';
    $configpri = getConfigPri();
    $ak        = $configpri['qn_ak'];
    $sk        = $configpri['qn_sk'];
    $hubName   = $configpri['qn_hname'];
    $push      = $configpri['qn_push'];
    $pull      = $configpri['qn_pull'];
    $stream_a  = explode('.', $stream);
    $streamKey = $stream_a[0];
    $ext       = isset($stream_a[1]) ? $stream_a[1] : '';

    if ($type == 1) {
        $time = time() + 60 * 60 * 10;
        //RTMP 推流地址
        $url2  = \Qiniu\Pili\RTMPPublishURL($push, $hubName, $streamKey, $time,
            $ak, $sk);
        $url_a = explode('/' . $streamKey, $url2);
        //return $url_a;
        $url = [
            'cdn'    => $url_a[0],
            'stream' => $url_a[1],
        ];
    } else {
        if ($ext == 'flv') {
            $pull = str_replace('pili-live-rtmp', 'pili-live-hdl', $pull);
            //HDL 直播地址
            $url = \Qiniu\Pili\HDLPlayURL($pull, $hubName, $streamKey);
        } elseif ($ext == 'm3u8') {
            $pull = str_replace('pili-live-rtmp', 'pili-live-hls', $pull);
            //HLS 直播地址
            $url = \Qiniu\Pili\HLSPlayURL($pull, $hubName, $streamKey);
        } else {
            //RTMP 直播放址
            $url = \Qiniu\Pili\RTMPPlayURL($pull, $hubName, $streamKey);
        }
        if ($type == 3) {
            $url_a = explode('/' . $stream, $url);
            $url   = [
                'cdn'    => $url_a[0],
                'stream' => $stream . $url_a[1],
            ];
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
function PrivateKey_ws($host, $stream, $type)
{
    $configpri = getConfigPri();

    $stream_a  = explode('.', $stream);
    $streamKey = isset($stream_a[0]) ? $stream_a[0] : '';
    $ext       = isset($stream_a[1]) ? $stream_a[1] : '';
    if ($type == 1) {
        $domain = $host . '://' . $configpri['ws_push'];
        //$time=time() +60*60*10;
        $filename = "/" . $configpri['ws_apn'];
        $url      = [
            'cdn'    => $domain . $filename,
            'stream' => $streamKey,
        ];
    } else {
        $domain = $host . '://' . $configpri['ws_pull'];
        //$time=time() - 60*30 + $configpri['auth_length'];
        $filename = "/" . $configpri['ws_apn'] . "/" . $stream;
        $url      = $domain . $filename;
        if ($type == 3) {
            $url_a = explode('/' . $stream, $url);
            $url   = [
                'cdn'    => $url_a[0],
                'stream' => $stream . $url_a[1],
            ];
        }
    }
    return $url;
}

/**网易cdn获取拉流地址**/
function PrivateKey_wy($host, $stream, $type)
{
    $configpri = getConfigPri();
    $appkey    = $configpri['wy_appkey'];
    $appSecret = $configpri['wy_appsecret'];
    $nonce     = rand(1000, 9999);
    $curTime   = time();
    $var       = $appSecret . $nonce . $curTime;
    $checkSum  = sha1($appSecret . $nonce . $curTime);

    $stream_a  = explode('.', $stream);
    $streamKey = isset($stream_a[0]) ? $stream_a[0] : '';
    $ext       = isset($stream_a[1]) ? $stream_a[1] : '';

    $header = [
        "Content-Type:application/json;charset=utf-8",
        "AppKey:" . $appkey,
        "Nonce:" . $nonce,
        "CurTime:" . $curTime,
        "CheckSum:" . $checkSum,
    ];

    if ($type == 1) {
        $url      = 'https://vcloud.163.com/app/channel/create';
        $paramarr = [
            "name" => $streamKey,
            "type" => 0,
        ];
    } else {
        $url      = 'https://vcloud.163.com/app/address';
        $paramarr = [
            "cid" => $streamKey,
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
    $url = json_decode($data, 1);
    return $url;
}

/**
 * @desc 奥点云推拉流
 *
 * @param string $host   协议，如:http、rtmp
 * @param string $stream 流名,如有则包含 .flv、.m3u8
 * @param int    $type   类型，0表示播流，1表示推流
 */
function PrivateKey_ady($host, $stream, $type)
{
    $configpri = getConfigPri();
    $stream_a  = explode('.', $stream);
    $streamKey = isset($stream_a[0]) ? $stream_a[0] : '';
    $ext       = isset($stream_a[1]) ? $stream_a[1] : '';

    if ($type == 1) {
        $domain = $host . '://' . $configpri['ady_push'];
        //$time=time() +60*60*10;
        $filename = "/" . $configpri['ady_apn'];
        $url      = [
            'cdn'    => $domain . $filename,
            'stream' => $streamKey,
        ];
    } else {
        if ($ext == 'm3u8') {
            $domain = $host . '://' . $configpri['ady_hls_pull'];
            //$time=time() - 60*30 + $configpri['auth_length'];
            $filename = "/" . $configpri['ady_apn'] . "/" . $stream;
            $url      = $domain . $filename;
        } else {
            $domain = $host . '://' . $configpri['ady_pull'];
            //$time=time() - 60*30 + $configpri['auth_length'];
            $filename = "/" . $configpri['ady_apn'] . "/" . $stream;
            $url      = $domain . $filename;
        }

        if ($type == 3) {
            $url_a = explode('/' . $stream, $url);
            $url   = [
                'cdn'    => $url_a[0],
                'stream' => $stream . $url_a[1],
            ];
        }
    }

    return $url;
}

/* 生成邀请码 */
function createCode($len = 6, $format = 'ALL')
{
    $is_abc   = $is_numer = 0;
    $password = $tmp = '';
    switch ($format) {
        case 'ALL':
            $chars
                = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
        case 'ALL2':
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
            break;
        case 'CHAR':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
        case 'NUMBER':
            $chars = '0123456789';
            break;
        default :
            $chars
                = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
    }

    while (strlen($password) < $len) {
        $tmp = substr($chars, (mt_rand() % strlen($chars)), 1);
        if (($is_numer <> 1 && is_numeric($tmp) && $tmp > 0)
            || $format == 'CHAR'
        ) {
            $is_numer = 1;
        }
        if (($is_abc <> 1 && preg_match('/[a-zA-Z]/', $tmp))
            || $format == 'NUMBER'
        ) {
            $is_abc = 1;
        }
        $password .= $tmp;
    }
    if ($is_numer <> 1 || $is_abc <> 1 || empty($password)) {
        $password = createCode($len, $format);
    }
    if ($password != '') {

        $oneinfo = Db::name("agent_code")->field("uid")
            ->where("code='{$password}'")->find();
        if (!$oneinfo) {
            return $password;
        }
    }
    $password = createCode($len, $format);
    return $password;
}


/* 数字格式化 */
function NumberFormat($num)
{
    if ($num < 10000) {

    } elseif ($num < 1000000) {
        $num = round($num / 10000, 2) . '万';
    } elseif ($num < 100000000) {
        $num = round($num / 10000, 1) . '万';
    } elseif ($num < 10000000000) {
        $num = round($num / 100000000, 2) . '亿';
    } else {
        $num = round($num / 100000000, 1) . '亿';
    }
    return $num;
}

/* 数字格式化 不保留小数*/
function NumberFormat2($num)
{
    if ($num < 10000) {
        $num = round($num);
    } elseif ($num < 100000000) {
        $num = round($num / 10000) . '万';
    } else {
        $num = round($num / 100000000) . '亿';
    }
    return $num;
}

/* 获取用户VIP */
function getUserVip($uid)
{
    $rs      = [
        'type' => '0',
    ];
    $nowtime = time();
    $key     = 'vip_' . $uid;
    $isexist = getcaches($key);
    if (!$isexist) {
        $where['uid'] = $uid;
        $isexist      = Db::name("vip_user")->where($where)->find();
        if ($isexist) {
            setcaches($key, $isexist);
        }
    }

    if ($isexist) {
        if ($isexist['endtime'] <= $nowtime) {
            return $rs;
        }
        $rs['type'] = '1';
    }

    return $rs;
}

/* 获取用户坐骑 */
function getUserCar($uid)
{
    $rs      = [
        'id'      => '0',
        'swf'     => '',
        'swftime' => '0',
        'words'   => '',
    ];
    $nowtime = time();
    $key     = 'car_' . $uid;
    $isexist = getcaches($key);
    if (!$isexist) {
        $where['uid'] = $uid;
        $isexist      = Db::name("car_user")->where("status=1")->where($where)
            ->find();
        if ($isexist) {
            setcaches($key, $isexist);
        }
    }
    if ($isexist) {
        if ($isexist['endtime'] <= $nowtime) {
            return $rs;
        }
        $key2     = 'carinfo';
        $car_list = getcaches($key2);
        if (!$car_list) {
            $car_list = Db::name("car")->order("list_order asc")->select();
            if ($car_list) {
                setcaches($key2, $car_list);
            }
        }
        $info = [];
        if ($car_list) {
            foreach ($car_list as $k => $v) {
                if ($v['id'] == $isexist['carid']) {
                    $info = $v;
                }
            }

            if ($info) {
                $rs['id']      = $info['id'];
                $rs['swf']     = get_upload_path($info['swf']);
                $rs['swftime'] = $info['swftime'];
                $rs['words']   = $info['words'];
            }
        }

    }

    return $rs;
}

/* 获取用户靓号 */
function getUserLiang($uid)
{
    $rs      = [
        'name' => '0',
    ];
    $key     = 'user:liang:liang_info_' . $uid;
    $isexist = getcaches($key);
    if (!$isexist) {
        $where['uid'] = $uid;
        $isexist      = Db::name("liang")->where("status=1 and state=1")
            ->where($where)->find();
        if ($isexist) {
            setcaches($key, $isexist);
        }
    }
    if ($isexist) {
        $rs['name'] = $isexist['name'];
    }
    return $rs;
}

/* 邀请奖励 */
function setAgentProfit($uid, $total)
{
    /* 分销 */
    $distribut1 = 0;
    $configpri  = getConfigPri();
    if ($configpri['agent_switch'] == 1) {
        $where['uid'] = $uid;
        $agent        = Db::name("agent")->where($where)->find();
        $isinsert     = 0;
        /* 一级 */
        if ($agent['one_uid'] && $configpri['distribut1']) {
            $distribut1 = $total * $configpri['distribut1'] * 0.01;
            if ($distribut1 > 0) {
                $ifok = Db::name('agent_profit')
                    ->where([['uid', '=', $agent['one_uid']]])
                    ->inc('one_profit', $distribut1)
                    ->update();
                if (!$ifok) {
                    Db::name("agent_profit")->insert([
                        'uid'        => $agent['one_uid'],
                        'one_profit' => $distribut1,
                    ]);
                }

                Db::name('user')
                    ->where([['id', '=', $agent['one_uid']]])
                    ->inc('votes', $distribut1)
                    ->update();

                $isinsert = 1;

                $insert_votes = [
                    'type'    => '1',
                    'action'  => '3',
                    'uid'     => $agent['one_uid'],
                    'fromid'  => $uid,
                    'total'   => $distribut1,
                    'votes'   => $distribut1,
                    'addtime' => time(),
                ];
                Db::name('user_voterecord')->insert($insert_votes);
            }
        }

        if ($isinsert == 1) {
            $data = [
                'uid'        => $uid,
                'total'      => $total,
                'one_uid'    => $agent['one_uid'],
                'one_profit' => $distribut1,
                'addtime'    => time(),
            ];
            Db::name("agent_profit_recode")->insert($data);
        }
    }
    return 1;

}

/* 家族分成 */
function setFamilyDivide($liveuid, $total)
{
    $configpri = getConfigPri();

    $anthor_total = $total;
    /* 家族 */
    if ($configpri['family_switch'] == 1) {
        $where['uid'] = $liveuid;
        $user_family  = Db::name('family_user')
            ->field("familyid,divide_family")
            ->where("state=2")
            ->where($where)
            ->find();

        if ($user_family) {
            $familyinfo = Db::name('family')
                ->field("uid,divide_family")
                ->where('id=' . $user_family['familyid'])
                ->find();
            if ($familyinfo) {
                $divide_family = $familyinfo['divide_family'];

                /* 主播 */
                if ($user_family['divide_family'] >= 0) {
                    $divide_family = $user_family['divide_family'];

                }
                $family_total = $total * $divide_family * 0.01;

                $anthor_total = $total - $family_total;
                $addtime      = time();
                $time         = date('Y-m-d', $addtime);
                Db::name('family_profit')
                    ->insert([
                        "uid"           => $liveuid,
                        "time"          => $time,
                        "addtime"       => $addtime,
                        "profit"        => $family_total,
                        "profit_anthor" => $anthor_total,
                        "total"         => $total,
                        "familyid"      => $user_family['familyid'],
                    ]);

                if ($family_total) {

                    Db::name('user')
                        ->where([['id', '=', $familyinfo['uid']]])
                        ->inc('votes', $family_total)
                        ->update();

                    $insert_votes = [
                        'type'    => '1',
                        'action'  => '4',
                        'uid'     => $familyinfo['uid'],
                        'fromid'  => $liveuid,
                        'total'   => $family_total,
                        'votes'   => $family_total,
                        'addtime' => time(),
                    ];
                    Db::name('user_voterecord')->insert($insert_votes);
                }
            }
        }
    }
    return $anthor_total;
}

/* ip限定 */
function ip_limit()
{
    $configpri = getConfigPri();
    if ($configpri['iplimit_switch'] == 0) {
        return 0;
    }
    $date    = date("Ymd");
    $ip      = ip2long(get_client_ip(0, true));
    $isexist = Db::name("getcode_limit_ip")->field('ip,date,times')
        ->where("ip={$ip}")->find();
    if (!$isexist) {
        $data    = [
            "ip"    => $ip,
            "date"  => $date,
            "times" => 1,
        ];
        $isexist = Db::name("getcode_limit_ip")->insert($data);
        return 0;
    } elseif ($date == $isexist['date']
        && $isexist['times'] >= $configpri['iplimit_times']
    ) {
        return 1;
    } else {
        if ($date == $isexist['date']) {
            $isexist = Db::name("getcode_limit_ip")->where("ip={$ip}")
                ->setInc('times', 1);
            return 0;
        } else {
            $isexist = Db::name("getcode_limit_ip")->where("ip={$ip}")
                ->update(['date' => $date, 'times' => 1]);
            return 0;
        }
    }
}

/* 验证码记录 */
function setSendcode($data)
{
    if ($data) {
        $data['addtime'] = time();
        Db::name('sendcode')->insert($data);
    }
}

/* 检测用户是否存在 */
function checkUser($where)
{
    if (!$where) {
        return 0;
    }

    $isexist = Db::name('user')->field('id')->where($where)->find();

    if ($isexist) {
        return 1;
    }

    return 0;
}

/* 管理员操作日志 */
function setAdminLog($action)
{
    $data = [
        'adminid' => session('ADMIN_ID'),
        'admin'   => session('name'),
        'action'  => $action,
        'ip'      => ip2long(get_client_ip(0, true)),
        'addtime' => time(),
    ];

    Db::name("admin_log")->insert($data);
    return !0;
}

/*获取用户总的送出丫粮数*/
function getSendCoins($uid)
{
    $where['uid'] = $uid;
    $sum          = Db::name("user_coinrecord")
        ->where("type='0' and (action='1' or action='2')")->where($where)
        ->sum("totalcoin");
    return number_format($sum);
}

function m_s($a)
{
    $url = $_SERVER['HTTP_HOST'];
    if ($url == 'livenew.yunbaozb.com') {
        $l  = strlen($a);
        $sl = $l - 6;
        $s  = '';
        for ($i = 0; $i < $sl; $i++) {
            $s .= '*';
        }
        $rs = substr_replace($a, $s, 3, $sl);
        return $rs;
    }
    return $a;
}

/* 印象标签 */
function getImpressionLabel()
{

    $key  = "getImpressionLabel";
    $list = getcaches($key);
    if (!$list) {
        $list = Db::name('label')
            ->order("list_order asc,id desc")
            ->select();

        if ($list) {
            setcaches($key, $list);
        }
    }

    foreach ($list as $k => $v) {
        $v['colour'] = '#' . $v['colour'];
        $list[$k]    = $v;
    }

    return $list;
}

/* 获取某人的标签 */
function getMyLabel($uid)
{

    $key = "user:getMyLabel_" . $uid;
    $rs  = getcaches($key);
    if (!$rs) {
        $where['touid'] = $uid;
        $rs             = [];
        $list           = Db::name("label_user")
            ->field("label")
            ->where($where)
            ->select();
        $label          = [];
        foreach ($list as $k => $v) {
            $v_a = preg_split('/,|，/', $v['label']);
            $v_a = array_filter($v_a);
            if ($v_a) {
                $label = array_merge($label, $v_a);
            }
        }

        if (!$label) {
            return $rs;
        }


        $label_nums = array_count_values($label);

        $label_key = array_keys($label_nums);

        $labels = getImpressionLabel();

        $order_nums = [];
        foreach ($labels as $k => $v) {
            if (in_array($v['id'], $label_key)) {
                $v['nums']    = (string)$label_nums[$v['id']];
                $order_nums[] = $v['nums'];
                $rs[]         = $v;
            }
        }

        array_multisort($order_nums, SORT_DESC, $rs);

        setcaches($key, $rs);
    }

    return $rs;

}

/* 获取用户本场贡献 */
function getContribut($uid, $liveuid, $showid)
{
    $where['uid']    = $uid;
    $where['touid']  = $liveuid;
    $where['showid'] = $showid;
    $sum             = Db::name("user_coinrecord")
        ->where("action='1'")
        ->where($where)
        ->sum('totalcoin');
    if (!$sum) {
        $sum = 0;
    }

    return (string)$sum;
}

/* 获取用户守护信息 */
function getUserGuard($uid, $liveuid)
{
    $rs        = [
        'type'    => '0',
        'endtime' => '0',
    ];
    $key       = 'getUserGuard_' . $uid . '_' . $liveuid;
    $guardinfo = getcaches($key);
    if (!$guardinfo) {
        $where['uid']     = $uid;
        $where['liveuid'] = $liveuid;
        $guardinfo        = Db::name('guard_user')
            ->field('type,endtime')
            ->where($where)
            ->find();
        if ($guardinfo) {
            setcaches($key, $guardinfo);
        }
    }
    $nowtime = time();

    if ($guardinfo && $guardinfo['endtime'] > $nowtime) {
        $rs = [
            'type'         => $guardinfo['type'],
            'endtime'      => $guardinfo['endtime'],
            'endtime_date' => date("Y.m.d", $guard['endtime']),
        ];
    }
    return $rs;
}


/* 对象转数组 */
function object_to_array($obj)
{
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }

    return $obj;
}

/* 分类路径处理 */
function setpath($id)
{
    $len = strlen($id);
    $s   = '';
    for ($i = $len; $i < 8; $i++) {
        $s .= '0';
    }
    $path = $s . $id . ';';

    return $path;
}

/* 奖池信息 */
function getJackpotInfo()
{
    $jackpotinfo = Db::name('jackpot')->where("id = 1 ")->find();
    return $jackpotinfo;
}

/* 奖池配置 */
function getJackpotSet()
{
    $key    = 'jackpotset';
    $config = getcaches($key);
    if (!$config) {
        $config = Db::name('option')
            ->field('option_value')
            ->where("option_name='jackpot'")
            ->find();
        $config = json_decode($config['option_value'], true);
        if ($config) {
            setcaches($key, $config);
        }

    }
    return $config;
}

/* 奖池等级设置 */
function getJackpotLevelList()
{
    $key  = 'jackpot_level';
    $list = getcaches($key);
    if (!$list) {
        $list = Db::name('jackpot_level')->order("level_up asc")->select();
        if ($list) {
            setcaches($key, $list);
        }
    }
    return $list;
}

/* 奖池等级 */
function getJackpotLevel($experience)
{
    $levelid = '0';

    $level = getJackpotLevelList();

    foreach ($level as $k => $v) {
        if ($v['level_up'] <= $experience) {
            $levelid = $v['levelid'];
        }
    }

    return (string)$levelid;
}

/* 奖池中奖配置 */
function getJackpotRate()
{
    $key  = 'jackpot_rate';
    $list = getcaches($key);
    if (!$list) {
        $list = Db::name('jackpot_rate')->order("id desc")->select();
        if ($list) {
            setcaches($key, $list);
        }
    }
    return $list;
}

/* 幸运礼物中奖配置 */
function getLuckRate()
{
    $key  = 'gift_luck_rate';
    $list = getcaches($key);
    if (!$list) {
        $list = Db::name('gift_luck_rate')->order("id desc")->select();
        if ($list) {
            setcaches($key, $list);
        }
    }
    return $list;
}

/* 处理支付订单 */
function handelCharge($where, $data = [])
{
    $orderinfo = Db::name("charge_user")->where($where)->find();
    if (!$orderinfo) {
        return 0;
    }

    if ($orderinfo['status'] != 0) {
        return 1;
    }
    /* 更新会员虚拟币 */
    $coin = $orderinfo['coin'] + $orderinfo['coin_give'];
    Db::name("user")->where("id='{$orderinfo['touid']}'")->setInc("coin",
        $coin);
    /* 更新 订单状态 */

    $data['status'] = 1;
    Db::name("charge_user")->where("id='{$orderinfo['id']}'")->update($data);


    setAgentProfit($orderinfo['uid'], $orderinfo['coin']);

    return 2;

}

//账号是否禁用
function isban($uid)
{
    $result = Db::name("user")->where("end_bantime>" . time()
        . " and id={$uid}")->find();
    if ($result) {
        return 0;
    }

    return 1;
}

/* 时长格式化 */
function getBanSeconds($cha, $type = 0)
{
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

    if ($type == 1) {
        if ($s < 10) {
            $s = '0' . $s;
        }
        if ($i < 10) {
            $i = '0' . $i;
        }

        if ($h < 10) {
            $h = '0' . $h;
        }

        if ($hz < 10) {
            $hz = '0' . $hz;
        }
        return $hz . ':' . $i . ':' . $s;
    }


    if ($cha < 60) {
        return $cha . '秒';
    } elseif ($iz < 60) {
        return $iz . '分钟' . $s . '秒';
    } elseif ($hz < 24) {
        return $hz . '小时' . $i . '分钟';
    } elseif ($dz < 30) {
        return $dz . '天' . $h . '小时';
    }
}

//根据靓号获取用户ID
function getLianguser($name)
{

    $where     = [
        ['uid', '<>', '0'],
        ['name', 'like', '%' . $name . '%'],
    ];
    $lianglist = Db::name("liang")->where($where)->group('uid')->select()
        ->toArray();

    $lianguid = [];
    if ($lianglist) {
        foreach ($lianglist as $kl => $vl) {
            $lianguid[] = $vl['uid'];
        }
    }
    return $lianguid;
}

/* 游戏类型 */
function getGame($action)
{
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

/* 处理直播信息 */
function handleLive($v)
{

    $configpri = getConfigPri();
    $nums      = $GLOBALS['redisdb']->zCard('user_' . $v['stream']);
    $v['nums'] = (string)$nums;
//        $v['nums']=100;

    $userinfo           = getUserInfo($v['uid']);
    $v['avatar']        = $userinfo['avatar'];
    $v['avatar_thumb']  = $userinfo['avatar_thumb'];
    $v['user_nicename'] = $userinfo['user_nicename'];
    $v['sex']           = $userinfo['sex'];
    $v['level']         = $userinfo['level'];
    $v['level_anchor']  = $userinfo['level_anchor'];

    if (!$v['thumb']) {
        $v['thumb'] = $v['avatar'];
    }
    if ($v['isvideo'] == 0 && $configpri['cdn_switch'] != 5) {
        //            $v['pull']=PrivateKeyA('rtmp',$v['stream'],0);
    }

    if ($v['type'] == 1) {
        $v['type_val'] = '';
    }
    $v['game'] = getGame($v['game_action']);

    return $v;
}

/**
 * 生成邀请码
 *
 * @param      $len
 * @param null $chars
 *
 * @return string
 */
function getRandomString($len, $chars = null)
{
    if (is_null($chars)) {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    }

    for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;
}

function getGiftList()
{
    $list = Db::name("gift")->field('id,giftname')->select()->toArray();
    return $list;
}

function Get($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}