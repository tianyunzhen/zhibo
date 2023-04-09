<?php

class Domain_Activity{
    public function getList($page,$type){
        $model = new Model_Activity();
        $rs    = $model->getList($page,$type);
        foreach($rs as $k=>&$v){
            $v['banner'] = get_upload_path($v['banner']);
        }
        return $rs;
    }
}
