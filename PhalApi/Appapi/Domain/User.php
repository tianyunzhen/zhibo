<?php

class Domain_User
{

    public function getBaseInfo($userId)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getBaseInfo($userId);

        return $rs;
    }

    public function checkName($uid, $name)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->checkName($uid, $name);

        return $rs;
    }

    public function userUpdate($uid, $fields)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->userUpdate($uid, $fields);

        return $rs;
    }

    public function updatePass($uid, $oldpass, $pass)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->updatePass($uid, $oldpass, $pass);

        return $rs;
    }

    public function getBalance($uid)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getBalance($uid);

        return $rs;
    }

    public function getChargeRules()
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getChargeRules();

        return $rs;
    }

    public function getProfit($uid)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getProfit($uid);

        return $rs;
    }

    public function setCash($data)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->setCash($data);

        return $rs;
    }

    public function setAttent($uid, $touid)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->setAttent($uid, $touid);

        return $rs;
    }

    public function setBlack($uid, $touid)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->setBlack($uid, $touid);

        return $rs;
    }

    public function getFollowsList($uid, $touid, $p)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getFollowsList($uid, $touid, $p);

        return $rs;
    }

    public function getFansList($uid, $touid, $p)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getFansList($uid, $touid, $p);

        return $rs;
    }

    public function getBlackList($uid, $touid, $p)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getBlackList($uid, $touid, $p);

        return $rs;
    }

    public function getLiverecord($touid, $p)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getLiverecord($touid, $p);

        return $rs;
    }

    public function getUserHome($uid, $touid)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getUserHome($uid, $touid);
        return $rs;
    }

    public function getContributeList($touid, $p)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getContributeList($touid, $p);
        return $rs;
    }

    public function setDistribut($uid, $code)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->setDistribut($uid, $code);
        return $rs;
    }

    public function getImpressionLabel()
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getImpressionLabel();

        return $rs;
    }

    public function getUserLabel($uid, $touid)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getUserLabel($uid, $touid);

        return $rs;
    }

    public function getPerSetting()
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getPerSetting();

        return $rs;
    }

    public function getUserAccountList($uid)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getUserAccountList($uid);

        return $rs;
    }

    public function getUserAccount($where)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getUserAccount($where);

        return $rs;
    }

    public function setUserAccount($data)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->setUserAccount($data);

        return $rs;
    }

    public function delUserAccount($data)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->delUserAccount($data);

        return $rs;
    }

    public function LoginBonus($uid)
    {
        $rs    = [];
        $model = new Model_User();
        $rs    = $model->LoginBonus($uid);
        return $rs;

    }

    public function getLoginBonus($uid)
    {
        $rs    = [];
        $model = new Model_User();
        $rs    = $model->getLoginBonus($uid);
        return $rs;

    }

    public function checkIsAgent($uid)
    {
        $rs    = [];
        $model = new Model_User();
        $rs    = $model->checkIsAgent($uid);
        return $rs;
    }

    public function getMyHome($touid)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getMyHome($touid);
        return $rs;
    }

    public function checkUserPhoneId($uid)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->checkUserPhoneId($uid);
        return $rs;
    }

    public function getCover($uid)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getCover($uid);
        return $rs;
    }

    public function autoIdCardAuthen($uid, $name, $idCard)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->autoIdCardAuthen($uid, $name, $idCard);
        return $rs;
    }

    public function idCardAuthen($uid, $name, $idCard)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->idCardAuthen($uid);
        return $rs;
    }

    public function getUserLevel($uid)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->getUserLevel($uid);
        return $rs;
    }

    public function getUserName($uid)
    {
        $name = DI()->notorm->user->select('user_nicename')->where('id = ?',
            $uid)->fetchOne();
        if ($name) {
            return [0, $name['user_nicename']];
        } else {
            return [1, '用户不存在'];
        }
    }

    public function transferMoney($uid, $otherid, $money)
    {
        $model = new Model_User();
        $rs    = $model->transferMoney($uid, $otherid, $money);
        return $rs;
    }

    public function feedBack($uid, $type, $content, $remark)
    {
        $rs = [];

        $model = new Model_User();
        $rs    = $model->feedBack($uid, $type, $content, $remark);
        return $rs;
    }

    public function cashRecord($uid, $page)
    {

        $model = new Model_CashRecord();
        $list  = $model->getRecord($uid, $page);
        return $list;
    }

    public function cashMoney($uid)
    {
        $model               = new Model_User();
        $user_info           = $model->get($uid, 'votes,coin,agent_money');
        $votes               = $user_info['votes'];
        $data['money']       = ($votes > 0) ? number_format(round($votes / (10000 * 100), 2),2)
            : number_format($votes);
        $data['votes']       = (string) round($votes / 100, 2);
        $data['coin']        = $user_info['coin'];
        $data['agent_money'] = $user_info['agent_money'];
        $data['proportion']  = 1;
        return $data;
    }

    public function transferMoneyRecord($uid, $page)
    {
        $page_total = 20;
        $page       = ($page - 1) * $page_total;
        $data       = DI()->notorm->user_coinrecord
            ->select('id,touid,addtime,totalcoin')
            ->where('uid = ? and action = ?', $uid, 2)
            ->order('id desc')
            ->limit($page, $page_total)
            ->fetchAll();
        foreach ($data as &$v) {
            if (!$v['touid']) {
                $v['touid'] = "官方";
            }
        }
        return $data;
    }
}
