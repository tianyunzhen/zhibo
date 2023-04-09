<?php
/**
 * 登录、注册
 */
if(!session_id()){
    session_start();
}

class Api_Login extends PhalApi_Api{
    public function getRules(){
        return [
            'userLoginByThird' => [
                'openid'    => [
                    'name'    => 'openid',
                    'type'    => 'string',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '第三方openid',
                ],
                'type'      => [
                    'name'    => 'type',
                    'type'    => 'string',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '第三方标识',
                ],
                'nicename'  => [
                    'name'    => 'nicename',
                    'type'    => 'string',
                    'default' => '',
                    'desc'    => '第三方昵称',
                ],
                'avatar'    => [
                    'name'    => 'avatar',
                    'type'    => 'string',
                    'default' => '',
                    'desc'    => '第三方头像',
                ],
                'signature' => [
                    'name'    => 'signature',
                    'type'    => 'string',
                    'default' => '',
                    'desc'    => '签名',
                ],
                'source'    => [
                    'name'    => 'source',
                    'type'    => 'string',
                    'default' => 'pc',
                    'desc'    => '来源设备',
                ],
                'pushid'    => [
                    'name' => 'pushid',
                    'type' => 'string',
                    'desc' => '极光ID',
                ],
            ],

            'getCode' => [
                'mobile' => [
                    'name'    => 'mobile',
                    'type'    => 'string',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '手机号',
                ],
                'sign'   => [
                    'name'    => 'sign',
                    'type'    => 'string',
                    'default' => '',
                    'desc'    => '签名',
                ],
            ],

            'getForgetCode' => [
                'mobile' => [
                    'name'    => 'mobile',
                    'type'    => 'string',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '手机号',
                ],
                'sign'   => [
                    'name'    => 'sign',
                    'type'    => 'string',
                    'default' => '',
                    'desc'    => '签名',
                ],
            ],
            'getUnionid'    => [
                'code' => [
                    'name' => 'code',
                    'type' => 'string',
                    'desc' => '微信code',
                ],
            ],

            'logout' => [
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

            'flashLogin' => [
                'token'    => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户Token',
                ],
                'platform' => [
                    'name'    => 'platform',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '平台(android或ios)',
                ],
                'source'   => [
                    'name'    => 'source',
                    'type'    => 'string',
                    'default' => 'pc',
                    'desc'    => '来源设备',
                ],
                'pushid'   => [
                    'name'    => 'pushid',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '极光注册ID',
                ],
            ],
            'vCodeLogin' => [
                'user_login' => [
                    'name'    => 'user_login',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户手机号',
                ],
                'code'       => [
                    'name'    => 'code',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '短信验证码',
                ],
                'source'     => [
                    'name'    => 'source',
                    'type'    => 'string',
                    'default' => 'pc',
                    'desc'    => '来源设备',
                ],
                'pushid'     => [
                    'name'    => 'pushid',
                    'type'    => 'string',
                    'require' => true,
                    'default' => 'pc',
                    'desc'    => '极光ID',
                ],
            ],
            'sendCode'   => [
                'mobile' => [
                    'name'    => 'mobile',
                    'type'    => 'string',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '手机号',
                ],
                'sign'   => [
                    'name'    => 'sign',
                    'type'    => 'string',
                    'default' => '',
                    'desc'    => '签名',
                ],
            ],
        ];
    }

    /**
     * 会员登陆 需要密码
     *
     * @desc 用于用户登陆信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function userLogin(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $user_login = checkNull($this->user_login);
        $user_pass  = checkNull($this->user_pass);
        $pushid     = checkNull($this->pushid);

        $domain = new Domain_Login();

        $info = $domain->userLogin($user_login, $user_pass);

        if($info == 1001){
            $rs['code'] = 1001;
            $rs['msg']  = '账号或密码错误';
            return $rs;
        }elseif($info == 1002){
            $rs['code'] = 1002;
            //禁用信息
            $baninfo       = $domain->getUserban($user_login);
            $rs['info'][0] = $baninfo;
            return $rs;
        }elseif($info == 1003){
            $rs['code'] = 1003;
            $rs['msg']  = '该账号已被禁用';
            return $rs;
        }

        $rs['info'][0] = $info;

        if($pushid){
            $domain->upUserPush($info['id'], $pushid);
        }
        return $rs;
    }

    /**
     * 会员注册
     *
     * @desc 用于用户注册信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function userReg(){

        $rs = ['code' => 0, 'msg' => '注册成功', 'info' => []];

        $user_login = checkNull($this->user_login);
        $user_pass  = checkNull($this->user_pass);
        $user_pass2 = checkNull($this->user_pass2);
        $source     = checkNull($this->source);
        $code       = checkNull($this->code);

        if(!$_SESSION['reg_mobile'] || !$_SESSION['reg_mobile_code']){
            $rs['code'] = 1001;
            $rs['msg']  = '请先获取验证码';
            return $rs;
        }
//
        if($user_login != $_SESSION['reg_mobile']){
            $rs['code'] = 1001;
            $rs['msg']  = '手机号码不一致';
            return $rs;
        }
//
        if($code != $_SESSION['reg_mobile_code']){
            $rs['code'] = 1002;
            $rs['msg']  = '验证码错误';
            return $rs;
        }

        if($user_pass != $user_pass2){
            $rs['code'] = 1003;
            $rs['msg']  = '两次输入的密码不一致';
            return $rs;
        }

        $check = passcheck($user_pass);
//
        if(!$check){
            $rs['code'] = 1004;
            $rs['msg']  = '密码为6-20位字母数字组合';
            return $rs;
        }

        $domain = new Domain_Login();
        $info   = $domain->userReg($user_login, $user_pass, $source);

        if($info == 1006){
            $rs['code'] = 1006;
            $rs['msg']  = '该手机号已被注册！';
            return $rs;
        }elseif($info == 1007){
            $rs['code'] = 1007;
            $rs['msg']  = '注册失败，请重试';
            return $rs;
        }


        //$rs['info'][0] = $info;

        $_SESSION['reg_mobile']            = '';
        $_SESSION['reg_mobile_code']       = '';
        $_SESSION['reg_mobile_expiretime'] = '';

        return $rs;
    }

    /**
     * 会员找回密码
     *
     * @desc 用于会员找回密码
     * @return int code 操作码，0表示成功，1表示验证码错误，2表示用户密码不一致,3短信手机和登录手机不一致 4、用户不存在 801 密码6-12位数字与字母
     * @return array info
     * @return string msg 提示信息
     */
    public function userFindPass(){

        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $user_login = checkNull($this->user_login);
        $user_pass  = checkNull($this->user_pass);
        $user_pass2 = checkNull($this->user_pass2);
        $code       = checkNull($this->code);

        if(!$_SESSION['forget_mobile'] || !$_SESSION['forget_mobile_code']){
            $rs['code'] = 1001;
            $rs['msg']  = '请先获取验证码';
            return $rs;
        }

        if($user_login != $_SESSION['forget_mobile']){
            $rs['code'] = 1001;
            $rs['msg']  = '手机号码不一致';
            return $rs;
        }

        if($code != $_SESSION['forget_mobile_code']){
            $rs['code'] = 1002;
            $rs['msg']  = '验证码错误';
            return $rs;
        }


        if($user_pass != $user_pass2){
            $rs['code'] = 1003;
            $rs['msg']  = '两次输入的密码不一致';
            return $rs;
        }

        $check = passcheck($user_pass);
        if(!$check){
            $rs['code'] = 1004;
            $rs['msg']  = '密码为6-20位字母数字组合';
            return $rs;
        }

        $domain = new Domain_Login();
        $info   = $domain->userFindPass($user_login, $user_pass);

        if($info == 1006){
            $rs['code'] = 1006;
            $rs['msg']  = '该帐号不存在';
            return $rs;
        }elseif($info === false){
            $rs['code'] = 1007;
            $rs['msg']  = '重置失败，请重试';
            return $rs;
        }

        $_SESSION['forget_mobile']            = '';
        $_SESSION['forget_mobile_code']       = '';
        $_SESSION['forget_mobile_expiretime'] = '';

        return $rs;
    }

    /**
     * 第三方登录
     *
     * @desc 用于用户登陆信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function userLoginByThird(){
        $rs       = ['code' => 0, 'msg' => '', 'info' => []];
        $openid   = checkNull($this->openid);
        $type     = checkNull($this->type);
        $nicename = checkNull($this->nicename);
        $avatar   = checkNull($this->avatar);
        $source   = checkNull($this->source);
        $sign     = checkNull($this->signature);
        $pushid   = checkNull($this->pushid);


        $checkdata = [
            'openid' => $openid,
        ];

        $issign = checkSign($checkdata, $sign);
        if(!$issign){
            $rs['code'] = 1001;
            $rs['msg']  = '签名错误';
            return $rs;
        }


        $domain = new Domain_Login();
        $info   = $domain->userLoginByThird($openid, $type, $nicename, $avatar,
            $source);

        if($info == 1002){
            $rs['code'] = 1002;
            //禁用信息
            $baninfo       = $domain->getThirdUserban($openid, $type);
            $rs['info'][0] = $baninfo;
            return $rs;
        }elseif($info == 1003){
            $rs['code'] = 1003;
            $rs['msg']  = '该账号已被禁用';
            return $rs;
        }elseif($info == 1004){
            $rs['code'] = 1004;
            $rs['msg']  = '该ip下账号已被封禁';
            return $rs;
        }

        $rs['info'][0] = $info;

        if($pushid){
            $domain->upUserPush($info['id'], $pushid);
        }

        if(isset($info['is_reg'])){
            $push_model = new Common_JPush($info['id']);
//            $push_model->sendAlias('注册成功', Common_JPush::REGISTER);
            Domain_Msg::addMsg('注册成功', Common_JPush::REGISTER, $info['id']);
        }

        return $rs;
    }

    /**
     * 获取注册短信验证码
     *
     * @desc 用于注册获取短信验证码
     * @return int code 操作码，0表示成功,2发送失败
     * @return array info
     * @return string msg 提示信息
     */

    public function getCode(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $mobile = checkNull($this->mobile);
        $sign   = checkNull($this->sign);

        $ismobile = checkMobile($mobile);
        if(!$ismobile){
            $rs['code'] = 1001;
            $rs['msg']  = '请输入正确的手机号';
            return $rs;
        }

        $checkdata = [
            'mobile' => $mobile,
        ];

        $issign = checkSign($checkdata, $sign);
        if(!$issign){
            $rs['code'] = 1001;
            $rs['msg']  = '签名错误';
            return $rs;
        }

        $where = "user_login='{$mobile}'";

        $checkuser = checkUser($where);

        if($checkuser){
            $rs['code'] = 1004;
            $rs['msg']  = '该手机号已注册，请登录';
            return $rs;
        }

        if(isset($_SESSION['reg_mobile']) && $_SESSION['reg_mobile'] == $mobile
            && $_SESSION['reg_mobile_expiretime'] > time()
        ){
            $rs['code'] = 1002;
            $rs['msg']  = '验证码5分钟有效，请勿多次发送';
            return $rs;
        }

        $limit = ip_limit();
        if($limit == 1){
            $rs['code'] = 1003;
            $rs['msg']  = '您已当日发送次数过多';
            return $rs;
        }
        $mobile_code = random(4, 1);

        /* 发送验证码 */
        $result = sendCode($mobile, $mobile_code);
        if($result['code'] == 0){
            $_SESSION['reg_mobile']            = $mobile;
            $_SESSION['reg_mobile_code']       = $mobile_code;
            $_SESSION['reg_mobile_expiretime'] = time() + 60 * 5;
        }elseif($result['code'] == 667){
            $_SESSION['reg_mobile']            = $mobile;
            $_SESSION['reg_mobile_code']       = $result['msg'];
            $_SESSION['reg_mobile_expiretime'] = time() + 60 * 5;

            $rs['code'] = 1002;
            $rs['msg']  = '验证码为：' . $result['msg'];
        }else{
            $rs['code'] = 1002;
            $rs['msg']  = $result['msg'];
        }


        return $rs;
    }

    /**
     * 获取找回密码短信验证码
     *
     * @desc 用于找回密码获取短信验证码
     * @return int code 操作码，0表示成功,2发送失败
     * @return array info
     * @return string msg 提示信息
     */

    public function getForgetCode(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $mobile = checkNull($this->mobile);
        $sign   = checkNull($this->sign);

        $ismobile = checkMobile($mobile);
        if(!$ismobile){
            $rs['code'] = 1001;
            $rs['msg']  = '请输入正确的手机号';
            return $rs;
        }

        $checkdata = [
            'mobile' => $mobile,
        ];

        $issign = checkSign($checkdata, $sign);
        if(!$issign){
            $rs['code'] = 1001;
            $rs['msg']  = '签名错误';
            return $rs;
        }

        $where     = "user_login='{$mobile}'";
        $checkuser = checkUser($where);

        if(!$checkuser){
            $rs['code'] = 1004;
            $rs['msg']  = '该手机号未注册';
            return $rs;
        }

        if(isset($_SESSION['reg_mobile'])
            && $_SESSION['forget_mobile'] == $mobile
            && $_SESSION['forget_mobile_expiretime'] > time()
        ){
            $rs['code'] = 1002;
            $rs['msg']  = '验证码5分钟有效，请勿多次发送';
            return $rs;
        }

        $limit = ip_limit();
        if($limit == 1){
            $rs['code'] = 1003;
            $rs['msg']  = '您已当日发送次数过多';
            return $rs;
        }
        $mobile_code = random(6, 1);

        /* 发送验证码 */
        $result = sendCode($mobile, $mobile_code);
        if($result['code'] == 0){
            $_SESSION['forget_mobile']            = $mobile;
            $_SESSION['forget_mobile_code']       = $mobile_code;
            $_SESSION['forget_mobile_expiretime'] = time() + 60 * 5;
        }elseif($result['code'] == 667){
            $_SESSION['forget_mobile']            = $mobile;
            $_SESSION['forget_mobile_code']       = $result['msg'];
            $_SESSION['forget_mobile_expiretime'] = time() + 60 * 5;

            $rs['code'] = 1002;
            $rs['msg']  = '验证码为：' . $result['msg'];
        }else{
            $rs['code'] = 1002;
            $rs['msg']  = $result['msg'];
        }

        return $rs;
    }

    /**
     * 获取微信登录unionid
     *
     * @desc 用于获取微信登录unionid
     * @return int code 操作码，0表示成功,2发送失败
     * @return array info
     * @return string info[0].unionid 微信unionid
     * @return string msg 提示信息
     */
    public function getUnionid(){

        $rs   = ['code' => 0, 'msg' => '', 'info' => []];
        $code = checkNull($this->code);

        if($code == ''){
            $rs['code'] = 1001;
            $rs['msg']  = '参数错误';
            return $rs;

        }

        //$configpri=getConfigPri();

        //$AppID = $configpri['login_wx_appid'];
        //$AppSecret = $configpri['login_wx_appsecret'];
        $AppID     = 'wxbee8d98b9852d612';
        $AppSecret = 'f9d4f74d9412691eeb271dc7632f24b6';
        /* 获取token */
        //$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid={$AppID}&secret={$AppSecret}&code={$code}&grant_type=authorization_code";
        $url
            = "https://api.weixin.qq.com/sns/jscode2session?appid={$AppID}&secret={$AppSecret}&js_code={$code}&grant_type=authorization_code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json = curl_exec($ch);
        curl_close($ch);
        $arr = json_decode($json, 1);
        //file_put_contents('./getUnionid.txt',date('Y-m-d H:i:s').' 提交参数信息 code:'.json_encode($code)."\r\n",FILE_APPEND);
        //file_put_contents('./getUnionid.txt',date('Y-m-d H:i:s').' 提交参数信息 arr:'.json_encode($arr)."\r\n",FILE_APPEND);
        if($arr['errcode']){
            $rs['code'] = 1003;
            $rs['msg']  = '配置错误';
            //file_put_contents('./getUnionid.txt',date('Y-m-d H:i:s').' 提交参数信息 arr:'.json_encode($arr)."\r\n",FILE_APPEND);
            return $rs;
        }


        /* 小程序 绑定到 开放平台 才有 unionid  否则 用 openid  */
        $unionid = $arr['unionid'];

        if(!$unionid){
            //$rs['code']=1002;
            //$rs['msg']='公众号未绑定到开放平台';
            //return $rs;

            $unionid = $arr['openid'];
        }

        $rs['info'][0]['unionid'] = $unionid;
        return $rs;
    }

    /**
     * 退出
     *
     * @desc 用于用户退出 注销极光
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function logout(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = $this->uid;
        $token = checkNull($this->token);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }


        $info = userLogout($uid);


        return $rs;
    }

    /**
     * 一键注册登录
     *
     * @desc 用于用户一键登录
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function flashLogin(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];
        try{
            $token      = checkNull($this->token);
            $platform   = checkNull($this->platform);
            $pushid     = checkNull($this->pushid);
            $source     = checkNull($this->source);
            $user_login = DI()->flash->getMobile($token, $platform);
            file_put_contents(API_ROOT . '/Runtime/falshLogin.log', json_encode($user_login) . date('Y-m-d H:i:s') . '----' . '\n', FILE_APPEND);
            if(!$user_login){
                $rs['code'] = 1005;
                $rs['msg']  = '手机号获取失败！';
                return $rs;
            }
            $domain = new Domain_Login();
            $info   = $domain->flashLogin($user_login, '', $source);
            if(!$info){
                $rs['code'] = 9999;
                $rs['msg']  = '登陆异常';
                return $rs;
            }
            if(is_int($info) && $info == 1007){
                $rs['code'] = 1007;
                $rs['msg']  = '注册失败，请重试';
                return $rs;
            }
            if(is_int($info) && $info == 1004){
                $rs['code'] = 1004;
                $rs['msg']  = '该ip下账号已被封禁';
                return $rs;
            }
            $rs['info'][0] = $info;
            if($pushid){
                $domain->upUserPush($info['id'], $pushid);
            }
            $_SESSION['reg_mobile']            = '';
            $_SESSION['reg_mobile_code']       = '';
            $_SESSION['reg_mobile_expiretime'] = '';

            return $rs;
        }catch(\Exception $e){
            $rs['code'] = $e->getCode();
            $rs['msg']  = $e->getMessage();
            return $rs;
        }

    }

    /**
     * 验证码登录
     *
     * @desc 用于验证码登录
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function vCodeLogin(){

        $rs = ['code' => 0, 'msg' => '注册成功', 'info' => []];

        $user_login = checkNull($this->user_login);
        $source     = checkNull($this->source);
        $pushid     = checkNull($this->pushid);
        $code       = checkNull($this->code);
        $arr        = ['13333333333', '14444444444'];
        $redis      = DI()->redis;
        if(!in_array($user_login, $arr) && ($user_login < 10000000001 || $user_login > 10000000100)){
            $mobile_key = 'reg_mobile_' . $user_login;
            $local_code = $redis->get($mobile_key);
            if(!$local_code || $local_code != $code){
                $rs['code'] = 1002;
                $rs['msg']  = '验证码错误';
                return $rs;
            }
        }

        $domain = new Domain_Login();
        $info   = $domain->flashLogin($user_login, '', $source);

        if(is_int($info) && $info == 1006){
            $rs['code'] = 1006;
            $rs['msg']  = '该手机号已被注册！';
            return $rs;
        }elseif(is_int($info) && $info == 1007){
            $rs['code'] = 1007;
            $rs['msg']  = '注册失败，请重试';
            return $rs;
        }elseif(is_int($info) && $info == 1004){
            $rs['code'] = 1004;
            $rs['msg']  = '该ip下账号已被封禁';
            return $rs;
        }
        if($pushid){
            $domain->upUserPush($info['id'], $pushid);
        }

        $rs['info'][0] = $info;

        $redis->del($mobile_key);

        return $rs;
    }

    /**
     * 发送验证码
     *
     * @desc 用于获取短信验证码(新)
     * @return int code 操作码，0表示成功,2发送失败
     * @return array info
     * @return string msg 提示信息
     */

    public function sendCode(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $mobile = checkNull($this->mobile);
        $sign   = checkNull($this->sign);

        $arr = ['13333333333', '14444444444'];
        if(in_array($mobile, $arr) || ($mobile >= 10000000001 && $mobile <= 10000000100)){
            return $rs;
        }else{
            $ismobile = checkMobile($mobile);
            if(!$ismobile){
                $rs['code'] = 1001;
                $rs['msg']  = '请输入正确的手机号';
                return $rs;
            }
        }

//        $checkdata = [
//            'mobile' => $mobile,
//        ];
//
//        $issign = checkSign($checkdata, $sign);
//        if(!$issign){
//            $rs['code'] = 1001;
//            $rs['msg']  = '签名错误';
//            return $rs;
//        }

        $mobile_code = random(4, 1);
        $redis       = DI()->redis;
        $mobile_key  = 'reg_mobile_' . $mobile;
        /* 发送验证码 */
        $result = sendCode($mobile, $mobile_code);
//        $result['code'] = 667;
        if($result['code'] == 0){
            $redis->set($mobile_key, $mobile_code, 300);
        }elseif($result['code'] == 667){
            $redis->set($mobile_key, $mobile_code, 300);
            $rs['code'] = 1002;
            $rs['msg']  = '验证码为：' . $mobile_code;
        }else{
            $rs['code'] = 1002;
            $rs['msg']  = $result['msg'];
        }
        return $rs;
    }
}
