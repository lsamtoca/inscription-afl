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
    private $htmlTemplateFile = 'CourrielUtilisateurs_htmlTemplate.php';
    private $mail;

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
        $this->mail = new MyMailer();
    }

    private function sendMail($destinataires) {
        global $config;
        //  get Infos about who is th Administrator
        $sql = 'SELECT * FROM `Administrateur` WHERE `ID_administrateur`=:ID';
        $assoc = array('ID' => $_SESSION['ID_administrateur']);
        $req = executePreparedQuery($sql, $assoc);
        $admin = $req->fetch();

        //  Prepare sending of mail(   
        $this->mail->setFrom("$admin[Prenom] $admin[Nom] <$config[webMasterEmail]>");
        $this->mail->setReplayTo("$admin[Prenom] $admin[Nom] <$admin[courriel]>");
        $this->mail->addAddressTo("$admin[Prenom] $admin[Nom] <$admin[courriel]>");
        $this->mail->subject = filter_input(INPUT_POST, 'objet');
        $this->mail->messageText = filter_input(INPUT_POST, 'message');
        $cc = filter_input(INPUT_POST, 'cc');
        if ($cc != '') {
            $this->mail->addAddressCc(filter_input(INPUT_POST, 'cc'));
        }

        $max = count($destinataires);
        $step = 50;
        $ret = true;
        for ($i = 0; $i < $max; $i+=$step) {
            $courriels = array_slice($destinataires, $i, $step);
            $this->mail->addAddressesBcc($courriels);
            $ret = $ret && $this->mail->send();
        }
        return $ret;
    }

    private function notify() {
        global $config;
        $notify = new MyMailer();
        $notify->setFrom($config['webMasterEmail']);
        $notify->addAddressTo($this->mail->from);
        $notify->subject = 'Notification WebRegatta sur l\'envoye de votre message';
        $notify->messageText = "Vous avez envoyé ce message via WebRegatta:\n"
                . "Sujet : " . $this->mail->subject . "\n"
                . "Votre Message : \n"
                . $this->mail->messageText . "\n"
                . "\n\nRapport sur des possibles erreurs.\n"
                . $this->mail->report();
        $notify->send();
    }

    public function execute() {
        $destinataires = explode(',', filter_input(INPUT_POST, 'to')); // destinataires en BCC
        $ret = $this->sendMail($destinataires);
        $this->notify();

        if (!$ret) { // S'il ya eut un problème
            $msg = "Problème avec l'envoye du courriel.\n"
                    . "Vous allez etre notifié par courriel sur ce problème";
            pageErreur($msg);
        } else { //Sinon
            // prepare and give an asnwer on the web
            $bcc = implode(',', $destinataires);
            if (strlen($bcc) >= 40) {
                $mails = explode(',', $bcc);
                $bccShort = $mails[0] . ",...," . $mails[count($destinataires) - 1];
            } else {
                $bccShort = $bcc;
            }
            $ackMessage = "Message envoyé de :\n\t"
                    . $this->mail->from
                    . "\n"
                    . "Message envoyé à:\n\t$bccShort";
            pageAnswer($ackMessage);
        }
        exit(0);
    }

    public function isActive() {
        return isset($_POST['envoyer_mail']);
    }

    public function html($noTabs=0) {
        $replace = array('{mailsClubs}', '{mailsCoureurs}', '{mailsAdmins}', '{self}');
        $with = array($this->mailsClubs, $this->mailsCoureurs, $this->mailsAdmins, urlSelf());
        $template = file_get_contents(__DIR__ . "/$this->htmlTemplateFile");
        echo utf8_encode(str_replace($replace, $with, $template));
    }

}
