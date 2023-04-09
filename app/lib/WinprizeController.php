<?php
/**
 * 会员等级
 */

namespace app\lib;

use think\Db;
use think\facade\Log;

class WinprizeController
{

    protected $uid; //用户ID
    protected $num; //礼物数量
    protected $gift_id; //礼物ID
    protected $redis; //redis连接对象
    protected $gift_info;//礼物信息
    protected $jsq_key          = 'gift_jsq_'; //计数器redis键值
    protected $gift_key         = 'gift_id_';//礼物redis键值
    protected $jackpot_info_key = 'jackpot_info_'; //礼物ID

    public function __construct($uid, $num, $gift_id)
    {
        require_once CMF_ROOT . 'app/redis.php';
        $this->uid              = $uid;
        $this->num              = $num;
        $this->gift_id          = $gift_id;
        $this->jsq_key          .= $gift_id;
        $this->gift_key         .= $gift_id;
        $this->jackpot_info_key .= $gift_id;
        $this->redis            = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }


    /**
     * 开始抽奖
     *
     * @return float|int
     */
    public function start()
    {
        try {
            $prize_count = 0;
            //获取礼物信息
            list($code, $error_msg, $this->gift_info) = $this->getGiftInfo();
            if ($code === 0) {
                //获取礼物计数器
                $jsq = $this->addJsq($this->num);
                //初始号
                $start_num = $jsq - $this->num;
                //大于上限
                if ($jsq >= $this->gift_info['max_num']) {
                    //第一次
                    $prize_count = $this->prize_sum($start_num + 1,
                        $this->gift_info['max_num']);
                    $this->delJackpost();
                    //跨奖池
                    $more_num = $jsq - ($this->gift_info['max_num']
                            - $start_num);
                    $this->delJsq();
                    if ($more_num >= $this->gift_info['max_num']) {
                        list($intval_num, $span_num)
                            = $this->get_div_and_mod($more_num,
                            $this->gift_info['max_num']);
                        if ($intval_num > 0) {
                            $prize_count += $intval_num
                                * array_sum($this->gift_info['jackpot']);
                        }
                        if ($span_num > 0) {
                            $this->addJsq($span_num);
                            $prize_count += $this->prize_sum(1, $span_num);
                        }
                    } else {
                        $this->addJsq($more_num);
                        $prize_count += $this->prize_sum(1, $more_num);
                    }
                } else {
                    $prize_count += $this->prize_sum($start_num, $jsq);
                }
            } else {
                $this->logCreate($error_msg);
                return [1, $error_msg];
            }
        } catch (\Exception $e) {
            $this->decJsq($this->num);
            $this->logCreate($e->getMessage(), 'error');
            return [99, $e->getMessage()];
        }
        return [0, $prize_count];
    }

    /**
     * 计数器
     *
     * @param $num
     *
     * @return int
     */
    public function addJsq($num)
    {
        return $this->redis->incrBy($this->jsq_key, $num);
    }

    protected function get_div_and_mod($left_operand, $right_operand)
    {
        $div = intval($left_operand / $right_operand);
        $mod = $left_operand % $right_operand;
        return [$div, $mod];
    }

    public function decJsq($num)
    {
        return $this->redis->decrBy($this->jsq_key, $num);
    }

    /**
     * 清空计数器
     */
    public function delJsq()
    {
        $this->redis->del($this->jsq_key);
    }

    /**
     * 中奖判断
     *
     * @param $start_num
     * @param $end_num
     *
     * @return float|int
     */
    protected function prize_sum($start_num, $end_num)
    {
        $prize_sum = 0;
        //获取奖池
        $jackpot_info = $this->getJackpot();
        $arr          = range($start_num, $end_num);
        $user_num     = array_fill_keys($arr, 1);
        $prize_num    = array_intersect_key($jackpot_info, $user_num);
        if ($prize_num) {
            $prize_sum = array_sum($prize_num);
        }
        return $prize_sum;
    }

    /**
     * 清空奖池
     */
    protected function delJackpost()
    {
        $this->redis->del($this->jackpot_info_key);
    }

    /**
     * 获取奖池
     *
     * @return array|bool|false|mixed|string
     */
    protected function getJackpot()
    {
        $jackpot_info = $this->redis->get($this->jackpot_info_key);
        if (!$jackpot_info) {
            $jackpot_num      = count($this->gift_info['jackpot']);
            $jackpot_jack_num = $this->randomRange(1,
                $this->gift_info['max_num'], $jackpot_num);
            $jackpot_info     = array_combine($jackpot_jack_num,
                $this->gift_info['jackpot']);
            $this->redis->set($this->jackpot_info_key,
                json_encode($jackpot_info));
        } else {
            $jackpot_info = json_decode($jackpot_info, true);
        }
        return $jackpot_info;
    }

    /**
     * 范围随机值
     *
     * @param Int    $min
     * @param Int    $max
     * @param String $num
     *
     * @return array
     */
    function randomRange(Int $min, Int $max, Int $num)
    {
        $arr = range($min, $max);
        shuffle($arr);
        return array_slice($arr, 0, $num);
    }

    /**
     * 礼物信息
     *
     * @return array|bool|mixed|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getGiftInfo()
    {
        $gift_info = $this->redis->get($this->gift_key);
        if (!$gift_info) {
            $gift_info = Db::name('gift')
                ->field('info')
                ->where(['id' => $this->gift_id])
                ->find();
            if (!$gift_info) {
                return [1, '礼物信息获取失败', ''];
            }
            $gift_info = json_decode($gift_info['info'], true);
            if (!$gift_info) {
                return [1, '礼物配置信息解析失败', ''];
            }
            if (!isset($gift_info['max_num']) || empty($gift_info['max_num'])) {
                return [1, '礼物上限配置信息为空', ''];
            }
            if (!isset($gift_info['jackpot']) || empty($gift_info['jackpot'])) {
                return [1, '礼物奖品配置信息为空', ''];
            }
            $this->redis->set($this->gift_key, json_encode($gift_info));
        } else {
            $gift_info = json_decode($gift_info, true);
        }
        return [0, '', $gift_info];
    }

    public function logCreate($error_msg)
    {
        $error_msg .= "--礼物ID$this->gift_id,用户ID:$this->uid,礼物数量:$this->num,礼物ID:$this->gift_id";
        Log::record($error_msg, 'log');
    }
}