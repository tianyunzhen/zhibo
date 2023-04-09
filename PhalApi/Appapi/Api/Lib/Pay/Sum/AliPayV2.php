<?php
require_once VENDOR . 'autoload.php';
require_once VENDOR . '../sdk/alipayV2/aop/AopClient.php';
require_once VENDOR . '../sdk/alipayV2/aop/request/AlipayTradeWapPayRequest.php';
class Api_Lib_Pay_Sum_AliPayV2 implements Api_Lib_Pay_Pay
{
    public function start($data, $h5)
    {
        try {
            $out_trade_no = $data['orderno'];
            $money = $data['money'];
            $aop = new \AopClient();
            $aop->gatewayUrl = '';
            $aop->appId = '';
            $aop->alipayrsaPublicKey = '';
            $aop->apiVersion = '1.0';
            $aop->postCharset='UTF-8';
            $aop->format='json';
            $aop->signType='RSA2';
            $request = new \AlipayTradeWapPayRequest();
            $request->setBizContent("{" .
                "    \"body\":\"波鸭\"," .
                "    \"subject\":\"波鸭\"," .
                "    \"out_trade_no\":\"$out_trade_no\"," .
                "    \"timeout_express\":\"90m\"," .
//                "    \"notify_url\":$aop->notifyUrl," .
                "    \"total_amount\":$money," .
                "    \"product_code\":\"QUICK_WAP_WAY\"" .
                "  }");
            $request->setNotifyUrl(DI()->config->get('app.Notify.ali'));
            $result = $aop->pageExecute($request);
            return [0, $result, ''];
        } catch (\Exception $e) {
            return [1, '', $e->getMessage()];
        }
    }

    public function notify($arr)
    {
        return __FUNCTION__;
    }
}