<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//require '../../classes/AnswerToForm.php';
//require "../../bootstrap/autoload.php";
require_once __DIR__ . '/../../php/User.php';
require_once __DIR__ . '/../../php/Formats.php';

assert(isset($_SESSION['ID_administrateur']));

//$_SESSION['ID_administrateur'] = 1;

class NouvelleRegate extends AnswerToForm {

    public $form;

    public function __construct() {

        $msgRequired = 'Ce champ est obligatoire';
        $msgEmail = 'Attention : ce courriel n\'est pas valide';
        $valRequired = ['type' => 'required', 'message' => $msgRequired];
        $valMinLength = ['type' => 'minlength', 'value' => 8, 'message' => ' Au moins 8 caractères'];
        $valMaxLength = ['type' => 'maxlength', 'value' => 10, 'message' => ' Au plus 10 caractères'];
        $stdValidations = [$valRequired, $valMinLength, $valMaxLength];

        $this->form = new Form('nouvelleRegate', [
            'submitValue' => 'Valider',
            'label' => 'Ouvrir une nouvelle régate',
            'legend' => 'Ouvrir une nouvelle régate',
            'captcha' => false,
            'withinFieldset' => true,
            'table' => 'Regate']
        );


        //
        $this->form->Identifiants = new Input('separator', 'Identifiants', ['value' => 'Identifiants']);

        $this->form->org_login = new Input('text', 'org_login', [
            'label' => 'Identifiant (login) pour cette régate',
            'size' => 10,
            'help' => 'Un club ne peut pas utiliser les memes identifiants'
            . ' pour plusieurs régates.',
            'validations' => $stdValidations
                ]
        );
        $this->form->org_passe = new Input('text', 'org_passe', [
            'label' => 'Mot de passe pour la régate',
            'size' => 10,
            'validations' => $stdValidations
                ]
        );

        //
        $this->form->ClubInfos = new Input('separator', 'ClubInfos', ['value' => 'Renseignements sur le club organisateur']);

        $this->form->club = new Input('text', 'club', [
            'label' => 'Libelé  du club',
            'size' => 40,
            'tableField' => 'cv_organisateur'
                ]
        );

        $this->form->lieu = new Input('text', 'lieu', [
            'label' => 'Lieu',
            'size' => 40,
            'tableField' => 'lieu'
                ]
        );

        $this->form->titre = new Input('text', 'titre', [
            'label' => 'Libelé de la régate',
            'size' => 40,
            'tableField' => 'titre'
                ]
        );

        $this->form->org_courriel = new Input('text', 'org_courriel', [
            'label' => 'Courriel du club',
            'size' => 30,
            'validations' => [
                ['type' => 'required', 'message' => $msgRequired],
                ['type' => 'email', 'message' => $msgEmail]
            ]
                ]
        );

        //
        $this->form->Dates = new Input('separator', 'Dates', ['value' => 'Dates']);

        $this->form->date_destr = new Input('datePicker', 'date_destr', [
            'label' => 'Date de destruction',
            'default' => date("d/m/Y", 31536000 + time()),
            'size' => 10,
            'format' => "dd/mm/yy",
            'help' => 'Avant cette date il ne sera pas possible détruire cette régate. '
            . 'Après cette date, ces identifiants ne seront plus valides.',
            'validations' => [
                ['type' => 'required', 'message' => $msgRequired]
            ]
                ]
        );
        $this->form->date_start = new Input('datePicker', 'date_start', [
            'label' => 'Debut de la régate',
            'default' => date("d/m/Y"),
            'size' => 10,
            'format' => "dd/mm/yy",
                ]
        );

        $this->form->date_end = new Input('datePicker', 'date_end', [
            'label' => 'Fin de la régate',
            'default' => date("d/m/Y"),
            'size' => 10,
            'format' => "dd/mm/yy",
            'tableField' => 'date_fin',
            'tableFieldTransform' => 'dateReformatJqueryToMysql'
                ]
        );

        $this->form->Valider = new Input('separator', 'Valider');

        $this->form->linkInputs();
    }

    private function envoyerMail($destinataire, $login, $mdp) {
        global $config;

        $sql = 'SELECT * FROM `Administrateur` WHERE `ID_administrateur`=:ID';
        $assoc = array('ID' => $_SESSION['ID_administrateur']);
        $req = executePreparedQuery($sql, $assoc);
        $admin = $req->fetch();

        $adminNom = $admin['Nom'];
        $adminEmail = $admin['courriel'];

        $mail = new MyMailer();
        $mail->setFrom($adminEmail);
        $mail->addAddressCc($adminEmail);
        $mail->addAddressTo($destinataire);
        $mail->subject = 'Création d\'une nouvelle régate sur '
                . 'le site des pré-inscription';

        if (isset($config['signature'])) {
            $signature = $config['signature'];
        } else {
            $signature = "l'AFL";
        }
        $message = "Bonjour,\n\n"
                . "M(me) $adminNom vient de créer une régate pour vous "
                . "sur le site des inscriptions aux régates\n"
                . "\thttp://" . format_path_to_site() . "\n\n"
                . "Vous pouvez gérer cette régate en vous identifiant à l'adresse \n"
                . format_url_login()
                . "\n"
                . "Vos identiants sont :\n\tLogin : '$login'\n\tMot de passe : '$mdp'\n"
                . "\n\n"
                . "Cordialement,\n\t $signature";
        $mail->messageText = $message;

        return $mail->send();
    }

    private function creerRegate($login, $passe, $courriel, $dateDestruction) {
        // On verifie d'abord s'il existe déjà un utilisateur avec le meme login   
        $user = User_selectByLogin($login);
        $user = NULL;
        if ($user != NULL) {
            $message = "Un utilisateur avec le même identifiant '$login' "
                    . "existe déjà dans la base de données.\n"
                    . "Veuillez choisir un autre identifiant";
            pageAnswer($message);
            exit(0);
        }

        $date = dateReformatJqueryToMysql($dateDestruction);
        $today = date('Y-m-d');
        $sql = 'INSERT INTO Regate (org_login, coded_org_passe,courriel,'
                . 'destruction,ID_administrateur,'
                . 'date_debut, date_fin, date_limite_preinscriptions) ' .
                'VALUES(:org_login, :coded_org_passe, :courriel, ' .
                ':destruction, :ID_administrateur,'
                . ':date_debut, :date_fin, :date_limite_preinscriptions)';
        $assoc = array(
            'org_login' => $login,
            'coded_org_passe' => md5($passe),
            'courriel' => $courriel,
            'destruction' => $date,
            'ID_administrateur' => $_SESSION['ID_administrateur'],
            'date_debut' => $today,
            'date_fin' => $today,
            'date_limite_preinscriptions' => $today,
        );
        // Here we shouldhave all the others fields
        executePreparedQuery($sql, $assoc);

        $sql2 = "SELECT ID_regate FROM Regate where org_login=:org_login AND coded_org_passe=:coded_org_passe";
        //echo $sql2;
        //exit(0);
        $assoc2 = array(
            'org_login' => $login,
            'coded_org_passe' => md5($passe));
        $req2 = executePreparedQuery($sql2, $assoc2);
        $id = $req2->fetch(PDO::FETCH_COLUMN);
        return $id;
    }

    public function execute() {
        // This does not do any filtering ;
        $this->form->fromPost(FILTER_DEFAULT);

        $login = $this->form->org_login->value;
        $passe = $this->form->org_passe->value;
        $dateDestruction = $this->form->date_destr->value;

        //For this we should have a method in form
        //to validate the data on the server side
        $courriel = filter_var($this->form->org_courriel->value, FILTER_VALIDATE_EMAIL);

        if ($login != '' &&
                $passe != '' &&
                $courriel != '' &&
                $dateDestruction != '' &&
                $courriel != FALSE
        ) {
            $id = $this->creerRegate($login, $passe, $courriel, $dateDestruction);
            $this->form->toDb($id);

            if (!$this->envoyerMail($courriel, $login, $passe)) {
                Layouts::pageErreurMail();
                exit(1);
            };
        } else {
            $message = "Un des champs ést vide ou le courriel donné n'est pas valide";
            pageErreur($message);
            exit(1);
        }
        //echo $id;


        Layouts::pageAnswer('La régate a bien été crée');
    }

    public function isActive() {
        return $this->form->isCompleted();
    }

    public function html($noTabs=0) {
        $this->form->displayValidation($noTabs);
        $this->form->displayUl($noTabs);
    }

}


if (false) {
    $main = new NouvelleRegate();
    if ($main->isActive()) {
        $main->execute();
        exit(0);
    }

    Layouts::xhtml_pre1("Test", $type = 'transitional');
    Layouts::requireJquery();
    Layouts::requireJqueryValidations();
    Layouts::requireJqueryDatePicker();

    $main->form->displayJQueryDatePickers();
    ?>

    <style>
        form  ul {list-style-type: none; padding-left: 10px;}
        form  ul > li { padding:2px; margin:2px;}
        form  ul > li > label.left:after {content:" : ";}
    </style>
    <?php
    Layouts::xhtml_pre2('Test');
    ?>
    <div class="contenu">
    <?php echo $main->html(1); ?>
    </div>

    <?php
    Layouts::xhtml_post();
}