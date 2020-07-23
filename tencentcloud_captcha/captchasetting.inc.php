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
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
global $_G;
require_once DISCUZ_ROOT . './source/plugin/tencentcloud_captcha/lib.class.php';
require_once DISCUZ_ROOT . './source/plugin/tencentcloud_center/lib/tencentcloud_helper.class.php';
/**
 * 判断是否是插件设置保存
 */
if(($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['tencentcloudcaptcha'])){
    $data = $_POST['tencentcloudcaptcha'];
    $save_data = array();
    $save_data['secretId'] = TencentCloudHelper::filterParam( $data['secretId']);
    $save_data['secretKey'] = TencentCloudHelper::filterParam( $data['secretKey']);
    $save_data['customSecret'] = TencentCloudHelper::filterParam( $data['customSecret']);
    $save_data['captchaAppId'] = TencentCloudHelper::filterParam( $data['captchaAppId']);
    $save_data['captchaAppKey'] = TencentCloudHelper::filterParam( $data['captchaAppKey']);
    C::t('common_setting')->update_batch(array("tencentcloud_captcha" => $save_data));
    updatecache('setting');
    $landurl = 'action=plugins&operation=config&do='.$pluginid.'&identifier=tencentcloud_captcha&pmod=captchasetting';
    $staticData=getTencentCloudDiscuzStaticData('save_config');
    TencentCloudHelper::sendUserExperienceInfo($staticData);
    cpmsg('plugins_edit_succeed', $landurl, 'succeed');
}
//获取插件配置参数
$config=config();
include template('tencentcloud_captcha:captchasetting');


