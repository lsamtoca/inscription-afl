<?php
// This, to be sure, should go under ssh

session_start();
require 'partage.php';
if (isset($_SESSION['ID_administrateur'])) {
    header('Location: Admin.php');
    exit(0);
}


function doLogin($login, $codedPass) {

    $sql = "select ID_administrateur from Administrateur where "
            . "admin_login=:LOGIN and coded_admin_passe=:PASS ;";
    $assoc = array('LOGIN' => $login, 'PASS' => $codedPass);
    $req = executePreparedQuery($sql, $assoc);

    $nbligne = $req->rowCount();
    if ($nbligne == 1) {
        $reponse = $req->fetch();
        $_SESSION["ID_administrateur"] = $reponse["ID_administrateur"];
        header("Location: Admin.php");
    } else {
        $message = "Le login ou le mot de passe n'est pas correcte";
        pageErreur($message, "LoginAdmin.php");
        exit(1);
    }
    exit(0); // We do not return from here
}


if (isset($_POST['submit'])) {
    /* Un des champs est manquant */
    if ((!isset($_POST['login']) || !isset($_POST['pass'])) ||
            ($_POST["pass"] == "" || $_POST["login"] == "")
    ) {
        $message("Un ou plusieurs champs sont manquants.");
        pageErreur($message, "LoginAdmin.php");
        exit(1);
    }


    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);
    $codedPass = md5($pass);

    doLogin($login, $codedPass);
    exit(0); // We do not return from here
}

xhtml_pre('Login Administrateur');
?>

<div class="contenu smallform">
    <form action="" method="post">
        <fieldset>
            <legend>Gérez les régates</legend>
            <div>
                <label for="login">Login :</label>
                <input name="login" type="text" id="login"/>
                <br />
                <label for="pass">Mot de passe :</label>
                <input name="pass" type="password" id="pass"/>

                <input type="submit" name="submit" id="submit" value="Valider"/>
            </div>
        </fieldset>

    </form>
</div>

<?php
xhtml_post();
