<?php
require_once "partage.php";
session_start();

$thisLogin = new Login;
if ($thisLogin->clubCorrectlyLogged()) {
    header('Location: Regate.php');
    exit(0);
}

function doLogin($login, $codedPass) {
    global $thisLogin;
    
    $sql = 'SELECT * from `Regate` '
            . 'WHERE `org_login`=:login AND `coded_org_passe`=:pass;';
    $assoc = array('login' => $login, 'pass' => $codedPass);
    $req = executePreparedQuery($sql, $assoc);
    $nbligne = $req->rowCount();
    if ($nbligne == 1) {
        $reponse = $req->fetch();
        $req->closeCursor();
        $ID_regate = $reponse['ID_regate'];
        $titre_regate = $reponse['titre'];
        $date_debut = $reponse['date_debut'];
        $courriel = $reponse['courriel'];
        $thisLogin->loginAsClub($ID_regate, $titre_regate, $date_debut, $courriel);
    } else {
        $req->closeCursor();
        $message = 'Login ou mot de passe incorrect';
//            $message .= "\n$login -- $pass";
        pageErreur($message);
    }
    exit(0); // we do not return
}

if (isset($_POST["submit"])) {

    if (
            (!isset($_POST["login"]) || !isset($_POST["pass"])) ||
            ($_POST["pass"] == "" || $_POST["login"] == "")
    ) { /* Un des champs est manquant */
        $message = 'Un ou plusieurs champs sont manquants';
        pageErreur($message);
        exit(0);
    } else {
        $login = $_POST['login'];
        $codedPass = md5($_POST['pass']);
        doLogin($login, $codedPass); // we do not return
        exit(0);
    }
}

//Here it begins the html

xhtml_pre("Gestion de la régate, login"); ?>

<div class="contenu" style="width:400px;padding:20px;">
    <form action="" method="post">
        <fieldset>
            <legend>Gérez votre régate</legend>
            <div style="padding:10px;">
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

<?php xhtml_post(); ?>
