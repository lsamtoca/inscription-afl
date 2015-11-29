<?php
require_once "partage.php";
session_start();

$thisLogin = new Login;
if ($thisLogin->clubCorrectlyLogged()) {
    header("Location: Regate.php");
}


if (isset($_POST["submit"])) {
    /* Un des champs est manquant */
    if (
            (!isset($_POST["login"]) || !isset($_POST["pass"])) ||
            ($_POST["pass"] == "" || $_POST["login"] == "")
    ) {
        $message = "Un ou plusieurs champs sont manquants";
        pageErreur($message);
        exit(0);
    } else {
        $bd = newBD();
//        $login = $bd->quote(htmlentities($_POST["login"]));
//        $pass = $bd->quote(htmlentities($_POST["pass"]));
        $login = $_POST['login'];
        $pass = md5($_POST['pass']);
        $sql = 'SELECT * from `Regate` '
                . 'WHERE `org_login`=:login AND `coded_org_passe`=:pass;';
        $assoc = array('login' => $login, 'pass' => $pass);
        $req = executePreparedQuery($sql, $assoc, $bd);
        $nbligne = $req->rowCount();
        if ($nbligne == 1) {
            $reponse = $req->fetch();
            $req->closeCursor();
            $ID_regate = $reponse['ID_regate'];
            $titre_regate = $reponse['titre'];
            $date_debut = $reponse['date_debut'];
            $courriel = $reponse['courriel'];
            $thisLogin->loginAsClub($ID_regate, $titre, $date_debut, $courriel);
            exit(0);
        } else {
            $req->closeCursor();
            $message = 'Login ou mot de passe incorrect';
//            $message .= "\n$login -- $pass";
            pageErreur($message);
            exit(0);
        }
    }
}
?>

<?php xhtml_pre("Gestion de la régate, login"); ?>

<form action="" method="post">
    <fieldset>
        <legend>Gérer Votre Régate</legend>
        <label for="login">Login :</label>
        <input name="login" type="text" id="login"/>
        <br />
        <label for="pass">Mot de passe :</label>
        <input name="pass" type="password" id="pass"/>

        <input type="submit" name="submit" id="submit" value="Valider"/>
    </fieldset>

</form>

<?php xhtml_post(); ?>
