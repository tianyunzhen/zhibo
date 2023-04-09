<?php
/**
 * Created by PhpStorm.
 * User: fengpeng
 * Date: 2020/8/18
 * Time: 14:13
 */

namespace app\common;
use think\Db;
class Message {

    /**
     * 发送消息
     *
     * @param     $title
     * @param     $content
     * @param int $uid
     * @param int $type
     *
     * @return long|string
     */
    public static function addMsg($title, $content, $uid = 0, $type = 1)
    {
        $data['uid']     = $uid;
        $data['type']    = $type;
        $data['title']   = $title;
        $data['content'] = $content;
        $data['addtime'] = time();
        return Db::name('message')->insert($data);
    }
}
