<?php
/**
 * Created by PhpStrom.
 * Author: 吴晓平 (121152168@qq.com)
 * Date: 2020/7/4
 * Time: 16:48
 */

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29
 * Time: 14:26
 */
class redisLbs
{
    /**
     * @description: 添加点
     *
     * @param int   $uid 用户id
     * @param float $lon 经度
     * @param float $lat 纬度
     *
     * @return bool
     */
    public function geoAdd($uid, $lon, $lat)
    {
        DI()->redis->geoAdd('current_user' . $uid, $lon, $lat, $uid);
        return true;
    }

    /**
     * @description: 添加点
     *
     * @param int    $uid         当前登录用户id
     * @param float  $longitude   经度
     * @param float  $latitude    纬度
     * @param int    $maxDistance 查询最大的距离
     * @param string $unit        单位，默认公里显示
     *
     * @return mixed
     */
    public function geoNearFind(
        $uid,
        $longitude,
        $latitude,
        $maxDistance = 0,
        $unit = 'km'
    ) {
        // 显示距离从近到远排序
        $options = ['WITHDIST', 'ASC', 'WITHCOORD'];
        return DI()->redis->geoRadius('current_user' . $uid, $longitude,
            $latitude, $maxDistance, $unit, $options);
    }

}