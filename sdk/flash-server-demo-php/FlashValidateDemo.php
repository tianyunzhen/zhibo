<?php

header('content-type:text/html;charset=utf-8');

/**
 * 本机号校验Demo
 */
 
$url = 'https://api.253.com/open/flashsdk/mobile-validate'; 
$appKey='xxxxxxxx';// 当前APP对应的appkey


$appId='xxxxxxxx'; // 当前APP对应的appid
$token='xxxxxxxx...'; // 运营商token,SDK返回参数
$mobile='133...'; //待校验的手机号码
$outId=''; // 客户流水号

$content='appId'.$appId.'mobile'.$mobile.'outId'.$outId.'token'.$token;
$sign=bin2hex(hash_hmac('sha256',$content, $appKey, true));

$params = [
    'appId' => $appId, 
    'token' => $token,
	'mobile' => $mobile,
    'outId' => $outId, 
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
		$tradeNo = $requestData['data']['tradeNo']; // 流水号
		$isVerify = $requestData['data']['isVerify']; // 手机号
		if('1' == $isVerify){
			echo '校验成功，是本机号码';		
		}else{ //不是本机号码
			echo '不是本机号码';	
		}
		
        // 拿到返回数据继续处理逻辑 TODO
		
    } else {
		echo $resultJson;
        // 响应异常处理逻辑 TODO
    }
}
?>