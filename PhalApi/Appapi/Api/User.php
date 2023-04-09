<?php
/**
 * 用户信息
 */
if(!session_id()){
    session_start();
}

class Api_User extends PhalApi_Api{

    public function getRules(){
        return [
            'iftoken' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token',],
            ],

            'getBaseInfo' => [
                'uid'         => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'token'       => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token',],
                'version_ios' => ['name' => 'version_ios', 'type' => 'string', 'desc' => 'IOS版本号',],
            ],

            'updateAvatar' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token',],
                'file'  => ['name' => 'file', 'type' => 'file', 'min' => 0, 'max' => 1024 * 1024 * 30, 'range' => ['image/jpg', 'image/jpeg', 'image/png'], 'ext' => ['jpg', 'jpeg', 'png'],],
                'scene' => ['name' => 'scene', 'type' => 'int', 'min' => 0, 'default' => 0, 'desc' => '场景',],
            ],

            'updateFields' => [
                'uid'    => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'token'  => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token',],
                'fields' => ['name' => 'fields', 'type' => 'string', 'require' => true, 'desc' => '修改信息，json字符串',],
            ],

            'updatePass' => [
                'uid'     => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'token'   => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token',],
                'oldpass' => ['name' => 'oldpass', 'type' => 'string', 'require' => true, 'desc' => '旧密码',],
                'pass'    => ['name' => 'pass', 'type' => 'string', 'require' => true, 'desc' => '新密码',],
                'pass2'   => ['name' => 'pass2', 'type' => 'string', 'require' => true, 'desc' => '确认密码',],
            ],

            'getBalance' => [
                'uid'         => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'token'       => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token',],
                'type'        => ['name' => 'type', 'type' => 'string', 'desc' => '设备类型，0android，1IOS',],
                'version_ios' => ['name' => 'version_ios', 'type' => 'string', 'desc' => 'IOS版本号',],
            ],

            'getProfit' => [
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
                    'desc'    => '用户token',
                ],
            ],

            'setCash' => [
                'uid'      => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'    => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户token',
                ],
                //                'accountid' => ['name' => 'accountid', 'type' => 'int', 'require' => true, 'desc' => '账号ID'],
                'cashvote' => [
                    'name'    => 'cashvote',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '提现的票数',
                ],
            ],

            'setAttent' => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
            ],

            'isAttent' => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
            ],

            'isBlacked'  => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
            ],
            'checkBlack' => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
            ],

            'setBlack' => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
            ],

            'getBindCode' => [
                'mobile' => [
                    'name'    => 'mobile',
                    'type'    => 'string',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '手机号',
                ],
            ],

            'setMobile' => [
                'uid'    => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'  => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户token',
                ],
                'mobile' => [
                    'name'    => 'mobile',
                    'type'    => 'string',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '手机号',
                ],
                'code'   => [
                    'name'    => 'code',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '验证码',
                ],
            ],

            'getFollowsList' => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
                'p'     => [
                    'name'    => 'p',
                    'type'    => 'int',
                    'min'     => 1,
                    'default' => 1,
                    'desc'    => '页数',
                ],
            ],

            'getFansList' => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
                'p'     => [
                    'name'    => 'p',
                    'type'    => 'int',
                    'min'     => 1,
                    'default' => 1,
                    'desc'    => '页数',
                ],
            ],

            'getBlackList' => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
                'p'     => [
                    'name'    => 'p',
                    'type'    => 'int',
                    'min'     => 1,
                    'default' => 1,
                    'desc'    => '页数',
                ],
            ],

            'getLiverecord' => [
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
                'p'     => [
                    'name'    => 'p',
                    'type'    => 'int',
                    'min'     => 1,
                    'default' => 1,
                    'desc'    => '页数',
                ],
            ],

            'getAliCdnRecord' => [
                'id' => [
                    'name'    => 'id',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '直播记录ID',
                ],
            ],

            'getUserHome' => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
            ],

            'getContributeList' => [
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
                'p'     => [
                    'name'    => 'p',
                    'type'    => 'int',
                    'default' => '1',
                    'desc'    => '页数',
                ],
            ],

            'getPmUserInfo' => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
            ],

            'getMultiInfo' => [
                'uid'  => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'uids' => [
                    'name'    => 'uids',
                    'type'    => 'string',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID，多个以逗号分割',
                ],
                'type' => [
                    'name'    => 'type',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '关注类型，0 未关注 1 已关注',
                ],
            ],

            'getUidsInfo'  => [
                'uid'  => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'uids' => [
                    'name'    => 'uids',
                    'type'    => 'string',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID，多个以逗号分割',
                ],
            ],
            'Bonus'        => [
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
                    'desc'    => '用户token',
                ],
            ],
            'getBonus'     => [
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
                    'desc'    => '用户token',
                ],
            ],
            'setDistribut' => [
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
                    'desc'    => '用户token',
                ],
                'code'  => [
                    'name'    => 'code',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '邀请码',
                ],
            ],

            'getUserLabel' => [
                'uid'   => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'touid' => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
            ],

            'setUserLabel' => [
                'uid'    => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'  => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户token',
                ],
                'touid'  => [
                    'name'    => 'touid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '对方ID',
                ],
                'labels' => [
                    'name'    => 'labels',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '印象标签ID，多个以逗号分割',
                ],
            ],

            'getMyLabel' => [
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
                    'desc'    => '用户token',
                ],
            ],

            'getUserAccountList' => [
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
                    'desc'    => '用户token',
                ],
            ],

            'setUserAccount' => [
                'uid'     => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'   => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户token',
                ],
                //                'type'         => ['name' => 'type', 'type' => 'int', 'require' => true, 'desc' => '账号类型，1表示支付宝，2表示微信，3表示银行卡'],
                //                'type'         => ['name' => 'type', 'type' => 'int', 'require' => true, 'desc' => '账号类型，1表示支付宝，2表示微信，3表示银行卡'],
                //                'account_bank' => ['name' => 'account_bank', 'type' => 'string', 'default' => '', 'desc' => '银行名称'],
                'account' => [
                    'name'    => 'account',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '账号',
                ],
                'name'    => [
                    'name'    => 'name',
                    'type'    => 'string',
                    'default' => '',
                    'desc'    => '姓名',
                ],
            ],

            'delUserAccount'      => [
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
                    'desc'    => '用户token',
                ],
                'id'    => [
                    'name'    => 'id',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '账号ID',
                ],
            ],
            'sendLocation'        => [
                'uid'  => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'lng'  => [
                    'name'    => 'lng',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '经度值',
                ],
                'lat'  => [
                    'name'    => 'lat',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '纬度值',
                ],
                'city' => [
                    'name'    => 'city',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '城市',
                ],
            ],
            'getMyHome'           => [
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
                    'desc'    => '用户token',
                ],
            ],
            'checkUserPhoneId'    => [
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
                    'desc'    => '用户token',
                ],
            ],
            'getBindPhoneCode'    => [
                'uid'    => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'  => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户token',
                ],
                'mobile' => [
                    'name'    => 'mobile',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '手机号',
                ],
            ],
            'AutoIdCardAuthen'    => [
                'uid'     => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'   => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户token',
                ],
                'name'    => [
                    'name'    => 'name',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '姓名',
                ],
                'id_card' => [
                    'name'    => 'id_card',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '身份证',
                ],
            ],
            'getUserLevel'        => [
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
                    'desc'    => '用户token',
                ],
            ],
            'getUserName'         => [
                'uid'     => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'otherid' => [
                    'name'    => 'otherid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '查询ID',
                ],
                'token'   => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户token',
                ],
            ],
            'transferMoney'       => [
                'uid'     => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'   => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户token',
                ],
                'otherid' => [
                    'name'    => 'otherid',
                    'type'    => 'int',
                    'require' => true,
                    'desc'    => '对方ID',
                ],
                'money'   => [
                    'name'    => 'money',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '钱钱钱钱',
                ],
            ],
            'feedBack'            => [
                'uid'     => [
                    'name'    => 'uid',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '用户ID',
                ],
                'token'   => [
                    'name'    => 'token',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '用户token',
                ],
                'type'    => [
                    'name'    => 'type',
                    'type'    => 'string',
                    'min'     => 1,
                    'max'     => 2,
                    'require' => true,
                    'desc'    => '1注销 2反馈',
                ],
                'content' => [
                    'name'    => 'content',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '内容',
                ],
                'remark'  => [
                    'name'    => 'remark',
                    'type'    => 'string',
                    'require' => true,
                    'desc'    => '备注',
                ],
            ],
            'cashRecord'          => [
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
                    'desc'    => '用户token',
                ],
                'page'  => [
                    'name'    => 'page',
                    'type'    => 'int',
                    'min'     => 1,
                    'require' => true,
                    'desc'    => '页码',
                ],
            ],
            'cashMoney'           => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token',],
            ],
            'transferMoneyRecord' => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token',],
                'page'  => ['name' => 'page', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '页码',],
            ],
            'setHeadPic'          => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token',],
                'url'   => ['name' => 'url', 'type' => 'string', 'require' => true, 'desc' => '头像地址',],
            ],
            'updateField'         => [
                'uid'    => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'token'  => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token',],
                'fields' => ['name' => 'fields', 'type' => 'string', 'require' => true, 'desc' => '修改信息，json字符串',],
            ],
            'getUserInfo'         => [
                'uid'   => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID',],
                'token' => ['name' => 'token', 'type' => 'string', 'require' => true, 'desc' => '用户token',],
                'toUid' => ['name' => 'toUid', 'type' => 'string', 'require' => true, 'desc' => '信息用户ID',],
            ],
        ];
    }

    /**
     * 判断token
     *
     * @desc 用于判断token
     * @return int code 操作码，0表示成功， 1表示用户不存在
     * @return array info
     * @return string msg 提示信息
     */
    public function iftoken(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $checkToken = checkToken($this->uid, $this->token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        return $rs;
    }

    /**
     * 获取用户信息
     *
     * @desc 用于获取单个用户基本信息
     * @return int code 操作码，0表示成功， 1表示用户不存在
     * @return array info
     * @return array info[0] 用户信息
     * @return int info[0].id 用户ID
     * @return string info[0].level 等级
     * @return string info[0].lives 直播数量
     * @return string info[0].follows 关注数
     * @return string info[0].fans 粉丝数
     * @return string info[0].agent_switch 分销开关
     * @return string info[0].family_switch 家族开关
     * @return string msg 提示信息
     */
    public function getBaseInfo(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain = new Domain_User();
        $info   = $domain->getBaseInfo($uid);
        if(!$info){
            $rs['code'] = 700;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $configpri      = getConfigPri();
        $configpub      = getConfigPub();
        $agent_switch   = $configpri['agent_switch'];
        $family_switch  = $configpri['family_switch'];
        $service_switch = $configpri['service_switch'];
        $service_url    = $configpri['service_url'];
        $ios_shelves    = $configpub['ios_shelves'];

        $info['agent_switch']  = $agent_switch;
        $info['family_switch'] = $family_switch;

        /* 是否有店铺 */
        $domain2 = new Domain_Shop();
        $isshop  = $domain2->isShop($uid);
        /* 个人中心菜单 */
        $version_ios = $this->version_ios;
        $list        = [];
        $list1       = [];
        $list2       = [];
        $list3       = [];
        $shelves     = 1;
        /*if($version_ios && $version_ios==$ios_shelves){
            $agent_switch=0;
            $family_switch=0;
            $shelves=0;
        }*/

        $list1[] = [
            'id'    => '19',
            'name'  => '我的视频',
            'thumb' => get_upload_path("/static/appapi/images/personal/video.png"),
            'href'  => '',
        ];
        if($shelves){
            $list1[] = [
                'id'    => '1',
                'name'  => '我的收益',
                'thumb' => get_upload_path("/static/appapi/images/personal/votes.png"),
                'href'  => '',
            ];
        }

        //$list1[]=array('id'=>'2','name'=>'我的'.$configpub['name_coin'],'thumb'=>get_upload_path("/static/appapi/images/personal/coin.png") ,'href'=>'');
        $list1[] = [
            'id'    => '3',
            'name'  => '我的等级',
            'thumb' => get_upload_path("/static/appapi/images/personal/level.png"),
            'href'  => get_upload_path("/Appapi/Level/index"),
        ];

        $list1[] = [
            'id'    => '11',
            'name'  => '我的认证',
            'thumb' => get_upload_path("/static/appapi/images/personal/auth.png"),
            'href'  => get_upload_path("/Appapi/Auth/index"),
        ];

        if($isshop == 1){
            $list1[] = [
                'id'    => '22',
                'name'  => '我的小店',
                'thumb' => get_upload_path("/static/appapi/images/personal/shop.png?t=1"),
                'href'  => '',
            ];
        }else{
            $list1[] = [
                'id'    => '22',
                'name'  => '我的小店',
                'thumb' => get_upload_path("/static/appapi/images/personal/shop.png?t=1"),
                'href'  => get_upload_path("/Appapi/shop/index"),
            ];
        }

        $list2[] = [
            'id'    => '20',
            'name'  => '房间管理',
            'thumb' => get_upload_path("/static/appapi/images/personal/room.png"),
            'href'  => '',
        ];
        if($shelves){
            //$list1[]=array('id'=>'14','name'=>'我的明细','thumb'=>get_upload_path("/static/appapi/images/personal/detail.png") ,'href'=>get_upload_path("/Appapi/Detail/index"));
            //$list2[]=array('id'=>'4','name'=>'在线商城','thumb'=>get_upload_path("/static/appapi/images/personal/shop.png") ,'href'=>get_upload_path("/Appapi/Mall/index"));
            $list2[] = [
                'id'    => '5',
                'name'  => '装备中心',
                'thumb' => get_upload_path("/static/appapi/images/personal/equipment.png"),
                'href'  => get_upload_path("/Appapi/Equipment/index"),
            ];
        }


        if($family_switch){
            $list2[] = [
                'id'    => '6',
                'name'  => '家族中心',
                'thumb' => get_upload_path("/static/appapi/images/personal/family.png"),
                'href'  => get_upload_path("/Appapi/Family/index2"),
            ];
            $list2[] = [
                'id'    => '7',
                'name'  => '家族驻地',
                'thumb' => get_upload_path("/static/appapi/images/personal/family2.png"),
                'href'  => get_upload_path("/Appapi/Family/home"),
            ];
        }

        if($agent_switch){
            $list2[] = [
                'id'    => '8',
                'name'  => '邀请奖励',
                'thumb' => get_upload_path("/static/appapi/images/personal/agent.png"),
                'href'  => get_upload_path("/Appapi/Agent/index"),
            ];
        }
        if($service_switch && $service_url){
            $list3[] = [
                'id'    => '21',
                'name'  => '在线客服(Beta)',
                'thumb' => get_upload_path("/static/appapi/images/personal/kefu.png"),
                'href'  => $service_url,
            ];
        }

        //$list[]=array('id'=>'12','name'=>'关于我们','thumb'=>get_upload_path("/static/appapi/images/personal/about.png") ,'href'=>get_upload_path("/portal/page/lists"));
        $list3[] = [
            'id'    => '13',
            'name'  => '个性设置',
            'thumb' => get_upload_path("/static/appapi/images/personal/set.png"),
            'href'  => '',
        ];

        $list[]        = $list1;
        $list[]        = $list2;
        $list[]        = $list3;
        $info['list']  = $list;
        $rs['info'][0] = $info;

        return $rs;
    }

    /**
     * 头像上传 (七牛)
     *
     * @desc 用于用户修改头像
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string list[0].avatar 用户主头像
     * @return string list[0].avatar_thumb 用户头像缩略图
     * @return string msg 提示信息
     */
    public function updateAvatar(){
        $rs = ['code' => 0, 'msg' => '设置头像成功', 'info' => []];

        $checkToken = checkToken($this->uid, $this->token);
        $scene      = checkNull($this->scene);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        if(!isset($_FILES["file"])){
            $rs['code'] = 1001;
            $rs['msg']  = T('miss upload file');
            return $rs;
        }

        if($_FILES["file"]["error"] > 0){
            $rs['code'] = 1002;
            $rs['msg']  = T('failed to upload file with error: {error}',
                ['error' => $_FILES['file']['error']]);
            DI()->logger->debug('failed to upload file with error: '
                . $_FILES['file']['error']);
            return $rs;
        }

        $uptype = DI()->config->get('app.uptype');

        if($uptype == 1){
            //七牛
//            var_dump($_FILES['file']);
            $url = DI()->qiniu->uploadFile($_FILES['file']['tmp_name']);
//			var_dump(DI()->qiniu);

            if(!empty($url)){
                $avatar       = $url . '?imageView2/2/w/600/h/600'; //600 X 600
                $avatar_thumb = $url . '?imageView2/2/w/200/h/200'; // 200 X 200
                $data         = [
                    "avatar"       => $avatar,
                    "avatar_thumb" => $avatar_thumb,
                ];

                $data2 = [
                    "avatar"       => $avatar,
                    "avatar_thumb" => $avatar_thumb,
                ];


                /* 统一服务器 格式 */
                /* $space_host= DI()->config->get('app.Qiniu.space_host');
                $avatar2=str_replace($space_host.'/', "", $avatar);
                $avatar_thumb2=str_replace($space_host.'/', "", $avatar_thumb);
                $data2=array(
                    "avatar"=>$avatar2,
                    "avatar_thumb"=>$avatar_thumb2,
                ); */
            }
        }elseif($uptype == 2){
            //本地上传
            //设置上传路径 设置方法参考3.2
            DI()->ucloud->set('save_path', 'avatar/' . date("Ymd"));

            //新增修改文件名设置上传的文件名称
            // DI()->ucloud->set('file_name', $this->uid);

            //上传表单名
            $res = DI()->ucloud->upfile($_FILES['file']);

            $files         = '../upload' . $res['file'];
            $newfiles      = str_replace(".png", "_thumb.png", $files);
            $newfiles      = str_replace(".jpg", "_thumb.jpg", $newfiles);
            $newfiles      = str_replace(".gif", "_thumb.gif", $newfiles);
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

            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg

            $PhalApi_Image->thumb(660, 660, IMAGE_THUMB_SCALING);
            $PhalApi_Image->save($files);

            $PhalApi_Image->thumb(200, 200, IMAGE_THUMB_SCALING);
            $PhalApi_Image->save($newfiles);

            $avatar = $res['url']; //600 X 600

            $avatar_thumb = str_replace(".png", "_thumb.png", $avatar);
            $avatar_thumb = str_replace(".jpg", "_thumb.jpg", $avatar_thumb);
            $avatar_thumb = str_replace(".gif", "_thumb.gif", $avatar_thumb);


            $avatar2 = '/api/upload' . $res['file']; //600 X 600

            $avatar_thumb2 = str_replace(".png", "_thumb.png", $avatar2);
            $avatar_thumb2 = str_replace(".jpg", "_thumb.jpg", $avatar_thumb);
            $avatar_thumb2 = str_replace(".gif", "_thumb.gif", $avatar_thumb2);

            $data = [
                "avatar"       => $avatar,
                "avatar_thumb" => $avatar_thumb,
            ];

            $data2 = [
                "avatar"       => $avatar2,
                "avatar_thumb" => $avatar_thumb2,
            ];

        }

        @unlink($_FILES['file']['tmp_name']);
        if(!$data){
            $rs['code'] = 1003;
            $rs['msg']  = '更换失败，请稍候重试';
            return $rs;
        }
        if($scene){
            /* 清除缓存 */
            delCache(Common_Cache::USERINFO . $this->uid);

            $domain = new Domain_User();
            $domain->userUpdate($this->uid, $data2);
            $rs['info'][0] = $data;
        }else{
            $rs['info'][0] = $data2;

        }
        return $rs;

    }

    /**
     * 修改用户头像（wanglin）
     *
     * @desc 用于修改用户头像
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string list[0].msg 修改成功提示信息
     * @return string msg 提示信息
     */
    public function setHeadPic(){
        $rs = ['code' => 0, 'msg' => '设置头像成功', 'info' => []];

        $checkToken = checkToken($this->uid, $this->token);
        $url        = checkNull($this->url);
        if($checkToken == 700){
            $rs['code']          = $checkToken;
            $rs['msg']           = '您的登陆状态失效或账号已被禁用';
            $rs['info']['is_ok'] = '1';
            return $rs;
        }
        $data['avatar']       = $url . '?imageView2/2/w/600/h/600'; //600 X 600
        $data['avatar_thumb'] = $url . '?imageView2/2/w/200/h/200'; // 200 X 200

        $domain = new Domain_User();
        if(!$domain->userUpdate($this->uid, $data)){
            $rs['code']          = 1;
            $rs['msg']           = '修改失败';
            $rs['info']['is_ok'] = '1';
        }else{
            $rs['info']['is_ok'] = '0';
            /* 清除缓存 */
            delCache(Common_Cache::USERINFO . $this->uid);
        }
        return $rs;
    }

    /**
     * 修改用户信息
     *
     * @desc 用于修改用户信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string list[0].msg 修改成功提示信息
     * @return string msg 提示信息
     */
    public function updateFields(){
        $rs         = ['code' => 0, 'msg' => '修改成功', 'info' => []];
        $checkToken = checkToken($this->uid, $this->token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $fields   = json_decode($this->fields, true);
        $is_first = $fields['is_first'] ?? false;
        $allow    = [
            'user_nicename',
            'sex',
            'signature',
            'birthday',
            'location',
            'sex_modifiable',
        ];
        $sp_allow = 'avatar';
        $domain   = new Domain_User();
        $flag     = false;
        foreach($fields as $k => $v){
            if(in_array($k, $allow) || $k === $sp_allow && $flag = true){
                $fields[$k] = checkNull($v);
            }else{
                unset($fields[$k]);
            }
        }
        if($flag){
            if(empty($fields['avatar'])){
                $fields['avatar']       = '/default.jpg';
                $fields['avatar_thumb'] = '/default_thumb.jpg';
            }else{
                $fields['avatar_thumb'] = $fields['avatar'];
            }
            if(empty($fields['user_nicename'])){
                $user_login              = getUserLogin($this->uid);
                $fields['user_nicename'] = '手机用户' . substr($user_login, -4);
            }
            if(empty($fields['sex'])){
                $fields['sex'] = 2;
            }
        }
        if(array_key_exists('user_nicename', $fields)){
            if($fields['user_nicename'] == ''){
                $rs['code'] = 1002;
                $rs['msg']  = '昵称不能为空';
                return $rs;
            }
            $isexist = $domain->checkName($this->uid, $fields['user_nicename']);
            if(!$isexist){
                $rs['code'] = 1002;
                $rs['msg']  = '昵称重复，请修改';
                return $rs;
            }
            //$fields['user_nicename']=filterField($fields['user_nicename']);
            $sensitivewords = sensitiveField($fields['user_nicename']);
            if($sensitivewords == 1001){
                $rs['code'] = 10011;
                $rs['msg']  = '输入非法，请重新输入';
                return $rs;
            }
        }
        if(array_key_exists('signature', $fields)){
            $sensitivewords = sensitiveField($fields['signature']);
            if($sensitivewords == 1001){
                $rs['code'] = 10011;
                $rs['msg']  = '输入非法，请重新输入';
                return $rs;
            }
        }

        if(array_key_exists('birthday', $fields)){
            $fields['birthday'] = strtotime($fields['birthday']);
        }

        if(isset($fields['sex']) && !empty($fields['sex']) && $is_first){
            $fields['sex_modifiable'] = 0;
            unset($fields['is_first']);
        }

        $info = $domain->userUpdate($this->uid, $fields);

        if($info === false){
            $rs['code'] = 1001;
            $rs['msg']  = '修改失败';
            return $rs;
        }
        /* 清除缓存 */
        delCache(Common_Cache::USERINFO . $this->uid);
//		$rs['info'][0]['msg']='修改成功';
        $rs['info'][0] = $domain->getBaseInfo($this->uid);
        return $rs;
    }

    /**
     * 修改密码
     *
     * @desc 用于修改用户信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string list[0].msg 修改成功提示信息
     * @return string msg 提示信息
     */
    public function updatePass(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid     = $this->uid;
        $token   = $this->token;
        $oldpass = $this->oldpass;
        $pass    = $this->pass;
        $pass2   = $this->pass2;

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        if($pass != $pass2){
            $rs['code'] = 1002;
            $rs['msg']  = '两次新密码不一致';
            return $rs;
        }

        $check = passcheck($pass);
        if(!$check){
            $rs['code'] = 1004;
            $rs['msg']  = '密码为6-20位字母数字组合';
            return $rs;
        }

        $domain = new Domain_User();
        $info   = $domain->updatePass($uid, $oldpass, $pass);

        if($info == 1003){
            $rs['code'] = 1003;
            $rs['msg']  = '旧密码错误';
            return $rs;
        }elseif($info === false){
            $rs['code'] = 1001;
            $rs['msg']  = '修改失败';
            return $rs;
        }

        $rs['info'][0]['msg'] = '修改成功';
        return $rs;
    }

    /**
     * 我的钻石
     *
     * @desc 用于获取用户余额,充值规则 支付方式信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].coin 用户余额
     * @return array info[0].rules 充值规则
     * @return string info[0].rules[].id 充值规则
     * @return string info[0].rules[].coin 钻石
     * @return string info[0].rules[].money 价格
     * @return string info[0].rules[].money_ios 苹果充值价格
     * @return string info[0].rules[].product_id 苹果项目ID
     * @return string info[0].rules[].give 赠送钻石，为0时不显示赠送
     * @return string info[0].aliapp_switch 支付宝开关，0表示关闭，1表示开启
     * @return string info[0].aliapp_partner 支付宝合作者身份ID
     * @return string info[0].aliapp_seller_id 支付宝帐号
     * @return string info[0].aliapp_key_android 支付宝安卓密钥
     * @return string info[0].aliapp_key_ios 支付宝苹果密钥
     * @return string info[0].wx_switch 微信支付开关，0表示关闭，1表示开启
     * @return string info[0].wx_appid 开放平台账号AppID
     * @return string info[0].wx_appsecret 微信应用appsecret
     * @return string info[0].wx_mchid 微信商户号mchid
     * @return string info[0].wx_key 微信密钥key
     * @return string msg 提示信息
     */
    public function getBalance(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $type        = checkNull($this->type);
        $version_ios = checkNull($this->version_ios);

        $checkToken = checkToken($this->uid, $this->token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain = new Domain_User();
        $info   = $domain->getBalance($this->uid);

        $key   = 'Charge:getChargeRules';
        $rules = getcaches($key);
        if(!$rules){
            $rules = $domain->getChargeRules();
            setcaches($key, $rules);
        }
        $info['rules'] = $rules;

        $configpub = getConfigPub();
        $configpri = getConfigPri();

        $aliapp_switch = $configpri['aliapp_switch'];

        $info['aliapp_switch']      = $aliapp_switch;
        $info['aliapp_partner']     = $aliapp_switch == 1
            ? $configpri['aliapp_partner'] : '';
        $info['aliapp_seller_id']   = $aliapp_switch == 1
            ? $configpri['aliapp_seller_id'] : '';
        $info['aliapp_key_android'] = $aliapp_switch == 1
            ? $configpri['aliapp_key_android'] : '';
        $info['aliapp_key_ios']     = $aliapp_switch == 1
            ? $configpri['aliapp_key_ios'] : '';

        $wx_switch            = $configpri['wx_switch'];
        $info['wx_switch']    = $wx_switch;
        $info['wx_appid']     = $wx_switch == 1 ? $configpri['wx_appid'] : '';
        $info['wx_appsecret'] = $wx_switch == 1 ? $configpri['wx_appsecret']
            : '';
        $info['wx_mchid']     = $wx_switch == 1 ? $configpri['wx_mchid'] : '';
        $info['wx_key']       = $wx_switch == 1 ? $configpri['wx_key'] : '';
        $info['votes']        = (string)round($info['votes'] / 100, 2);

        $aliscan_switch = $configpri['aliscan_switch'];
        /* 支付列表 */
        $shelves     = 1;
        $ios_shelves = $configpub['ios_shelves'];
        if($version_ios && $version_ios == $ios_shelves){
            $shelves = 0;
        }

        $paylist = [];

//        if($aliapp_switch && $shelves){
        $paylist[] = [
            'id'    => 'ali',
            'name'  => '支付宝支付',
            'thumb' => get_upload_path("/static/app/pay/ali.png"),
            'href'  => '',
        ];
//        }

//        if($wx_switch && $shelves){
        $paylist[] = [
            'id'    => 'wx',
            'name'  => '微信支付',
            'thumb' => get_upload_path("/static/app/pay/wx.png"),
            'href'  => '',
        ];
//        }

        // if($aliscan_switch && $shelves){
        // $paylist[]=[
        // 'id'=>'2',
        // 'name'=>'当面付',
        // 'thumb'=>get_upload_path("/static/app/pay/ali.png"),
        // 'href'=>get_upload_path("/appapi/aliscan/index"),
        // ];
        // }

        $ios_switch = $configpri['ios_switch'];

//        if(($ios_switch || $shelves == 0) && $type == 1){
//            $paylist[] = [
//                'id'    => 'apple',
//                'name'  => '苹果支付',
//                'thumb' => get_upload_path("/static/app/pay/apple.png"),
//                'href'  => '',
//            ];
//        }

        /* $paylist[]=[
                'id'=>'1',
                'name'=>'测试1',
                'thumb'=>get_upload_path("/static/app/pay/apple.png"),
                'href'=>'https://livenew.yunbaozb.com/portal/page/index?id=31',
            ]; */

        $info['paylist'] = $paylist;
        $info['tip_t']   = $configpub['name_coin'] . '/'
            . $configpub['name_score'] . '说明:';
        $info['tip_d']   = $configpub['name_coin'] . '可通过平台提供的支付方式进行充值获得，'
            . $configpub['name_coin'] . '适用于平台内所有消费； '
            . $configpub['name_score'] . '可通过直播间内游戏奖励获得，所得'
            . $configpub['name_score'] . '可用于平台商城内兑换会员、坐 骑、靓号等服务，不可提现。';


        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * 我的收益
     *
     * @desc 用于获取用户收益，包括可体现金额，今日可提现金额
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].votes 可提取映票数
     * @return string info[0].votestotal 总映票
     * @return string info[0].cash_rate 映票兑换比例
     * @return string info[0].total 可体现金额
     * @return string info[0].tips 温馨提示
     * @return string msg 提示信息
     */
    public function getProfit(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $checkToken = checkToken($this->uid, $this->token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain = new Domain_User();
        $info   = $domain->getProfit($this->uid);

        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * 用户提现（wanglin）
     *
     * @desc 用于进行用户提现
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].msg 提现成功信息
     * @return string msg 提示信息
     */
    public function setCash(){
        $rs = ['code' => 0, 'msg' => '提现成功', 'info' => []];

        $uid      = checkNull($this->uid);
        $token    = checkNull($this->token);
        $cashvote = checkNull($this->cashvote);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $data   = [
            'uid'      => $uid,
            'cashvote' => $cashvote,
        ];
        $domain = new Domain_User();
        list($rs['code'], $rs['msg']) = $domain->setCash($data);
        return $rs;
    }
//    public function setCash(){
//        $rs = ['code' => 0, 'msg' => '提现成功', 'info' => []];
//
//        $uid       = checkNull($this->uid);
//        $token     = checkNull($this->token);
////        $accountid = checkNull($this->accountid);
//        $cashvote  = checkNull($this->cashvote);
//
//        $checkToken = checkToken($uid, $token);
//        if($checkToken == 700){
//            $rs['code'] = $checkToken;
//            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
//            return $rs;
//        }
//
////        if(!$accountid){
////            $rs['code'] = 1001;
////            $rs['msg']  = '请选择提现账号';
////            return $rs;
////        }
//
//        if(!$cashvote){
//            $rs['code'] = 1002;
//            $rs['msg']  = '请输入有效的提现票数';
//            return $rs;
//        }
//
//        $data   = [
//            'uid'       => $uid,
////            'accountid' => $accountid,
//            'cashvote'  => $cashvote,
//        ];
//        $config = getConfigPri();
//        $domain = new Domain_User();
//        $info   = $domain->setCash($data);
//        if($info == 1001){
//            $rs['code'] = 1001;
//            $rs['msg']  = '余额不足';
//            return $rs;
//        }elseif($info == 1003){
//            $rs['code'] = 1003;
//            $rs['msg']  = '请先进行身份认证';
//            return $rs;
//        }elseif($info == 1004){
//            $rs['code'] = 1004;
//            $rs['msg']  = '单次提现必须100元以上,且只能整百提现';
//            return $rs;
//        }elseif($info == 1005){
//            $rs['code'] = 1005;
//            $rs['msg']  = '不在提现期限内，不能提现';
//            return $rs;
//        }elseif($info == 1006){
//            $rs['code'] = 1006;
//            $rs['msg']  = '每月只可提现' . $config['cash_max_times'] . '次,已达上限';
//            return $rs;
//        }elseif($info == 1007){
//            $rs['code'] = 1007;
//            $rs['msg']  = '提现账号信息不正确';
//            return $rs;
//        }elseif(!$info){
//            $rs['code'] = 1002;
//            $rs['msg']  = '提现失败，请重试';
//            return $rs;
//        }
//
//        $rs['info'][0]['msg'] = '提现成功';
//        return $rs;
//    }

    /**
     * 判断是否关注
     *
     * @desc 用于判断是否关注
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].isattent 关注信息，0表示未关注，1表示已关注
     * @return string msg 提示信息
     */
    public function isAttent(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $info = isAttention($this->uid, $this->touid);

        $rs['info'][0]['isattent'] = (string)$info;
        return $rs;
    }

    /**
     * 关注/取消关注
     *
     * @desc 用于关注/取消关注
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].isattent 关注信息，0表示未关注，1表示已关注
     * @return string msg 提示信息
     */
    public function setAttent(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        if($this->uid == $this->touid){
            $rs['code'] = 1001;
            $rs['msg']  = '不能关注自己';
            return $rs;
        }
        $domain = new Domain_User();
        $info   = $domain->setAttent($this->uid, $this->touid);

        $rs['info'][0]['isattent'] = (string)$info;
        return $rs;
    }

    /**
     * 判断是否拉黑
     *
     * @desc 用于判断是否拉黑
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].isattent  拉黑信息,0表示未拉黑，1表示已拉黑
     * @return string msg 提示信息
     */
    public function isBlacked(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $info = isBlack($this->uid, $this->touid);

        $rs['info'][0]['isblack'] = (string)$info;
        return $rs;
    }

    /**
     * 检测拉黑状态
     *
     * @desc 用于私信聊天时判断私聊双方的拉黑状态
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].u2t  是否拉黑对方,0表示未拉黑，1表示已拉黑
     * @return string info[0].t2u  是否被对方拉黑,0表示未拉黑，1表示已拉黑
     * @return string msg 提示信息
     */
    public function checkBlack(){
        $rs    = ['code' => 0, 'msg' => '', 'info' => []];
        $uid   = $this->uid;
        $touid = $this->touid;
        $u2t   = isBlack($uid, $touid);
        $t2u   = isBlack($touid, $uid);

        $userAttentionModel                 = new Model_UserAttention();
        $mutualNums                         = $userAttentionModel->mutual($uid, $touid);
        $rs['info'][0]['is_mutual_follows'] = $mutualNums > 1 ? '1' : '2';
        $rs['info'][0]['u2t']               = (string)$u2t;
        $rs['info'][0]['t2u']               = (string)$t2u;
        return $rs;
    }

    /**
     * 拉黑/取消拉黑
     *
     * @desc 用于拉黑/取消拉黑
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].isblack 拉黑信息,0表示未拉黑，1表示已拉黑
     * @return string msg 提示信息
     */
    public function setBlack(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $domain = new Domain_User();
        $info   = $domain->setBlack($this->uid, $this->touid);

        $rs['info'][0]['isblack'] = (string)$info;
        return $rs;
    }

    /**
     * 获取找回密码短信验证码
     *
     * @desc 用于找回密码获取短信验证码
     * @return int code 操作码，0表示成功,2发送失败
     * @return array info
     * @return array info[0]
     * @return string msg 提示信息
     */

    public function getBindCode(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $mobile = $this->mobile;

        $ismobile = checkMobile($mobile);
        if(!$ismobile){
            $rs['code'] = 1001;
            $rs['msg']  = '请输入正确的手机号';
            return $rs;
        }

        if($_SESSION['set_mobile'] == $mobile
            && $_SESSION['set_mobile_expiretime'] > time()
        ){
            $rs['code'] = 1002;
            $rs['msg']  = '验证码5分钟有效，请勿多次发送';
            return $rs;
        }

        $mobile_code = random(4, 1);
        $redis       = DI()->redis;
        $mobile_key  = 'set_mobile_' . $mobile;
        /* 发送验证码 */
        $result = sendCode($mobile, $mobile_code);
        if($result['code'] == 0){
            $redis->set($mobile_key, $mobile_code, 300);
        }elseif($result['code'] == 667){
            $redis->set($mobile_key, $mobile_code, 300);
            $rs['code'] = 1002;
            $rs['msg']  = '验证码为：' . $result['msg'];
        }else{
            $rs['code'] = 1002;
            $rs['msg']  = $result['msg'];
        }
        return $rs;
    }

    /**
     * 绑定手机号
     *
     * @desc 用于用户绑定手机号
     * @return int code 操作码，0表示成功，非0表示有错误
     * @return array info
     * @return object info[0].msg 绑定成功提示
     * @return string msg 提示信息
     */
    public function setMobile(){

        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $mobile_key  = 'set_mobile_' . $this->mobile;
        $redis       = DI()->redis;
        $mobile_code = $redis->get($mobile_key);

        if($mobile_code != $this->code){
            $rs['code'] = 10001;
            $rs['msg']  = '验证码错误';
            return $rs;
        }

        $checkToken = checkToken($this->uid, $this->token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $domain = new Domain_User();

        $where = ['mobile' => $this->mobile];
        if(checkUser($where)){
            $rs['code'] = 1002;
            $rs['msg']  = '该手机号已被绑定';
            return $rs;
        }
        //更新数据库
        $data   = ["mobile" => $this->mobile];
        $result = $domain->userUpdate($this->uid, $data);
        if($result === false){
            $rs['code'] = 1003;
            $rs['msg']  = '绑定失败';
            return $rs;
        }
        Domain_Msg::addMsg('绑定成功', Common_JPush::BDSJ, $this->uid);
        $rs['info'][0]['msg'] = '绑定成功';

        return $rs;
    }

    /**
     * 关注列表
     *
     * @desc 用于获取用户的关注列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[].isattent 是否关注,0表示未关注，1表示已关注
     * @return string msg 提示信息
     */
    public function getFollowsList(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $domain = new Domain_User();
        $info   = $domain->getFollowsList($this->uid, $this->touid, $this->p);

        $rs['info'] = $info;
        return $rs;
    }

    /**
     * 粉丝列表
     *
     * @desc 用于获取用户的关注列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[].isattent 是否关注,0表示未关注，1表示已关注
     * @return string msg 提示信息
     */
    public function getFansList(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $domain = new Domain_User();
        $info   = $domain->getFansList($this->uid, $this->touid, $this->p);

        $rs['info'] = $info;
        return $rs;
    }

    /**
     * 黑名单列表
     *
     * @desc 用于获取用户的名单列表
     * @return int code 操作码，0表示成功
     * @return array info 用户基本信息
     * @return string msg 提示信息
     */
    public function getBlackList(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $domain = new Domain_User();
        $info   = $domain->getBlackList($this->uid, $this->touid, $this->p);

        $rs['info'] = $info;
        return $rs;
    }

    /**
     * 直播记录
     *
     * @desc 用于获取用户的直播记录
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[].nums 观看人数
     * @return string info[].datestarttime 格式化的开播时间
     * @return string info[].dateendtime 格式化的结束时间
     * @return string info[].video_url 回放地址
     * @return string info[].file_id 回放标示
     * @return string msg 提示信息
     */
    public function getLiverecord(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $domain = new Domain_User();
        $info   = $domain->getLiverecord($this->touid, $this->p);

        $rs['info'] = $info;
        return $rs;
    }

    /**
     *获取阿里云cdn录播地址
     *
     * @desc 如果使用的阿里云cdn，则使用该接口获取录播地址
     * @return int code 操作码，0表示成功
     * @return string info[0].url 录播视频地址
     * @return string msg 提示信息
     */
    public function getAliCdnRecord(){

        $rs     = ['code' => 0, 'msg' => '', 'info' => []];
        $domain = new Domain_Cdnrecord();
        $info   = $domain->getCdnRecord($this->id);

        if(!$info['video_url']){
            $rs['code'] = 1002;
            $rs['msg']  = '直播回放不存在';
            return $rs;
        }

        $rs['info'][0]['url'] = $info['video_url'];

        return $rs;
    }


    /**
     * 个人主页
     *
     * @desc 用于获取个人主页数据
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].uid 用户id
     * @return string info[0].display_id 显示id
     * @return string info[0].follows 关注数
     * @return string info[0].signature 个性签名
     * @return string info[0].level 等级
     * @return string info[0].level_thumb 等级图标
     * @return string info[0].consumption 送礼
     * @return string info[0].votestotal 收礼
     * @return string info[0].verify 是否加V认证 0 未认证 1 已认证
     * @return string info[0].isattention 是否关注，0表示未关注，1表示已关注
     * @return string info[0].isblack 我是否拉黑对方，0表示未拉黑，1表示已拉黑
     * @return string info[0].isblack2 对方是否拉黑我，0表示未拉黑，1表示已拉黑
     * @return array info[0].contribute 贡献榜前三
     * @return array info[0].contribute[].avatar 头像
     * @return string info[0].is_live 是否正在直播，0表示未直播，1表示直播
     * @return array info[0].label 印象标签
     * @return string msg 提示信息
     */
    public function getUserHome(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = checkNull($this->uid);
        $touid = checkNull($this->touid);

        $domain = new Domain_User();
        $info   = $domain->getUserHome($uid, $touid);

        /* 守护 */
        $data = [
            "liveuid" => $touid,
        ];

        $domain_guard = new Domain_Guard();
        $guardlist    = $domain_guard->getGuardList($data);

        $info['guardlist']  = array_slice($guardlist, 0, 3);
        $info['votestotal'] = (string)round($info['votestotal'] / 100, 2);

        $info['label'] = [];
        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * 贡献榜
     *
     * @desc 用于获取贡献榜
     * @return int code 操作码，0表示成功
     * @return array info 排行榜列表
     * @return string info[].total 贡献总数
     * @return string info[].userinfo 用户信息
     * @return string msg 提示信息
     */
    public function getContributeList(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $domain = new Domain_User();
        $info   = $domain->getContributeList($this->touid, $this->p);

        $rs['info'] = $info;
        return $rs;
    }

    /**
     * 私信用户信息
     *
     * @desc 用于获取其他用户基本信息
     * @return int code 操作码，0表示成功，1表示用户不存在
     * @return array info
     * @return string info[0].id 用户ID
     * @return string info[0].isattention 我是否关注对方，0未关注，1已关注
     * @return string info[0].isattention2 对方是否关注我，0未关注，1已关注
     * @return string msg 提示信息
     */
    public function getPmUserInfo(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $info = getUserInfo($this->touid);
        if(empty($info)){
            $rs['code'] = 1001;
            $rs['msg']  = T('user not exists');
            return $rs;
        }
        $info['isattention2'] = (string)isAttention($this->touid, $this->uid);
        $info['isattention']  = (string)isAttention($this->uid, $this->touid);

        $rs['info'][0] = $info;

        return $rs;
    }

    /**
     * 获取多用户信息
     *
     * @desc 用于获取获取多用户信息
     * @return int code 操作码，0表示成功
     * @return array info 排行榜列表
     * @return string info[].utot 是否关注，0未关注，1已关注
     * @return string info[].ttou 对方是否关注我，0未关注，1已关注
     * @return string msg 提示信息
     */
    public function getMultiInfo(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $configpri = getConfigPri();

        if($configpri['letter_switch'] != 1){
            return $rs;
        }

        $uids = explode(",", $this->uids);

        foreach($uids as $k => $userId){
            if($userId){
                $userinfo = getUserInfo($userId);
                if($userinfo){
                    $userinfo['utot'] = isAttention($this->uid, $userId);

                    $userinfo['ttou'] = isAttention($userId, $this->uid);

                    if($userinfo['utot'] == $this->type){
                        $rs['info'][] = $userinfo;
                    }
                }
            }
        }

        return $rs;
    }

    /**
     * 获取多用户信息(不区分是否关注)
     *
     * @desc 用于获取多用户信息
     * @return int code 操作码，0表示成功
     * @return array info 排行榜列表
     * @return string info[].utot 是否关注，0未关注，1已关注
     * @return string info[].ttou 对方是否关注我，0未关注，1已关注
     * @return string msg 提示信息
     */
    public function getUidsInfo(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uids = explode(",", $this->uids);

        foreach($uids as $k => $userId){
            if($userId){
                $userinfo = getUserInfo($userId);
                if($userinfo){
                    $userinfo['utot'] = isAttention($this->uid, $userId);

                    $userinfo['ttou'] = isAttention($userId, $this->uid);

                    $rs['info'][] = $userinfo;

                }
            }
        }

        return $rs;
    }

    /**
     * 登录奖励
     *
     * @desc 用于用户登录奖励
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].bonus_switch 登录开关，0表示未开启
     * @return string info[0].bonus_day 登录天数,0表示已奖励
     * @return string info[0].count_day 连续登陆天数
     * @return string info[0].bonus_list 登录奖励列表
     * @return string info[0].bonus_list[].day 登录天数
     * @return string info[0].bonus_list[].coin 登录奖励
     * @return string msg 提示信息
     */
    public function Bonus(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = checkNull($this->uid);
        $token = checkNull($this->token);
        //file_put_contents(API_ROOT.'/Runtime/LoginBonus_'.date('Y-m-d').'.txt',date('Y-m-d H:i:s').' 提交参数信息 uid:'.json_encode($uid)."\r\n",FILE_APPEND);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_User();
        $info   = $domain->LoginBonus($uid);

        $rs['info'][0] = $info;

        return $rs;
    }

    /**
     * 登录奖励
     *
     * @desc 用于用户登录奖励
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].bonus_switch 登录开关，0表示未开启
     * @return string info[0].bonus_day 登录天数,0表示已奖励
     * @return string msg 提示信息
     */
    public function getBonus(){
        $rs = ['code' => 0, 'msg' => '领取成功', 'info' => []];

        $uid   = checkNull($this->uid);
        $token = checkNull($this->token);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_User();
        $info   = $domain->getLoginBonus($uid);

        if(!$info){
            $rs['code'] = 1001;
            $rs['msg']  = '领取失败';
            return $rs;
        }

        return $rs;
    }

    /**
     * 设置分销上级
     *
     * @desc 用于用户首次登录设置分销关系
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].msg 提示信息
     * @return string msg 提示信息
     */
    public function setDistribut(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = $this->uid;
        $token = checkNull($this->token);
        $code  = checkNull($this->code);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        if($code == ''){
            $rs['code'] = 1001;
            $rs['msg']  = '请输入邀请码';
            return $rs;
        }

        $domain = new Domain_User();
        $info   = $domain->setDistribut($uid, $code);
        if($info == 1004){
            $rs['code'] = 1004;
            $rs['msg']  = '已设置，不能更改';
            return $rs;
        }

        if($info == 1002){
            $rs['code'] = 1002;
            $rs['msg']  = '邀请码错误';
            return $rs;
        }

        if($info == 1003){
            $rs['code'] = 1003;
            $rs['msg']  = '不能填写自己下级的邀请码';
            return $rs;
        }

        $rs['info'][0]['msg'] = '设置成功';

        return $rs;
    }

    /**
     * 获取用户间印象标签
     *
     * @desc 用于获取用户间印象标签
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[].id 标签ID
     * @return string info[].name 名称
     * @return string info[].colour 色值
     * @return string info[].ifcheck 是否选择
     * @return string msg 提示信息
     */
    public function getUserLabel(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = checkNull($this->uid);
        $touid = checkNull($this->touid);

        $key   = "getUserLabel_" . $uid . '_' . $touid;
        $label = getcaches($key);

        if(!$label){
            $domain = new Domain_User();
            $info   = $domain->getUserLabel($uid, $touid);
            $label  = $info['label'];
            setcaches($key, $label);
        }

        $label_check = preg_split('/,|，/', $label);

        $label_check = array_filter($label_check);

        $label_check = array_values($label_check);


        $key2       = "getImpressionLabel";
        $label_list = getcaches($key2);
        if(!$label_list){
            $domain     = new Domain_User();
            $label_list = $domain->getImpressionLabel();
        }

        foreach($label_list as $k => $v){
            $ifcheck = '0';
            if(in_array($v['id'], $label_check)){
                $ifcheck = '1';
            }
            $label_list[$k]['ifcheck'] = $ifcheck;
        }

        $rs['info'] = $label_list;

        return $rs;
    }

    /**
     * 获取个性设置列表
     *
     * @desc 用于获取个性设置列表
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function getPerSetting(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $domain = new Domain_User();
        $info   = $domain->getPerSetting();

        $info[] = [
            'id'    => '17',
            'name'  => '意见反馈',
            'thumb' => '',
            'href'  => get_upload_path('/Appapi/feedback/index'),
        ];
        $info[] = ['id' => '15', 'name' => '修改密码', 'thumb' => '', 'href' => ''];
        $info[] = ['id' => '18', 'name' => '清除缓存', 'thumb' => '', 'href' => ''];
        $info[] = ['id' => '16', 'name' => '检查更新', 'thumb' => '', 'href' => ''];


        $rs['info'] = $info;

        return $rs;
    }

    /**
     * 获取用户提现账号（wanglin）
     *
     * @desc 用于获取用户提现账号
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[].id 账号ID
     * @return string info[].account 账号
     * @return string info[].name 姓名
     * @return string msg 提示信息
     */
    public function getUserAccountList(){
        $rs = ['code' => 0, 'msg' => '', 'info' => []];

        $uid   = checkNull($this->uid);
        $token = checkNull($this->token);


        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }


        $domain = new Domain_User();
        $info   = $domain->getUserAccountList($uid);

        $rs['info'] = $info;

        return $rs;
    }

    /**
     * 设置用户提现账号（wanglin）
     *
     * @desc 用于设置用户提现账号
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function setUserAccount(){
        $rs = ['code' => 0, 'msg' => '设置成功', 'info' => []];

        $uid   = checkNull($this->uid);
        $token = checkNull($this->token);

//        $type         = checkNull($this->type);
//        $account_bank = checkNull($this->account_bank);
        $account = checkNull($this->account);
        $name    = checkNull($this->name);

        $type = 1;

//        if($type == 3){
//            if($account_bank == ''){
//                $rs['code'] = 1001;
//                $rs['msg']  = '银行名称不能为空';
//                return $rs;
//            }
//        }

        if($account == ''){
            $rs['code'] = 1002;
            $rs['msg']  = '账号不能为空';
            return $rs;
        }


        if(mb_strlen($account) > 40){
            $rs['code'] = 1002;
            $rs['msg']  = '账号长度不能超过40个字符';
            return $rs;
        }

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $data = [
            'uid'     => $uid,
            'type'    => $type,
            //            'account_bank' => $account_bank,
            'account' => $account,
            'name'    => $name,
            'addtime' => time(),
        ];

        $domain  = new Domain_User();
        $where   = [
            'uid'     => $uid,
            'type'    => $type,
            //            'account_bank' => $account_bank,
            'account' => $account,
        ];
        $isexist = $domain->getUserAccount($where);
        if($isexist){
            $rs['code'] = 1004;
            $rs['msg']  = '账号已存在';
            return $rs;
        }

        $result = $domain->setUserAccount($data);

        if(!$result){
            $rs['code'] = 1003;
            $rs['msg']  = '设置失败，请重试';
            return $rs;
        }

        $rs['info'][0] = $result;

        return $rs;
    }


    /**
     * 删除用户提现账号
     *
     * @desc 用于删除用户提现账号
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function delUserAccount(){
        $rs = ['code' => 0, 'msg' => '删除成功', 'info' => []];

        $uid   = checkNull($this->uid);
        $token = checkNull($this->token);

        $id = checkNull($this->id);

        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $data = [
            'uid' => $uid,
            'id'  => $id,
        ];

        $domain = new Domain_User();
        $result = $domain->delUserAccount($data);

        if(!$result){
            $rs['code'] = 1003;
            $rs['msg']  = '删除失败，请重试';
            return $rs;
        }

        return $rs;
    }

    /**
     * 保存用户定位
     *
     * @desc 用于保存用户经纬度
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string list[0].msg 修改成功提示信息
     * @return string msg 提示信息
     */
    public function sendLocation(){
        $rs     = ['code' => 0, 'msg' => '', 'info' => []];
        $uid    = checkNull($this->uid);
        $lng    = checkNull($this->lng);
        $lat    = checkNull($this->lat);
        $city   = checkNull($this->city);
        $fields = [
            'lng'  => $lng,
            'lat'  => $lat,
            'city' => $city,
        ];
        $domain = new Domain_User();
        $info   = $domain->userUpdate($uid, $fields);
        if($info === false){
            $rs['code'] = 1001;
            $rs['msg']  = '修改失败';
            return $rs;
        }
        return $rs;
    }

    /**
     * 我的主页
     *
     * @desc 用于获取我的主页
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].uid 用户id
     * @return string info[0].display_id 显示id
     * @return string info[0].sex 性别
     * @return string info[0].follows 关注数
     * @return string info[0].signature 个性签名
     * @return string info[0].level 等级
     * @return string info[0].level_thumb 等级图标
     * @return string info[0].coin 余额
     * @return string info[0].votestotal 收益
     * @return string info[0].verify 是否加V认证 0 未认证 1 已认证
     * @return string info[0].auth_status 身份认证状态 0 未提交 1 待审核 2 认证通过 3 认证失败
     * @return array info[0].contribute 贡献榜前三
     * @return array info[0].contribute[].avatar 头像
     * @return array info[0].label 印象标签
     * @return array info[0].agent_money 代理金币余额
     * @return array info[0].family_state 用户家族状态 0 无家族 1 家族成员 2 家族族长
     * @return string msg 提示信息
     */
    public function getMyHome(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain        = new Domain_User();
        $info          = $domain->getMyHome($uid);
        $info['label'] = [];
        $rs['info'][0] = $info;
        return $rs;
    }

    /**
     * 验证手机号和身份证（汪林）
     *
     * @desc 用于验证手机号和身份证
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[].type 0通过 1手机和身份证都未认证 2身份证未认证 3 身份认证中 4 身份认证失败
     * @return string info[].mobile 手机号
     * @return string msg 提示信息
     */
    public function checkUserPhoneId(){
//        checkIdCard('汪林','500102199309144633');
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain     = new Domain_User();
        $checkRes   = $domain->checkUserPhoneId($uid);
        $rs['info'] = $checkRes;
        if($checkRes['type'] > 0){
            if($checkRes['type'] === 1){ // 0通过 1手机  2身份证
                $rs['msg'] = '请绑定手机号';
            }elseif($checkRes['type'] === 2){
                $rs['msg'] = '请先进行身份认证或等待审核';
            }
        }
        return $rs;
    }

    /**
     * 获取绑定手机验证码（汪林）
     *
     * @desc 用于获取绑定手机验证码
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function getBindPhoneCode(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $mobile     = checkNull($this->mobile);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }

        $where     = 'id = ' . $uid;
        $user_info = checkUser($where);
        if($user_info && !empty($user_info['phone'])){
            $rs['code'] = 1001;
            $rs['msg']  = '您已绑定手机号，请勿重复绑定';
            return $rs;
        }

        $where = 'mobile = ' . $mobile;
        if(checkUser($where)){
            $rs['code'] = 1002;
            $rs['msg']  = '该手机号已被绑定';
            return $rs;
        }
        $mobile_key  = 'set_mobile_' . $mobile;
        $mobile_code = random(4, 1);

        /* 发送验证码 */
        $result = sendCode($mobile, $mobile_code);
        $redis  = DI()->redis;
        if($result['code'] === 0){
            $redis->set($mobile_key, $mobile_code, 300);
        }elseif($result['code'] == 667){
            $redis->set($mobile_key, $mobile_code, 300);

            $rs['code'] = 1002;
            $rs['msg']  = '验证码为：' . $result['msg'];

        }else{
            $rs['code'] = 1002;
            $rs['msg']  = $result['msg'];
        }

        return $rs;
    }

    /**
     * 实名自动认证（汪林）
     *
     * @desc 用于实名自动认证
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string msg 提示信息
     */
    public function AutoIdCardAuthen(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $name       = checkNull($this->name);
        $idCard     = checkNull($this->id_card);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_User();
        $key    = Common_Cache::CHECK_NAME . $uid;
        $time   = time() - strtotime(date('Y-m-d 23:59:59'));
        $nums   = DI()->redis->INCR($key);
        $nums == 1 && DI()->redis->Expire($key, $time - time());
        if($nums > 5){
            $rs['code'] = 1;
            $rs['msg']  = '每日最多可认证5次';
            return $rs;
        }
        list($code, $msg) = $domain->autoIdCardAuthen($uid, $name, $idCard);
        if($code > 0){
            $push_model = new Common_JPush($uid);
            $push_model->sendAlias('认证失败',
                sprintf(Common_JPush::RZFAIL, '123456'));
            $rs['code'] = 1001;
        }else{
            DI()->redis->del($key);
            $push_model = new Common_JPush($uid);
            $push_model->sendAlias('认证成功', Common_JPush::RZPASSES);
        }

        $rs['msg'] = $msg;
        return $rs;
    }

    /**
     * 获取当前经验和等级上限值
     *
     * @desc 用于获取当前等级和等级上限值
     * @return int code 操作码，0表示成功
     * @return int info.consumption 当前经验
     * @return int info.level_list.levelname 当前经验值
     * @return int info.level_list.level_up 等级经验
     * @return string msg 提示信息
     */
    public function getUserLevel(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain     = new Domain_User();
        $res        = $domain->getUserLevel($uid);
        $rs['info'] = $res;
        return $rs;
    }

    /**
     * 根据ID获取用户名（汪林）
     *
     * @desc 用于根据ID获取用户名
     * @return int code 操作码，0表示成功
     * @return int info.name 用户吗
     * @return string msg 提示信息
     */
    public function getUserName(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $otherid    = checkNull($this->otherid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_User();
        list($code, $name) = $domain->getUserName($otherid);
        if($code > 0){
            $rs['code'] = $code;
            $rs['msg']  = $name;
        }else{
            $rs['info']['name'] = $name;
        }
        return $rs;
    }

    /**
     * 代理转账（汪林）
     *
     * @desc 用于代理转账
     * @return int code 操作码，0表示成功
     * @return string msg 提示信息
     */
    public function transferMoney(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $otherid    = checkNull($this->otherid);
        $money      = checkNull($this->money);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_User();
        list($rs['code'], $rs['msg']) = $domain->transferMoney($uid, $otherid,
            $money);
        return $rs;
    }

    /**
     * 反馈（汪林）
     *
     * @desc 用于反馈
     * @return int code 操作码，0表示成功
     * @return int info.name 用户吗
     * @return string msg 提示信息
     */
    public function feedBack(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $type       = checkNull($this->type);
        $content    = checkNull($this->content);
        $remark     = checkNull($this->remark);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain = new Domain_User();
        list($rs['code'], $rs['msg']) = $domain->feedBack($uid, $type, $content,
            $remark);
        return $rs;
    }

    /**
     * 提现记录（汪林）
     *
     * @desc 用户查询提现记录
     * @return int code 操作码，0表示成功
     * @return int info.money 提现金额
     * @return int info.status 状态 0审核中，1审核通过，2审核拒绝
     * @return int info.addtime 时间
     * @return string msg 提示信息
     */
    public function cashRecord(){
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
        $domain     = new Domain_User();
        $rs['info'] = $domain->cashRecord($uid, $page);
        return $rs;
    }

    /**
     * 获取账户余额（汪林）
     *
     * @desc 用户获取账户余额
     * @return int code 操作码，0表示成功
     * @return int info.money 提现金额
     * @return int info.votes 钻石余额
     * @return int info.coin 金币余额
     * @return int info.agent_money 代理金币余额
     * @return int info.proportion 兑换比例
     * @return string msg 提示信息
     */
    public function cashMoney(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $uid        = checkNull($this->uid);
        $token      = checkNull($this->token);
        $checkToken = checkToken($uid, $token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $domain     = new Domain_User();
        $rs['info'] = $domain->cashMoney($uid);
        return $rs;
    }

    /**
     * 获取赠送记录（汪林）
     *
     * @desc 用户获取赠送记录
     * @return int code 操作码，0表示成功
     * @return int info.touid 用户ID
     * @return int info.addtime 时间
     * @return int info.totalcoin 金额
     * @return string msg 提示信息
     */
    public function transferMoneyRecord(){
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
        $domain     = new Domain_User();
        $rs['info'] = $domain->transferMoneyRecord($uid, $page);
        return $rs;
    }

    /**
     * 修改用户信息
     *
     * @desc 用于修改用户信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string list[0].msg 修改成功提示信息
     * @return string msg 提示信息
     */
    public function updateField(){
        $rs         = ['code' => 0, 'msg' => '修改成功', 'info' => []];
        $checkToken = checkToken($this->uid, $this->token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $fields   = json_decode($this->fields, true);
        $is_first = $fields['is_first'] ?? false;
        $allow    = [
            'user_nicename',
            'sex',
            'signature',
            'birthday',
            'location',
            'sex_modifiable',
        ];
        $sp_allow = 'avatar';
        $domain   = new Domain_User();
        $flag     = false;
        foreach($fields as $k => $v){
            if(in_array($k, $allow) || $k === $sp_allow && $flag = true){
                $fields[$k] = checkNull($v);
            }else{
                unset($fields[$k]);
            }
        }
        if($flag){
            if(empty($fields['avatar'])){
                $fields['avatar']       = '/default.jpg';
                $fields['avatar_thumb'] = '/default_thumb.jpg';
            }else{
                $fields['avatar_thumb'] = $fields['avatar'];
            }
            if(empty($fields['user_nicename'])){
                $user_login              = getUserLogin($this->uid);
                $fields['user_nicename'] = '手机用户' . substr($user_login, -4);
            }
            if(empty($fields['sex'])){
                $fields['sex'] = 2;
            }
        }
        if(array_key_exists('user_nicename', $fields)){
            if($fields['user_nicename'] == ''){
                $rs['code'] = 1002;
                $rs['msg']  = '昵称不能为空';
                return $rs;
            }
            $isexist = $domain->checkName($this->uid, $fields['user_nicename']);
            if(!$isexist){
                $rs['code'] = 1002;
                $rs['msg']  = '昵称重复，请修改';
                return $rs;
            }
            //$fields['user_nicename']=filterField($fields['user_nicename']);
            $sensitivewords = sensitiveField($fields['user_nicename']);
            if($sensitivewords == 1001){
                $rs['code'] = 10011;
                $rs['msg']  = '输入非法，请重新输入';
                return $rs;
            }
        }
        if(array_key_exists('signature', $fields)){
            $sensitivewords = sensitiveField($fields['signature']);
            if($sensitivewords == 1001){
                $rs['code'] = 10011;
                $rs['msg']  = '输入非法，请重新输入';
                return $rs;
            }
        }

        if(array_key_exists('birthday', $fields)){
            $fields['birthday'] = strtotime($fields['birthday']);
        }

        if(isset($fields['sex']) && !empty($fields['sex']) && $is_first){
            $fields['sex_modifiable'] = 0;
            unset($fields['is_first']);
        }

        $info = $domain->userUpdate($this->uid, $fields);

        if($info === false){
            $rs['code'] = 1001;
            $rs['msg']  = '修改失败';
            return $rs;
        }
        /* 清除缓存 */
        delCache(Common_Cache::USERINFO . $this->uid);
//		$rs['info'][0]['msg']='修改成功';
        $rs['info'][0] = $domain->getBaseInfo($this->uid);
        return $rs;
    }

    /**
     * 获取用户信息
     * @desc 用于获取用户信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string list[0].msg 修改成功提示信息
     * @return string msg 提示信息
     */
    public function getUserInfo(){
        $rs         = ['code' => 0, 'msg' => '', 'info' => []];
        $checkToken = checkToken($this->uid, $this->token);
        if($checkToken == 700){
            $rs['code'] = $checkToken;
            $rs['msg']  = '您的登陆状态失效或账号已被禁用';
            return $rs;
        }
        $toUid      = $this->toUid;
        $rs['info'] = getUserInfo($toUid);
        return $rs;
    }
}
