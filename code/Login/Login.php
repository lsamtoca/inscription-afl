<?php
// This, to be sure, should go under ssh


require_once 'php/Login.php';
require_once 'php/User.php';

// Is the stuff below really needed ?
// 
//$Login = new Login;
//if ($Login->adminCorrectlyLogged()) {
//    header('Location: Admin.php');
//    exit(0);
//}
// This might rise problems
//if ($Login->clubCorrectlyLogged()) {
//    header('Location: Regate.php');
//    exit(0);
//}


function doLogin($login, $codedMdp) {
    global $Login;

    $user = User_selectByLoginAndMdp($login, $codedMdp);
    if ($user == NULL) {
        $message = "Le login ou le mot de passe n'est pas correcte";
        pageErreur($message);
        exit(1);
    }

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

function sendEmailWithNonceLink($courriel, $id, $nonce) {
    return 0;
}

function doSetNonceAndSendEmail($courriel) {

    $user = User_selectByLastCourriel($courriel);
    if ($user == NULL) {
        $message = "Ce courriel ne correspond à aucun utilisateur";
        pageErreur($message);
        exit(1);
    }

    $id = $user['ID'];
    $nonce = User_setNonce($id);
    sendEmailWithNonceLink($courriel, $id, $nonce);
}

if (isset($_POST['mdPOublie'])) {

    $courriel = filter_input(INPUT_POST,'courriel',FILTER_VALIDATE_EMAIL);
    if ($courriel == NULL) {
        $message = "Pas un adresse email";
        pageErreur($message);
    }
    
    doSetNonceAndSendEmail($courriel);
    $message = "Nous vous avons envoyé un courriel"
            . "contenant un lien qui vous permettra de "
            . "re-initialiser votre mot de passe";
    pageAnswer($message);
    exit(0);
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

<?php xhtml_post();
