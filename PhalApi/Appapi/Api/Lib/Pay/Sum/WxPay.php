<?php
require_once VENDOR . 'autoload.php';

class Api_Lib_Pay_Sum_WxPay implements Api_Lib_Pay_Pay
{
    public function start($data, $h5)
    {
        try {
//            $configpri = getConfigPri();
            $noceStr = md5(rand(100, 1000) . time());//获取随机字符串
            $paramarr         = [
//                "appid"        => $configpri['wx_appid'],
                "appid"        => '',
                "body"         => "充值{$data['coin']}虚拟币",
//                "mch_id"       => $configpri['wx_mchid'],
                "mch_id"       => 0,
                "nonce_str"    => $noceStr,
                "notify_url"   => DI()->config->get('app.Notify.wx'),
                "out_trade_no" => $data['orderno'],
                'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
                "total_fee"    => (int) ($data['money'] * 100),
                "trade_type"   => $h5 ? "MWEB" : "APP",
            ];
            $sign             = $this->sign($paramarr, '');//生成签名
            $paramarr['sign'] = $sign;
            $paramXml         = "<xml>";
            foreach ($paramarr as $k => $v) {
                $paramXml .= "<" . $k . ">" . $v . "</" . $k . ">";
            }
            $paramXml .= "</xml>";

            $ch = curl_init();
            @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
            @curl_setopt($ch, CURLOPT_URL,
                "https://api.mch.weixin.qq.com/pay/unifiedorder");
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            @curl_setopt($ch, CURLOPT_POST, 1);
            @curl_setopt($ch, CURLOPT_POSTFIELDS, $paramXml);
            @$resultXmlStr = curl_exec($ch);
            if (curl_errno($ch)) {
                //print curl_error($ch);
                file_put_contents('./wxpay.txt', date('y-m-d H:i:s') . ' 提交参数信息 ch:'
                    . json_encode(curl_error($ch)) . "\r\n", FILE_APPEND);
            }
            curl_close($ch);

            $result2 = $this->xmlToArray($resultXmlStr);

            if ($result2['return_code'] == 'FAIL') {
                return [1005, '', $result2['return_msg']];
            }
            $time2             = time();
            $prepayid          = $result2['prepay_id'];
            $noceStr           = md5(rand(100, 1000) . time());//获取随机字符串
            $paramarr2         = [
//                "appid"     => $configpri['wx_appid'],
                "appid"        => '',
                "mweb_url" => $h5 ? $result2['mweb_url'] : '',
                "noncestr"  => $noceStr,
                "package"   => "Sign=WXPay",
//                "partnerid" => $configpri['wx_mchid'],
                "partnerid" => 0,
                "prepayid"  => $prepayid,
                "timestamp" => $time2,

            ];
            if (!$h5) {
                unset($paramarr2['mweb_url']);
            }
            $paramarr2["sign"] = $this->sign($paramarr2,
                '');//生成签名
            return [0, json_encode($paramarr2), ''];
        } catch (Exception $e) {
            return [1, '', $e->getMessage()];
        }
    }


    public function notify($arr)
    {
        return __FUNCTION__;
    }

    /**
     * sign拼装获取
     */
    public function sign($param, $key)
    {
        $sign = "";
        foreach ($param as $k => $v) {
            $sign .= $k . "=" . $v . "&";
        }
        $sign .= "key=" . $key;
        $sign = strtoupper(md5($sign));
        return $sign;
    }

    /**
     * xml转为数组
     */
    protected function xmlToArray($xmlStr)
    {
        $msg     = [];
        $postStr = $xmlStr;
        $msg     = (array)simplexml_load_string($postStr, 'SimpleXMLElement',
            LIBXML_NOCDATA);
        return $msg;
    }
}