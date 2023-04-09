<?php

class Domain_Guide
{
    public function getGuide()
    {
        $rs = [];

        $model = new Model_Guide();
        $rs    = $model->getGuide();

        return $rs;
    }

}
