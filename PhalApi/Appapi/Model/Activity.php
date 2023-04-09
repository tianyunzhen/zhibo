<?php

class Model_Activity extends PhalApi_Model_NotORM{
    //type
    const OFF = 1; //开启
    const NO  = 0; //关闭

    public function getList($page, $type){
        if($type == self::OFF)//开启
        {
            $where = 'and end_time > :time';
        }else{
            $where = 'and end_time < :time';
        }
        $total = 20;
        $page  = $page > 0 ? ($page - 1) * $total : 0;
        $sql   = "select * from cmf_activity where `status` = 1 {$where} order by list_order asc limit :page,:total";
        return $this->getORM()->queryAll($sql, [':page' => $page, ':total' => $total, ':time' => time()]);
    }
}
