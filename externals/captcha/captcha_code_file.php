<?php

/*
 *
 * this code is based on captcha code by Simon Jarvis 
 * http://www.white-hat-web-design.co.uk/articles/php-captcha.php
 *
 * This program is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation
 *
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
 * GNU General Public License for more details: 
 * http://www.gnu.org/licenses/gpl.html
 */

if (!isset($_SESSION)) {
    session_start();
}

$base = dirname(__FILE__);
if (isset($_GET['captchaName'])) {
    $captchaName = filter_input(INPUT_GET, 'captchaName', FILTER_SANITIZE_STRING);
} else {
    $captchaName = 'captcha';
}

if (!function_exists('gd_info') or ! function_exists('imagettfbbox')) {
    $_SESSION[$captchaName] = '123456';
    $file = 'img/favicon.png';
    $type = 'image/png';
    if (file_exists($file) && is_readable($file)) {
        header('Content-Type:' . $type);
        header('Content-Length: ' . filesize($file));
        readfile($file);
    } else {
        throw new Exception('Problème en lecdutre de ' . $file);
    }
}


//Settings: You can customize the captcha here
$image_width = 120;
$image_height = 40;
//$image_width = 300;
//$image_height = 100;
$characters_on_image = 6;
$font = "$base/monofont.ttf";

//The characters that can be used in the CAPTCHA code.
//avoid confusing characters (l 1 and i for example)
$possible_letters = '23456789bcdfghjkmnpqrstvwxyz';
$random_dots = 0;
$random_lines = 20;
$captcha_text_color = "0x142864";
$captcha_noice_color = "0x142864";

$code = '';


$i = 0;
while ($i < $characters_on_image) {
    $code .= substr($possible_letters, mt_rand(0, strlen($possible_letters) - 1), 1);
    $i++;
}


$font_size = $image_height * 0.75;
$image = @imagecreate($image_width, $image_height);


/* setting the background, text and noise colours here */
$background_color = imagecolorallocate($image, 255, 255, 255);

$arr_text_color = hexrgb($captcha_text_color);
$text_color = imagecolorallocate($image, $arr_text_color['red'], $arr_text_color['green'], $arr_text_color['blue']);

$arr_noice_color = hexrgb($captcha_noice_color);
$image_noise_color = imagecolorallocate($image, $arr_noice_color['red'], $arr_noice_color['green'], $arr_noice_color['blue']);


/* generating the dots randomly in background */
for ($i = 0; $i < $random_dots; $i++) {
    imagefilledellipse($image, mt_rand(0, $image_width), mt_rand(0, $image_height), 2, 3, $image_noise_color);
}


/* generating lines randomly in background of image */
for ($i = 0; $i < $random_lines; $i++) {
    imageline($image, mt_rand(0, $image_width), mt_rand(0, $image_height), mt_rand(0, $image_width), mt_rand(0, $image_height), $image_noise_color);
}


/* create a text box and add 6 letters code in it */
$textbox = imagettfbbox($font_size, 0, $font, $code);
$x = ($image_width - $textbox[4]) / 2;
$y = ($image_height - $textbox[5]) / 2;
imagettftext($image, $font_size, 0, $x, $y, $text_color, $font, $code);


/* Show captcha image in the page html page */
header('Content-Type: image/jpeg'); // defining the image type to be shown in browser widow
imagejpeg($image); //showing the image
imagedestroy($image); //destroying the image instance
$_SESSION[$captchaName] = $code;

function hexrgb($hexstr) {
    $int = hexdec($hexstr);

    return array("red" => 0xFF & ($int >> 0x10),
        "green" => 0xFF & ($int >> 0x8),
        "blue" => 0xFF & $int);
}