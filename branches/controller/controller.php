<?php 
 
$req=str_replace('afl_dev/', 'afl/',$_GET['REQ']);
$host=str_replace('regateslaser.info', 'régateslaser.info',$_GET['HOST']);

$redirection="http://$host$req";
#echo $redirection;
header("Location: $redirection");

?>
