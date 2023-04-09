<?php

class Domain_Detail
{
    const PAGE_SIZE = 10;
    const COIN_ACTION
                    = [
            1 => '官方直充',
            2 => '代理直充',
            3 => '任务',
            4 => '兑换',
            5 => '中奖',
            6 => '送礼',
            7 => '买靓号',
            8 => '手动充值',
            9 => '排位赛',
            10 => '后台扣币',
        ];
    const DIAMOND_ACTION
                    = [
            1 => '收礼物',
            2 => '提现',
            3 => '兑换',
            4 => '家族分成',
            5 => '提现驳回',
            6 => '排位赛',
        ];

    public function coin($where, $start_time, $end_time, $page)
    {
        $query
            = DI()->notorm->user_coinrecord->select('id,totalcoin as coin,action,addtime as add_time')
            ->where($where);
        if ($start_time || $end_time) {
            $query = $query->where('addtime >= ?', $start_time)->where('addtime <= ?', $end_time + 86400);
        }
        $pnum       = self::PAGE_SIZE;
        $count      = ceil($query->count() / $pnum);
        $total_coin = $query->sum('totalcoin') ?? 0;
        $start      = ($page - 1) * $pnum;
        $record     = $query->order('id desc')->limit($start, $pnum)->fetchAll();
        foreach ($record as &$v) {
            $v['action']   = self::COIN_ACTION[$v['action']] ?? '未知';
            $v['icon']     = "http://by.boyaduck.com/coin.jpg";
            $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
        }
        return [$total_coin, $record, $count];
    }

    public function diamond($where, $start_time, $end_time, $page)
    {
        $query
            = DI()->notorm->user_voterecord->select('id,votes as diamond,action,addtime as add_time')
            ->where($where);
        if ($start_time || $end_time) {
            $query = $query->where('addtime >= ? AND addtime <= ?', $start_time,
                $end_time + 86400);
        }
        $pnum       = self::PAGE_SIZE;
        $count      = ceil($query->count() / $pnum);
        $total_coin = $query->sum('votes') ?? 0;
        $start      = ($page - 1) * $pnum;
        $record     = $query->order('id desc')->limit($start, $pnum)->fetchAll();
        foreach ($record as &$v) {
            $v['diamond']   = (string) round($v['diamond'] / 100, 2);
            $v['action']   = self::DIAMOND_ACTION[$v['action']] ?? '未知';
            $v['icon']     = "https://by.boyaduck.com/yaliang.png";
            $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
        }
        return [$total_coin, $record, $count];
    }
}
