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

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
/**
 * reload cache
 */
if (isset($_GET['action']) && $_GET['action'] == 'plugins' && isset($_GET['operation']) && $_GET['operation'] == 'config' && isset($_GET['tencentcloudcaptcha']['captchaAppId'])) {
    updatecache('tencentcloud_captcha:conf');
}

