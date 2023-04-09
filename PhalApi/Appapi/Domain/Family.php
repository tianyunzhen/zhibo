<?php

class Domain_Family{
    const YA_LIANG = "丫粮";
    const GIFT_NUM = "个";
    const LIVE_NUM = "次";
    const LIVE_MAN = "人";

    public function userFamilyList($uid, $types, $page){
        $res['list']    = [];
        $res['my_info'] = [];
        if($page > 3){
            return $res;
        }
        switch($types){
        case '2': //周
            $start_time = mktime(0, 0, 0, date('m'), date('d') - (date('w') ?: 7) + 1, date('y'));
            $tt         = 300;
            break;
        case '3': //总
            $start_time = 0;
            $tt         = 300;
            break;
        case '1': //日
        default:
            $start_time = mktime(0, 0, 0, date('m'), date('d'), date('y'));
            $tt         = 60;
            break;
        }
        $key         = Common_Cache::FAMILY_E_LIST . $page . '_' . $types;
        $res['list'] = getcaches($key);
        if(!$res['list']){
            $end_time              = time();
            $e_family_profit_model = new Elast_FamilyProfit();
            $data                  = $e_family_profit_model->glamourList($start_time, $end_time, $page);
            $family_model          = new Model_Family();
            $no                    = ($page - 1) * 10;
            $liveModel             = new Model_Live();
            foreach($data as $k => $v){
                ++$no;
                $family_info   = $family_model->get($v['key'], 'uid,name');
                $user_info     = getUserInfo($family_info['uid']);
                $is_live       = $liveModel->isLive($family_info['uid']);
                $list          = [
                    'level'              => $user_info['level'],
                    'level_anchor'       => $user_info['level_anchor'],
                    'avatar_thumb'       => $user_info['avatar_thumb'],
                    'level_thumb'        => $user_info['level_thumb'],
                    'level_anchor_thumb' => $user_info['level_anchor_thumb'],
                    'verify'             => $user_info['remark_info'] ? 1 : 0,
                    'liang'              => getUserLiang($family_info['uid'])['name'],
                    'no'                 => $no,
                    'user_nicename'      => $family_info['name'],
                    'is_live'            => $is_live ? '1' : '0',
                    'uid'                => $family_info['uid'],
                    'votes'              => $v['sum_total']['value'],
                    'head_border'        => $user_info['head_border'],
                ];
                $res['list'][] = $list;
            }
            if($res['list']){
                setcaches($key, json_encode($res['list']), $tt);
            }
        }else{
            $res['list'] = json_decode($res['list'], true);
        }
        $res['list'] = $res['list'] ?: [];
        return $res;
    }
//    public function userFamilyList($uid, $type) {
//        switch($type){
//        case '2': //周
//            $start_time = mktime(0, 0, 0, date('m'),
//                date('d') - date('w') + 1, date('y'));;
//            break;
//        case '3': //总
//            $start_time = mktime(0, 0, 0, date('m'), 1, date('y'));
//            break;
//        case '1': //日
//        default:
//            $start_time = mktime(0, 0, 0, date('m'), date('d'), date('y'));
//            break;
//        }
//        $end_time = time();
//        $where = '';
//        if($type != 3){
//            $where    = " gr.addtime between " . $start_time . " and " . $end_time;
//        }
//        $family_model = new Model_Family();
//        $family_info  = $family_model->listNo($where);
//        $my_info      = [];
//        if($family_info){
//            foreach($family_info as $k => &$v){
//                $num               = $k + 1;
//                $v['level']        = getLevelV2($v['consumption']);
//                $v['level_anchor'] = getLevelAnchorV2($v['votestotal']);
//                $v['avatar_thumb'] = get_upload_path($v['avatar_thumb']);
//
//                /** @var 财富等级和魅力等级 $thumb */
//                $thumb            = getLevelThumb($v['level']);
//                $v['level_thumb'] = get_upload_path($thumb['thumb']);
//                $anchor_thumb     = getLevelThumb($v['level_anchor'],
//                    'level_anchor');
//                $v['level_anchor_thumb']
//                                  = get_upload_path($anchor_thumb['thumb']);
//                $v['verify']      = (string)$v['verify'];
//                $v['liang']       = getUserLiang($uid)['name'];
//                $v['no']          = $num;
//
//                $v['is_live'] = 0;
//                $live = isLive($v['uid']);
//                if ($live) {
//                    if (isAttention($uid, $v['uid']) || !$live['is_black']) {
//                        $v['is_live'] = 1;
//                    }
//                }
//                if($v['uid'] == $uid){
//                    $my_info = $v;
//                }
//            }
//            if(!$my_info){
//                $family_model     = new Model_Family();
//                $family_user_info = $family_model->familyInfo($uid, 'name,id');
//                if($family_user_info){
//                    $where = '';
//                    if($type != 3){
//                        $end_time = time();
//                        $where    = " and gr.addtime between " . $start_time
//                            . " and " . $end_time;
//                    }
//                    $total                         = $family_model->familyLsTj($family_user_info['id'], $where);
//                    $user_info                     = getUserInfo($uid);
//                    $my_info['votes']              = $total[0]['totals'];
//                    $my_info['level']              = $user_info['level'];
//                    $my_info['level_anchor']       = $user_info['level_anchor'];
//                    $my_info['avatar_thumb']       = $user_info['avatar_thumb'];
//                    $my_info['level_thumb']        = $user_info['level_thumb'];
//                    $my_info['level_anchor_thumb'] = $user_info['level_anchor_thumb'];
//                    $my_info['liang']              = $user_info['liang'];
//                    $my_info['verify']             = $user_info['remark_info'] ?: '0';
//                    $my_info['no']                 = 0;
//                }
//            }
//        }
//        return ['list' => $family_info, 'my_info' => $my_info];
//    }

    public static function management($uid, $start_time, $end_time){
        $model  = new Model_Family();
        $family = $model->getFamilyAdmin($uid);
        if(!$family){
            return 801;
        }
        $record = $model->getLiveRecordV2($family['id']);
        $liveLength = $record['live_length'] ?? 0;//总直播时长
        $hours = intval($liveLength / 3600);
        $liveLength = $hours . ":" . gmstrftime('%M:%S', $liveLength);
        $anchorNum = $model->getFamilyUserV2($family['id']);
        $anchorNum = $anchorNum['number_count'] ?? 0;
        $profit = $model->familyProfitV2($family['id']);
        $familyProfit = $profit['family_profit'] ?? 0;//家族收益
        $anchorProfit = $profit['anchor_profit'] ?? 0;//主播收益
        $recordAfterV = $model->getLiveRecordAfterV($family['id'], $start_time, $end_time);
        $hours = intval($recordAfterV['live_length'] / 3600);
        $timeTotal = $hours . ":" . gmstrftime('%M:%S', $recordAfterV['live_length']);
        $dailyDetail = [];
        $disTime = strtotime($end_time) - strtotime($start_time);
        $days = $disTime / 86400;
        for($i = 0; $i <= $days; $i++) {
            $date = date("Y-m-d", strtotime("-$i days", strtotime($end_time)));
            $live_length = $recordAfterV['list'][$date]['live_length'] ?? 0;
            $hours = intval( $live_length / 3600);
            $live_length = $hours . ":" . gmstrftime('%M:%S', $live_length);
            $tem = [
                'day' => $date,
                'live_count' => $recordAfterV['list'][$date]['live_count'] ?? 0,
                'live_length' => $live_length,
                'anchor_profit' => (string) round(($recordAfterV['list'][$date]['anchor_profit'] ?? 0) / 100, 2),
                'family_profit' => (string) round(($recordAfterV['list'][$date]['anchor_profit'] ?? 0) / 1000, 2),
            ];
            $dailyDetail[] = $tem;
        }
        $rs = [
            'total' => [
                'id'                 => $family['id'],
                'uid'                => $uid,
                'name'               => $family['name'],
                'nickname'           => $family['user_nicename'],
                'avatar'             => get_upload_path($family['avatar']),
                'total_anchor_count' => $anchorNum,
                'total_live_length'  => $liveLength,
                'total_anchor_profit' => (string) round($anchorProfit / 100, 2),
                'total_family_profit' => (string) round($familyProfit / 100, 2),
            ],
            'time_total' => [
                'live_count' => $recordAfterV['live_count'],
                'live_length' => $timeTotal,
                'anchor_profit' => (string) round($recordAfterV['anchor_profit'] / 100, 2),
                'family_profit' => (string) round($recordAfterV['anchor_profit'] / 1000, 2),
            ],
            'daily_detail' => $dailyDetail
        ];
        return $rs;
    }

    public static function liveDetail($uid, $day, $page)
    {
        $model = new Model_Family();
        $family = $model->getFamilyAdmin($uid);
        if (!$family) {
            return 801;
        }
        $result = $model->getLiveRecordDailyV2($family['id'], $day, $page);
        $recordList = $result['list'];
        $uidsArr = array_column($recordList, 'uid');
        $uids = implode(',', $uidsArr);
        $profit =  $model->getDailyProfitV2($family['id'], $day, $uids);
        $profitList = $profit['list'];
        $anchorProfit = (string) round($profit['total_anchor_profit'] / 100, 2);
        $familyProfit = (string) round($profit['total_anchor_profit'] / 1000, 2);
        $liveLength = (string) round( $result['total_live_length'] / 3600, 2) . 'h';
        $liveCount = $result['total_live_count'];
        $pageList = [];
        foreach ($recordList as $v) {
            if (!$v['live_length'] && !$profitList[$v['uid']]['anchor_profit']) {
                continue;
            }
            $tem = [
                'uid' => $v['uid'],
                'user_nicename' => $v['user_nicename'],
                'live_length' => (string) round($v['live_length'] / 3600, 2 ) . 'h',
                'anchor_profit' => (string) round($profitList[$v['uid']]['anchor_profit'] / 100, 2),
                'family_profit' => (string) round($profitList[$v['uid']]['family_profit'] / 100, 2),
            ];
            $pageList[] = $tem;
        }
        return [
            'live_count' => $liveCount,
            'live_length' => $liveLength,
            'anchor_profit' => $anchorProfit,
            'family_profit' => $familyProfit,
            'day_live' => $pageList ? : [],
        ];
    }

    public static function myIndex($anchor_id, $start_time, $end_time){
        $model      = new Model_Family();
        $familyInfo = $model->getUserFamilyInfo($anchor_id);
        $remarkModel = new Model_UserRemark();
        $remarkInfo =  $remarkModel->getUserRemark($anchor_id);
        if ($remarkInfo) {
            $remarkInfo['icon'] = get_upload_path($remarkInfo['icon']);
        } else {
            $remarkInfo = null;
        }
        $record = $model->getUserLiveRecordV2($anchor_id);//总
        $timeTotal = (string) round($record['live_length'] / 3600,2) .'h';

        $recordAfterV = $model->getLiveRecordAll($start_time, $end_time, $anchor_id);//时间筛选记录
        $profitAfterV = $model->getLiveProfitAll($start_time, $end_time, $anchor_id);//时间筛选收益记录

        $timeTotalTime = (string) round($recordAfterV['live_length'] / 3600, 2) .'h';
        $dailyDetail = [];
        $disTime = strtotime($end_time) - strtotime($start_time);
        $days = $disTime / 86400;
        for($i = 0; $i <= $days; $i++) {
            $date = date("Y-m-d", strtotime("-$i days", strtotime($end_time)));
            $anchor_profit = (string)round(($profitAfterV['list'][$date]['anchor_profit'] ?? 0) / 100, 2);
            $live_length = $recordAfterV['list'][$date]['live_length'] ?? 0;
            $live_length = (string) round($live_length / 3600, 2) . 'h';
            $tem = [
                'day' => $date,
                'live_times' => $recordAfterV['list'][$date]['live_times'] ?? 0,
                'live_length' => $live_length,
                'anchor_profit' => $anchor_profit,
            ];
            $dailyDetail[] = $tem;
        }
        $rs = [
            'total'      => [
                'uid' => $anchor_id,
                'nickname'             => $familyInfo['user_nicename'],
                'avatar'               => get_upload_path($familyInfo['avatar']),
                'total_live_length'    => $timeTotal,
                'total_live_times'     => $record['live_count'] ?? 0,
                'total_anchor_profit'  => (string)round(($record['total_votes'] ?? 0) / 100, 2),
                'remark_info'   => $remarkInfo,
            ],
            'time_total' => [
                'live_times' => $recordAfterV['live_times'] ?? 0,
                'live_length' => $timeTotalTime,
                'anchor_profit' => (string) round(($profitAfterV['anchor_profit'] ?? 0) / 100, 2),
            ],
            'daily_detail' => $dailyDetail,
        ];
        return $rs;
    }

    public static function managementV2($uid, $start_time, $end_time){
        $model  = new Model_Family();
        $family = $model->getFamilyAdmin($uid);
        if(!$family){
            return 801;
        }
        $record = $model->getLiveRecordV2($family['id']);
        $liveLength = $record['live_length'] ?? 0;//总直播时长
        $anchorNum = $model->getFamilyUserV2($family['id']);
        $anchorNum = $anchorNum['number_count'] ?? 0;//家族人数
        $profit = $model->familyProfitV2($family['id']);
        $familyProfit = $profit['family_profit'] ?? 0;//家族收益
        $anchorProfit = $profit['anchor_profit'] ?? 0;//主播收益
        $recordAfterV = $model->getLiveRecordAfterV2($family['id'], $start_time, $end_time, 0);//时间筛选数据
        $profitAfterV = $model->getLiveProfitAfterV2($family['id'], $start_time, $end_time, 0);//时间筛选收益记录
        $dailyDetail = array_values($recordAfterV['list']);
        $profitDetail = $profitAfterV['list'];
        foreach ($dailyDetail as &$v) {
            $v['live_length'] = (string) round($v['live_length'] / 3600, 2) . 'h';
            $v['live_count'] = count(array_unique($v['live_count']));
            $v['anchor_profit'] = (string) round(($profitDetail[$v['day']]['anchor_profit'] ?? 0) / 100, 2);
            $v['family_profit'] = (string) round(($profitDetail[$v['day']]['anchor_profit'] ?? 0) / 1000, 2);
            unset($v['live_times']);
        }
        $rs = [
            'total' => [
                'id'                 => $family['id'],
                'uid'                => $uid,
                'name'               => $family['name'],
                'nickname'           => $family['user_nicename'],
                'avatar'             => get_upload_path($family['avatar']),
                'total_anchor_count' => $anchorNum,
                'total_live_length'  => (string) round($liveLength / 3600, 2) .'h',
                'total_anchor_profit' => (string) round($anchorProfit / 100, 2),
                'total_family_profit' => (string) round($familyProfit / 100, 2),
            ],
            'time_total' => [
                'live_count' => $recordAfterV['live_count'],
                'live_length' => (string) round($recordAfterV['live_length'] / 3600, 2) . 'h',
                'anchor_profit' => (string) round($profitAfterV['anchor_profit'] / 100, 2),
                'family_profit' => (string) round($profitAfterV['anchor_profit'] / 1000, 2),
            ],
            'daily_detail' => $dailyDetail
        ];
        return $rs;
    }

    public static function anchorIndexV2($anchor_id, $start_time, $end_time, $type = 0){
        $model      = new Model_Family();
        $familyInfo = $model->getUserFamilyInfo($anchor_id);
        $remarkModel = new Model_UserRemark();
        $remarkInfo =  $remarkModel->getUserRemark($anchor_id);
        if ($remarkInfo) {
            $remarkInfo['icon'] = get_upload_path($remarkInfo['icon']);
        } else {
            $remarkInfo = null;
        }
        $fStatus    = $familyInfo['f_state'] ?? 0;
        $fuStatus   = $familyInfo['fu_state'] ?? 0;
        $familyFlag = ($fStatus == 2 && ($fuStatus == 1 || $fuStatus == 2));
        $addTime    = $familyInfo['addtime'] ?? 0;
        if($type && !$familyFlag){
            return 10009;
        }
        if(!$type){
            $addTime = 0;
        }
        $record = $model->getUserLiveRecordV2($anchor_id, $addTime);//总记录
        $profit = $model->getUserLiveProfitV2($anchor_id, $addTime);//总收益
        $recordAfterV = $model->getLiveRecordAfterV2($familyInfo['id'], $start_time, $end_time, $anchor_id);//时间筛选记录
        $profitAfterV = $model->getLiveProfitAfterV2($familyInfo['id'], $start_time, $end_time, $anchor_id);//时间筛选收益记录
        $dailyDetail = array_values($recordAfterV['list']);//每日明细
        $profitDetail = $profitAfterV['list'];
        foreach ($dailyDetail as &$v) {
            $v['live_length'] = (string) round($v['live_length'] / 3600, 2) . 'h';
            $v['anchor_profit'] = (string) round(($profitDetail[$v['day']]['anchor_profit'] ?? 0) / 100, 2);
            $v['family_profit'] = (string) round(($profitDetail[$v['day']]['anchor_profit'] ?? 0) / 1000, 2);
            unset($v['live_count']);
        }
        $rs = [
            'total'      => [
                'id'                   => $familyFlag ? $familyInfo['id'] : 0,
                'anchor_id'            => $anchor_id,
                'family_name'          => $familyFlag ? $familyInfo['name'] : '',
                'nickname'             => $familyInfo['user_nicename'],
                'avatar'               => get_upload_path($familyInfo['avatar']),
                'total_live_length'    => (string) round($record['live_length'] / 3600, 2) . 'h',
                'total_live_times'     => $record['live_count'] ?? 0,
                'total_anchor_profit'  => (string)round(($profit['total_votes'] ?? 0) / 100, 2),
                'total_family_profit'   => (string)round(($profit['total_votes'] ?? 0) / 1000, 2),
                'remark_info'   => $remarkInfo,
            ],
            'time_total' => [
                'live_times' => $recordAfterV['live_times'] ?? 0,
                'live_length' => (string) round($recordAfterV['live_length'] / 3600, 2) . 'h',
                'anchor_profit' => (string) round(($profitAfterV['anchor_profit'] ?? 0) / 100, 2),
                'family_profit' => (string) round(($profitAfterV['anchor_profit'] ?? 0) / 1000, 2),
            ],
            'daily_detail' => $dailyDetail,
        ];
        return $rs;
    }
}
