<?php

class Domain_Festival{

    protected $startTime;
    protected $endTime;
    protected $giftId;
    protected $giftKeyNum;

    public function __construct(){
        $this->startTime  = strtotime("2020-10-1 00:00:00");
        $this->endTime    = strtotime("2020-10-1 23:59:59");
        $this->giftId     = 46;
        $this->giftKeyNum = Common_Cache::FESTIVAL_GIFT . date('Ymd', $this->startTime);
    }

    /**
     * 获取中奖榜
     * @param $page
     * @return array
     */
    public function getFestivalPrizeList($page){
        $model    = new Elast_JackpotRecord();
        $listData = $model->countList($this->startTime, $this->endTime, $this->giftId, $page);
        $list     = [];
        foreach($listData as $k => $v){
            $userInfo = getUserInfo($v['key']);
            $list[]   = [
                'userName'      => $userInfo['user_nicename'],
                'id'            => $userInfo['id'],
                'countMultiple' => $v['count_multiple']['value'],
            ];
        }
        return $list;
    }

    /**
     * 获取收礼榜
     * @param $page
     * @param $uid
     * @return array
     */
    public function getFestivalGiftList($page, $uid){
        $listData = $this->getCacheNo($this->giftKeyNum, $page, 10);
        $list     = [];
        foreach($listData as $k => $v){
            $userInfo = getUserInfo($k);
            $list[]   = [
                'countNums' => $v,
                'id'        => $k,
                'userName'  => $userInfo['user_nicename'],
            ];
        }
        return $list;
    }

    /**
     * 设置收礼榜
     * @param $giftId
     * @param $num
     * @param $liveId
     */
    public function setFestivalGift($giftId, $num, $liveId){
        if(time() >= $this->startTime && time() <= $this->endTime && $giftId == $this->giftId){
            $redis = DI()->redis;
            $nums  = $redis->ZINCRBY($this->giftKeyNum, $num, $liveId);
            if($num == $nums){
                $redis->expire($this->giftKeyNum, 2626560);
            }
        }
    }

    /**
     * 获取集合分页数据
     * @param $key
     * @param $page
     * @param $total
     * @return mixed
     */
    public function getCacheNo($key, $page, $total){
        $pages = ($page > 1) ? (($page - 1) * $total) + ($page - 1) : 0;
        $total += $pages;
        return DI()->redis->Zrevrange($key, $pages, $total, true);
    }

    /**
     * 获取排名和分数
     * @param $key
     * @param $uid
     * @return array
     */
    protected function getUserCacheInfo($key, $uid){
        $redis = DI()->redis;
        $range = $redis->ZREVRANK($key, $uid);
        if($range === false){
            $range = -1;
            $score = 0;
        }else{
            $score = $redis->ZSCORE($key, $uid) ?: 0;
        }

        return [$range, $score];
    }

    /**
     * 获取收礼榜个人信息
     * @param $uid
     * @return array
     */
    public function getFestivalGiftUserInfo($uid){
        list($range, $score) = $this->getUserCacheInfo($this->giftKeyNum, $uid);
        $userInfo = getUserInfo($uid);
        if($range === false || $range >= 100){
            $range = '100+';
        }else{
            $range += 1;
        }
        return [
            'no'        => (string)$range,
            'nums'      => $score,
            'userName'  => $userInfo['user_nicename'],
            'pic'       => $userInfo['avatar_thumb'],
            'is_remark' => $userInfo['remark_info'] ? '1' : '0',
        ];
    }

    /**
     * 获取中奖榜个人信息
     * @param $uid
     * @return array
     */
    public function getFestivalPrizeUserInfo($uid){
        $userInfo     = getUserInfo($uid);
        $elasticModel = new Elast_JackpotRecord();
        $score        = $elasticModel->getUserJackpotInfo($uid, $this->startTime, $this->endTime, $this->giftId);
        return [
            'userName' => $userInfo['user_nicename'],
            'nums'     => $score,
            'pic'      => $userInfo['avatar_thumb'],
        ];
    }
}