<?php

// FORMATTER DES URLS
function format_url_regate($id_regate, $gets = "") {
    global $path_to_site_inscription;
    if ($gets != "")
        $gets = "&gets";
    return sprintf("http://%sFormulaire?regate=%d%s", $path_to_site_inscription, $id_regate, $gets);
}

function format_url_login_club() {
    global $path_to_site_inscription;
    return sprintf("http://%sLogin", $path_to_site_inscription);
}

function format_url_chpwd() {
    global $path_to_site_inscription;
    return sprintf("http://%schangePwd", $path_to_site_inscription);
}

function format_url_login() {
    global $path_to_site_inscription;
    return sprintf("http://%sLogin", $path_to_site_inscription);
}

function format_url_club() {
    global $path_to_site_inscription;
    return sprintf("http://%sRegate", $path_to_site_inscription);
}

function format_url_forms($id_regate, $gets = "") {
    $url = format_url_regate($id_regate, $gets);
    return "$url#forms";
}

function format_url_preinscrits($id_regate, $gets = "") {
    $url = format_url_regate($id_regate, $gets);
    return "$url#preinscrits";
}

function format_url_aut_parentale() {
    global $path_to_site_inscription;
    return sprintf("http://%sdocs/AUTORISATION-PARENTALE.pdf", $path_to_site_inscription);
}

//function format_url_preinscrits($id_regate) {
//    global $path_to_site_inscription;
//    return sprintf("http://%sPreinscrits.php?regate=%d", $path_to_site_inscription, $id_regate);
//}

function format_confirmation_regate($id_coureur) {
    global $path_to_site_inscription;
    return sprintf("http://%sConfirmation.php?ID=%d", $path_to_site_inscription, $id_coureur);
}
