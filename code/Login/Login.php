<?php
// This, to be sure, should go under ssh

//session_start();
//require_once 'partage.php';
require_once 'php/Login.php';

$Login = new Login;

if ($Login->adminCorrectlyLogged()) {
    header('Location: Admin.php');
    exit(0);
}

// This might rise problems
//if ($Login->clubCorrectlyLogged()) {
//    header('Location: Regate.php');
//    exit(0);
//}

$adminFields = array(
    'ID' => 'ID_administrateur',
    'codedPasse' => 'coded_admin_passe',
    'login' => 'admin_login',
    'role' => "'Admin'"
);

$clubFields = array(
    'ID' => 'ID_regate',
    'codedPasse' => 'coded_org_passe',
    'login' => 'org_login',
    'role' => "'Club'"
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

//echo $users;
//exit(0);

function doLogin($login, $codedMdp) {
    global $users, $Login;

    $sql = "select ID,role from $users as Users where "
            . "Users.login=:LOGIN and Users.codedPasse=:PASS ;";
    $assoc = array('LOGIN' => $login, 'PASS' => $codedMdp);
    $req = executePreparedQuery($sql, $assoc);

    $nbligne = $req->rowCount();
    if ($nbligne == 1) {
        $user = $req->fetch();
        switch ($user['role']) {
            case 'Admin':
                $Login->loginAsAdmin($user['ID']);
                header("Location: Admin.php");
                break;
            case 'Club':
                $Login->loginAsClub($user['ID']);
                header("Location: Regate.php");
                break;
        }
    } else {
        $message = "Le login ou le mot de passe n'est pas correcte";
        pageErreur($message);
        exit(1);
    }
    exit(0); // We do not return from here
}

if (isset($_POST['submit'])) {
    /* Un des champs est manquant */
    if ((!isset($_POST['login']) || !isset($_POST['pass'])) ||
            ($_POST["pass"] == "" || $_POST["login"] == "")
    ) {
        $message = "Un ou plusieurs champs sont manquants.";
        pageErreur($message);
        exit(1);
    }


    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);
    $codedPass = md5($pass);

    doLogin($login, $codedPass);
    exit(0); // We do not return from here
}

function doResetMdp ($courriel){
    global $users;
    
    $sql = "select ID,role from $users as User where "
            . "Users.courriel=:COURRIEL;";
    $assoc = array('COURRIEL' => $courriel);
    $req = executePreparedQuery($sql, $assoc);
    
}
if (isset($_POST['mdPOublie'])) {
    
    $sql = "select ID,role from $users as Users where "
            . "Users.login=:LOGIN and Users.codedPasse=:PASS ;";
    $assoc = array('LOGIN' => $login, 'PASS' => $codedMdp);
    $req = executePreparedQuery($sql, $assoc);
}


xhtml_pre('');
?>

<div class="contenu smallform">
    <form action="" method="post">
        <fieldset>
            <legend>Vos identifiants</legend>
            <div>
                <label for="login">Login :</label>
                <input name="login" type="text" id="login"/>
                <br />
                <label for="pass">Mot de passe :</label>
                <input name="pass" type="password" id="pass"/>

                <input type="submit" name="submit" id="submit" value="Valider"/>
            </div>
        </fieldset>
        <br />
        <br />
        <form action="" method="post">
            <fieldset>
                <legend>Identifiants oubliés</legend>
                <div>
                    <label for="courriel">Votre courriel :</label>
                    <input name="courriel" type="text" />

                    <input type="submit" name="mdpOublie" value="Valider"/>
                </div>
            </fieldset>

        </form>
</div>

<?php
xhtml_post();
