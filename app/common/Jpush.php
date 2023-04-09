<?php
/**
 * Created by PhpStorm.
 * User: fengpeng
 * Date: 2020/8/15
 * Time: 18:12
 */

namespace app\common;
use JPush\Client;
use think\Db;

class Jpush {
    //推送消息类型
    const TXSQSB = '恭喜鸭，认证成功，波鸭有你更精彩！'; //认证审核通过后
    const DHJB   = '对不起鸭，认证失败，再试试吧？客服微信：Boya-kefu01'; //认证审核拒绝后
    const RHCG   = '恭喜鸭，家族入驻申请已通过，快去邀请好友吧~'; //家族入驻审核通过后
    const GMLH   = '对不起鸭，家族入驻申请未通过，再试试吧？客服微信：Boya-kefu01'; //家族入驻审核拒绝后
    const GMZJ   = '恭喜鸭，提现申请成功，[真实金额]元武装鸭运中！'; //提现申请审核通过后
    const GMZA   = '恭喜鸭，提现已打款成功，[真实金额]元武装鸭运中！'; //提现申请审核通过后
    const BDSJ   = '对不起鸭，提现审核失败，再试试吧？客服微信：Boya-kefu01'; //提现申请审核失败后
    const FKXG   = '打款成功，钱已到账，冲鸭！'; //提现打款成功后
    const TJJB   = '对不起鸭，提现失败，[接口提示]，客服微信：Boya-kefu01'; //提现申请失败后
    const DLCZ   = '您的代理账户充值到账'; //提现申请失败后

    public function __construct($uid = null)
    {
        require_once CMF_ROOT.'sdk/JPush/autoload.php';
        /* 极光推送 */
        $app_key       = '73d377f819cc736ca3aa255b';
        $master_secret = '5191031f68b23c127828351b';
        $this->client   = new Client($app_key, $master_secret);
        if ($uid) {
            $rid_info  = Db::name('user_pushid')->where(['uid' => $uid])
                ->find();
            $this->rid = $rid_info['pushid'];
            $this->uid = $uid;
        }
    }

    public function addLabel($label)
    {
        try {
            $this->client->device()->addTags($this->rid, $label);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function removeLabel($label)
    {
        try {
            $this->client->device()->removeTags($this->rid, $label);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function setAlias()
    {
        try {
            if ($this->client->device()->getAliasDevices($this->uid)) {
                $this->client->device()->deleteAlias($this->uid);
            }
            $res = $this->client->device()->updateAlias($this->rid, $this->uid);
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
            return $this->client->push()
                ->setPlatform('all')
                ->addAlias($this->uid)
                ->iosNotification(['title' => $title, 'body' => $content])
                ->androidNotification($content, [
                    'title' => $title,
                ])
                ->options(['apns_production' => false])
                ->send();
        } catch (\Exception $e) {
            file_put_contents(CMF_DATA . 'jpush.txt',
                date('y-m-d h:i:s') . '提交参数信息 设备名:'
                . json_encode($this->uid) . '----'. $e->getMessage(). "\r\n", FILE_APPEND);
             throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function sendLabel($title, $content, $label = [])
    {
        try {
            return $this->client->push()
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
        $c = null;
        try {
            $c = $this->client->push();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        try {
            $c = $c->setPlatform('all');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        try {
            $c = $c->addRegistrationId($this->rid);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        try {
            $c = $c->iosNotification(['title' => $title, 'body' => $content]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        try {
            $c = $c->androidNotification($content, [
                'title' => $title,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        try {
            $c = $c->options(['apns_production' => false]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        try {
            $c->send();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
//        try {
//            return $this->client->push()
//                ->setPlatform('all')
//                ->addRegistrationId($this->rid)
//                ->iosNotification(['title' => $title, 'body' => $content])
//                ->androidNotification($content, [
//                    'title' => $title,
//                ])
//                ->options(['apns_production' => false])
//                ->send();
//        } catch (\Exception $e) {
//            file_put_contents(CMF_DATA . 'newJpush.txt',
//                date('y-m-d h:i:s') . '提交参数信息 设备名:'
//                . json_encode($this->uid) . '----'. $e->getMessage(). $e->getFile()."\r\n", FILE_APPEND);
//            throw new \Exception($e->getMessage(), $e->getCode());
//        }
    }
}