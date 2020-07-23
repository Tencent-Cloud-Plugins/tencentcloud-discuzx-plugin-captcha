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
//自定义密钥关闭标志
const CUSTOM_SECRET_FLAG_OFF = '0';
const CUSTOM_SECRET_FLAG_ON = '1';
const SITE_SEC_OPEN = '1';
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

function captchaJsparams() {
    global $_G;
    if(!isset($_G['cache']['plugin'])) {
        loadcache('plugin');
    }

    if(substr($_G['setting']['jspath'],0,6)=='static') {
            $jspath = 'data/cache/';
    } else {
            $jspath = $_G['setting']['jspath'];
    }
    $return =  array('<script src="'.$jspath.'tencentcloudcaptcha.js?'.$_G['style']['verhash'].'" reload="1"></script>
               <div id="tencentcloudcaptcha" class="','" style="display:none;"></div>','');

    return $return;

}

function config() {
    global $_G;
    if (isset($_G['setting']['tencentcloud_captcha'])) {
        $plugin = C::t('common_plugin')->fetch_by_identifier('tencentcloud_captcha');
        C::t('common_pluginvar')->delete_by_pluginid($plugin['pluginid']);
        $params = unserialize($_G['setting']['tencentcloud_captcha']);
    } else {
        $params = array (
            'customSecret' => CUSTOM_SECRET_FLAG_OFF,
            'secretId' => '',
            'secretKey' => '',
            'captchaAppId' => '',
            'captchaAppKey' => '',
        );
    }
    if ($params['customSecret'] == CUSTOM_SECRET_FLAG_OFF && isset($_G['setting']['tencentcloud_center'])){
        $centerConfig = unserialize($_G['setting']['tencentcloud_center']);
        $params['secretId'] = $centerConfig['secretId'];
        $params['secretKey'] = $centerConfig['secretKey'];
    }
    return $params;
}
function getTencentCloudDiscuzStaticData($action){
    global $_G;
    require_once DISCUZ_ROOT.'./source/plugin/tencentcloud_center/lib/tencentcloud_helper.class.php';
    $static_data['action'] = $action;
    $static_data['plugin_type'] = 'captcha';
    $site_url = TencentCloudHelper::siteUrl();
    $site_app = TencentCloudHelper::getDiscuzSiteApp();
    $site_id = TencentCloudHelper::getDiscuzSiteID();
    $static_data['data'] = array(
        'site_url' => $site_url,
        'site_app' => $site_app,
        'site_id' => $site_id,
    );
    $captchaParam = unserialize($_G['setting']['tencentcloud_captcha']);

    $secretId = '';
    $secretKey = '';

    $params = unserialize($_G['setting']['tencentcloud_center']);
    if ($captchaParam['customSecret'] == CUSTOM_SECRET_FLAG_ON && isset($captchaParam['secretId']) && isset($captchaParam['secretKey'])) {
        $secretId = $captchaParam['secretId'];
        $secretKey = $captchaParam['secretKey'];
    }elseif($params['site_sec_on'] == SITE_SEC_OPEN && isset($params['secretId']) && isset($params['secretKey'])){
        $secretId = $params['secretId'];
        $secretKey = $params['secretKey'];
    }

    $static_data['data']['uin'] = TencentCloudHelper::getUserUinBySecret($secretId, $secretKey);
    $static_data['data']['cust_sec_on'] = $captchaParam['customSecret'] == CUSTOM_SECRET_FLAG_ON ? 1 : 2;
    $others =array(
        'captcha_appid' => $captchaParam['captchaAppId'],
    );
    $static_data['data']['others'] = json_encode($others);
    return $static_data;
}
