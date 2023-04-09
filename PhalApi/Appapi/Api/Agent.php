<?php

/**
 * 分销
 */
class Api_Agent extends PhalApi_Api
{

    public function getRules()
    {
        return [
            'getCode'       => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'desc' => '用户ID'],
                'token' => [
                    'name' => 'token',
                    'type' => 'string',
                    'desc' => '用户token',
                ],
            ],
            'getShareImage' => [],
        ];
    }


    /**
     * 分享信息
     *
     * @desc 用于 获取分享信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].code 邀请码
     * @return string info[0].href 二维码链接
     * @return string info[0].qr 二维码图片链接
     * @return string msg 提示信息
     */
    public function getCode()
    {
        $rs    = ['code' => 0, 'msg' => '', 'info' => []];
        $uid   = checkNull($this->uid);
        $token = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if ($checkToken == 700) {
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_Agent();
        $info   = $domain->getCode($uid);
        if (!$info) {
            $rs['code'] = 1001;
            $rs['msg']  = '信息错误';
            return $rs;
        }
        //http://livenewtest.yunbaozb.com/Portal/index/scanqr
        $href         = get_upload_path('/Portal/index/scanqr');
        $info['href'] = $href;
        $qr           = scerweima($href);
        $info['qr']   = get_upload_path($qr);

        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * 获取分享图片
     *
     * @desc 用于获取用户分享图片
     * @return int code 操作码，0表示成功
     * @return array info 轮播列表
     * @return string info['bg'] 背景图片链接
     * @return string info['qrcode'] 二维码图片链接
     * @return string msg 提示信息
     */
    public function getShareImage()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];
        $info       = [
            'bg'     => 'http://fs.51liaobei.com/%E7%9F%A9%E5%BD%A2%204.png',
            'qrcode' => 'https://by.boyaduck.com/16841599487734_.pic_hd.jpg',
        ];
        $rs['info'] = $info;
        return $rs;
    }
}
