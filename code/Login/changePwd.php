<?php
require_once('partage.php');
require_once('php/Regate.php');
require_once('php/Administrateur.php');

session_start();

$login = new Login;
$club=$login->clubCorrectlyLogged();
$admin=$login->adminCorrectlyLogged();
if($admin){
    $club=false;
}
if (!$club and !$admin) {
    header("Location: Login.php");
}


function verifyPostField($field) {
    return isset($_POST[$field]) && $_POST[$field] != '';
}

function lengthNewPwd() {
    return
            strlen($_POST['newPwd']) >= 8 &&
            strlen($_POST['newPwd']) <= 10
    ;
}

function verifyCondition($cond, $message) {
    if (!$cond) {
        pageErreur($message);
        exit(0);
    }
    return true;
}

function verifyOldPwd() {
    global $admin, $club;
    
    $coded_pwd = '';
    if ($club) {
        $ID_regate = $_SESSION['ID_regate'];
        $regate = Regate_selectById($ID_regate);
        $coded_pwd = $regate['coded_org_passe'];
    }

    if ($admin) {
        $ID_admin = $_SESSION['ID_administrateur'];
        $admin = Administrateur_selectById($ID_admin);
        $coded_pwd = $admin['coded_admin_passe'];
    }

    if ($coded_pwd == '') {
        return FALSE;
    }

    $oldPwd = $_POST['oldPwd'];
    return md5($oldPwd) == $coded_pwd;
}

function updatePwd() { 
    global $club, $admin;   
    
    $new_coded_pwd = md5($_POST['newPwd']);

    if ($admin) {
        $ID_administrateur = $_SESSION['ID_administrateur'];
        Administrateur_setField($ID_administrateur, 'coded_admin_passe', $new_coded_pwd);
        return True;
    }

    if ($club) {
        $ID_regate = $_SESSION['ID_regate'];
        Regate_setField($ID_regate, 'coded_org_passe', $new_coded_pwd);
        return True;
    }

    return False;
}

if (isset($_POST['submit'])) {
    verifyCondition(verifyPostField('oldPwd'), "L'ancien mot de passe est absent");
    verifyCondition(verifyPostField('newPwd'), "Le nouveau mot de passe est absent");
    verifyCondition(verifyPostField('confirmNewPwd'), "Vouz n'avez pas confirmé le mot de passe est absent");
    verifyCondition($_POST['newPwd'] == $_POST['confirmNewPwd'], "Les deux mots de passe rentrés ne sont pas egaux");
    verifyCondition(lengthNewPwd(), "Le nouveau mot de passe est trop court ou trop long");
    verifyCondition(verifyOldPwd(), "L'autentication a echouée");


    updatePwd();

    if($club){
        $goback='Regate.php';
    }else{
        $goback='Admin.php';
    }
    pageAnswer('Votre mot de passe a eté mis à jour',$goback);
}

$whichPwd = 'Club';
if ($admin) {
    $whichPwd = 'Administrateur';
}

$title = 'Modifiez votre mot de passe';
xhtml_pre1($title);
xhtml_pre2('');
?>
<div class="contenu smallform">
    <form action="" method="POST" >

        <fieldset>
            <legend>Modification du mot de passe 
                <?php echo $whichPwd ?>
            </legend>
            <div>

                <label for="odlPwd">
                    Ancien mot de passe :
                </label>
                <input type="password" name="oldPwd" size="10"/>

                <br />
                <label for="newPwd">
                    Nouveau mot de passe :
                </label>
                <input type="password" name="newPwd" size="10"/>

                <br />
                <label for="confirmNewPwd">
                    Confirmez le nouveau mot de passe :
                </label>
                <input type="password" name="confirmNewPwd" size="10"/>

                <br />
                <input type="submit" name="submit" value="Valider" />
            </div>
        </fieldset>
    </form>
</div>

<?php
xhtml_post();
