<?php

// Il faut connaitre le nom de la regate ...
if (!isset($_GET['regate'])) {
    pageErreur("A quelle régate souhaitez vous vous inscrire ?");
}
assert('isset($_GET[\'regate\'])');
$ID_regate = $_GET['regate'];

// $mainform_elements is needed
// since we need to prefill the form
// if we are in mode confirmation
require_once 'mainform-elements.php';

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

if ($comingfromsearch)
    $_POST['no_email'] = '';


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
    'taille_polo' => 'taillepolo'
        //    'conf' => 'conf',
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
    global $formData, $mainformInputs, $ID_regate;
    global $confirmation, $ID_inscrit;

    $mainformInputs['IDR']['default'] = $ID_regate;
    if ($confirmation) {
        // Si nous arrivons pour confirmation
        $mainformInputs['conf']['default'] = '1';
        $mainformInputs['ID_inscrit']['default'] = $ID_inscrit;
    }

    // Pour quelle raison cela ?
    $formData['search_lic'] = '';
    $formData['search_isaf'] = '';
}

function fill_form_from_db() {

    global $assoc_INSCRIT_form, $assoc_COUREUR_form, $formData, $mainformInputs;
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
        //       echo 'OK';
    }


    if (!$confirmation) {
        // Si on est pas en train de confirmer
        // Chercher par no licence on no isaf
        $sql = "Select * from `Inscrit` "
                . "where $field_key=:field_value "
                . "order by `date preinscription` desc";
        $assoc = array(
            'field_value' => $field_value);
        $req = executePreparedQuery($sql, $assoc);
    } else {
        // Sinon, on est en train de confirmer
        // Chercher par aussi via le hash
        // le hash est ajouté pour garantir
        // un certain anonymat de l'inscription
        $sql = "SELECT * FROM `Inscrit` "
                . "WHERE $field_key=:field_value AND hash=:hash "
                . "ORDER BY `date preinscription` DESC";
        $assoc = array(
            'hash' => $hash,
            'field_value' => $field_value);
        $req = executePreparedQuery($sql, $assoc);
    }

    if ($req->rowCount() > 0) {
        // Si on a trouvé qqchose on pre-complete le formulaire
        // via le champ 'defaut' des elements

        $row = $req->fetch();

        foreach ($assoc_INSCRIT_form as $field_bd => $field_form) {
            $mainformInputs[$field_form]['default'] = $_POST[$field_form] = $row[$field_bd];
        }
        $mainformInputs['naissance']['default'] = $_POST['naissance'] = dateReformatMysqlToJquery($mainformInputs['naissance']['default']);

        if ($gotoinscription) {
            $_POST['ID_inscrit'] = '0';
            $_POST['maSoumission'] = '';
            $_POST['IDR'] = $_GET['regate'];
            // We get now 'lang' from the ?
            //$_POST['lang'] = 'fr';
            $_POST['conf'] = '0';

            // Ces donnés devraient être soumis à un contrôle
            // Afin de ne pas propager des erreurs
            //               print_r($_POST);
            return;
        }
    } else
    // On a rien trouvé  dans la table INSCRIT
    // On cherche alors dans la table COUREUR.DBF
    // Faire la requete sur la table COUREUR.DBF
    if (isset($_POST['search_lic'])) {
        $sql = "Select * from `COUREUR.DBF` where `NO_LIC`= ?";
        $assoc = array($_POST['search_lic']);
        $req = executePreparedQuery($sql, $assoc);

        //        $req->debugDumpParams();
        function strip_spaces($string) {
            return str_replace(' ', '', $string);
        }

        // Si on a trouvé qqchose on pre-remplis le formulaire
        if ($req->rowCount() > 0) {
            $row = $req->fetch();
            foreach ($assoc_COUREUR_form as $field_bd => $field_form) {
                $mainformInputs[$field_form]['default'] = strip_spaces($row[$field_bd]);
            }
            // On doit aussi re-ajouster la date de naissance
            // qui est stockée dans COUREUR.DBF sous le format AAAAMMDD
            $mainformInputs['naissance']['default'] = dateReformatDbfToJquery($mainformInputs['naissance']['default']);
        } else
            pageErreur('On vous a pas trouvé');
    }
}

fill_form_from_scratch();
if ($comingfromsearch or $confirmation) {
    fill_form_from_db();
    if ($gotoinscription) {
        // We should not include 
        // from inside a function 
        // as this raises scope issues
        include 'Inscription.php';
        exit(0);
    }
}
if ($regate['polo'] == 1) {
    $mainformInputs['taillepolo']['rendering'] = 'radio';
}
