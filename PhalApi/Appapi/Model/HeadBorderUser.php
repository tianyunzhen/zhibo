<?php

class Model_HeadBorderUser extends PhalApi_Model_NotORM{
    protected $tableName = 'head_border_user';
    const USE       = 1;
    const UNINSTALL = 2;

    public function getHeadDes($uid, $headId, $columns = '*'){
        return $this->getORM()
            ->where([
                'uid'     => $uid,
                'head_id' => $headId,
            ])
            ->select($columns)
            ->fetchOne();
    }

    public function getUserHeadBorderList($page, $uid){
        $total = 15;
        $page  = $this->paging($page, $total);
        $sql   = "select b.title,b.pic,b.type,a.id,a.is_use,a.expire,a.create_time,a.update_time from cmf_head_border_user a left join cmf_head_border b on a.head_id = b.id where a.uid = :uid and (a.expire = 0 or a.expire >= :times) order by a.is_use asc,a.update_time desc,a.id desc limit :page,:total";
        return $this->getORM()->queryAll($sql, [
            ':uid'   => $uid,
            ':times' => time(),
            ':page'  => $page,
            ':total' => $total,
        ]);
    }

    public function uninstallAll($uid){
        try{
            return $this->getORM()
                ->where(['uid' => $uid, 'is_use' => self::USE])
                ->update([
                    'is_use'      => self::UNINSTALL,
                    'update_time' => time(),
                ]);
        }catch(\Exception $e){
            return false;
        }
    }

    public function useHeadBorder($id, $uid){
        try{
            return $this->getORM()
                ->where(['id' => $id, 'uid' => $uid])
                ->update([
                    'is_use'      => self::USE,
                    'update_time' => time(),
                ]);
        }catch(\Exception $e){
            return false;
        }
    }

    public function getUserHeadBorder($uid){
        $sql = "select b.title,b.pic,b.type,a.id,a.is_use,a.expire,a.create_time,a.update_time from cmf_head_border_user a left join cmf_head_border b on a.head_id = b.id where a.uid = :uid and a.is_use = 1 and (a.expire = 0 or a.expire >= :times) limit 1";
        return $this->getORM()->queryAll($sql, [
            ':uid'   => $uid,
            ':times' => time(),
        ]);
    }

    public function all($data){
        $this->getORM()->insert_multi($data);
    }
}
