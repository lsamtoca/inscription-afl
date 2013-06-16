<?php
require "partage.php";

//
// Demander les infos sur la régate
//
try {
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
    $sql = 'SELECT `ID_regate`,`titre`,`lieu`,`description`, ' .
            'DATE_FORMAT(`date_debut`, \'%d-%m-%Y\') as `date_debut`, '.
            'DATE_FORMAT(`date_fin`, \'%d-%m-%Y\') as `date_fin`, '.
            '`date_limite_preinscriptions` '.
            'FROM `Regate` '.
            'WHERE ID_regate = ?';
    $req = $bdd->prepare($sql);
    $req->execute(array($_GET['regate']));
    if($req->RowCount() == 0)
        die('Cette régate n\'existe pas :-(');

    // Tout ce qu'on veut savoir sur la regate
    $regate = $req->fetch();
    $URLPRE=format_url_preinscrits($_GET['regate']);
    if($regate['date_limite_preinscriptions'] != '') {
        $now = new DateTime;
        $limite = new DateTime($regate['date_limite_preinscriptions']);
        $limite->setTime(23,59);
    }

}catch(Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : '.$e->getMessage());
}

// Pre-remplissage du formulaire

// Association :
// bd => form
$association = array(
        'nom' => 'Nom',
        'prenom' => 'Prenom',
        'naissance' => 'naissance',
        'num_lic' => 'lic',
        'isaf_no' => 'isaf_no',
        'num_club' => 'num_club',
        'nom_club' => 'nom_club',
        'prefix_voile' => 'Cvoile',
        'num_voile' => 'Nvoile',
        'serie' => 'serie',
        'adherant' => 'adherant',
        'sexe' => 'sexe',
        'mail' => 'mail',
        'statut' => 'statut',

        //'conf' => '0',
        //'ID_regate' => 'IDR',
        //'date_preinscription' =>  date('Y-m-d G:i:s')
);

$assoc_COUREUR_form = array(
        'NOM' => 'Nom',
        'PRENOM' => 'Prenom',
        'DAT_NAIS' => 'naissance',
        'NO_LIC' => 'lic',
        'ISAF_ID' => 'isaf_no',
        'NO_CLUB' => 'num_club',
        'SEXE' => 'sexe',
);

foreach($association as $field_bd => $field_form)
    $data[$field_form] = '';

$data['naissance']='1994-01-01';
$data['search_lic']='';
$data['search_isaf']='';
$data['M']='';
$data['F']='';

$data['LAS']='';
$data['LAR']='';
$data['LA4']='';

$data['Licencie']='';
$data['Etranger']='';
$data['Autre']='';

$data['ad_AFL']='';
$data['non_ad_AFL']='';

//
// Si on demande de pre-remplir le formulaire
//

if(isset($_POST['search_submit'])) {

    try {
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);

        // Faire d'abord la requête sur la table Inscrit

        // Set the field to search for
        $field_key='num_lic';
        if(isset($_POST['search_isaf']))
            $field_key='isaf_no';

        // Set its value
        if(isset($_POST['search_lic'])) {
            $field_value=$_POST['search_lic'];
            $data['search_lic']=$_POST['search_lic'];
        }
        if(isset($_POST['search_isaf'])) {
            $field_value=$_POST['search_isaf'];
            $data['search_isaf']=$_POST['search_isaf'];
        }

        $sql="Select * from `Inscrit` where $field_key=? order by `date preinscription` desc";
        $req = $bdd->prepare($sql);
        $req->execute(array($field_value));
        //        $req->debugDumpParams();

        // Si on a trouvé qqchose on s'inscrit
        if($req->rowCount() > 0) {
            $row=$req->fetch();
            foreach($association as $field_bd => $field_form) {
                $_POST[$field_form] = $row[$field_bd];
                //  $data[$field_form] = $row[$field_bd];

            }
            $_POST['maSoumission']='';
            $_POST['IDR']=$_GET['regate'];
            $_POST['lang']='fr';
            $_POST['no_email']=true;

            // Ces donnés devraient être soumis à un contrôle
            // Afin de ne pas propager des erreurs
            include  'Inscription.php';
            exit;
        }

        // Sinon, on cherche dans la table COUREUR.DBF
        // Faire la requete sur la table COUREUR.DBF
        if(isset($_POST['search_lic'])) {
            $sql="Select * from `COUREUR.DBF` where `NO_LIC`= ?";
            $req = $bdd->prepare($sql);
            $req->execute(array($_POST['search_lic']));
            //        $req->debugDumpParams();
            function strip_spaces($string) {
                return str_replace(' ','',$string);
            }
            // Si on a trouvé qqchose on pre-remplis le formulaire
            if($req->rowCount() > 0) {
                $row=$req->fetch();
                foreach($assoc_COUREUR_form as $field_bd => $field_form) {
                    $data[$field_form] = strip_spaces($row[$field_bd]);
                }
//                $naissance=date_create_from_format('Ymd',$data['naissance']);
//                $data['naissance']=date_format($naissance,'Y-m-d');
                list($year,$month,$day) = sscanf($data['naissance'],'%04d%02d%02d');
                $data['naissance'] = "$year-$month-$day";
                $data[$data['sexe']]='checked';
                $data['Licencie']='checked';
                $data['statut']='Licencie';
            }
        }
    }catch(Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : '.$e->getMessage());
    }


}
?>


<?php   xhtml_pre1("Préinscription"); ?>

<script type="text/javascript" src="classes/calendarDateInput.js">
    /***********************************************
     * Jason's Date Input Calendar- By Jason Moon http://calendar.moonscript.com/dateinput.cfm
     * Script featured on and available at http://www.dynamicdrive.com
     * Keep this notice intact for use.
     ***********************************************/
</script>

<script src="classes/javascript_form/gen_validatorv4.js" type="text/javascript">
    /***********************************************
     * http://www.javascript-coder.com/html-form/javascript-form-validation.phtml
     ***********************************************/
</script>

<script type="text/javascript">
    //<![CDATA[

    var strings_fr = [];
    var strings_en = [];
    var labels = [];
    var langue = 'fr';

    function debug_labels(){
        var msg='';
        for(i in labels){
            msg=msg + "\n" + labels[i] + " :\n" + get_string(labels[i]);
        }
        alert(msg);
    }

    // function double_label_test(french,english,at_begin){
    //
    //   alert(this.partentNode.nodeName);
    //   label=this.partentNode.nodeName;
    //
    //   strings_fr[label]=french;
    //   strings_en[label]=english;
    //
    //   var found=false;
    //   for(l in labels){
    //     if(l==label) { found=true; break;}
    //     }
    //   if(!found)
    //     labels.push(label);
    //
    //   if(at_begin) {
    //     if(langue=='fr')
    //       document.getElementById(label).innerHTML=strings_fr[label];
    //     else
    //       document.getElementById(label).innerHTML=strings_en[label];
    //       }
    // }

    function double_label(label,french,english,at_begin){
        strings_fr[label]=french;
        strings_en[label]=english;

        var found=false;
        for(i in labels){
            if(labels[i]==label) { found=true; break;}
        }
        if(!found)
            labels.push(label);

        if(at_begin) {
            if(langue=='fr')
                document.getElementById(label).innerHTML=strings_fr[label];
            else
                document.getElementById(label).innerHTML=strings_en[label];
        }
    }

    function get_string(label){
        if(langue=='fr')
            return strings_fr[label];
        else
            return strings_en[label];
    }

    function set_anglais(){
        langue='en';
        document.getElementById('input_lang').value='en';

        add_validations_searchform();
        add_validations_searchform_isaf();
        add_validations_mainform();

        for (i in labels) {
            if(! document.getElementById(labels[i]).innerHTML=='')
                document.getElementById(labels[i]).innerHTML=strings_en[labels[i]];
        }
    }

    function set_francais(){
        langue='fr';
        document.getElementById('input_lang').value='fr';

        add_validations_searchform();
        add_validations_searchform_isaf();
        add_validations_mainform();

        for (i in labels) {
            if(! document.getElementById(labels[i]).innerHTML=='')
                document.getElementById(labels[i]).innerHTML=strings_fr[labels[i]];
        }
    }

    function switch_language(){
        if(langue=='fr')
            set_anglais();
        else
            set_francais();

    }

    // A bunch of function to ease handling
    // of validation and double language

    function my_validation_required(form,champ,validator){

        double_label(form+'_'+champ+'_errorloc',
        'Le champ '+ strings_fr['l_'+champ]+ ' est obligatoire',
        'Field '+ strings_en['l_'+champ]+ ' is required',
        false);

        //   alert(get_string(form+'_'+champ+'_errorloc'));
        validator.addValidation(champ,'required',get_string(form+'_'+champ+'_errorloc'));
    }

    function my_validation_required_condition(form,champ,validator,condition,expl_fr,expl_en){

        double_label(form+'_'+champ+'_errorloc',
        expl_fr + ' : le champ '+ strings_fr['l_'+champ]+ ' est obligatoire',
        expl_en + ': field '+ strings_en['l_'+champ]+ ' is required',
        false);

        //   alert(get_string(form+'_'+champ+'_errorloc'));
        validator.addValidation(champ,'required',get_string(form+'_'+champ+'_errorloc'),condition);
    }

    function my_validation_email(form,champ,validator){
        double_label(form+'_'+champ+'_errorloc',
        'Le champ '+ strings_fr['l_'+champ]+ ' n\'est pas une adresse email valide',
        'Field '+ strings_en['l_'+champ]+ ' is not a valid email address',
        false);

        validator.addValidation(champ,'email',get_string(form+'_'+champ+'_errorloc'));
    }

    function my_validation_radio(form,champ,validator,french,english){
        double_label(form+'_'+champ+'_errorloc',french,english,false);
        validator.addValidation(champ,'selone_radio',get_string(form+'_'+champ+'_errorloc'));
    }

    function my_validation_regexp(form,champ,validator,regexp,french,english){
        double_label(form+'_'+champ+'_errorloc',
        'Le champ '+ strings_fr['l_'+champ]+ ' n\'est pas de la forme ' + french,
        'Field '+ strings_en['l_'+champ]+ ' is not of the form ' + english,
        false);

        /*    debug_labels();
    alert(form+'_'+champ+'_errorloc' + "\n" + get_string(form+'_'+champ+'_errorloc'));*/
        validator.addValidation(champ,'regexp=^'+regexp+'$',get_string(form+'_'+champ+'_errorloc'));
    }


    //]]>
</script>

<?php xhtml_pre2("Préinscription");?>

<div id='infos_regate'>

    <!--Dates, titre, description-->

    <p>
        <?php if($regate['date_debut'] != "00-00-0000" and $regate['date_fin'] != "00-00-0000"): ?>
        Du <?php echo $regate['date_debut']; ?> au <?php echo $regate['date_fin']; ?> :
        <?php endif; ?>
        <b><?php echo $regate['titre']; ?></b>
        <?php if($regate['lieu'] != ""): ?>
        à <?php echo $regate['lieu']; ?>
        <?php endif; ?>.
    </p>
    <p><?php echo $regate['description']?></p>

    <!--Lien sur la liste des préinscrits-->
    <p>
        <a href="<?php echo $URLPRE;?>">
            <span id='liste_preinscrits'></span></a>
        <script type="text/javascript">
            double_label("liste_preinscrits","Liste des préinscrits.","Preregistered sailoirs.",true)
        </script>
    </p>

    <!--Date limite pré-inscription-->

    <?php if($regate['date_limite_preinscriptions'] != ''): ?>
    <p>
        <span id="deadline"></span>
        <script type="text/javascript">
            double_label("deadline","Date ultime pour se préinscrire : le ","Deadline for preregistration: ",true)
        </script>
            <?php echo $limite->format( 'd-m-Y' ); ?>
    </p>

        <?php if($now > $limite): ?>
    <p>
        La date limite pour se préinscrire à cette régate,
        le <?php echo $limite->format( 'd-m-Y' ); ?> est passée.
        <br />
        Il n'est plus possible se préinscrire à cette régate :-
    </p>
            <?php xhtml_post();
            die(''); ?>
        <?php endif; ?>
    <?php endif; ?>

</div> <!--infos_regate-->

<div id='choix_langue' class="white_over_dark">
    [<a onclick='switch_language()'><span id='choisir_langue'>
            <script  type="text/javascript" xml:space="preserve">
                double_label('choisir_langue','this form in English','ce formulaire en Français',true);
            </script>
        </span></a>]
</div><!--choix langue-->

<div id='search'>

    <fieldset>
        <legend><span id='search_legend'>
                <script  type="text/javascript" xml:space="preserve">
                    double_label('search_legend',
                    'Si vous avez déjà utilisé ce système -- ou si vous êtes licencié(e) -- ' +
                        'vous pouvez compléter le formulaire avec un click. ' +
                        'Me chercher par : ',
                    'If you already used the system, ' +
                        'then you can fill the form in one click. ' +
                        'Look for myself by',true);
                </script>
            </span></legend>

        <table><tr>
                <td>
                    <form name="searchform" id="searchform" action="<?php echo $_SERVER['PHP_SELF']."?regate=".$_GET['regate'];?>" method="post" onsubmit="return validateForm();">
                        <label for="search_lic"><span id='l_search_lic'>
                                <script  type="text/javascript" xml:space="preserve">
                                    double_label('l_search_lic','Numéro de licence ','Licence number',true);
                                </script>
                            </span> : </label>
                        <input name="search_lic" id="search_lic" type="text" size="8" maxlength="8" value="<?php echo $data['search_lic']; ?>"/>
                        <input name="search_submit" type='submit' value="Chercher">
                    </form>
                </td>
                <td>&emsp;</td>
                <td>

                    <form name="searchform_isaf" id="searchform_isaf"
                          action="<?php echo $_SERVER['PHP_SELF']."?regate=".$_GET['regate'];?>"
                          method="post" onsubmit="return validateForm();">

                        <label for="search_isaf"><span id='l_search_isaf'>
                                <script  type="text/javascript" xml:space="preserve">
                                    double_label('l_search_isaf','Numéro ISAF ','ISAF number',true);
                                </script>
                            </span> : </label>
                        <input name="search_isaf"
                               id="search_isaf" type="text" size="8" maxlength="8"
                               value="<?php echo $data['search_isaf']; ?>"/>

                        <input name="search_submit" type='submit' value="Chercher">

                    </form>
                </td>
                <td>
                    <span id='searchform_search_lic_errorloc' class='error_strings'></span>
                    <span id='searchform_isaf_search_isaf_errorloc' class='error_strings'></span>
                </td>
            </tr></table>
    </fieldset>

    <script  type="text/javascript" xml:space="preserve">
        //<![CDATA[

        function add_validations_searchform(){

            var searchvalidator  = new Validator('searchform');
            // Choix de l'affichage des messages d'erreur
            searchvalidator.EnableOnPageErrorDisplay();
            // search_validator.EnableOnPageErrorDisplaySingleBox();
            //search_validator.EnableMsgsTogether();

            searchvalidator.clearAllValidations();
            searchvalidator.formobj.old_onsubmit = null;


            my_validation_required(
            'searchform',
            'search_lic',
            searchvalidator,
            'Champ nécessaire',
            'Required');

            my_validation_regexp(
            'searchform',
            'search_lic',
            searchvalidator,
            '[0-9]{7,7}[A-Za-z]',
            'NNNNNNNL (7 chiffres et 1 lettre)',
            'NNNNNNNL (7 digits and 1 letter)');
        }

        //]]>
    </script>
    <script  type="text/javascript" xml:space="preserve">
        //<![CDATA[

        function add_validations_searchform_isaf(){

            var searchvalidator  = new Validator('searchform_isaf');
            // Choix de l'affichage des messages d'erreur
            searchvalidator.EnableOnPageErrorDisplay();
            // search_validator.EnableOnPageErrorDisplaySingleBox();
            //search_validator.EnableMsgsTogether();

            searchvalidator.clearAllValidations();
            searchvalidator.formobj.old_onsubmit = null;


            my_validation_required(
            'searchform_isaf',
            'search_isaf',
            searchvalidator,
            'Champ demandé',
            'Field required');

            my_validation_regexp(
            'searchform_isaf',
            'search_isaf',
            searchvalidator,
            '[A-Za-z]{5}[0-9]+',
            'LLLLLN... (5 lettres et au moins 1 chiffre)',
            'LLLLLN... (5 letters and at least 1 digit)');

        }

        //]]>
    </script>

</div>

<div id='formulaire'>

    <form id="mainform" action="Inscription.php" method="post" onsubmit="return validateForm();">
        <fieldset>
            <legend><span id='legend'></span></legend>

            <input name="lang" type="hidden" id="input_lang" value="fr" />
            <input name="IDR" type="hidden" id="IDR" value=<?php echo '"'.$_GET['regate'].'"' ;?>/>

            <!-- Donnés personnels : nom prenom, date naissance, sexe -->
            <label for="Nom"><span id='l_Nom'></span>:</label>
            <input name="Nom" type="text" id="Nom" value="<?php echo $data['Nom']; ?>"/>

            <label for="Prenom"><span id='l_Prenom'></span>:</label>
            <input name="Prenom" type="text" id="Prenom" value="<?php echo $data['Prenom']; ?>"/>
            <span id='mainform_Nom_errorloc' class='error_strings'></span>
            <span id='mainform_Prenom_errorloc' class='error_strings'></span>
            <br />


            <table><tr>
                    <td>    <label><span id='l_naissance'></span>:</label></td>
                    <td>
                    <!--    <input name="naissance" type="text" id="naissance" value="YYYY-MM-DD" size="10" maxlength="10" /> -->
                        <script type="text/javascript">DateInput('naissance', true, 'YYYY-MM-DD','<?php echo $data['naissance']; ?>');</script>
                    </td>
                    <td>
                        <div id='mainform_naissance_errorloc' class='error_strings'>
                        </div>
                    </td>
                </tr>
            </table>

            <input type="radio" name="sexe" id="radio4" value="M" <?php echo $data['M']; ?> />
            <label for="radio4"><span id='l_homme'></span></label>

            <input type="radio" name="sexe" id="radio5" value="F" <?php echo $data['F']; ?> />
            <label for="radio5"><span id='l_femme'></span></label><span id='mainform_sexe_errorloc' class='error_strings'></span>

            <hr />

            <!-- Contact -->

            <label for="mail">
                <span id='l_mail'></span>: </label>
            <input name="mail" id="mail" type="text" value="<?php echo $data['mail']; ?>" />

            <span id='mainform_mail_errorloc' class='error_strings'></span>


            <hr />

            <!-- Club -->
            <label for="nom_club">
                <span id='l_nom_club'></span>: </label>
            <input name="nom_club" id="nom_club" type="text" value="<?php echo $data['nom_club']; ?>"/>

            <label for="num_club">
                <span id='l_num_club'></span>: </label>
            <input name="num_club" id="num_club" type="text" size="5" maxlength="5" value="<?php echo $data['num_club']; ?>"/>
            <span id='mainform_num_club_errorloc' class='error_strings'></span>
            <hr />

            <!-- Serie -->

            <input type="radio" name="serie" id="radio6" value="LAS" <?php echo $data['LAS']; ?>/>
            <label for="radio6">Laser Standard</label>

            <input type="radio" name="serie" id="radio7" value="LAR" <?php echo $data['LAR']; ?> />
            <label for="radio7">Laser Radial</label>

            <input type="radio" name="serie" id="radio8" value="LA4" <?php echo $data['LA4']; ?> />
            <label for="radio8">Laser 4.7</label><span id='mainform_serie_errorloc' class='error_strings'></span>
            <br />

            <label for="Cvoile">
                <span id='l_Nvoile'></span>:
            </label>
            <input name="Cvoile" type="text" id="Cvoile" size="3" maxlength="3" value="<?php echo $data['Cvoile']; ?>"/>
            <input name="Nvoile" type="text" id="Nvoile" size="6" maxlength="6" value="<?php echo $data['Nvoile']; ?>"/>
            <span id='mainform_Cvoile_errorloc' class='error_strings'></span>
            <span id='mainform_Nvoile_errorloc' class='error_strings'></span>
            <hr />

            <!-- Statut : Licence et AFL -->

            <input type="radio" name="statut" id="radio1" value="Licencie" <?php echo $data['Licencie']; ?> />
            <label for="radio1"><span id='l_ffv'></span></label>

            <input type="radio" name="statut" id="radio3" value="Etranger" <?php echo $data['Etranger']; ?> />
            <label for="radio3"><span id='l_etranger'></span></label>

            <input type="radio" name="statut" id="radio2" value="Autre" <?php echo $data['Autre']; ?> />
            <label for="radio2"><span id='l_autre'></span></label>

            <span id='mainform_statut_errorloc' class='error_strings'></span>
            <br />

            <label ><span id='l_afl'></span>:</label>
            <input type="radio" name="adherant" id="radio9" value="1" <?php echo $data['ad_AFL']; ?> />

            <label for="radio9"><span id='l_oui'></span></label>

            <input type="radio" name="adherant" id="radio10" value="0" <?php echo $data['non_ad_AFL']; ?>/>
            <label for="radio10"><span id='l_non'></span></label>

            <span id='mainform_adherant_errorloc' class='error_strings'></span>
            <br />

            <label for="lic"><span id='l_lic'></span></label>
            <input name="lic" id="lic" type="hidden" size="8" maxlength="8" value="<?php echo $data['lic']; ?>"/>

            <label for="isaf_no"><span id="l_isaf_no"></span></label><input name="isaf_no" id="isaf_no" type="hidden" size="7" value="<?php echo $data['isaf_no']; ?>"/>
            <span id='mainform_lic_errorloc' class='error_strings'></span><span id='mainform_isaf_no_errorloc' class='error_strings'></span>
            <br />
            <div id="message">
            </div>
            <hr />

            <!--  <div id='mainform_errorloc' class='error_strings'></div>--><input type="submit" name="maSoumission" id="soumission" value="Valider"/>
        </fieldset>
    </form>
</div>
<script  type="text/javascript" xml:space="preserve">
    //<![CDATA[

    // Un peu de chaines de caracteres


    double_label('legend','Préinscription ','Preregistration',true);
    double_label('l_Nom','Nom ','Family name',true);
    double_label('l_Prenom','Prénom ','First name',true);
    double_label('l_naissance','Date de naissance ','Date of birth',true);
    double_label('l_homme','Homme ','Male',true);
    double_label('l_femme','Femme ','Female',true);
    double_label('l_mail','Courriel ','Email',true);
    double_label('l_nom_club','Club ','Club',true);
    double_label('l_num_club','no ','number',true);
    double_label('l_Nvoile','Numéro de voile ','Sail number',true);
    double_label('l_ffv','Licencié FFV ','Licenced FFV',true);
    double_label('l_etranger','Coureur étranger ','Foreign sailor',true);
    double_label('l_autre','Pas encore licencié ','Not licenced yet',true);
    double_label('l_afl','Adhérant AFL ','AFL member',true);
    double_label('l_oui','Oui','Yes',true);
    double_label('l_non','Non','No',true);
    double_label('l_lic','Numéro licence : ','Licence number: ',false);
    double_label('l_isaf_no','Numéro ISAF : ','ISAF number: ',false);
    //double_label('maSoumission','Valider','Submit',true);


    var message_ffv_fr = "<p>Lors de l'inscription au club "+
        "vous devez présenter votre licence FFV visée par un médecin sportif ou présenter un cértificat médical de moins de trois mois.</p>";

    var message_ffv_en = "<p>When completing your registration at the club "+
        "you need to present your FFV licence undersigned by a doctor, or a medical certificate not older than 3 months.</p>";

    var message_non_lic_fr ="<p>Vous devez :<ol>" +
        "<LI>vous licencier auprès d'un club FFV de votre choix. Cette licence doit être visée par un médecin sportif.</LI>" +
        "<li>être affilié à l'Association France Laser. Vous pouvez régulariser cette affiliation soit à l'inscription, soit auprès de votre délégué laser local" +
        "http://www.francelaser.org/ (divers -> liste des délégués), ou directement a l'AFL.</li>" +
        "</ol></p>";

    var message_non_lic_en ="<p>You must:<ol>" +
        "<LI>obtain a FFV licence at a FFV club of your choice. This licence need to be undersigned by a doctor.</LI>" +
        "<li>become member of the Association France Laser. You can pay your membership either at registration, or to your local laser delegate" +
        "http://www.francelaser.org/ (divers -> liste des délégués), or directly to the AFL.</li>" +
        "</ol></p>";

    var message_etr_fr ="<p>Vous devez présenter lors de l'inscription au club :" +
        "<ol><LI>un certificat médical de moins de trois mois,</LI>" +
        "<li>un document attestant que vous avez une assurance responsabilité civile d'un montant d'au moins 1,5MEuros,</li>" +
        "<li>une carte ILCA.</li></ol></p>";

    var message_etr_en ="<p>When registering at the Club you'll need to present :" +
        "<ol><LI>a medical certificate not older than three months,</LI>" +
        "<li>a document giving evidence that you have a third-party insurance to the amount of at least 1,5MEuros,</li>" +
        "<li>an ILCA card.</li></ol></p>";






    function add_validations_mainform(){

        var frmvalidator  = new Validator("mainform");
        // Choix de l'affichage des messages d'erreur
        frmvalidator.EnableOnPageErrorDisplay();
        // frmvalidator.EnableOnPageErrorDisplaySingleBox();
        //frmvalidator.EnableMsgsTogether();

        frmvalidator.clearAllValidations();
        frmvalidator.formobj.old_onsubmit = null;

        my_validation_required('mainform','Nom',frmvalidator);
        my_validation_required('mainform','Prenom',frmvalidator);
        /*  my_validation_required('mainform','naissance',frmvalidator);*/

        /*
  Why this does not wotk anymore ?
  var year="[1-2][0-9]{3}";
  var mois="0[1-9]|1[0-2]";
  var jour="0[1-9]|[1-2][0-9]|3[0-1]";
  var date= year + "-" + mois + "-" + jour;
  my_validation_regexp('mainform','naissance',frmvalidator,date,'AAAA-MM-JJ','YYYY-MM-DD'); 
         */

        my_validation_radio('mainform','sexe',frmvalidator,'Etes vous homme ou femme ?','Are you Male or Female ?');

        my_validation_required('mainform','mail',frmvalidator);
        my_validation_email('mainform','mail',frmvalidator);

        my_validation_required_condition('mainform','num_club',frmvalidator,
        "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'Licencie')",
        'Vous êtes licencié FFV',
        'You have an FFV licence');


        my_validation_regexp('mainform','num_club',frmvalidator,'[0-9]{5}','NNNNN (5 chiffres)','NNNNN (5 digits)');

        my_validation_radio('mainform','serie',frmvalidator,'Choisssez : Standard, Radial, ou 4.7','Choose : Standard, Radial, or 4.7');

        my_validation_required('mainform','Nvoile',frmvalidator);
        my_validation_regexp('mainform','Nvoile',frmvalidator,'[0-9]{1,6}','NNNNNN (au plus 6 chiffres)','NNNNNN (at most 6 digits)');
        my_validation_regexp('mainform','Cvoile',frmvalidator,'[A-Z]{3,3}','LLL (3 lettres)','LLL (3 letters');

        my_validation_radio('mainform','statut',frmvalidator,'Licencié FFV ?','Do you have an FFV licence?');
        my_validation_radio('mainform','adherant',frmvalidator,'Adhérant AFL ?','Are you member of the AFL?');

        my_validation_required_condition('mainform','lic',frmvalidator,
        "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'Licencie')",
        'Vous êtes licencié FFV',
        'You have an FFV licence');

        my_validation_regexp('mainform','lic',frmvalidator,'[0-9]{7,7}[A-Za-z]',
        'NNNNNNNL (7 chiffres et 1 lettre)',
        'NNNNNNNL (7 digits and 1 letter)');



        my_validation_required_condition('mainform','isaf_no',frmvalidator,
        "VWZ_IsChecked(document.forms['mainform'].elements['statut'],'Etranger')",
        'Vous êtes coureur étranger',
        'You are an international sailor');

        my_validation_regexp('mainform','isaf_no',frmvalidator,'[A-Za-z]{5}[0-9]+',
        'LLLLLN... (5 lettres et au moins 1 chiffre)',
        'LLLLLN... (5 letters and at least 1 digit)');

    }

    set_francais();



    // For IE explorer ... sic :-(
    function changeInputType(oldObject, oType) {
        var newObject = document.createElement('input');
        newObject.type = oType;
        if(oldObject.size) newObject.size = oldObject.size;
        if(oldObject.value) newObject.value = oldObject.value;
        if(oldObject.name) newObject.name = oldObject.name;
        if(oldObject.id) newObject.id = oldObject.id;
        if(oldObject.className) newObject.className = oldObject.className;
        // Something like the line below ????
        //  if(oldObject.add_validation) newObject.add_validation = oldObject.add_validation;
        oldObject.parentNode.replaceChild(newObject,oldObject);
        return newObject;
    }

    function display_html(id){
        document.getElementById(id).innerHTML = get_string(id);
    }

    function display_isaf_no(){
        changeInputType(document.getElementById('isaf_no'),'text');
        display_html('l_isaf_no');
    }

    function display_lic_no(){
        //document.getElementById('lic').type='text';
        changeInputType(document.getElementById('lic'),'text'); // IE sic :-
        display_html('l_lic');
    }

    function hide_isaf_no(){
        //document.getElementById('isaf_no').type='hidden';
        changeInputType(document.getElementById('isaf_no'),'hidden');
        document.getElementById('l_isaf_no').innerHTML ='';
        document.getElementById('mainform_isaf_no_errorloc').innerHTML = '';
    }


    function hide_lic_no(){
        //document.getElementById('lic').type='hidden';
        changeInputType(document.getElementById('lic'),'hidden');
        document.getElementById('l_lic').innerHTML = '';
        document.getElementById('mainform_lic_errorloc').innerHTML = '';
    }

    var cas_FFV = document.getElementById('radio1');
    var cas_nonlic = document.getElementById('radio2');
    var cas_etr = document.getElementById('radio3');

    //var valider = document.getElementById('soumission');

    cas_FFV.onclick = function()
    {
        display_lic_no();
        display_isaf_no();
        document.getElementById('mainform_isaf_no_errorloc').innerHTML = '';

        double_label('message',message_ffv_fr,message_ffv_en,true);
        add_validations_mainform();
    }

    cas_etr.onclick = function()
    {
        hide_lic_no();
        display_isaf_no();

        double_label('message',message_etr_fr,message_etr_en,true);
        add_validations_mainform();
    }

    cas_nonlic.onclick = function()
    {
        hide_lic_no();
        hide_isaf_no();
        double_label('message',message_non_lic_fr,message_non_lic_en,true);
        add_validations_mainform();
    }


    //]]>

<?php
switch($data['statut']) {
    case 'Licencie':
        echo 'cas_FFV.onclick()'."\n";
        break;

    case 'Etranger':
        echo 'cas_etr.onclick()'."\n";
        break;
    case 'Autre':
        echo 'cas_nonlic.onclick()'."\n";
    default:
}

?>
</script>



<?php 
xhtml_post(); ?>
