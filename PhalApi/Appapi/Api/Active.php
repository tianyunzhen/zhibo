<?php

/**
 * 活动
 */
class Api_Active extends PhalApi_Api{

    public function getRules(){
        return [
            'activeList'       => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'require' => true, 'desc' => '用户ID'],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token'],
                'page'  => ['name' => 'page', 'type' => 'int', 'require' => true, 'desc' => '页码'],
                'type'  => ['name' => 'type', 'type' => 'int', 'min' => 1, 'max' => 2, 'require' => true, 'desc' => '1进行中  2关闭'],
            ]
        ];
    }

    /**
     * 活动列表
     * @desc 用于 获取活动列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].name 活动名
     * @return string info[0].banner 图片
     * @return string info[0].link 链接
     * @return string msg 提示信息
     */
    public function activeList(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $page       = checkNull($this->page);
        $type       = checkNull($this->type);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $model      = new Domain_Activity();
        $rs['info'] = $model->getList($page, $type);
        return $rs;
    }
}