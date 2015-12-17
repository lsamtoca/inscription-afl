<?php
/* Fonctions pour formatter les dates */

function dateReformatJqueryToMysql($string) {
    list($day, $month, $year) = sscanf($string, '%02d/%02d/%04d');
    return sprintf('%d-%d-%d', $year, $month, $day);
}

function dateReformatMysqlToJquery($string) {
    list($year, $month, $day) = sscanf($string, '%04d-%02d-%02d');
    return sprintf('%02d/%02d/%04d', $day, $month, $year);
}

function dateReformatDbfToJquery($string) {
    list($year, $month, $day) = sscanf($string, '%04d%02d%02d');
    return sprintf('%02d/%02d/%04d', $day, $month, $year);
}
