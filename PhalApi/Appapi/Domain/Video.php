<?php

class Domain_Video
{
    public function setVideo($data, $music_id)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->setVideo($data, $music_id);

        return $rs;
    }

    public function setComment($data)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->setComment($data);

        return $rs;
    }

    public function addView($uid, $videoid)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->addView($uid, $videoid);

        return $rs;
    }

    public function addLike($uid, $videoid)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->addLike($uid, $videoid);

        return $rs;
    }

    public function addStep($uid, $videoid)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->addStep($uid, $videoid);

        return $rs;
    }

    public function addShare($uid, $videoid)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->addShare($uid, $videoid);

        return $rs;
    }

    public function setBlack($uid, $videoid)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->setBlack($uid, $videoid);

        return $rs;
    }

    public function addCommentLike($uid, $commentid)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->addCommentLike($uid, $commentid);

        return $rs;
    }

    public function getVideoList($uid, $p)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->getVideoList($uid, $p);

        return $rs;
    }

    public function getAttentionVideo($uid, $p)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->getAttentionVideo($uid, $p);

        return $rs;
    }

    public function getVideo($uid, $videoid)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->getVideo($uid, $videoid);

        return $rs;
    }

    public function getComments($uid, $videoid, $p)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->getComments($uid, $videoid, $p);

        return $rs;
    }

    public function getReplys($uid, $commentid, $p)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->getReplys($uid, $commentid, $p);

        return $rs;
    }

    public function getMyVideo($uid, $p)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->getMyVideo($uid, $p);

        return $rs;
    }

    public function del($uid, $videoid)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->del($uid, $videoid);

        return $rs;
    }

    public function getHomeVideo($uid, $touid, $p)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->getHomeVideo($uid, $touid, $p);

        return $rs;
    }

    public function report($data)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->report($data);

        return $rs;
    }

    public function getRecommendVideos($uid, $p, $isstart)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->getRecommendVideos($uid, $p, $isstart);

        return $rs;
    }


    public function test()
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->test();

        return $rs;
    }

    public function getNearby($uid, $lng, $lat, $p)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->getNearby($uid, $lng, $lat, $p);

        return $rs;
    }

    public function getReportContentlist()
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->getReportContentlist();

        return $rs;
    }

    public function setConversion($videoid)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->setConversion($videoid);

        return $rs;
    }

    public function getClassVideo($videoclassid, $uid, $p)
    {
        $rs = [];

        $model = new Model_Video();
        $rs    = $model->getClassVideo($videoclassid, $uid, $p);

        return $rs;
    }
}
