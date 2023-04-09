<?php

class Model_Shops extends PhalApi_Model_NotORM{
    public function getLiang($page, $display_num, $type_id){
        if($page < 1){
            $page = 0;
        }else{
            $page = ($page - 1) * $display_num;
        }
        $sql  = "select id,`name`,coin,list_order,`type`,expire from cmf_liang where type = :type_id and status = 1 and uid = 0 order by list_order asc limit {$page},{$display_num}";
        $data = $this->getORM()->queryAll($sql, [':type_id' => $type_id]);
        return $data;
    }

    public function getCar($page, $display_num, $type_id){
        if($page < 1){
            $page = 0;
        }else{
            $page = ($page - 1) * $display_num;
        }
        $data = DI()->notorm->car
            ->select('id,name,thumb,needcoin,list_order,expire')
            ->where('type = ? and status = 1', $type_id)
            ->limit($page, $display_num)
            ->order('list_order asc')
            ->fetchAll();
        return $data;
    }

    public function getCarInfo($car_id){
        return DI()->notorm->car
            ->where('id = ?', $car_id)
            ->ferchOne();
    }
}
