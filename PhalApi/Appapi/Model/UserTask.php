<?php

class Model_UserTask extends PhalApi_Model_NotORM
{

    /* 是否签到 */
    public function isSignIn($uid)
    {
        $time    = date("Y-m-d");
        $redinfo = DI()->notorm->user_sign_task
            ->select("time")
            ->where('uid = ? ', $uid)
            ->fetchOne();
        if ($redinfo && $redinfo['time'] == $time) {
            return true;
        } else {
            return false;
        }
    }

    public function userSign($uid)
    {
        $time    = date("Y-m-d");
        $redinfo = DI()->notorm->user_sign_task
            ->select("time")
            ->where('uid = ? ', $uid)
            ->fetchOne();
        if ($redinfo && $redinfo['time'] == $time) {
            return [0, '签到成功'];
        }
        //签到金币 {后台配置}
        $money = 1000;
        //更新签到记录
        $sing_data['time']        = date('Y-m-d');
        $sing_data['update_time'] = time();
        try {
            DI()->notorm->user_sign_task->queryAll('begin');
            if (!$redinfo) {
                $sing_data['create_time'] = time();
                $sing_data['uid']         = $uid;
                $res
                                          = DI()->notorm->user_sign_task->insert($sing_data);
            } else {
                $res = DI()->notorm->user_sign_task->where('uid = ?', $uid)
                    ->update($sing_data);
            }
            if (!$res) {
                DI()->notorm->user_sign_task->queryAll('rollback');
                return [1, '签到失败'];
            }
            //增加钱包
            if (!DI()->notorm->user
                ->where('id = ?', $uid)
                ->update(['coin' => new NotORM_Literal("coin + {$money}")])
            ) {
                DI()->notorm->user_sign_task->queryAll('rollback');
                return [1, '更新钱包失败'];
            }
            //增加流水
            $insert = [
                "type"      => 1, //收入
                "action"    => 3, //签到
                "uid"       => $uid, //用户ID
                "totalcoin" => $money,
                "addtime"   => time(),
            ];
            if (!DI()->notorm->user_coinrecord->insert($insert)) {
                DI()->notorm->user_sign_task->queryAll('rollback');
                return [1, '增加流水失败'];
            }
        } catch (\Exception $e) {
            DI()->notorm->user_sign_task->queryAll('rollback');
            return [99, $e->getMessage()];
        }
        DI()->notorm->user_sign_task->queryAll('commit');

        return [0, '签到成功'];
    }
}
