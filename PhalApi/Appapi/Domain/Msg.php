<?php

class Domain_Msg
{
    public function notRead($uid)
    {
        $model = new Model_Msg();
        return $model->notRead($uid);
    }

    public function msgList($uid, $page)
    {
        $model = new Model_Msg();
        return $model->msgList($uid, $page);
    }

    public function readAll($uid)
    {
        $model = new Model_Msg();
        $list  = $model->notReadList($uid);
        if ($list) {
            $list = array_column($list, 'id');
            $data = [];
            foreach ($list as $v) {
                $data[] = [
                    'msg_id'  => $v,
                    'uid'     => $uid,
                    'addtime' => time(),
                ];
            }
            $ids = join(',', $list);
            try {
                DI()->notorm->message->queryAll('begin');
                if (!DI()->notorm->message_read->insert_multi($data)) {
                    DI()->notorm->message->queryAll('rollback');
                    return [1, '阅读失败'];
                }
                $res = $model->update_read($ids);
                if (!$res) {
                    DI()->notorm->message->queryAll('rollback');
                    return [2, '阅读失败'];
                }
                DI()->notorm->message->queryAll('commit');
            } catch (\Exception $e) {
                DI()->notorm->message->queryAll('rollback');
                return [99, '发生异常:' . $e->getMessage()];
            }
        }

        return [0, 'ok'];
    }

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
        $model           = new Model_Msg();
        return $model->insert($data);
    }
}
