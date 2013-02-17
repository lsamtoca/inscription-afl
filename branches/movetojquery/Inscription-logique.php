<?php
//session_start();
require_once 'mailer.php';

function protect($protect,$string){
    return $protect.$string.$protect;    
}

function query_insert($fields) {

    $callback = function ($value) {
                return "`$value`";
            };
    $fields_protected = array_map($callback, $fields);
    $columns = implode(',', $fields_protected);

    $callback = function ($value) {
                return ":$value";
            };
    $fields_content = array_map($callback, $fields);
    $values = implode(',', $fields_content);
    $query = "INSERT INTO Inscrit ($columns) VALUES ($values)";
    return $query;
}

function query_update($fields) { 
    $callback=function ($value){
        return "$value:=$value";
    };
    $fields=array_map($callback, $fields);
    $pairs=implode(',',$fields); 
    $query="UPDATE Inscrit SET $pairs WHERE ID_inscrit=:ID_inscrit";
    return $query;
}


// Compose the mail...
function compose_mail($ID_inscrit,$titre_regate,$courriel_cv) {

    global $message_email_fr, $message_email_fr;
    global $development;
    
    // Format the body of answer
    $url_confirmation =format_url_regate($ID_inscrit);
    $message_email_fr = "Bonjour " . $_POST['Prenom'] . ",\n\n"
            . "veuillez confirmer votre inscription à la régate '$titre_regate' en cliquant le lien suivant:\n"
            . $url_confirmation . "\n\n"
            . "Bon vent,\n\t l'AFL (pour le club organisateur)";

    $message_email_en = "Hello " . $_POST['Prenom'] . ",\n\n"
            . "please confirm your registration to the race '$titre_regate' by clicking on the following link:\n"
            . $url_confirmation . "\n\n"
            . "Bon vent,\n\t the AFL (for the organizing club)";

    // Format fields of answer
    $ME = "inscriptions-afl@regateslaser.info";
    $subject="Inscription à la régate, confirmation";
    if(filter_var($courriel_cv, FILTER_VALIDATE_EMAIL)) {
        $sender=$courriel_cv;
        $cc=$courriel_cv;
    }
    else {
        $sender=$ME;
        $cc='';
    }
    $to=$_POST['mail'];
    $bcc='';


    if ($_POST['lang'] == 'en') {
        $message = $message_email_en;
    } else {
        $message = $message_email_fr;
    }

    if($development){
        $sender=$to='luigi.santocanale@lif.univ-mrs.fr';
        $cc=$sender;
    }

    return send_mail_text($sender,$to,$subject,$message,$cc,$bcc);
}

// Add to the bd the informations got from the form
// These have been validated in the client
// pdo is going to clean them up
// TODO : add validations from the server side
// as one could send post data directly, 
// not using the javascript from the form
function do_insert_or_update() {
    
    global $pdo_path, $user, $pwd;
    
    try {
        // On se connecte à MySQL
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);
        // That is for my Mac !!!
        $bdd->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

        // formatter la date pour mysql
        list($day, $month, $year) = sscanf($_POST['naissance'], '%02d/%02d/%04d');
        $_POST['naissance'] = sprintf('%d-%d-%d 0:00:00', $year, $month, $day);


        // Ajouter le coureur parmi les inscrits
//	$date=$_POST['anne_naissance']."-".$_POST['mois_naissance']."-".$_POST['jour_naissance'];

        $fields = explode(',', 
                "nom,prenom,naissance,num_lic,isaf_no,num_club,nom_club,"
                . "prefix_voile,num_voile,serie,adherant,sexe,"
                . "conf,mail,statut,ID_regate,date preinscription"
                );

        if ($_POST['conf'] == '0')
            $sql = query_insert($fields);
        else
            $sql = query_update($fields);
      //pageErreur($sql);
       // echo $sql;
        
        
        date_default_timezone_set('Europe/Paris');
        
        $assoc=array(
            'nom' => nom_normaliser($_POST['Nom']),
            'prenom' => nom_normaliser($_POST['Prenom']),
//		'naissance' => $date,
            'naissance' => $_POST['naissance'],
            'num_lic' => strtoupper($_POST['lic']),
            'isaf_no' => strtoupper($_POST['isaf_no']),
            'num_club' => $_POST['num_club'],
            'nom_club' => $_POST['nom_club'],
            'prefix_voile' => $_POST['Cvoile'],
            'num_voile' => $_POST['Nvoile'],
            'serie' => $_POST['serie'],
            'adherant' => $_POST['adherant'],
            'sexe' => $_POST['sexe'],
            'conf' => $_POST['conf'],
            'mail' => $_POST['mail'],
            'statut' => $_POST['statut'],
            'ID_regate' => $_POST['IDR'],
            'ID_inscrit' => $_POST['ID_inscrit'],
            'date preinscription' => date('Y-m-d G:i:s')
        );

        $callback = function($value){ return protect('\'',$value);};
        $assoc=array_map($callback,$assoc);
        // print_r($assoc);
        
            // Debug the  query
      /*
        $callback = function($value) {
            return ':'.$value;
        };
        $assoc2=array_map($callback, array_keys($assoc));
        $exQuery=str_replace($assoc2, array_values($assoc), $sql);
        echo $exQuery;
        echo '<br>';
        //
        */
        
        $req=$bdd->prepare($sql);
    //    $req=$bdd->prepare($exQuery);
        
        $req->execute($assoc);
    } catch (Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }
}

function do_mail() {
    global $pdo_path, $user, $pwd;

    try {
        // On se connecte à MySQL
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        $bdd = new PDO($pdo_path, $user, $pwd, $pdo_options);


        // Prepaper la réponse :
        // Nous avons bésoin de connaître l'ID du coureur
        // Et possiblement des données sur la régate :
        // Titre de la regate et courriel_du_cv
        $sql = 'SELECT ID_inscrit FROM Inscrit ' .
                'WHERE nom=:nom AND prenom=:prenom AND ID_regate=:idr ' .
                'ORDER BY ID_inscrit DESC';
        $req = $bdd->prepare($sql);
        $req->execute(array(
            'nom' => $_POST['Nom'],
            'prenom' => $_POST['Prenom'],
            'idr' => $_POST['IDR'],
        ));
        assert('$req->rowCount() > 0');
        $row = $req->fetch();
        $ID_inscrit = $row['ID_inscrit'];

        // QUERY la regate
        $sql = 'SELECT * FROM Regate WHERE ID_regate=:idr';
        $req = $bdd->prepare($sql);
        $req->execute(array(
            'idr' => $_POST['IDR'],
        ));
    } catch (Exception $e) {
        // En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : ' . $e->getMessage());
    }


    assert('$req->rowCount() == 1');
    $row = $req->fetch();

    $titre_regate = $row['titre'];
    $courriel_cv = $row['courriel'];

    compose_mail($ID_inscrit, $titre_regate, $courriel_cv);

}

// On vient par la seulement si on a complete le formulaire !!!
assert('isset($_POST[\'maSoumission\'])');
if (!isset($_POST['maSoumission'])){
    pageErreur('Hacker !!!');
}

// Mettre à jour la BD
do_insert_or_update();
if ($_POST['conf'] == '0') { 
    // Si c'est la premiere fois qu'on met à jour
    do_mail();
} else {
    header("Location:".format_confirmation_regate($_POST['ID_inscrit']));
}

?>