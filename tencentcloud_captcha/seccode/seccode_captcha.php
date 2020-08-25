<?php

/*
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
require_once DISCUZ_ROOT . './source/plugin/tencentcloud_captcha/vendor/autoload.php';

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Captcha\V20190722\CaptchaClient;
use TencentCloud\Captcha\V20190722\Models\DescribeCaptchaResultRequest;


if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class seccode_captcha {
    //set global secret  0-off 1-on
    const CUSTOM_SECRET_FLAG_OFF = '0';
    //valied captcha function  1-pass others-fail
    const VERIFY_SUCCESS_FLG = 1;
    public $version = '1.1.0';
    public $name = '';
    public $description = '';
    public $copyright = '';

    public function __construct()
    {
        $tencentcloud_captcha = lang('plugin/tencentcloud_captcha');
        $this->name = $tencentcloud_captcha['plugin_name'];
        $this->description = $tencentcloud_captcha['description'];
        $this->copyright = $tencentcloud_captcha['copyright'];
    }

    /**
     * valide captcha
     * @return bool true/false
     */
    public function check(){
        global $_G;

        if (!isset($_G['setting']['tencentcloud_captcha'])){
            loadcache('setting');
        }
        $params = unserialize($_G['setting']['tencentcloud_captcha']);

        if ($params['customSecret'] == self::CUSTOM_SECRET_FLAG_OFF && isset($_G['setting']['tencentcloud_center'])){
            $centerConfig = unserialize($_G['setting']['tencentcloud_center']);
            $params['secretId'] = $centerConfig['secretId'];
            $params['secretKey'] = $centerConfig['secretKey'];
        }
        $ticket = $_GET['codeVerifyTicket'];
        $randStr = $_GET['codeVerifyRandstr'];

        $verifyCode = self::verifyCodeReal($params['secretId'],$params['secretKey'],$ticket,$randStr,$params['captchaAppId'],$params['captchaAppKey']);
        if ($verifyCode['CaptchaCode'] != self::VERIFY_SUCCESS_FLG) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * output
     * @param $idhash formm hash
     */
    public function make($idhash){
        global $_G;
        loadcache('tencentcloud_captcha');
        echo $_G['cache']['tencentcloud_captcha'][0].$idhash.$_G['cache']['tencentcloud_captcha'][1];
    }

    /**
     * check captcha on server
     * @param $secretID
     * @param $secretKey
     * @param $ticket
     * @param $randStr
     * @param $codeAppId
     * @param $codeSecretKey
     * @return array|mixed
     */
    public static function verifyCodeReal($secretID, $secretKey,$ticket, $randStr, $codeAppId, $codeSecretKey){

        try {
            $remote_ip = preg_replace('/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR']);
            $cred = new Credential($secretID, $secretKey);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("captcha.tencentcloudapi.com");
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new CaptchaClient($cred, "", $clientProfile);
            $req = new DescribeCaptchaResultRequest();
            $params = array('CaptchaType' => 9, 'Ticket' => $ticket, 'Randstr' => $randStr, 'CaptchaAppId' => intval($codeAppId), 'AppSecretKey' => $codeSecretKey, 'UserIp' => $remote_ip);
            $req->fromJsonString(json_encode($params));
            $resp = $client->DescribeCaptchaResult($req);
            return json_decode($resp->toJsonString(), JSON_OBJECT_AS_ARRAY);
        } catch (TencentCloudSDKException $e) {
            return array('requestId' => $e->getRequestId(), 'errorCode' => $e->getErrorCode(), 'errorMessage' => $e->getMessage());
        }
    }
}

