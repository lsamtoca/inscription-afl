<?php

require_once './php/mailer.php';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CourrielUtilisateurs {

    private $mailsClubs;
    private $mailsCoureurs;
    private $mailsAdmins;

    public function isActived() {
        return isset($_POST['envoyer_mail']);
    }

    public function sendMail() {
        $sql = 'SELECT * FROM `Administrateur` WHERE `ID_administrateur`=:ID';
        $assoc = array('ID' => $_SESSION['ID_administrateur']);
        $req = executePreparedQuery($sql, $assoc);
        $admin = $req->fetch();

        $to = $sender = $admin['courriel'];

        $subject = clean_post_var($_POST['objet']);
        $message = clean_post_var($_POST['message']);
        $bcc = $_POST['to']; // destinataires en BCC
        $cc = $_POST['cc'];

        if (send_mail_text_attachement($sender, $to, $subject, $message, $cc, $bcc)) {
            if (strlen($bcc) >= 40) {
                $mails = explode(',', $bcc);
                $bccShort = $mails[0] . ",...," . $mails[count($mails) - 1];
            } else {
                $bccShort = $bcc;
            }
            $ackMessage = "Message envoyé de :\n\t$sender"
                    . "\n"
                    . "Message envoyé à:\n\t$bccShort";
            pageAnswer($ackMessage);
            exit(0);
        }
    }

    function __construct() {
        $sql = "SELECT DISTINCT `mail` from `Inscrit` where 1 ORDER BY `mail` ASC";
        $req = executePreparedQuery($sql, []);
        $this->mailsCoureurs = implode(',', $req->fetchAll(PDO::FETCH_COLUMN));

        $sql2 = "SELECT DISTINCT `courriel` from `Regate` where 1 ORDER BY `courriel` ASC";
        $req2 = executePreparedQuery($sql2, []);
        $this->mailsClubs = implode(',', $req2->fetchAll(PDO::FETCH_COLUMN));

        $sql3 = "SELECT DISTINCT `courriel` from `Administrateur` where 1 ORDER BY `courriel` ASC";
        $req3 = executePreparedQuery($sql3, []);
        $this->mailsAdmins = implode(',', $req3->fetchAll(PDO::FETCH_COLUMN));
    }

    public function html() {
        $replace = array('{mailsClubs}', '{mailsCoureurs}', '{mailsAdmins}', '{self}');
        $with = array($this->mailsClubs, $this->mailsCoureurs, $this->mailsAdmins, urlSelf());
        $template = file_get_contents(__DIR__ . '/Admin-html-courriel-utilisateurs.php');
        echo str_replace($replace, $with, $template);
    }

}
