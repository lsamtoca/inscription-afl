<?php

require_once 'php/hash.php';
require_once 'php/Regate.php';
require_once 'php/Administrateur.php';

$adminFields = array(
    'ID' => 'ID_administrateur',
    'codedPasse' => 'coded_admin_passe',
    'login' => 'admin_login',
    'courriel' => 'courriel',
    'role' => "'Admin'",
    'date' => "'00/00/0000'",
    'nonce' => 'nonce'
);

$clubFields = array(
    'ID' => 'ID_regate',
    'codedPasse' => 'coded_org_passe',
    'login' => 'org_login',
    'courriel' => 'courriel',
    'role' => "'Club'",
    'date' => "date_debut",
    'nonce' => 'nonce'
);

function formatSql($fields, $table) {

    //$last_key = end(array_keys($fields));
    $sql = "select ";
    foreach ($fields as $key => $value) {
        $sql .="$value as $key";
        if (next($fields) == true) {
            $sql .= ', ';
        } else {
            $sql .= ' ';
        }
    }
    $sql.="from $table"; //where 1 ";
    return $sql;
}

$adminSql = formatSql($adminFields, 'Administrateur');
$clubSql = formatSql($clubFields, 'Regate');
$users = "(($adminSql) UNION ($clubSql))";

function User_selectByLoginAndMdp($login, $codedMdp) {
    global $users;

    $sql = "select * from $users as Users where "
            . "Users.login=:LOGIN and Users.codedPasse=:PASS ;";
    $assoc = array('LOGIN' => $login, 'PASS' => $codedMdp);
    $req = executePreparedQuery($sql, $assoc);

    $nbligne = $req->rowCount();
    if ($nbligne != 1) {
        return NULL;
    }
    return $req->fetch();
}

function User_selectByIdAndNonce($id, $nonce) {
    global $users;
    if($nonce == '')
        return NULL;
    
    $sql = "select * from $users as Users where "
            . "Users.id=:ID and Users.nonce=:NONCE ;";
    $assoc = array('ID' => $id, 'NONCE' => $nonce);
    $req = executePreparedQuery($sql, $assoc);

    $nbligne = $req->rowCount();
    if ($nbligne != 1) {
        return NULL;
    }
    return $req->fetch();
}

function User_selectByLogin($login) {
    global $users;

    $sql = "select * from $users as Users where "
            . "Users.login=:LOGIN;";
    $assoc = array('LOGIN' => $login);
    $req = executePreparedQuery($sql, $assoc);

    $nbligne = $req->rowCount();
    if ($nbligne != 1) {
        return NULL;
    }
    return $req->fetch();
}

function User_selectByLastCourriel($courriel) {
    global $users;

    $sql = "select * from $users as Users where "
            . "Users.courriel=:COURRIEL order by `date` Desc;";
    $assoc = array('COURRIEL' => $courriel);
    $req = executePreparedQuery($sql, $assoc);

    $nbligne = $req->rowCount();
    if ($nbligne == 0) {
        return NULL;
    }
    return $req->fetch();
}

function User_selectByCourriel($courriel) {
    global $users;

    $sql = "select * from $users as Users where "
            . "Users.courriel=:COURRIEL order by `date` Desc;";
    $assoc = array('COURRIEL' => $courriel);
    $req = executePreparedQuery($sql, $assoc);

    $nbligne = $req->rowCount();
    if ($nbligne == 0) {
        return NULL;
    }
    return $req->fetch_all();
}

function User_setNonce($user) {

    $id = $user['ID'];
    for ($i = 0; $i < 20; $i++) {
        $nonce = generateHash();
        $otherUser = User_selectByIdAndNonce($id, $nonce);
        if ($otherUser == NULL) {
            break;
        }
    }
    if($i == 20){
        return NULL;
    }
    
    $role = $user['role'];
    switch ($role) {
        case 'Admin':
            Administrateur_setField($id, 'nonce', $nonce);
            break;
        case 'Club':
            Regate_setField($id, 'nonce', $nonce);
            break;
        default:
            return NULL;
            break;
   }
    return $nonce;
}

function User_destroyNonce($user) {
    $id = $user['ID'];
    $role = $user['role'];

    switch ($role) {
        case 'Admin':
            Administrateur_setField($id, 'nonce', '');
            break;
        case 'Club':
            Regate_setField($id, 'nonce', '');
            break;
        default:
            return NULL;
            break;
    }
}
