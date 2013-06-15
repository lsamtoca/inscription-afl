<?php

$test_this = false;
$ison = true;


$request = $_GET['REQ'];
$requested_host = $_GET['HOST'];
if ($test_this) {
    $request = 'GET /inscriptions_afl_dev/ HTTP/1.1';
    //$requested_host = 'régateslaser.info';
    $requested_host = 'xn--rgateslaser-bbb.info';
}
$reqp_ex = explode(' ', $request);
$requested_page = $reqp_ex[1];


$host = $requested_host;
$page = $requested_page;
if ($ison) {
// origin_host -> new_host
    $origin_host = '/xn--rgateslaser-bbb.info|régateslaser.info/';
    $destination_host = 'regateslaser.info';
    $host = preg_replace($origin_host, $destination_host, $requested_host);
// testing -> production
    $page = str_replace('afl_dev', 'afl', $requested_page);
}

// Just a very bad trick to avoid infinite loops in redirections
if ("$host$page" == 'regateslaser.info/inscriptions_afl/')
    $page = $page . 'index.php';

$redirection = "http://$host$page";

if ($test_this) {
    echo "$requested_host$requested_page";
    echo " --> ";
    echo $redirection;
    echo "\n";
} else {
    header("Location: $redirection");
}
?>