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
// plugin_status 1-activate 0-deactivate
const PLUGIN_AVAILABE_FLAG = '1';
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
$file = DISCUZ_ROOT . './source/plugin/tencentcloud_center';
$pluginInfo=C::t('common_plugin')->fetch_by_identifier('tencentcloud_center');
if (!is_dir($file) || !isset($pluginInfo)) {
    $landurl = 'action=plugins';
    $tencentcloud_captcha = lang('plugin/tencentcloud_captcha');
    cpmsg($tencentcloud_captcha['plugin_operate_fail'], $landurl . (!empty($_GET['system']) ? '&system=1' : ''), 'error');
    return;
}

if ($pluginInfo['available'] != PLUGIN_AVAILABE_FLAG){
    C::t('common_plugin')->update($pluginInfo['pluginid'], array('available' => PLUGIN_AVAILABE_FLAG));
}
runquery("UPDATE cdb_tencentcloud_pluginInfo SET status = 'true' WHERE plugin_name='tencentcloud_captcha'");
require_once DISCUZ_ROOT . './source/plugin/tencentcloud_captcha/lib.class.php';
require_once DISCUZ_ROOT . './source/plugin/tencentcloud_center/lib/tencentcloud_helper.class.php';
$data=getTencentCloudDiscuzStaticData('activate');
TencentCloudHelper::sendUserExperienceInfo($data);

