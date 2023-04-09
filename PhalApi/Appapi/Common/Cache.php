<?php

class Common_Cache{
    //用户
    const USERINFO  = 'user:info:user_info_'; //用户信息
    const USERLIANG = 'user:liang:liang_info_'; //用户靓号
    const HEADER    = 'user:head_border:head_info_'; //用户靓号
    const USERTOKEN = 'user:login:token_';//用户token
    const USERCAR   = 'user:car:info_';//用户坐骑
    //排行榜
    const GLAMOUR_LIST  = 'common:glamour:glamour_list_'; //魅力排行榜
    const WEALTH_LIST   = 'common:wealth:wealth_list_'; //财富排行榜
    const FAMILY_E_LIST = 'common:wealth:family_list_'; //家族魅力榜
    //等级
    const LEVEL        = 'common:level:info';//等级
    const LEVEL_ANCHOR = 'common:level:anchor';//魅力等级
    //配置
    const SYSCONFIG    = 'common:config:sys_confg'; //系统配置
    const PUB_CONFIG   = 'common:config:pub_confg'; //公共配置
    const LIVE_HOT     = 'live:hot:list_'; //热门直播
    const LIVE_HOT_PIC = 'live:hot:pic'; //热门轮播图
    //token
    const ENTER_ROOM_TOKEN = 'common:token:enter:'; //进入直播token
    const SEND_GIFT_TOKEN  = 'common:token:send_gift:'; //进入直播token
    //直播
    const LIVE_AUDIENCE = 'live:audience:info_'; //本场直播观看人数
    const LIVE_NOW_NUMS = 'live:user_now:'; //本场直播实时人数

    //僵尸粉
    const CORPSE_OVER_FLOW = 'live:corpse_over_flow:'; //僵尸溢出数量
    const CORPSE_INSET     = 'live:corpse_insert:'; //僵尸插入数量

    const CHECK_NAME = 'user:check:';

    //连麦
    const LINK_MIC = 'link:mic';

    //Pk
    const USER_PK_WINNING = 'pk:winning:';
    const PK_TIMES        = 'pk:time';
    const PK_USER         = 'pk:user';
    const PK_GIFT         = 'pk:gift';
    const PK_GIFT_USER    = 'pk:gift:user:';

    //活动
    const ACTIVE_RANGKING_SAME              = 'active:ranking:same_day_'; //主播当日排行榜
    const ACTIVE_RANGKING_YESTER            = 'active:ranking:yesterday_';//主播昨日排行榜
    const ACTIVE_RANKING_FAMILY_SAME        = 'active:ranking:famili_same_day_'; //家族日榜
    const ACTIVE_RANKING_FAMILY_YESTER      = 'active:ranking:famili_yesterday_';//家族昨日榜
    const ACTIVE_RANKING_FAMILY_WEEK_SAME   = 'active:ranking:famili_week_same_'; //家族本周榜
    const ACTIVE_RANKING_FAMILY_WEEK_YESTER = 'active:ranking:famili_week_yester_'; //家族上周榜
    const ACTIVE_DAY_WATER                  = 'active:day_water:'; //日流水
    const ACTIVE_FAMILY_DAY_WATER           = 'active:family_day_water:'; //家族日流水
    const ACTIVE_FAMILY_WEEK_WATER          = 'active:family_week_water:'; //家族周流水
    const ACTIVE_EFFECTIVE                  = 'active:effective:'; //周有效主播

    const FESTIVAL_GIFT = 'active:festival_gift:';

    public static function addCache($key, $val){
        setcaches($key, $val);
    }
}