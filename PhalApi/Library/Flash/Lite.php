<?php
/**
 * Created by PhpStorm.
 * User: fengpeng
 * Date: 2020/6/22
 * Time: 15:37
 */

class Flash_Lite
{
    const ENCRYPT_TYPE = "0";
    private $config;
    private $appId;
    private $appKey;

    public function __construct() {
        $this->config = DI()->config->get('app.ChuangLan');
    }

    private function getSign($token, $clientIp, $outId)
    {
        $content='appId'.$this->appId.'clientIp'.$clientIp.'encryptType'.self::ENCRYPT_TYPE.'outId'.$outId.'token'.$token;
        $sign = bin2hex(hash_hmac('sha256',$content, $this->appKey, true));
        $params = [
            'appId' => $this->appId,
            'token' => $token,
            'outId' => $outId,
            'clientIp' => $clientIp,
            'encryptType' => self::ENCRYPT_TYPE,
            'sign' => $sign //签名
        ];
        return $params;
    }

    public function getMobile($token, $platform, $clientIp = '', $outId = '')
    {
        $this->appId = $this->config[$platform . '_appId'];
        $this->appKey = $this->config[$platform. '_appKey'];
        $params = $this->getSign($token, $clientIp, $outId);
        if (empty($params)) {
            return 0;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $this->config['url']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $resultJson = curl_exec($ch);
        $curlInfo = curl_getinfo($ch);
        $errNo = curl_errno($ch);
        if ($errNo > 0) {
            // 通信失败处理逻辑，请自己填充 CURL 错误信息
            $errMsg = curl_error($ch) ? : "'curl请求失败'";
            throw new Exception($errMsg, $errNo);
        } elseif (!empty($curlInfo) && intval($curlInfo['http_code']) != 200) {
            // http 状态码不是 200，请求失败处理逻辑
            $httpCode = $curlInfo['http_code'];
            $errMsg = "发送失败";
            throw new Exception($errMsg, $httpCode);
            // TODO
        } else {
            // 拿到请求结果，使用返回结果逻辑
            $requestData = json_decode($resultJson, true);
            if ($requestData['code'] == 200000) {
                $mobile = $requestData['data']['mobileName']; // 手机号
                if('0' == self::ENCRYPT_TYPE){ //AES解密 ，默认方式
                    $key=md5($this->appKey);
                    $mobile=openssl_decrypt(hex2bin($mobile),  'AES-128-CBC', substr($key,0,16), OPENSSL_RAW_DATA,  substr($key,16));
                    // 如解密失败 请检查$appKey是否正确
                }elseif('1' == self::ENCRYPT_TYPE){ //RSA解密
                    $pi_key =  openssl_pkey_get_private($this->config['privateKey']);
                    openssl_private_decrypt(hex2bin($mobile),$mobile,$pi_key);//私钥解密
                    // 如解密失败 请检查$private_key是否正确
                }
                return $mobile;
            } else {
                throw new Exception($requestData['message'], $requestData['code']);
            }
        }
    }

}