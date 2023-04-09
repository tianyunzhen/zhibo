<?php

class Model_UserVoteRecord extends PhalApi_Model_NotORM
{
    protected $tableName = 'user_voterecord';
    //type字段
    const INCOME = 1; //收入
    const OUT    = 0; //支出
    //action
    const SHOULI  = 1; //收礼
    const TIXIAN  = 2; //提现
    const DUIHUAN = 3; //兑换
    const JIAZU   = 4; //家族分成
    const PAIWEI   = 6; //家族分成

    public function glamour_list($where)
    {
        $sql          = "select ifnull(a.votes_sum,0) votes_sum,
(select count(*) from cmf_live where uid = a.uid) is_live,
b.user_nicename,b.verify,b.votestotal,b.consumption,b.id,b.avatar_thumb,b.signature from (
select sum(votes) votes_sum,uid from cmf_user_voterecord where type = 1 and action = 1 {$where} group by uid having votes_sum > 0 order by votes_sum desc limit 30
) a left join cmf_user b on a.uid = b.id";
        return $this->getORM()->queryAll($sql);
    }
}
