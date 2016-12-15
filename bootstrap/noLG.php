<?php

//$ip='/90\.34\.221\.[0-9]{1,3}/';
$ip = '/90\.34\.[0-9]{1,3}\.[0-9]{1,3}/';
//$ip='/82\.234\.226\.[0-9]{1,3}/';
$ua = '/MSIE/';
if (
        preg_match($ip, $_SERVER['REMOTE_ADDR']) === 1 &&
        preg_match($ua, $_SERVER['HTTP_USER_AGENT']) === 1
) {
    header('Location: http://www.mozilla.org/en-US/firefox/all/');
    exit(0);
}

