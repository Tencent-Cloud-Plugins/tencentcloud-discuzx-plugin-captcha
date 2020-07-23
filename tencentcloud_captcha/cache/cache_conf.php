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

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
function build_cache_plugin_conf() {
    global $_G;
    include_once DISCUZ_ROOT . './source/plugin/tencentcloud_captcha/lib.class.php';
    if(!isset($_G['cache']['plugin'])) {
        loadcache('plugin');
    }
    $config = unserialize($_G['setting']['tencentcloud_captcha']);
    ob_start();
    include template('tencentcloud_captcha:captchajs');
    $message = ob_get_contents();
    ob_end_clean();
    write_js_to_cache('tencentcloudcaptcha',$message);

    savecache('tencentcloud_captcha',captchaJsparams());
}

function write_js_to_cache($name, $content) {
    $remove = array(
        array(
            '/(^|\r|\n)\/\*.+?\*\/(\r|\n)/is',
            "/([^\\\:]{1})\/\/.+?(\r|\n)/",
            '/\/\/note.+?(\r|\n)/i',
            '/\/\/debug.+?(\r|\n)/i',
            '/(^|\r|\n)(\s|\t)+/',
            '/(\r|\n)/',
        ), array(
            '',
            '\1',
            '',
            '',
            '',
            '',
        )
    );
    $message = preg_replace($remove[0], $remove[1], $content);
    if(@$fp = fopen(DISCUZ_ROOT.'./data/cache/'.$name.'.js', 'w')) {
        fwrite($fp, $message);
        fclose($fp);
    } else {
        exit('Can not write to cache files, please check directory ./data/ and ./data/cache/ .');
    }
}
