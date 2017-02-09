<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ListeRegates
 *
 * @author lsantoca
 */
class ListeRegates extends AnswerToForm {

    private $listeRegateHtmlFile = 'ListeRegates_htmlTemplate.php';
    private $idregate;
    private $regate = array();

    public function __construct() {

        if (isset($_POST['IDR'])) {
            $this->idregate = filter_input(INPUT_POST, 'IDR', FILTER_VALIDATE_INT);
            if (!$this->idregate) {
                throw new Exception('Regate inexistante');
            }
            $this->regate = Regate_selectById($this->idregate);
        }
    }

    public function html($noTabs=0) {
        $sql = 'SELECT * FROM Regate ORDER BY `ID_regate` DESC';
        $assoc = array();
        $req = executePreparedQuery($sql, $assoc);
        // $regates used in the Html File
        $regates = $req->fetchall();
        $req->closeCursor();
        include(__DIR__ . "/$this->listeRegateHtmlFile");
    }

    function isActive() {
        if (isset($_POST['IDR'])) {
            return isset($_POST['detruire']) || isset($_POST['loginAsClub']);
        } else {
            return false;
        }
    }

    function execute() {
        if (isset($_POST['detruire'])) {
            $this->destroyRace();
            header('Location: Admin#listeRegates');
        }
        if (isset($_POST['loginAsClub'])) {
            $this->loginAsClub();
        }
    }

    function destroyRace() {
        if ($this->isDestroyableRegate()) {
            $this->destroyRaceFromDb();
        } else {
            $message = "Pour l'instant, il n'est pas possible détruire cette régate, "
                    . "car \n  (1) ou bien la date de destruction de cette régate n'est pas passée,"
                    . "\n (2) ou bien la régate a déjà un inscrit";
            pageErreur($message);
            exit(0);
        }
    }

    function numberOfSailors($idregate) {
        $sql = 'SELECT COUNT(*) as `num` FROM Inscrit WHERE ID_regate= :IDR';
        $assoc = array('IDR' => $idregate);
        $req = executePreparedQuery($sql, $assoc);
        $req->execute();
        $row = $req->fetch();
        return ($row['num']);
    }

    function destroyRaceFromDb() {
        $bd = newBD();
        // we delete the inscrits
        $sql1 = 'DELETE FROM Inscrit WHERE ID_regate= :IDR';
        $assoc = array('IDR' => $this->idregate);
        executePreparedQuery($sql1, $assoc, $bd);
        // we delete the race
        $sql2 = 'DELETE FROM Regate WHERE ID_regate= :IDR';
        executePreparedQuery($sql2, $assoc, $bd);
    }

    function isDestroyableRegate() {
        // Under which conditions we can destroy the race
        return (Regate_estDestructible($this->regate) || $this->numberOfSailors($this->idregate) == 0);
    }

    function loginAsClub() {// Login as Club
        if (
                ($_SESSION['ID_administrateur'] != $this->regate['ID_administrateur'])
                and ( $_SESSION['ID_administrateur'] != 1)
        ) {
            $message = "Vous n'êtes pas autorisé à administrer cette régate,"
                    . " car vous n'êtes pas son créateur";
            Layouts::pageErreur($message);
            exit(0);
        }

        $ID_regate = $this->regate['ID_regate'];
        $login = new Login;
        $login->loginAsClub($ID_regate);
        header('Location: Regate');
        exit(0);
    }

}
