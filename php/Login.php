<?php

// LOGIN
class Login {

    public function loginAsClub($ID_regate, $titre, $date_debut, $courriel) {
        $_SESSION['ID_regate'] = $ID_regate;
        $_SESSION['titre_regate'] = $titre;
        $_SESSION['debut_regate'] = $date_debut;
        $_SESSION['courriel'] = $courriel;
        header("Location: Regate.php");
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

}

function assertAdmin() {
    if (!isset($_SESSION['ID_administrateur'])) {
        header('Location: LoginAdmin.php');
        exit(-1);
    }
}
