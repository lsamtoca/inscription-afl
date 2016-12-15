<?php

ob_start();
passthru('ls img/backgrounds/*.jpg', $ret_val);
$listing = ob_get_contents();
ob_end_clean();

$images = explode("\n", trim($listing));
$backgroundImg = $images[rand(0, sizeof($images) - 1)];

if ($backgroundImg != '') {
    $config['defaultBackground'] = $backgroundImg;
}
