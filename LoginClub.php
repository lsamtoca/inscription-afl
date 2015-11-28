<?php
session_start();
if (isset($_SESSION['ID_regate'])) {
    header("Location: Regate.php");
}
require "partage.php";


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
            $_SESSION['ID_regate'] = $reponse['ID_regate'];
            $_SESSION['titre_regate'] = $reponse['titre'];
            $_SESSION['debut_regate'] = $reponse['date_debut'];
            $_SESSION['courriel'] = $reponse['courriel'];
            header("Location: Regate.php");
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
