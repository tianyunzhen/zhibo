<?php

class Domain_Cdnrecord
{
    public function getCdnRecord($id)
    {
        $rs = [];

        $model = new Model_Cdnrecord();
        $rs    = $model->getCdnRecord($id);

        return $rs;
    }

}
