<?php

class Model_Msg extends PhalApi_Model_NotORM
{
    protected $tableName = 'message';

    /**
     * 未读消息
     *
     * @param $uid
     *
     * @return mixed
     */
    public function notRead($uid)
    {
        $sql
             = "select count(*) number from cmf_message where (type = 2 or uid = :uid) and id not in (
select msg_id from cmf_message_read where uid = :uid)";
        $res = $this->getORM()->queryAll($sql, [':uid' => $uid]);
        return $res[0]['number'];
    }

    /**
     * 未读列表
     *
     * @param $uid
     *
     * @return mixed
     */
    public function notReadList($uid)
    {
        $sql
            = "select id from cmf_message where (type = 2 or uid = :uid) and id not in (
select msg_id from cmf_message_read where uid = :uid)";
        return $this->getORM()->queryAll($sql, [':uid' => $uid]);
    }

    public function msgList($uid, $page)
    {
        $page_total = 20;
        $page       = $page < 1 ? 0 : ($page - 1) * $page_total;
        $sql
                    = "select if(b.id is null,2,1) is_read,a.title,a.content,a.addtime from cmf_message a left join cmf_message_read b on a.id = b.msg_id
where (a.type = 2 or a.uid = :uid) and (b.uid = :uid or b.uid is null) order by is_read,a.addtime desc limit {$page},{$page_total}";
        $res        = $this->getORM()->queryAll($sql, [':uid' => $uid]);
        return $res;
    }

    public function update_read($ids)
    {
        return $this->getORM()
            ->where("id in ({$ids})")
            ->update(['read_number' => new NotORM_Literal('read_number + 1')]);
    }

    public function insertAll($data)
    {
        return $this->getORM()->insert_multi($data);
    }
}