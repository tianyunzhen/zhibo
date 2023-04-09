<?php

class Domain_UserCoin{

    public function updateCoin($data){
        $recordData = [
            'type'      => $data['type'],
            'action'    => $data['action'],
            'uid'       => $data['uid'],
            'touid'     => $data['touid'] ?? $data['uid'],
            'giftid'    => $data['giftid'] ?? 0,
            'giftcount' => $data['giftcount'] ?? 0,
            'totalcoin' => $data['totalcoin'] ?? $data['totalcoin'],
            'showid'    => $data['showid'] ?? 0,
            'addtime'   => time(),
            'mark'      => $data['mark'] ?? 0,
        ];
        $userModel  = new Model_User();
        if(!$userModel->updateCoin($data['uid'], $data['totalcoin'], $data['type'])){
            return [1, '用户金额更新失败'];
        }

        $userCoinModel = new Model_UserCoinRecord();
        if(!$userCoinModel->insert($recordData)){
            return [1, '流水记录添加失败'];
        }

        return [0, 'ok'];
    }
}
