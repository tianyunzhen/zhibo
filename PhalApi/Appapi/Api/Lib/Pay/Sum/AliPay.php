<?php
require_once VENDOR . 'autoload.php';

class Api_Lib_Pay_Sum_AliPay implements Api_Lib_Pay_Pay
{
    public function __construct()
    {
        \Alipay\EasySDK\Kernel\Factory::setOptions($this->getOptions());
    }

    public function start($data, $h5)
    {
        try {
            $result = \Alipay\EasySDK\Kernel\Factory::payment()->app()
                ->pay($data['title'], $data['orderno'], $data['money']);
            $responseChecker
                    = new \Alipay\EasySDK\Kernel\Util\ResponseChecker();
            if ($responseChecker->success($result)) {
                return [0, $result->body, ''];
            } else {
                return [1, '', $result->msg . "，" . $result->subMsg . PHP_EOL];
            }
        } catch (Exception $e) {
            return [1, '', $e->getMessage()];
        }
    }

    protected function getOptions()
    {
        $options              = new \Alipay\EasySDK\Kernel\Config();
        $options->protocol    = 'https';
        $options->gatewayHost = 'openapi.alipay.com';
        $options->signType    = 'RSA2';
        $options->appId       = '';
        $options->merchantPrivateKey = '';
        $options->alipayPublicKey = '';
        $options->notifyUrl
                              = "";
        $options->encryptKey  = "";
        return $options;
    }

    public function notify($arr)
    {
        return \Alipay\EasySDK\Kernel\Factory::payment()->common()
            ->verifyNotify($arr);
    }
}