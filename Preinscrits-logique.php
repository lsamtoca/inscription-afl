<?php

require_once "partage.php";
require_once 'php/Regate.php';

function cell($text) {
    return "<td>$text</td>\n";
}

function rcell($text) {
    return "<td align=\"right\">$text</td>\n";
}

function construct_html_serie($id_regate, $titre_serie, $serie) {

    global $pdo_path, $user, $pwd, $pdo_options;

    $sql = 'SELECT `nom`,`prenom`,`num_voile`,`prefix_voile` FROM `Inscrit` ';
    $condition = ' WHERE  `ID_regate` = :id_regate ';
    $condition.=' AND `conf`=\'1\' ';
    $condition.=' AND `serie`=:serie ';
    $orderby = ' ORDER BY `num_voile` ';
    $sql.= $condition . $orderby;

    $assoc = array(
        ':id_regate' => $id_regate,
        ':serie' => $serie
    );
    $req = executePreparedQuery($sql, $assoc);

    $ret = "";
    if ($req->rowCount() > 0) {
  
        //$ret.="<div id=\"$serie\" style=\"margin-left:auto;margin-right:auto;width:200;margin-top:10mm;margin-bottom:10mm\">" . "\n";

//        $ret.='<table border="1" align="center">' . "\n";
        $ret.='<tr class=\'even\'><th colspan="5">' . $titre_serie . '</th></tr>';
        $ret.='<tr class=\'even\'>';
        $ret.=cell('');
        $ret.=cell('Pays');
        $ret.=cell('No. Voile');
        $ret.=cell('Nom');
        $ret.=cell('Prénom');
        $ret.='</tr>';

//        $row=$req->fe
        $i = 1;
        $class = 'odd';
        while ($row = $req->fetch()) {
            $ret.="<tr class=\"$class\">";
            $ret.=rcell($i++);
            $ret.=rcell(strtoupper($row['prefix_voile']));
            $ret.=rcell($row['num_voile']);
            $ret.=cell(nom_normaliser($row['nom']));
            $ret.=cell(nom_normaliser($row['prenom']));
            $ret.='</tr>' . "\n";
            if ($class == 'odd') {
                $class = 'even';
            } else {
                $class = 'odd';
            }
        }
//        $ret.="</table>\n";
//        $ret.="</div><!-- $serie -->\n\n";
    }
    return $ret;
}

$regate = Regate_selectById($_GET['regate']);
$titre_regate = $regate['titre'];
$id_regate = $regate['ID_regate'];

$LA4 = construct_html_serie($id_regate, 'Laser 4.7', 'LA4');
$LAR = construct_html_serie($id_regate, 'Laser Radial', 'LAR');
$LAS = construct_html_serie($id_regate, 'Laser Standard', 'LAS');

