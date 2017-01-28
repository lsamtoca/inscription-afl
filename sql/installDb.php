<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$userPwdDb = 'WebRegatta';

function doMysql($user, $pwd, $db, $file, $sql = '') {
    $dbOption = '';
    if ($db != '') {
        $dbOption = "-D$db";
    }
    if ($file != '') {
        $command = "mysql -u$user -p$pwd $dbOption < $file ";
        system($command);
    } else {
        $tmpFile = 'init.php.sql_tmp';
        file_put_contents($tmpFile, $sql);
        $command = "mysql -u$user -p$pwd $dbOption < $tmpFile";
        system($command);
        system("rm $tmpFile");
    }
}

function getVar($prompt) {
    global $userPwdDb;
    echo "$prompt (default '$userPwdDb') : ";
    $ret = trim(fgets(STDIN));
    if ($ret == '') {
        return $userPwdDb;
    } else {
        return $ret;
    }
}

$user = getVar("Nouveau utilisateur mysql");
$pwd = getVar("Mot de passe pour utilisateur $user");
$db = getVar("Nouvellebase de donnés  mysql");

echo "Root password for root@mysql : ";
system('stty -echo');
$rootPwd = trim(fgets(STDIN));
system('stty echo');
echo "\n";

$createUser = file_get_contents('createUser.sql');
$sql = str_replace(['{user}', '{pwd}', '{db}'], [$user, $pwd, $db], $createUser);
doMysql('root', $rootPwd, '', '', $sql);
doMysql($user, $pwd, $db, 'structure.sql');
doMysql($user, $pwd, $db, 'inhabit.sql');

$localhostDb = file_get_contents('localhostDb.php');
$php = str_replace(['{user}', '{pwd}', '{db}'], [$user, $pwd, $db], $localhostDb);
file_put_contents('../databases/localhostDb.php', $php);
