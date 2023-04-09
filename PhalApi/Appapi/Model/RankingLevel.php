<?php

class Model_RankingLevel extends PhalApi_Model_NotORM{
    protected $tableName = 'ranking_level';

    const USER        = 1;
    const FAMILY_DAY  = 2;
    const FAMILY_WEEK = 3;

    public function getLevel($type, $no){
        $sql = "select title,`no`,money,`time` from cmf_ranking_level where type = {$type} and `min` <= {$no} and `max` >= {$no}";
        return $this->getORM()->queryAll($sql);
    }

    public function getNoInfo($no)
    {
        $sql = "select `min`,`max`,`title`,`money` from cmf_ranking_level where `no` = {$no}";
        return $this->getORM()->queryAll($sql);
    }

    public function getMinMoney()
    {
        $sql = "select `min`,`time`,`max`,`no` from cmf_ranking_level where type = 1 order by no desc limit 1";
        $res = $this->getORM()->queryAll($sql);
        if($res)
        {
            return $res[0];
        }else{
            return [];
        }
    }
}
