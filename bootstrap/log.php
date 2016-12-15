<?php

$remoteIp = $_SERVER['REMOTE_ADDR'];
$remoteUserAgent = $_SERVER['HTTP_USER_AGENT'];
$request = $_SERVER['REQUEST_URI'];
$date = date('Y-m-d H:i:s');
$separator = ',';

// LOG REQUEST
$logLine = "$date$separator$remoteIp$separator$request$separator$remoteUserAgent\n";
file_put_contents('connections.log', $logLine, FILE_APPEND | LOCK_EX);

