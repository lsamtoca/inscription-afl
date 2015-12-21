<?php

require_once 'php/Regate.php';

// LOGIN
class Login {

    public function loginAsClub($ID_regate) {
        $regate = Regate_selectById($ID_regate);
        $_SESSION['ID_regate'] = $ID_regate;
        $_SESSION['titre_regate'] = $regate['titre'];
        $_SESSION['debut_regate'] = $regate['date_debut'];
        $_SESSION['courriel'] = $regate['courriel'];
        header("Location: Regate.php");
    }

    public function loginAsAdmin($ID_admin) {
        $_SESSION['ID_administrateur'] = $ID_admin;
        header("Location: Admin.php");
    }

    private function sessionHasExpired($minutes = 60) {
        # Check for session timeout, else initiliaze time
        if (isset($_SESSION['timeout'])) {
            # Check Session Time for expiry
            #
	# Time is in seconds. 10 * 60 = 600s = 10 minutes
            if ($_SESSION['timeout'] + $minutes * 60 < time()) {
                session_destroy();
                return TRUE;
            }
        } else {
            # Initialize time
            $_SESSION['timeout'] = time();
        }
        return FALSE;
    }

    public function clubCorrectlyLogged() {
        return isset($_SESSION["ID_regate"]) &&
                !$this->sessionHasExpired();
    }

    public function adminCorrectlyLogged() {
        return isset($_SESSION['ID_administrateur']);
    }

    public function assertAdmin() {
        if (!$this->adminCorrectlyLogged()) {
            header('Location: Login.php');
        }
    }

    public function assertClub() {
        if (!$this->clubCorrectlyLogged()) {
            header('Location: Login.php');
        }
    }

}

function assertAdmin() {

    if (!isset($_SESSION['ID_administrateur'])) {
        header('Location: LoginAdmin.php');
        exit(-1);
    }
}
