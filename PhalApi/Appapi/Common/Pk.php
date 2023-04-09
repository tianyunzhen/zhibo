<?php
require VENDOR . '/autoload.php';

class Common_Pk{
    private $secretId  = '';
    private $secretKey = '';
    /**
     * @var \TencentCloud\Live\V20180801\LiveClient
     */
    private $client;

    public function __construct(){
        $cred        = new \TencentCloud\Common\Credential($this->secretId, $this->secretKey);
        $httpProfile = new \TencentCloud\Common\Profile\HttpProfile();
        $httpProfile->setEndpoint("live.tencentcloudapi.com");
        $clientProfile = new \TencentCloud\Common\Profile\ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $this->client = new \TencentCloud\Live\V20180801\LiveClient($cred, "", $clientProfile);
    }

    public function Blend($params){
        $req    = new \TencentCloud\Live\V20180801\Models\CreateCommonMixStreamRequest();
        $params = [
            "MixStreamSessionId" => "",
            "Version"            => "2018-08-01",
            "InputStreamList"    => [
                [
                    "InputStreamName" => "",
                    "LayoutParams"    => [
                        "ImageLayer"  => 2,
                        "ImageWidth"  => 0.25,
                        "ImageHeight" => 0.21,
                        "LocationX"   => 0.75,
                        "LocationY"   => 0.6,
                    ],
                ],
                [
                    "InputStreamName" => "",
                    "LayoutParams"    => [
                        "ImageLayer"  => 1,
                        "ImageWidth"  => 1280,
                        "ImageHeight" => 1920,
                    ],
                ],
            ],
            "OutputParams"       => [
                "OutputStreamName" => '',
            ],
        ];
        $req->fromJsonString(json_encode($params));
        $res = $this->client->CreateCommonMixStream($req);
    }

//    public function
}