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

class mobileplugin_tencentcloud_captcha
{
    function global_footer_mobile()
    {
        global $seccodecheck, $_G;
        $show = 0;
        if ($_G['setting']['seccodedata']['type'] == 'tencentcloud_captcha:captcha') {
            if (CURSCRIPT . CURMODULE === 'memberlogging') {
                list($seccodecheck) = seccheck('login');
                if ($seccodecheck) {
                    $show = 1;
                }
            } elseif (CURSCRIPT . CURMODULE === 'memberregister') {
                list($seccodecheck) = seccheck('register');
                if ($seccodecheck) {
                    $show = 1;
                }
            } elseif (CURSCRIPT == 'forum') {
                $modulelist = array('viewthread', 'post');
                if (in_array(CURMODULE, $modulelist) && $seccodecheck) {
                    $show = 1;
                }
            } else {
                $show = 0;
            }
        }
        if ($show) {
            loadcache('tencentcloud_captcha');
            if ($_G['cache']['tencentcloud_captcha'][2]) {
                return ((IN_MOBILE == 1) ? '<script src="' . $_G['setting']['jspath'] . 'mobile/jquery.min.js"></script>' : '') . $_G['cache']['tencentcloud_captcha'][2];
            }
        }
    }
}