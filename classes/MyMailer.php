<?php

class File {

    public $path;
    public $name;
    public $type;

    public function __construct($path, $name, $type) {
        $this->path = $path;
        $this->name = $name;
        $this->type = $type;
    }

}

class MyMailer {

    public $from = '';
    public $replayTo = '';
    public $subject = '';
    private $to = array();
    private $cc = array();
    private $bcc = array();
    public $messageText = '';
    public $messageHtml = '';
    public $footerText = '';
    public $footerHtml = '';
    public $files = array();
    // To be independent of context
    private $developer = 'luigi.santocanale@lif.univ-mrs.fr';
    private $webMaster = 'luigi.santocanale@lif.univ-mrs.fr';
    private $development = false;
    // To be used with mail function
    private $mailTo;
    private $mailSubject;
    private $mailMessage;
    private $mailHeaders;
    private $eol = "\r\n";
    // A Logger -- see class Logger.php
    private $log;

    function __construct() {

        global $config, $development;

        if (isset($config['developerEmail'])) {
            $this->developer = $config['developerEmail'];
        }
        if (isset($config['webMasterEmail'])) {
            $this->webMaster = $config['webMasterEmail'];
        }
        if (isset($development)) {
            $this->development = $development;
        }

        // Send this mail by default to the web master
        $this->addAddressCc($this->webMaster);
        if (
                isset($_FILES['attachment']) &&
                $_FILES['attachment']['error'] == 0
        ) {
            $path = $_FILES['attachment']['tmp_name'];
            $name = $_FILES['attachment']['name'];
            $type = $_FILES['attachment']['type'];
            $this->addFile($path, $name, $type);
        }

        $this->log = new Logger();
    }

    private function addAddress($whichArray, $address) {
        $courriel0 = filter_var($address, FILTER_SANITIZE_EMAIL);
        $courriel = filter_var($courriel0, FILTER_VALIDATE_EMAIL);
//        echo $courriel;
//        exit(0);
        if ($courriel == false) {
            $this->log->logWarning("$whichArray : Courriel '$address' n'est pas valide");
            return false;
        } else {
            array_push($this->$whichArray, $courriel);
            return true;
        }
    }

    private function addAddresses($whichArray, $addresses) {
        if (gettype($addresses) == 'string') {
            $arrayOfAddresses = explode(',', $addresses);
        } else {
            $arrayOfAddresses = $addresses;
        }
        $ret = true;
        foreach ($arrayOfAddresses as $address) {
            $ret = $ret && $this->addAddress($whichArray, $address);
        }
        return $ret;
    }

    public function addAddressTo($to) {
        return $this->addAddress('to', $to);
    }

    public function addAddressesTo($tos) {
        return $this->addAddresses('to', $tos);
    }

    public function addAddressCc($cc) {
        return $this->addAddress('cc', $cc);
    }

    public function addAddressesCc($ccs) {
        return $this->addAddresses('cc', $ccs);
    }

    public function addAddressBcc($bcc) {
        return $this->addAddress('bcc', $bcc);
    }

    public function addAddressesBcc($bccs) {
        return $this->addAddresses('bcc', $bccs);
    }

    public function setFrom($address) {
        $courriel0 = filter_var($address, FILTER_SANITIZE_EMAIL);
        $courriel = filter_var($courriel0, FILTER_VALIDATE_EMAIL);
        if (!$courriel) {
            $this->log->logError("From : courriel '$address' invalide");
            return false;
        } else {
            $this->from = $address;
            return true;
        }
    }

    public function setReplayTo($address) {
        $courriel0 = filter_var($address, FILTER_SANITIZE_EMAIL);
        $courriel = filter_var($courriel0, FILTER_VALIDATE_EMAIL);
        if (!$courriel) {
            $this->log->Warning("Replay : courriel '$address' invalide");
            return false;
        } else {
            $this->from = $address;
            return true;
        }
    }

    public function addFile($path, $name, $type) {
        $file = new File($path, $name, $type);
        array_push($this->files, $file);
    }

    private function subject_encode_from_utf8($subject) {
        return '=?UTF-8?B?' . base64_encode($subject) . '?=';
    }

    private function setDevelFooter() {
        $to = implode(',', $this->to);
        $cc = implode(',', $this->cc);
        $bcc = implode(',', $this->bcc);
        $this->footerText = "\n\n" . str_repeat('-', 60) . "\n"
                . "Email sent in developer mode\n"
                . "\nOriginal message:\n"
                . "\tfrom -> $this->from\n"
                . "\tto -> $to, \n"
                . "\tcc -> $cc, \n\tbcc -> $bcc";
    }

    private function setDevel() {
        if ($this->development) {
            $this->setDevelFooter();
            $this->to = array($this->developer);
            $this->cc = array();
            $this->bcc = array();
        }
    }

    private function setTo() {
        $this->mailTo = implode(',', $this->to);
    }

    private function setSubject() {
        if ($this->subject == '') {
            $this->subject = '(no subject)';
        }
        $this->mailSubject = $this->subject_encode_from_utf8($this->subject);
    }

    private function setMessageText() {
        $this->mailMessage = $this->messageText . $this->footerText;
    }

    private function setMessageHtml() {
        $this->mailMessage = $this->messageHtml . $this->footerHtml;
    }

    private function setHeadersCommon() {
        $HEADERS = "From: $this->from" . $this->eol;
        if ($this->replayTo == '') {
            $this->replayTo = $this->from;
        }
        $HEADERS .= "Reply-To: $this->replayTo" . $this->eol;
        if (count($this->cc) > 0) {
            $CC = implode(',', $this->cc);
            $HEADERS .= "CC: $CC\r\n";
        }
        if (count($this->bcc) > 0) {
            $BCC = implode(',', $this->bcc);
            $HEADERS .= "BCC: $BCC\r\n";
        }
        $HEADERS .= 'MIME-Version: 1.0' . $this->eol;
        $this->mailHeaders .= $HEADERS;
    }

    private function setHeadersTextPlain() {
        $HEADERS = 'Content-Type: text/plain; charset="UTF-8"' . $this->eol;
        $HEADERS .= 'X-Mailer: PHP/' . phpversion();
        $this->mailHeaders .= $HEADERS;
    }

    private function setHeadersTextHtml() {
        $HEADERS = 'Content-Type: text/html; charset="UTF-8"' . $this->eol;
        $HEADERS .= 'X-Mailer: PHP/' . phpversion();
        $this->mailHeaders .= $HEADERS;
    }

    private function setHeadersMultipart() {
        $random_hash = md5(time());
        $php_mixed = "PHP-mixed-$random_hash";
        //$php_alt = "PHP-alt-$random_hash";

        $headers = "Content-Type: multipart/mixed; boundary=\"$php_mixed\"" . $this->eol;
        $headers .= "This is a multi-part message in MIME format." . $this->eol;
        $headers .= 'X-Mailer: PHP/' . phpversion();
        $this->mailHeaders .= $headers;

        return $php_mixed;
    }

    private function setMessageMultipart($php_mixed) {
        $message = '';
        if ($this->messageText != '') {
            //TEXT MESSAGE
            //define the body of the message.
            $message = '--' . $php_mixed . $this->eol;
            $message.='Content-Type: text/plain; charset="UTF-8"' . $this->eol;
            $message.='Content-Transfer-Encoding: 8bit' . $this->eol . $this->eol;
            $message.= $this->messageText . $this->footerText . $this->eol . $this->eol;
        }

        // HTML MESSAGE
        if ($this->messageHtml != '') {
            $message .= '--' . $php_mixed . $this->eol;
            $message.='Content-Type: text/html; charset="UTF-8"' . $this->eol;
            $message.='Content-Transfer-Encoding: 8bit' . $this->eol . $this->eol;
            $message.= $this->messageHtml . $this->footerHtml . $this->eol . $this->eol;
        }

        // FILES 
        if (count($this->files) > 0) {
            foreach ($this->files as $file) {
                $path = $file->path;
                $name = $file->name;
                $type = $file->type;

                if (!file_exists($path)) {
                    throw new Exception("Le fichier $path n'existe pas.");
                }

                // The attachment
                //read the attachment file contents into a string,
                //encode it with MIME base64,
                //and split it into smaller chunks
                $attachment = chunk_split(base64_encode(file_get_contents($path)));
                $message.='--' . $php_mixed . $this->eol;
                $message.="Content-Type: $type; name=\"$name\"" . $this->eol;
                $message.='Content-Transfer-Encoding: base64' . $this->eol;
                $message.='Content-Disposition: attachment' . $this->eol . $this->eol;
                $message.=$attachment . $this->eol . $this->eol;
            }
        }
        // Fin
        $message.='--' . $php_mixed . '--' . $this->eol;
        $this->mailMessage = $message;
    }

    private function setText() {
        $this->setTo();
        $this->setSubject();
        $this->setHeadersCommon();
        $this->setHeadersTextPlain();
        $this->setMessageText();
    }

    private function setHtml() {
        $this->setTo();
        $this->setSubject();
        $this->setHeadersCommon();
        $this->setHeadersTextHtml();
        $this->setMessageHtml();
    }

    private function setMultipart() {
        $this->setTo();
        $this->setSubject();
        $this->setHeadersCommon();
        $php_mixed = $this->setHeadersMultipart();
        $this->setMessageMultipart($php_mixed);
    }

    public function send() {
        $this->setDevel();

        $noAttachments = count($this->files);
        if ($noAttachments == 0 && $this->messageHtml == '') {
            $this->setText();
        } elseif ($noAttachments == 0 && $this->messageText == '') {
            $this->setHtml();
        } else {
            $this->setMultipart();
        }

        $ret = mail($this->mailTo, $this->mailSubject, $this->mailMessage, $this->mailHeaders);
        $tos = implode(',', $this->to);
        $ccs = implode(',', $this->cc);
        $bccs = implode(',', $this->bcc);
        $summary = "from : $this->from\n"
                . "to : $tos\n"
                . "cc : $ccs\n"
                . "bcc : $bccs\n";

        if (!$ret) {
            $this->log->logError("Problème avec le courriel :\n$summary");
        } else {
            $this->log->logAck("Courriel envoyé :\n$summary");
        }
        return $ret;
    }

    public function report() {
        return $this->log->report();
    }

}
