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

runquery("DELETE FROM  cdb_tencentcloud_pluginInfo  WHERE plugin_name = 'tencentcloud_captcha'");
require_once DISCUZ_ROOT . './source/plugin/tencentcloud_captcha/lib.class.php';
require_once DISCUZ_ROOT . './source/plugin/tencentcloud_center/lib/tencentcloud_helper.class.php';
$data = getTencentCloudDiscuzStaticData('uninstall');
TencentCloudHelper::sendUserExperienceInfo($data);
