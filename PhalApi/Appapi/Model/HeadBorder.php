<?php

class Model_HeadBorder extends PhalApi_Model_NotORM{
    protected $tableName = 'head_border';
    const UPPER = 1; //上架
    const LOWER = 2; //下架

    const ACTIVE = 1;

    public function getList($type, $page, $columns = '*'){
        $total = 10;
        $page  = $this->paging($page, $total);
        return $this->getORM()
            ->where([
                'is_up' => self::UPPER,
                'type'  => $type,
            ])
            ->limit($page, $total)
            ->select($columns)
            ->fetchAll();
    }
}
