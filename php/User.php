<?php

require_once 'php/hash.php';

$adminFields = array(
    'ID' => 'ID_administrateur',
    'codedPasse' => 'coded_admin_passe',
    'login' => 'admin_login',
    'courriel' => 'courriel',
    'role' => "'Admin'",
    'date' => "'00/00/0000'"
);

$clubFields = array(
    'ID' => 'ID_regate',
    'codedPasse' => 'coded_org_passe',
    'login' => 'org_login',
    'courriel' => 'courriel',
    'role' => "'Club'",
    'date' => "date_debut"
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


function User_setNonce($id,$role) {

    switch ($role){
        default:
            return NULL;
            break;
            
            case 'Club':
                $nonce = generateHash();
                Regate_setField($id, 'nonce', $nonce);
                break;
        
    }
    return $nonce;
    
}
