<?php


if (!isset($_GET['regate'])) {
    pageErreur("A quelle régate souhaitez vous vous inscrire ?");
}


//
// Récuellir les informations sur la régate
//
try {
    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
    $sql = 'SELECT `ID_regate`,`titre`,`lieu`,`description`, ' .
            'DATE_FORMAT(`date_debut`, \'%d-%m-%Y\') as `date_debut`, ' .
            'DATE_FORMAT(`date_fin`, \'%d-%m-%Y\') as `date_fin`, ' .
            '`date_limite_preinscriptions` ' .
            'FROM `Regate` ' .
            'WHERE ID_regate = ?';
    $req = $bdd->prepare($sql);
    $req->execute(array($_GET['regate']));
    if ($req->RowCount() == 0)
        pageErreur('Cette régate n\'existe pas :-(');
    // Tout ce qu'on veut savoir sur la regate
    $regate = $req->fetch();
} catch (Exception $e) {
    // En cas d'erreur, on affiche un message et 
    // on arrête tout de suite
    if ($development) {
        $_GET['regate'] = 0;
        $regate['date_debut'] = "13-11-1967";
        $regate['date_fin'] = "14-11-2067";
        $regate['date_limite_preinscriptions'] = "13-11-2067";
        $regate['titre'] = "Regate test";
        $regate['lieu'] = "Marseille";
        $regate['description'] = "Pour tester";
    }
    else
        die('Erreur : ' . $e->getMessage());
}

// On etablit quand est le dernier moment 
// - pour pouvoir se preinscrire
$URLPRE = format_url_preinscrits($_GET['regate']);
if ($regate['date_limite_preinscriptions'] != '') {
    date_default_timezone_set('Europe/Paris');
    $now = new DateTime;
    $limite = new DateTime($regate['date_limite_preinscriptions']);
    $limite->setTime(23, 59);
}

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
    global $assoc_INSCRIT_form, $data;


    // On preremplit tout en blanc
    foreach ($assoc_INSCRIT_form as $field_bd => $field_form)
        $data[$field_form] = '';

    // On preremplit avec des données pour tester
    $data['naissance'] = '01/01/1994';
    $data['conf'] = '0';
    
    // Aucun des radio-buttons est coché
    // Sexe
    $data['M'] = '';
    $data['F'] = '';
    // Serie
    $data['LAS'] = '';
    $data['LAR'] = '';
    $data['LA4'] = '';
    // Statut
    $data['Licencie'] = '';
    $data['Etranger'] = '';
    $data['Autre'] = '';
    // Adhérant
    $data['ad_AFL_0'] = '';
    $data['ad_AFL_1'] = '';

    $data['ID_inscrit']='0';

    
    // Pour quelle raison cela ?
    $data['search_lic'] = '';
    $data['search_isaf'] = '';

}

function fill_form_from_db() {

    global $assoc_INSCRIT_form, $data;
    global $pdo_path, $user, $pwd;

    assert(isset($_POST['search_submit']));

    try {
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);

        // Faire d'abord la requête sur la table Inscrit
        // Set the field to search for
        $field_key = 'num_lic';
        if (isset($_POST['search_isaf']))
            $field_key = 'isaf_no';

        // Set its value
        if (isset($_POST['search_lic'])) {
            $field_value = $_POST['search_lic'];
            $data['search_lic'] = $_POST['search_lic'];
        }
        if (isset($_POST['search_isaf'])) {
            $field_value = $_POST['search_isaf'];
            $data['search_isaf'] = $_POST['search_isaf'];
        }

        $sql = "Select * from `Inscrit` where $field_key=? order by `date preinscription` desc";
        $req = $bdd->prepare($sql);
        $req->execute(array($field_value));
        //        $req->debugDumpParams();
        // Si on a trouvé qqchose on s'inscrit
        if ($req->rowCount() > 0) {
            $row = $req->fetch();
            foreach ($assoc_INSCRIT_form as $field_bd => $field_form) {
                $_POST[$field_form] = $row[$field_bd];
                $data[$field_form] = $row[$field_bd];
            }
            list($year, $month, $day) = sscanf($data['naissance'], '%04d-%02d-%02d');

            /*
              if (GOTOINSCRIPTIONDIRECT) {

              $_POST['maSoumission'] = '';
              $_POST['IDR'] = $_GET['regate'];
              $_POST['lang'] = 'fr';
              $_POST['no_email'] = true;

              // Ces donnés devraient être soumis à un contrôle
              // Afin de ne pas propager des erreurs
              include 'Inscription.php';
              exit;
              }
             */
        } else  // Sinon, on cherche dans la table COUREUR.DBF
        // Faire la requete sur la table COUREUR.DBF
        if (isset($_POST['search_lic'])) {
            $sql = "Select * from `COUREUR.DBF` where `NO_LIC`= ?";
            $req = $bdd->prepare($sql);
            $req->execute(array($_POST['search_lic']));

            //        $req->debugDumpParams();
            function strip_spaces($string) {
                return str_replace(' ', '', $string);
            }

            // Si on a trouvé qqchose on pre-remplis le formulaire
            if ($req->rowCount() > 0) {
                $row = $req->fetch();
                foreach ($assoc_COUREUR_form as $field_bd => $field_form) {
                    $data[$field_form] = strip_spaces($row[$field_bd]);
                    list($year, $month, $day) = sscanf($data['naissance'], '%04d%02d%02d');
                }
            }
        }
    } catch (Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout de suite
        die('Erreur : ' . $e->getMessage());
    }


    $data['naissance'] = "$day/$month/$year";

    if(isset($_GET['CONF'])){
            // Si nous arrivons pour confirmation
        $data['conf'] = '1';
        $data['ID_inscrit'] = $_GET['CONF'];
    }

    // Set radio  buttons
    $data[$data['sexe']] = 'checked';
    $data[$data['serie']] = 'checked';
    $data[$data['statut']] = 'checked';
    // This is a hack
    $data['ad_AFL_'.$data['adherant']] = 'checked';
    
}

fill_form_from_scratch();
//
// Si on demande de pre-remplir le formulaire
//

if (isset($_POST['search_submit']))
//if (isset($_GET['ID']))
    fill_form_from_db();
?>