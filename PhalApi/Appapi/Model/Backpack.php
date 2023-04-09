<?php

class Model_Backpack extends PhalApi_Model_NotORM
{
    /* 背包礼物 */
    public function getBackpack($uid)
    {

        $list = DI()->notorm->backpack
            ->select('giftid,nums')
            ->where('uid=? and nums>0', $uid)
            ->fetchAll();

        return $list;
    }

    /* 添加背包礼物 */
    public function addBackpack($uid, $giftid, $nums)
    {

        $rs = DI()->notorm->backpack
            ->where('uid=? and giftid=?', $uid, $giftid)
            ->update(['nums' => new NotORM_Literal("nums + {$nums} ")]);
        if (!$rs) {
            $rs = DI()->notorm->backpack
                ->insert(['uid' => $uid, 'giftid' => $giftid, 'nums' => $nums]);
        }

        return $rs;
    }

    /* 减少背包礼物 */
    public function reduceBackpack($uid, $giftid, $nums)
    {

        $rs = DI()->notorm->backpack
            ->where('uid=? and giftid=? and nums>=?', $uid, $giftid, $nums)
            ->update(['nums' => new NotORM_Literal("nums - {$nums} ")]);

        return $rs;
    }

    public function getBackpackV2($uid, $type)
    {
        if (1 == $type) {
            $model = new Model_CarUser();
            $res   = $model->getCarInfoList($uid);
        }
        if (2 == $type) {
            $model = new Model_Liang();
            return $model->userList($uid);
        }
        return $res;
    }

    public function equipmentSwitch($id, $uid, $type, $status)
    {
        if (1 == $type) {
            $res = DI()->notorm->car_user
                ->where(['uid' => $uid, 'id' => $id])
                ->update(['status' => $status]);
            if ($status = 1) {
                $res = DI()->notorm->car_user
                    ->where(['uid' => $uid])
                    ->where('id <> ?', $id)
                    ->update(['status' => 0]);
            }
            $key = Common_Cache::USERCAR . $uid;
            delcache($key);
        }
        if (2 == $type) {
            $res = DI()->notorm->liang
                ->where(['uid' => $uid, 'id' => $id])
                ->update(['state' => $status]);
            if ($status = 1) {
                $res = DI()->notorm->liang
                    ->where(['uid' => $uid])
                    ->where('id <> ?', $id)
                    ->update(['state' => 0]);
            }
            $key = Common_Cache::USERLIANG . $uid;
            delcache($key);
        }
        if ($res === false) {
            return false;
        }
        return true;
    }
}
