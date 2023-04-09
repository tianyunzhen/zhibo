<?php

/**
 * 奖池
 */
class Api_Jackpot extends PhalApi_Api
{

    public function getRules()
    {
        return [
        ];
    }


    /**
     * 奖池
     *
     * @desc 用于 获取奖池信息
     * @return int code 操作码，0表示成功
     * @return array info
     * @return string info[0].total 总额
     * @return string info[0].level 等级
     * @return string msg 提示信息
     */
    public function getJackpot()
    {
        $rs = ['code' => 0, 'msg' => '', 'info' => []];


        $info = getJackpotInfo();

        unset($info['id']);
        $rs['info'][0] = $info;
        return $rs;
    }


}
