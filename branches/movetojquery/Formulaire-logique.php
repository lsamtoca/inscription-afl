<?php

// Il faut connaitre le nom de la regate ...
if (!isset($_GET['regate'])) {
    pageErreur("A quelle régate souhaitez vous vous inscrire ?");
}
assert('isset($_GET[\'regate\'])');
$ID_regate = $_GET['regate'];

$premiere_inscription = TRUE;
$confirmation = FALSE;
$comingfromsearch = FALSE;
$gotoinscription = FALSE;

require_once 'php/hash.php';

if (isset($_GET[$hashGetString])) {
    $premiere_inscription = FALSE;
    $comingfromsearch = FALSE;
    $confirmation = TRUE;
    $gotoinscription = false;
    $hash = decodeHashFromHashString($_GET[$hashGetString]);
    $ID_inscrit = decodeIdFromHashString($_GET[$hashGetString]);
}

if (isset($_POST['search_submit'])) {
    $premiere_inscription = TRUE;
    $comingfromsearch = TRUE;
    $confirmation = FALSE;
    $gotoinscription = TRUE;
}

if($comingfromsearch)
    $_POST['no_email']='';
    
// Radio buttons
function set_zero_radio_buttons() {
    global $formData;

    $formData['F'] = '';
    $formData['M'] = '';
    $formData['LA4'] = '';
    $formData['LAR'] = '';
    $formData['LAS'] = '';
    $formData['Licencie'] = '';
    $formData['Etranger'] = '';
    $formData['Autre'] = '';
    $formData['ad_AFL_0'] = '';
    $formData['ad_AFL_1'] = '';
}

function set_default_radio_buttons() {
    global $formData;

    set_zero_radio_buttons();

    $formData['F'] = 'checked';
    $formData['LA4'] = 'checked';
    $formData['Autre'] = 'checked';
    $formData['ad_AFL_0'] = 'checked';
}

function set_fromData_radio_buttons() {
    global $formData;

    set_zero_radio_buttons();

    // Set radio  buttons
    $formData[$formData['sexe']] = 'checked';
    $formData[$formData['serie']] = 'checked';
    $formData[$formData['statut']] = 'checked';
    // This is a real hack
    $formData['ad_AFL_' . $formData['adherant']] = 'checked';
}

require_once 'php/Regate.php';

//
// Récuellir les informations sur la régate
//

$regate = Regate_selectById($ID_regate);
$URLPRE = format_url_preinscrits($ID_regate);

// Pour le preremplissage du formulaire
// Association :
// bd => form
$assoc_INSCRIT_form = array(
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
    'conf' => 'conf',
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

function fill_form_from_scratch() {
    global $assoc_INSCRIT_form, $formData;


    // On preremplit tout en blanc
    foreach ($assoc_INSCRIT_form as $field_bd => $field_form)
        $formData[$field_form] = '';

    // On preremplit avec des données pour tester
    $formData['naissance'] = '01/01/1994';
    $formData['conf'] = '0';
    $formData['ID_inscrit'] = '0';

    // Aucun des radio-buttons est coché
    // Sexe
    set_default_radio_buttons();

    // Pour quelle raison cela ?
    $formData['search_lic'] = '';
    $formData['search_isaf'] = '';
}

function fill_form_from_db() {

    global $assoc_INSCRIT_form, $assoc_COUREUR_form, $formData;
    global $pdo_path, $user, $pwd, $pdo_options;
    global $gotoinscription, $confirmation;
    global $ID_inscrit, $hash;

    assert(isset($_POST['search_submit']) or $confirmation);

    // Determiner comment faire la recherche sur la DB
    $field_value = '';
    if (isset($_POST['search_lic'])) {
        $field_key = 'num_lic';
        $field_value = $_POST['search_lic'];
        // $formData['search_lic'] = $_POST['search_lic'];
  //      echo 'OK';
    }
    if ($field_value == '' and isset($_POST['search_isaf'])) {
        $field_key = 'isaf_no';
        $field_value = $_POST['search_isaf'];
        // $formData['search_isaf'] = $_POST['search_isaf'];
//        echo 'OK';
    }
    if ($confirmation) {
        $field_key = 'ID_inscrit';
        $field_value = $ID_inscrit;
        // We need here to refill the form
        $formData['conf'] = '1';
        $formData['ID_inscrit'] = $ID_inscrit;
 //       echo 'OK';
    }

    try {
        $bd = new PDO($pdo_path, $user, $pwd, $pdo_options);

        if (!$confirmation) {
            $sql = "Select * from `Inscrit` "
                    . "where $field_key=:field_value "
                    . "order by `date preinscription` desc";
            $req = $bd->prepare($sql);
            $req->execute(array(
                'field_value' => $field_value));
        } else {
            $sql = "SELECT * FROM `Inscrit` "
                    . "WHERE $field_key=:field_value AND hash=:hash "
                    . "ORDER BY `date preinscription` DESC";
            $req = $bd->prepare($sql);
            $req->execute(array(
                'hash' => $hash,
                'field_value' => $field_value));
        }

        /*
         * Test
         */
        //  $req->debugDumpParams();
        //      pageErreur("'".$field_value."' --".$sql);
        //   echo $req->rowCount();
        //   exit;
        // Si on a trouvé qqchose on s'inscrit

        if ($req->rowCount() > 0) {

            $row = $req->fetch();

            foreach ($assoc_INSCRIT_form as $field_bd => $field_form) {
                $formData[$field_form] = $_POST[$field_form] = $row[$field_bd];
            }
            $formData['naissance'] = $_POST['naissance'] =
                    dateReformatMysqlToJquery($formData['naissance']);

            if ($gotoinscription) {

                $_POST['ID_inscrit'] = '0';
                $_POST['maSoumission'] = '';
                $_POST['IDR'] = $_GET['regate'];
                // We get now 'lang' from the
                //$_POST['lang'] = 'fr';
                $_POST['conf'] = '0';

                // Ces donnés devraient être soumis à un contrôle
                // Afin de ne pas propager des erreurs
                //               print_r($_POST);
                include 'Inscription.php';
                exit;
            }
        } else  // Sinon, on cherche dans la table COUREUR.DBF
        // Faire la requete sur la table COUREUR.DBF
        if (isset($_POST['search_lic'])) {
            $sql = "Select * from `COUREUR.DBF` where `NO_LIC`= ?";
            $req = $bd->prepare($sql);
            $req->execute(array($_POST['search_lic']));

            //        $req->debugDumpParams();
            function strip_spaces($string) {
                return str_replace(' ', '', $string);
            }

            // Si on a trouvé qqchose on pre-remplis le formulaire
            if ($req->rowCount() > 0) {
                $row = $req->fetch();
                foreach ($assoc_COUREUR_form as $field_bd => $field_form) {
                    $formData[$field_form] = strip_spaces($row[$field_bd]);
                }
            }
        }
    } catch (Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout de suite
        die('Erreur : ' . $e->getMessage());
    }

    if (isset($_POST['search_lic'])) {
        $formData['naissance'] = dateReformatDbfToJquery($formData['naissance']);
    }

    if ($confirmation) {
        // Si nous arrivons pour confirmation
        $formData['conf'] = '1';
        $formData['ID_inscrit'] = $ID_inscrit;
    }

    // Set radio  buttons
    set_fromData_radio_buttons();
}

fill_form_from_scratch();
if ($comingfromsearch) {
    fill_form_from_db();
} elseif ($confirmation) {
    fill_form_from_db();
}
else
    set_default_radio_buttons();
?>