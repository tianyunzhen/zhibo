<?php

class Domain_JackPot{

    protected $jsq_key       = 'jackPot:jack_pot_jsq_';
    protected $gift_info;
    protected $gift_number;
    protected $max_number;
    protected $jack_pot_conf;
    protected $pool_periods;
    protected $multiple_desc = [];
    protected $pool_key      = 'jackPot:jack_pool_';

    public function __construct($gift_info, $gift_number, $uid = 0){
        $is_uid = '';
        if($uid > 0){
            $is_uid = $uid . '_';
        }
        $this->jsq_key       .= $is_uid . $gift_info['id'];
        $this->pool_key      .= $is_uid . $gift_info['id'] . '_';
        $this->gift_info     = $gift_info;
        $this->gift_number   = $gift_number;
        $this->jack_pot_conf = json_decode($gift_info['info'], true);
        $this->max_number    = $gift_info['max_num'];
    }

    public function start(){
        $jsq = DI()->redis->INCRBY($this->jsq_key, $this->gift_number);
        //初始值
        $startNumber = $jsq - $this->gift_number;
        //
        //第一次期数
        list($this->pool_periods, $start) = $this->get_div_and_mod($startNumber, $this->max_number);
        $multiple   = 0;
        $end_number = $start + $this->gift_number;
        //是否超过上限
        if($end_number > $this->max_number){
            //判断第一次抽奖
            $multiple += $this->judge_jack_pot($start, $this->max_number);
            $this->del_pool();
            //是否跨池
            $surplus = $this->gift_number - ($this->max_number - $start);
            if($surplus > $this->max_number){
                $intval_num = intval($surplus / $this->max_number);
                if($intval_num > 0){
                    $multiple += $intval_num * array_sum($this->jack_pot_conf);
                    array_merge($this->multiple_desc, $this->jack_pot_conf);
                }
            }
            //第二次期数
            list($this->pool_periods, $end_number)
                = $this->get_div_and_mod($jsq, $this->max_number);
            $multiple += $this->judge_jack_pot(1, $end_number);
        }else{
            $multiple += $this->judge_jack_pot($start, $end_number);
        }
        return [$multiple, $this->multiple_desc];
    }

    protected function del_pool(){
        DI()->redis->del($this->pool_key . $this->pool_periods);
    }


    protected function get_div_and_mod($left_operand, $right_operand){
        $div = intval($left_operand / $right_operand);
        $mod = $left_operand % $right_operand;
        return [$div, $mod];
    }

    //判断中奖
    protected function judge_jack_pot($start, $end){
        $num = 0;
        //获取奖池
        $jackpot_info = $this->getJackpot();
        foreach($jackpot_info as $k => $v){
            if($k >= $start && $k <= $end){
                $this->multiple_desc[] = $v;
                $num                   += $v;
                unset($jackpot_info[$k]);
            }
        }
        if($num > 0){
            DI()->redis->set($this->pool_key . $this->pool_periods, json_encode($jackpot_info));
        }
        return $num;
    }

    protected function getJackpot(){
        $key          = $this->pool_key . $this->pool_periods;
        $jackpot_info = DI()->redis->get($key);
        if(!$jackpot_info){
            $jackpot_info = $this->createPool();
            DI()->redis->set($key, json_encode($jackpot_info));
        }else{
            $jackpot_info = json_decode($jackpot_info, true);
        }
        return $jackpot_info;
    }

    protected function randomRange(Int $min, Int $max, Int $num){
        $arr = range($min, $max);
        shuffle($arr);
        return array_slice($arr, 0, $num);
    }

    protected function createPool(){
        $jackpot_info = [];
        foreach($this->jack_pot_conf as $k => $v){
            $num_arr          = explode('-', $k);
            $jackpot_num      = count($v);//奖池数量
            $jackpot_jack_num = $this->randomRange($num_arr[0], $num_arr[1], $jackpot_num);
            $arr              = array_combine($jackpot_jack_num, $v);
            $jackpot_info     += $arr;
        }
        ksort($jackpot_info);
        return $jackpot_info;
    }
}
