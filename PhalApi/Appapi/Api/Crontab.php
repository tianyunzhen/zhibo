<?php
/**
 * 定时任务
 */
class Api_Crontab extends PhalApi_Api
{
    public function getRules()
    {
        return [
            'liang' => [],
            'createBlackLive' => [
                'uid'         => ['name' => 'uid', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'],
            ],
            'words' => [],
            'shuju' => [
                'p' => ['name' => 'p', 'type' => 'int'],
            ],
        ];
    }

   /** 靓号到期处理 */
    public function liang()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];
        DI()->notorm->liang
            ->where('uid > 0 and expire > 0 and end_time <= ?', time())
            ->update(['uid' => 0, 'state' => 0, 'end_time' => 0, 'buytime' => 0]);
        return $rs;
    }

    public function test()
    {
        $rs = ['code' => 0, 'msg' => 'success', 'info' => []];
////        $users = DI()->notorm->user->where('id < ?',10000500)->select('id')->fetchAll();
////        $ids = array_column($users, 'id');
////        foreach ($users as $v) {
////            $key = "﻿user:getUserGuard_". $v['id'].'_'.array_pop($ids);
//            delcache($key);
////        }
        $res = DI()->redis->keys('*shutup');
        foreach ($res as $v) {
            DI()->redis->del($v);
        }
        return $rs;
    }

    public function createBlackLive()
    {
        $rs = ['code' => 0, 'msg' => 'success', 'info' => []];
        $uid = checkNull($this->uid);
        $count = DI()->notorm->user_attention
                ->where('touid=?', $uid)
                ->count() ?? 0;
        if ($count) {
            $model = new Model_User();
            $model->createBlackLive($uid, [], 1);
        }
        return $rs;
    }

    public function goalList()
    {
        $rs = ['code' => 0, 'msg' => 'success', 'info' => []];
        $model = new Model_GiftRecord();
        $model->nationalDay();
        return $rs;
    }

    public function words() {
        $rs = ['code' => 0, 'msg' => 'success', 'info' => []];
        $result = DI()->notorm->sensitive->where(['status' => 1])->select('name')->fetchAll();
        $rs['info'] = array_column($result, 'name');
        return $rs;
    }

    public function shuju() {
//        $count = DI()->notorm->user->where(['is_js' => 0])->count();
//        var_dump($count);die;
        $rs = ['code' => 0, 'msg' => 'success', 'info' => []];
//        var_dump(456);die;
        $pnum   = 100;
        $p = 1;
        for ($i = $p; $i < 20; $i++) {
            $start  = ($i - 1) * $pnum;
            $result = DI()->notorm->user->where('id<=10025043 and id>10024048 and is_js=0')->limit($start, $pnum)->fetchAll();
            foreach ($result as $v) {
                $spend_votes = DI()->notorm->user_voterecord->where(['uid' => $v['id'], 'type' => 0])->sum('votes') ?? 0;
                $spend_votes = round($spend_votes / 100,2);
                $votes = round($v['votes'] / 100, 2);
                $level        = getLevelV2($v['consumption']);
                $level_anchor = getLevelAnchorV2($v['votestotal']);
                $family_id = DI()->notorm->family_user->where(['uid' => $v['id']])->select('familyid')->fetchOne() ?? 0;
                if ($family_id) {
                    $family_name = DI()->notorm->family->where(['id' => $family_id])->select('name')->fetchOne();
                } else {
                    $family_name = '';
                }
                $remark_id =  DI()->notorm->user_remark->where(['uid' => $v['id']])->select('remark_id')->fetchOne() ?? 0;
                if ($remark_id) {
                    $remark_name = DI()->notorm->remark->where(['id' => $remark_id])->select('name')->fetchOne();
                } else {
                    $remark_name = '';
                }
                $tem = [
                    'user_id' => $v['id'],
                    'nickname' => $v['user_nicename'],
                    'family_id' => $family_id,
                    'family_name' => $family_name,
                    'level1' => $level,
                    'level2' => $level_anchor,
                    'spend_coin' => $v['consumption'],
                    'spend_vote' => $spend_votes,
                    'coin' => $v['coin'],
                    'vote' => $votes,
                    'last_login' => date('Y-m-d H:i:s', $v['last_login_time']),
                    'remark' => $remark_name,
                ];
                DI()->notorm->linshi->insert($tem);
            }
            sleep(1);
        }
        return $rs;
    }

    public function water() {
        $pnum   = 500;
        $p = 1;
        for ($i = $p; $i < 80; $i++) {
            $start  = ($i - 1) * $pnum;
            $all = DI()->notorm->user_coinrecord->select('uid,touid,totalcoin')
                ->where('addtime>=1603900800 and addtime<1603987200')
                ->limit($start, $pnum)
                ->fetchAll();
            foreach ($all as $value) {
                $sender = DI()->notorm->user->where(['id' => $value['uid']])->fetchOne('user_nicename') ?? '';
                $receiver = DI()->notorm->user->where(['id' => $value['touid']])->fetchOne('user_nicename') ?? '';
                $tem = [
                    'uid' => $value['uid'],
                    'touid' => $value['touid'],
                    'coin' => $value['totalcoin'],
                    'sender' => $sender,
                    'receiver' => $receiver,
                ];
                DI()->notorm->water->insert($tem);
            }
//            sleep(1);
        }
//        $all = DI()->notorm->user_voterecord->select('uid,touid,totalcoin')->where('addtime>=1603900800 and addtime<1603987200')->fetchAll();
    }
}
