<?php

class Model_Guard extends PhalApi_Model_NotORM
{
    /* 守护用户列表 */
    public function getGuardList($data)
    {

        $rs = [];

        $liveuid = $data['liveuid'];

        $nowtime = time();
        $w       = date('w', $nowtime);
        //获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
        $first = 1;
        //周一
        $week       = date('Y-m-d H:i:s',
            strtotime(date("Ymd", $nowtime) . "-" . ($w ? $w - $first : 6)
                . ' days'));
        $week_start = strtotime(date("Ymd", $nowtime) . "-" . ($w ? $w - $first
                : 6) . ' days');

        //本周结束日期 
        //周天
        $week_end = strtotime("{$week} +1 week");

        $order  = [];
        $order2 = [];
        $list   = DI()->notorm->guard_user
            ->select('uid,type')
            ->where('liveuid=? and endtime>?', $liveuid, $nowtime)
            //->order("type desc")
            ->fetchAll();
        foreach ($list as $k => $v) {
            $userinfo = getUserInfo($v['uid']);

            $userinfo['type']       = $v['type'];
            $userinfo['contribute'] = $this->getWeekContribute($v['uid'],
                $week_start, $week_end);

            $order[]  = $userinfo['contribute'];
            $order2[] = $userinfo['type'];
            $rs[]     = $userinfo;
        }


        array_multisort($order, SORT_DESC, $order2, SORT_DESC, $rs);


        return $rs;
    }

    public function getWeekContribute($uid, $starttime = 0, $endtime = 0)
    {
        $contribute = '0';
        if ($uid > 0) {
            $where = "action in ('1','10') and uid = {$uid}";
            if ($starttime > 0) {
                $where .= " and addtime > {$starttime}";
            }
            if ($endtime > 0) {
                $where .= " and addtime < {$endtime}";
            }

            $contribute = DI()->notorm->user_coinrecord
                ->where($where)
                ->sum('totalcoin');
            if (!$contribute) {
                $contribute = 0;
            }
        }

        return (string)$contribute;
    }

    /* 守护信息列表 */
    public function getList()
    {
        $list = DI()->notorm->guard
            ->select('id,name,type,coin')
            ->order("list_order asc")
            ->fetchAll();

        return $list;
    }

    /* 获取用户守护信息 */
    public function getUserGuard($uid, $liveuid)
    {
        $rs = [
            'type'    => '0',
            'endtime' => '0',
        ];
        return $rs;
    }

    /* 获取主播守护总数 */
    public function getGuardNums($liveuid)
    {

        $nowtime = time();

        $nums = DI()->notorm->guard_user
            ->where('liveuid=? and endtime>?', $liveuid, $nowtime)
            ->count();
        return (string)$nums;
    }
}
