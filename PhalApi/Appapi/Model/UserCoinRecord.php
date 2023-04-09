<?php

class Model_UserCoinRecord extends PhalApi_Model_NotORM{
    protected $tableName = 'user_coinrecord';
    //type字段
    const INCOME = 1; //收入
    const OUT    = 0; //支出
    //action
    const GFZC     = 1; //官方直冲
    const DLZC     = 2; //代理直冲
    const RWQD     = 3; //任务（签到）
    const DH       = 4; //兑换
    const ZJ       = 5; //中奖
    const SL       = 6; //送礼
    const LH       = 7; //靓号
    const PAIWEI   = 9; //排位赛
    const HOUTAI   = 10; //后台扣款
    const TOUKAUNG = 11; //头框
}
