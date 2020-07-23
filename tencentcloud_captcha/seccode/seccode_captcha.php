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
    //自定义密钥标记 0-未开启 1-已开启
    const CUSTOM_SECRET_FLAG_OFF = '0';
    //验证码验证通过标记 1-通过 其它-失败
    const VERIFY_SUCCESS_FLG = 1;
    //版本号
    public $version = '1.1.0';
    //名称
    public $name = '腾讯云验证码';
    //描述
    public $description = '提供丰富的安全认证';
    //作者
    public $copyright = '腾讯云';

    /**
     * 校验验证码
     * @return bool true通过/false不通过
     */
    public function check(){
        global $_G;
        //判断缓存是否存在，不存在加载缓存
        if (!isset($_G['setting']['tencentcloud_captcha'])){
            loadcache('setting');
        }
        //反序列化保存的数据
        $params = unserialize($_G['setting']['tencentcloud_captcha']);
        //判断是否开启了自定义密钥
        //TODO:定义成明确意义的常量
        if ($params['customSecret'] == self::CUSTOM_SECRET_FLAG_OFF && isset($_G['setting']['tencentcloud_center'])){
            $centerConfig = unserialize($_G['setting']['tencentcloud_center']);
            $params['secretId'] = $centerConfig['secretId'];
            $params['secretKey'] = $centerConfig['secretKey'];
        }
        $ticket = $_GET['codeVerifyTicket'];
        $randStr = $_GET['codeVerifyRandstr'];
        //验证验证码
        $verifyCode = self::verifyCodeReal($params['secretId'],$params['secretKey'],$ticket,$randStr,$params['captchaAppId'],$params['captchaAppKey']);
        //判断返回结果是否通过
        if ($verifyCode['CaptchaCode'] != self::VERIFY_SUCCESS_FLG) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * 输出验证码
     * @param $idhash 表单hash
     */
    public function make($idhash){
        global $_G;
        loadcache('tencentcloud_captcha');
        echo $_G['cache']['tencentcloud_captcha'][0].$idhash.$_G['cache']['tencentcloud_captcha'][1];
    }

    /**
     * 验证码服务端验证
     * @param $secretID 腾讯云密钥ID
     * @param $secretKey 腾讯云密钥Key
     * @param $ticket 用户验证票据
     * @param $randStr 用户验证时随机字符串
     * @param $codeAppId 验证码应用ID
     * @param $codeSecretKey 验证码应用蜜月
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

