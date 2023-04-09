<?php
if(!session_id()){
    session_start();
}

class Model_Home extends PhalApi_Model_NotORM{
    protected $live_fields = 'uid,title,city,stream,pull,thumb,isvideo,type,type_val,goodnum,anyway,starttime,isshop,game_action,is_black,pkuid';
    protected $user_fields = 'id,user_nicename,avatar,avatar_thumb,sex,signature,location,verify,consumption, votestotal';


    /* 轮播 */
    public function getSlide($id){
        $rs = DI()->notorm->slide_item
            ->select("image as slide_pic,url as slide_url")
            ->where("status='1' and slide_id=$id ")
            ->order("list_order asc")
            ->fetchAll();
        foreach($rs as $k => $v){
            $rs[$k]['slide_pic'] = get_upload_path($v['slide_pic']);
        }

        return $rs;
    }

    /* 热门 */
    public function getHot($p){
        $f = 'a.uid,a.title,a.city,a.stream,a.pull,a.thumb,a.isvideo,a.type,a.type_val,a.goodnum,a.anyway,a.starttime,a.isshop,a.game_action,a.is_black,pkuid';
        if($p < 1){
            $p = 1;
        }
        $pnum   = 20;
        $start  = ($p - 1) * $pnum;
        $sql    = "select (if(hot_deadline<unix_timestamp(now()),0,hot_updtime)) hot_time,{$f},hotvotes from cmf_live a left join cmf_user b
 on a.uid = b.id where islive = 1 and hide = 0 and is_black =0 order by hot_time desc,cast(net_hotvotes as signed) + live_weight desc limit {$start},{$pnum}";
        $result = DI()->notorm->live->queryAll($sql);
        foreach($result as $k => $v){
            $v          = handleLive($v);
            $result[$k] = $v;
        }
        return $result;
    }

    /* 关注列表 */
    public function getFollow($uid, $p){
        $rs = [
            'title' => '你还没有关注任何主播',
            'des'   => '赶快去关注自己喜欢的主播吧~',
            'list'  => [],
        ];
        if($p < 1){
            $p = 1;
        }
        $result = [];
        $pnum   = 50;
        $start  = ($p - 1) * $pnum;

        $touid = DI()->notorm->user_attention
            ->select("touid")
            ->where('uid=?', $uid)
            ->fetchAll();

        if(!$touid){
            return $rs;
        }

        $rs['title'] = '你关注的主播没有开播';
        $rs['des']   = '赶快去看看其他主播的直播吧~';
        $where       = " islive='1' ";
        if($p != 1){
            $endtime = $_SESSION['follow_starttime'] ?? 0;
            if($endtime){
                $start = 0;
                $where .= " and starttime < {$endtime}";
            }

        }

        $touids  = array_column($touid, "touid");
        $touidss = implode(",", $touids);
        $where   .= " and uid in ({$touidss})";
        $result  = DI()->notorm->live
            ->select($this->live_fields)
            ->where($where)
            ->order("starttime desc")
            ->limit($start, $pnum)
            ->fetchAll();

        foreach($result as $k => $v){

            $v = handleLive($v);

            $result[$k] = $v;
        }

        if($result){
            $last                         = end($result);
            $_SESSION['follow_starttime'] = $last['starttime'];
        }

        $rs['list'] = $result;

        return $rs;
    }

    /* 最新 */
    public function getNew($lng, $lat, $p){
        if($p < 1){
            $p = 1;
        }
        $pnum  = 20;
        $where = " islive='1' and hide = 0 and is_black = 0";

        if($p != 1){
            $endtime = $_SESSION['new_starttime'] ?? 0;
            if($endtime){
                $where .= " and starttime < {$endtime}";
            }
        }

        $juli = "(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*({$lng}-lng)/360),2)+COS(PI()*{$lat}/180)* COS(lat * PI()/180)*POW(SIN(PI()*({$lat}-lat)/360),2)))) as distance";

        $result = DI()->notorm->live
            ->select($this->live_fields . ',lng,lat')
            ->where($where)
            ->order("starttime desc")
            ->limit(0, $pnum)
            ->fetchAll();
        foreach($result as $k => $v){

            $v = handleLive($v);

            $distance = '好像在火星';
//            if($lng != '' && $lat != '' && $v['lat'] != ''
//                && $v['lng'] != ''
//            ){
//                $distance = getDistance($lat, $lng, $v['lat'], $v['lng'])
//                    . 'km';
//            }elseif($v['city']){
//                $distance = $v['city'];
//            }

            $v['distance'] = $distance;
            unset($v['lng']);
            unset($v['lat']);

            $result[$k] = $v;

        }
        if($result){
            $last                      = end($result);
            $_SESSION['new_starttime'] = $last['starttime'];
        }

        return $result;
    }

    /* 搜索 */
    public function search($uid, $key, $p){
        if($p < 1){
            $p = 1;
        }
        $pnum  = 50;
        $start = ($p - 1) * $pnum;
        $where = ' user_type="2" and ( id=? or user_nicename like ?  or goodnum like ? ) and id!=?';
        if($p != 1){
            $id = $_SESSION['search'];
            if($id){
                $where .= " and id < {$id}";
            }
        }

        $result = DI()->notorm->user
            ->select("id,user_nicename,avatar,sex,signature,consumption,votestotal,verify")
            ->where($where, $key, '%' . $key . '%', '%' . $key . '%', $uid)
            ->order("id desc")
            ->limit($start, $pnum)
            ->fetchAll();
        foreach($result as $k => $v){
            $v['level']              = (string)getLevelV2($v['consumption']);
            $thumb                   = getLevelThumb($v['level']);
            $v['level_thumb']        = $thumb ? $thumb['thumb'] : '';
            $v['level_anchor']       = (string)getLevelAnchorV2($v['votestotal']);
            $anchorThumb             = getLevelThumb($v['level_anchor'],
                'level_anchor');
            $v['level_anchor_thumb'] = $anchorThumb ? $anchorThumb['thumb']
                : '';
            $v['isattention']        = (string)isAttention($uid, $v['id']);
            $v['is_live']            = 0;
            $live                    = isLive($v['id']);
            if($live && ($v['isattention'] == 1 || $live['is_black'] == 0)){
                $v['is_live'] = 1;
            }
            $v['avatar']      = get_upload_path($v['avatar']);
            $v['remark_info'] = getRemarkInfo($v['id']);
            unset($v['consumption']);

            $result[$k] = $v;
        }

        if($result){
            $last               = end($result);
            $_SESSION['search'] = $last['id'];
        }

        return $result;
    }

    /* 附近 */
    public function getNearby($lng, $lat, $p){
        if($p < 1){
            $p = 1;
        }
        $pnum   = 50;
        $start  = ($p - 1) * $pnum;
        $where  = " islive='1' and hide = 0 and is_black = 0 and lng!='' and lat!='' ";
        $juli   = "(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*({$lng}-lng)/360),2)+COS(PI()*{$lat}/180)* COS(lat * PI()/180)*POW(SIN(PI()*({$lat}-lat)/360),2)))) as distance";
        $result = DI()->notorm->live
            ->select($this->live_fields . ",lat, lng, province ," . $juli)
            ->where($where)
            ->order('distance asc')
            ->limit($start, $pnum)
            ->fetchAll();
        foreach($result as &$v){
            $v = handleLive($v);
            if($v['distance'] > 50){
                $v['distance'] = 50 . 'km外';
            }else{
                $v['distance'] = round($v['distance'], 1) . 'km';
            }

        }
        return $result;
    }

    /* 附近的人 */
    public function getNearUser($lng, $lat, $p, $uid){
        if($p < 1){
            $p = 1;
        }
        $pnum     = 50;
        $start    = ($p - 1) * $pnum;
        $where    = "lng!='' and lat!='' and id!=$uid and is_near = 1";
        $userInfo = getUserInfo($uid);
        if($userInfo['sex'] == 1){
            $where .= " and sex=2";
        }
        //更新坐标
        $zb_data = [
            'lng' => $lng,
            'lat' => $lat,
        ];
        DI()->notorm->user
            ->where('id = ?', $uid)
            ->update($zb_data);
        $juli
                = "(2 * 6378.137* ASIN(SQRT(POW(SIN(PI()*({$lng}-lng)/360),2)+COS(PI()*{$lat}/180)* COS(lat * PI()/180)*POW(SIN(PI()*({$lat}-lat)/360),2)))) as distance";
        $result = DI()->notorm->user
            ->select($this->user_fields . ",lat, lng," . $juli)
            ->where($where)
            ->order('distance asc')
            ->limit($start, $pnum)
            ->fetchAll();
        $live   = DI()->notorm->live
            ->where('islive = 1')
            ->select('pull,is_black')
            ->fetchPairs('uid');
        foreach($result as $k => &$v){
            $v['level']        = getLevelV2($v['consumption']);
            $thumb             = getLevelThumb($v['level']);
            $v['level_thumb']  = get_upload_path($thumb['thumb']);
            $v['avatar_thumb'] = get_upload_path($v['avatar_thumb']);
            $v['avatar']       = get_upload_path($v['avatar']);

            $v['level_anchor']       = getLevelAnchorV2($v['votestotal']);
            $anchor_thumb            = getLevelThumb($v['level_anchor'], 'level_anchor');
            $v['level_anchor_thumb'] = get_upload_path($anchor_thumb['thumb']);

            $v['is_live'] = 0;
            $v['pull']    = '';
//            $v['distance'] = getDistance($lat, $lng, $v['lat'], $v['lng']);
//            $v['distance'] = getDistance($lat, $lng, $v['lat'], $v['lng']);
            if($v['distance'] > 50){
                $v['distance'] = 50 . 'km外';
            }else{
                $v['distance'] = round($v['distance'], 1) . 'km';
            }

            $v['is_attention'] = (string)isAttention($uid, $v['id']);
            if(array_key_exists($v['id'], $live)){
                if($v['is_attention'] == 1 || $live[$v['id']]['is_black'] == 0){
                    $v['is_live'] = 1;
                    $v['pull']    = $live[$v['id']]['pull'];
                }
            }
            $v['remark_info'] = getRemarkInfo($v['id']);
            unset($v['lat'], $v['lng']);
        }
//        $distance = array_column($result, 'distance');
//        array_multisort($distance, SORT_ASC, $result);
//        array_walk($result, function (&$value, $key) {
//            $value['distance'] .= 'km';
//        });
        return $result;
    }

    public function getYouLike(){
        $sql
                = 'select u.id user_id,u.user_nicename,u.signature,u.sex,u.verify,u.location,if(i.showid,1,0) as is_live,i.thumb as avatar
from cmf_user as u right join cmf_live as i on u.id=i.uid where i.islive = 1 and i.is_black = 0 order by rand() limit 3';
        $result = $this->getORM()->queryAll($sql, []);
        if(count($result) > 3){
            $randomKeys = array_rand($result, 3);
        }else{
            foreach($result as &$v){
                $v['avatar']      = get_upload_path($v['avatar']);
                $v['remark_info'] = getRemarkInfo($v['user_id']);
            }
            return $result;
        }
        $data = [];
        foreach($randomKeys as $randomKey){
            $tem                = $result[$randomKey];
            $tem['avatar']      = get_upload_path($tem['avatar']);
            $tem['remark_info'] = getRemarkInfo($tem['user_id']);
            $data[]             = $tem;
        }
        return $data;
    }

    public function intimateList($uid, $p, $type, $touid){
        if($p < 1){
            $p = 1;
        }
        $pnum        = 30;
        $start       = ($p - 1) * $pnum;
        $commonWhere = static::getCommonWhere($type);
        $where       = $commonWhere . " and touid = $touid";
        $result      = DI()->notorm->user_coinrecord
            ->select('sum(totalcoin) as totalcoin, uid')
            ->where($where)
            ->group('uid')
            ->order('totalcoin desc')
            ->limit($start, $pnum)
            ->fetchAll();
        $myWhere     = $where . " and uid = $uid";
        $totalCoin   = DI()->notorm->user_coinrecord
                ->where($myWhere)
                ->sum('totalcoin') ?? 0;
        $userinfo    = getUserInfo($uid);
        $myResult    = [
            'uid'                => $userinfo['id'],
            'user_nicename'      => $userinfo['user_nicename'],
            'verify'             => $userinfo['verify'],
            'signature'          => $userinfo['signature'],
            'totalcoin'          => $totalCoin,
            'avatar'             => $userinfo['avatar'],
            'avatar_thumb'       => $userinfo['avatar_thumb'],
            'level'              => $userinfo['level'],
            'level_thumb'        => $userinfo['level_thumb'],
            'level_anchor'       => $userinfo['level_anchor'],
            'level_anchor_thumb' => $userinfo['level_anchor_thumb'],
        ];
        $result      = static::dealResult($result, $uid);
        return [$result, $myResult];
    }

    /**
     * 组建排行榜条件
     *
     * @param $type
     *
     * @return string
     */
    private static function getCommonWhere($type){
        switch($type){
        case 'day':
            //获取今天开始结束时间
            $dayStart = strtotime(date("Y-m-d"));
            $dayEnd   = strtotime(date("Y-m-d 23:59:59"));
            $where
                      = " addtime >={$dayStart} and addtime<={$dayEnd} and ";

            break;

        case 'week':
            $w = date('w');
            //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
            $first = 1;
            //周一
            $week       = date('Y-m-d H:i:s',
                strtotime(date("Ymd") . "-" . ($w ? $w - $first : 6)
                    . ' days'));
            $week_start = strtotime(date("Ymd") . "-" . ($w ? $w - $first
                    : 6) . ' days');

            //本周结束日期
            //周天
            $week_end = strtotime("{$week} +1 week") - 1;

            $where
                = " addtime >={$week_start} and addtime<={$week_end} and ";

            break;

        case 'month':
            //本月第一天
            $month       = date('Y-m-d', strtotime(date("Ym") . '01'));
            $month_start = strtotime(date("Ym") . '01');

            //本月最后一天
            $month_end = strtotime("{$month} +1 month") - 1;

            $where
                = " addtime >={$month_start} and addtime<={$month_end} and ";

            break;

        case 'total':
            $where = " ";
            break;

        default:
            //获取今天开始结束时间
            $dayStart = strtotime(date("Y-m-d"));
            $dayEnd   = strtotime(date("Y-m-d 23:59:59"));
            $where
                      = " addtime >={$dayStart} and addtime<={$dayEnd} and ";
            break;
        }

        $where .= " type=0 and action in ('1','2')";
        return $where;
    }

    /**
     * 排行榜结果处理
     *
     * @param $result
     * @param $uid
     *
     * @return mixed
     */
    public static function dealResult($result, $uid){
        foreach($result as $k => $v){
            $userinfo           = getUserInfo($v['uid']);
            $v['avatar']        = $userinfo['avatar'];
            $v['avatar_thumb']  = $userinfo['avatar_thumb'];
            $v['user_nicename'] = $userinfo['user_nicename'];
            $v['signature']     = $userinfo['signature'];
            $v['sex']           = $userinfo['sex'];
            $v['isAttention']   = isAttention($uid, $v['uid']);//判断当前用户是否关注了该主播

            /** @var 获取财富等级和魅力等级 $thumb */
            $v['level']              = $userinfo['level'];
            $v['level_thumb']        = $userinfo['level_thumb'];
            $v['level_anchor']       = $userinfo['level_anchor'];
            $v['level_anchor_thumb'] = $userinfo['level_anchor_thumb'];

            $v['verify'] = $userinfo['verify'];
            $result[$k]  = $v;
        }
        return $result;
    }
}
