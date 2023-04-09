<?php

header('content-type:text/html;charset=utf-8');

/**
 * 免密登录Demo
 */
 
$url = 'https://api.253.com/open/flashsdk/mobile-query'; 
$appKey='xxxxxxxx';// 当前APP对应的appkey

//应用私钥，可选，如用RSA解密，必须填写。
$private_key = '-----BEGIN RSA PRIVATE KEY-----
xxxxxxxx...
-----END RSA PRIVATE KEY-----';

$appId='xxxxxxxx'; // 当前APP对应的appid
$token='xxxxxxxx...'; // 运营商token,SDK返回参数
$outId=''; // 客户流水号
$clientIp=''; // 客户端IP
$encryptType='1'; // 加密方式：0 AES 1 RSA  ， 默认0 AES

$content='appId'.$appId.'clientIp'.$clientIp.'encryptType'.$encryptType.'outId'.$outId.'token'.$token;
$sign=bin2hex(hash_hmac('sha256',$content, $appKey, true));

$params = [
    'appId' => $appId, 
    'token' => $token,
    'outId' => $outId, 
    'clientIp' => $clientIp, 
	'encryptType' => $encryptType, 
    'sign' => $sign //签名
];


// CURL 模拟 post 请求
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$resultJson = curl_exec($ch);
$curlInfo = curl_getinfo($ch);

// CURL 错误码
$errNo = curl_errno($ch);
if ($errNo > 0) {
    // 通信失败处理逻辑，请自己填充

    // CURL 错误信息
    $errMsg = curl_error($ch);
    // TODO
} elseif (!empty($curlInfo) && intval($curlInfo['http_code']) != 200) {
    // http 状态码不是 200，请求失败处理逻辑
    $httpCode = $curlInfo['http_code'];
    // TODO
} else {
    // 拿到请求结果，使用返回结果逻辑
    $requestData = json_decode($resultJson, true);
    if ($requestData['code'] == 200000) {
        $chargeStatus = $requestData['chargeStatus']; // 是否收费，枚举值：1 ：收费 0：不收费
        $mobile = $requestData['data']['mobileName']; // 手机号
		
		if('0' == $encryptType){ //AES解密 ，默认方式
			$key=md5($appKey);
			$mobile=openssl_decrypt(hex2bin($mobile),  'AES-128-CBC', substr($key,0,16), OPENSSL_RAW_DATA,  substr($key,16));
			// 如解密失败 请检查$appKey是否正确
		}elseif('1' == $encryptType){ //RSA解密
			$pi_key =  openssl_pkey_get_private($private_key);
			openssl_private_decrypt(hex2bin($mobile),$mobile,$pi_key);//私钥解密
			// 如解密失败 请检查$private_key是否正确
		}
		
        $tradeNo = $requestData['data']['tradeNo']; // 流水号
        // 拿到返回数据继续处理逻辑 TODO
		echo $mobile;
    } else {
		echo $resultJson;
        // 响应异常处理逻辑 TODO
    }
}
?>