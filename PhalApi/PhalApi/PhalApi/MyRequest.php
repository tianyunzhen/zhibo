<?php
/**
 * Created by PhpStorm.
 * User: fengpeng
 * Date: 2020/7/24
 * Time: 11:13
 */

class My_Request extends PhalApi_Request {

    public function __construct($data = NULL) {
        parent::__construct($data);
        // json处理
        $this->post = json_decode(file_get_contents('php://input'), TRUE);
        // 普通xml处理
        $this->post = simplexml_load_string (
          file_get_contents('php://input'),
          'SimpleXMLElement',
          LIBXML_NOCDATA
         );
        $this->post = json_decode(json_encode($this->post), TRUE);
    }
}