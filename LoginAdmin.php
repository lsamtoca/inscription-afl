<?php

// This, to be sure, should go under ssh

session_start();
require 'partage.php';

function doLogin($login, $codedPass) {
    global $pdo_path, $user, $pwd, $pdo_options;
    
    try {
//            $login = $bdd->quote(htmlentities($_POST["login"]));
//            $pass = $bdd->quote(htmlentities($_POST["pass"]));

        $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);
        $sql = "select ID_administrateur from Administrateur where "
                . "admin_login= :LOGIN and coded_admin_passe= :PASS ;";
        $req = $bd->prepare($sql);
        $req->execute(array('LOGIN' => $login, 'PASS' => $codedPass));

        $nbligne = $req->rowCount();
        if ($nbligne == 1) {
            $reponse = $req->fetch();
            $_SESSION["ID_administrateur"] = $reponse["ID_administrateur"];
            header("Location: Admin.php");
        } else {
            $message = "Le login ou le mot de passe n'est pas correcte";
            pageErreur($message,"LoginAdmin.php");
            exit(1);
        }
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}

if (isset($_SESSION['ID_administrateur'])) {
    header('Location: Admin.php');
    exit();
}

if (isset($_POST['submit'])) {
    /* Un des champs est manquant */
    if ((!isset($_POST['login']) || !isset($_POST['pass'])) ||
            ($_POST["pass"] == "" || $_POST["login"] == "")
    ) {
        $message("Un ou plusieurs champs sont manquants.");
        pageErreur($message,"LoginAdmin.php");
        exit(1);
    }


    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);
    $codedPass = md5($pass);
    
    doLogin($login,$codedPass);
    
    }   

xhtml_pre('Login Administrateur');
?>

<form action="" method="post">
    <fieldset>
        <legend>Gérez les régates : </legend>
        <label for="login">Login :</label>
        <input name="login" type="text" id="login"/>
        <br />
        <label for="pass">Mot de passe :</label>
        <input name="pass" type="password" id="pass"/>

        <input type="submit" name="submit" id="submit" value="Valider"/>
    </fieldset>

</form>

<?php
xhtml_post();
?>