<?php
require VENDOR . '/autoload.php';
class Common_JPush
{
    //关注key
    const FOLLOW      = 'follow_';
    const FOLLOW_PUSH = 'follow_push_';

    //推送消息类型
    const REGISTER = '你好鸭，欢迎来到波鸭！'; //注册后
    const RZPASSES = '恭喜鸭，认证成功，波鸭有你更精彩'; //认证成功
    const RZFAIL   = '对不起鸭，认证失败，再试试吧？客服微信：%s'; //认证失败
    const CZCG     = '充值成功鸭，%u%s已到账，请查收！'; //充值成功
    const RZJZCG   = '恭喜鸭，家族入驻申请已通过，快去邀请好友吧~'; //入驻家族成功
    const RZJZSB   = '对不起呀，认证失败，再试试吧？客服微信：%f'; //入驻家族失败
    const TXSQSL   = '好的鸭，提现申请已受理，鸭鸭点钞中~';//提现申请受理
    const TXSQSHCG = '恭喜鸭，提现申请成功，[真实金额]元武装鸭运中！'; //提现申请审核成功
    const TXSQSHSB = '对不起鸭，提现审核失败，再试试吧？客服微信：%s'; //提现申请审核失败
    const DKCG     = '打款成功，钱已到账，冲鸭！'; //打款成功
    const TXSQSB   = '对不起鸭，提现失败，[接口提示]，客服微信：%s'; //提现申请失败
    const DHJB     = '土豪鸭，兑换金币%u已到账。'; //提现到账
    const RHCG     = '鸭呼~入会成功，快和小伙伴们打个招呼吧~'; //入会成功
    const GMLH     = '恭喜鸭，靓号[%s]购买成功，从此走上人生巅峰！'; //购买靓号
    const GMZJ     = '恭喜鸭，豪车[%s]购买成功，你是这条街最靓的崽！'; //购买座驾
    const BDSJ     = '恭喜鸭，手机绑定成功~'; //绑定手机
    const FKXG     = '收到鸭，客服鸭鸭狂奔中~'; //反馈提交
    const TJJB     = '收到鸭，客服鸭鸭受理中，贯彻爱与真实的正义~'; //提交举报

    public function __construct($uid = null)
    {
        $configpri = getConfigPri();
        /* 极光推送 */
        $app_key       = '';
        $master_secret = '';
//        $this->clien   = new \JPush\Client($app_key, $master_secret, API_ROOT . '/Runtime/Jpush/' . date('Y-m-d') . '.log');
        $this->clien   = new \JPush\Client($app_key, $master_secret, null);
        if ($uid) {
            $rid_info  = DI()->notorm->user_pushid->where('uid = ?', $uid)
                ->fetchOne();
            $this->rid = $rid_info['pushid'];
            $this->uid = 'zs_' . $uid;
        }
    }

    public function addLabel($label)
    {
        try {
            $this->clien->device()->addTags($this->rid, $label);
        } catch (\Exception $e) {
            return false;
        }

    }

    public function removeLabel($label)
    {
        try {
            $this->clien->device()->removeTags($this->rid, $label);
        } catch (\Exception $e) {
            return false;
        }

    }

    public function setAlias()
    {
        try {
            if ($this->clien->device()->getAliasDevices($this->uid)) {
                $this->clien->device()->deleteAlias($this->uid);
            }
            $res = $this->clien->device()->updateAlias($this->rid, $this->uid);
            if ($res['http_code'] != 200) {
                return false;
            } else {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

    }

    public function sendAlias($title, $content, $type = 1)
    {
        try {
            return $this->clien->push()
                ->setPlatform('all')
                ->addAlias($this->uid)
                ->iosNotification(['title' => $title, 'body' => $content])
                ->androidNotification($content, [
                    'title' => $title,
                ])
                ->options(['apns_production' => false])
                ->send();
        } catch (\Exception $e) {
            file_put_contents(API_ROOT . '/Runtime/Jpush.txt',
                date('y-m-d h:i:s') . '提交参数信息 设备名:'
                . json_encode($this->uid) . '----'. $e->getMessage(). "\r\n", FILE_APPEND);
        }

    }

    public function sendLabel($title, $content, $label = [])
    {
        try {
            return $this->clien->push()
                ->setPlatform('all')
                ->addTag($label)
                ->iosNotification(['title' => $title, 'body' => $content])
                ->androidNotification($content, [
                    'title' => $title,
                ])
                ->options(['apns_production' => false])
                ->send();
        } catch (\Exception $e) {
            return false;
        }

    }

    public function sendByRegistrationId($title, $content)
    {
        try {
            return $this->clien->push()
                ->setPlatform('all')
                ->addRegistrationId($this->rid)
                ->iosNotification(['title' => $title, 'body' => $content])
                ->androidNotification($content, [
                    'title' => $title,
                ])
                ->options(['apns_production' => false])
                ->send();
        } catch (\Exception $e) {
            file_put_contents(API_ROOT . '/Runtime/newJpush.txt',
                date('y-m-d h:i:s') . '提交参数信息 设备名:'
                . json_encode($this->uid) . '----'. $e->getMessage(). "\r\n", FILE_APPEND);
        }

    }
}