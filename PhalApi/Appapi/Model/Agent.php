<?php

class Model_Agent extends PhalApi_Model_NotORM
{
    /* 引导页 */
    public function getCode($uid)
    {

        $agentinfo = DI()->notorm->agent_code
            ->select('code')
            ->where('uid=?', $uid)
            ->fetchOne();

        return $agentinfo;
    }

    public function getShareImage($slideId)
    {
        $rs              = DI()->notorm->slide_item
            ->select("image as slide_pic")
            ->where("status='1' and slide_id=$slideId")
            ->fetchOne();
        $rs['slide_pic'] = get_upload_path($rs['slide_pic']) ?? '';
        return $rs;
    }

}
