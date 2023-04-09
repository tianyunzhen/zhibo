<?php

/**
 * 身份认证人工（H5）
 */
class Api_Auth extends PhalApi_Api{

    public function getRules(){
        return [
            'index'       => [
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
            'saveAuth'    => [
                'uid'          => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'        => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户Token',
                ],
                'front_view'   => [
                    'name'    => 'front_view',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '身份证正面照',
                ],
                'back_view'    => [
                    'name'    => 'back_view',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '身份证反面照',
                ],
                'handset_view' => [
                    'name'    => 'handset_view',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '手持身份证照',
                ],
                'car_no' => [
                    'name'    => 'car_no',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '身份证号',
                ],
                'real_name' => [
                    'name'    => 'real_name',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '真实姓名',
                ],
            ],
            'uploadImage' => [
//                'uid' => array('name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
'token' => [
    'name'    => 'token',
    'type'    => 'string',
    'require' => true,
    'desc'    => '用户Token',
],
'file'  => [
    'name'  => 'file',
    'type'  => 'array',
    'min'   => 0,
    'max'   => 1024 * 1024 * 30,
    'range' => ['image/jpg', 'image/jpeg', 'image/png'],
    'ext'   => ['jpg', 'jpeg', 'png'],
],
            ],
        ];
    }

    /**
     * 身份认证首页
     *
     * @desc 用于获取用户身份认证信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info['status'] 认证状态 0 未提交 1 待审核 2 认证通过 3 认证失败
     * @return array info['auth_info'] 认证资料
     * @return array info['auth_info']['front_view'] 正面照
     * @return array info['auth_info']['back_view'] 北面照
     * @return array info['auth_info']['handset_view'] 手持身份证照
     * @return string msg 提示信息
     */
    public function index(){
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
        $userInfo = $di->user->where(['id' => $uid])->select('mobile')
            ->fetchOne();
        if(!$userInfo['mobile']){
            $rs['code'] = 701;
            $rs['msg']  = '请先认证手机号';
            return $rs;
        }
        $authInfo = $di->user_auth->where(["uid" => $uid])
            ->select('front_view,back_view,handset_view,status')
            ->fetchOne();
        if(!$authInfo){
            $status   = 0;
            $authInfo = [];
        }else{
            $status                = $authInfo['status'];
            $authInfo['front_view']
                                   = get_upload_path($authInfo['front_view']);
            $authInfo['back_view'] = get_upload_path($authInfo['back_view']);
            $authInfo['handset_view']
                                   = get_upload_path($authInfo['handset_view']);
            unset($authInfo['status']);
        }
        $rs['info']['status']    = $status;
        $rs['info']['auth_info'] = $authInfo;
        return $rs;
    }

    /**
     * 身份认证提交
     *
     * @desc 用于人工身份认证
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string list[0].msg 成功提示信息
     * @return string msg 提示信息
     */
    public function saveAuth(){
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
        $frontView   = checkNull($this->front_view);
        $backView    = checkNull($this->back_view);
        $handsetView = checkNull($this->handset_view);
        $real_name   = checkNull($this->real_name);
        $car_no      = checkNull($this->car_no);
        $authInfo    = $di->user_auth->where(["uid" => $uid])->fetchOne();
        if($authInfo){
            if($authInfo['status'] != 3){
                $rs['code'] = 701;
                $rs['msg']  = '请勿重复提交';
                return $rs;
            }
            $di->user_auth->where(["uid" => $uid])->delete();
        }
        $data   = [
            'uid'          => $uid,
            'car_no'       => $car_no,
            'real_name'    => $real_name,
            'front_view'   => $frontView,
            'back_view'    => $backView,
            'handset_view' => $handsetView,
            'addtime'      => time(),
            'uptime'       => time(),
        ];
        $result = $di->user_auth->insert($data);
        if(!$result){
            $rs['code'] = 702;
            $rs['msg']  = '提交失败';
            return $rs;
        }
        return $rs;
    }
}
