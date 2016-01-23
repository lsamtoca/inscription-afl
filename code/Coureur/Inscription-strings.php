<?php

$base = dirname(dirname(dirname(__FILE__)));
require_once "$base/" . 'php/ParseProperties.php';
//require_once 'php/ParseProperties.php';

// SET A DEFAULT LANGUAGE
if (isset($post['lang'])) {
    $lang = $post['lang'];
} else {
    $lang = 'fr';
}

// IF A LANGUAGE IS NOT YET IMPLEMENTED USE ENGLISH
if (!in_array($lang, array('fr', 'en', 'it','es'))) {
    $lang = 'en';
}

/*
  //$lang = 'it';
  // provides 13 words
  $providedWords = array(
  'titleModeInsert',
  'lastemail',
  'youremail',
  'messageAckInsertion',
  'messageUseAck',
  'titleModeConfirm',
  'messageAckConfirmation',
  'message_paiement',
  'message_mineur',
  'message_email',
  'messageErrAlreadyThere',
  'messageErrPreregClosed',
  'messageInvalidEmail'
  );
 * 
 * 
 */

// We need it as we might be within a function, 
// so the scope is not necessartily global
global $dict;

function getLang($lang, $path = 'bundle/') {
    return parseProperties('Inscription_' . $lang, $path);
}

function messageAckInsertion($prenom, $email = '') {
    global $dict;
    $dict['prenom'] = $prenom;
    if ($email == '') {
        $dict['toWhichEmail'] = $dict['lastemail'];
    } else {
        $dict['toWhichEmail'] = fixVariablesInProperties(
                $dict['youremail'],
                array('email'=> $email));
    }
    
    $messageAckInsertion = fixVariablesInProperties(
            $dict['messageAckInsertion'], $dict);
    return $messageAckInsertion ." ". $dict['messageUseAck'];
}

function messageAckConfirmation($prenom, $url_listePreiscrits) {

    global $dict;
    $dict['prenom'] = $prenom;
    $dict['url_listePreiscrits'] = $url_listePreiscrits;
    $messageAckConfirmation = fixVariablesInProperties(
            $dict['messageAckConfirmation'], $dict);
    return $messageAckConfirmation;
}

// Email asking confirmation
function message_email($prenom, $titre_regate, $url_confirmation, 
        $url_paiement = '', $url_aut_parentale,$est_mineur = false) {

    global $dict;
    $dict['url_paiement'] = $url_paiement;
    $dict['url_confirmation'] = $url_confirmation;
    $dict['url_aut_parentale'] = $url_aut_parentale;
    $dict['prenom'] = $prenom;
    $dict['titre_regate'] = $titre_regate;

    if ($url_paiement == '') {
        $dict['message_paiement'] = '';
    } else {
        $dict['message_paiement'] = fixVariablesInProperties(
                        $dict['message_paiement'], $dict) ;
    }

    if (!$est_mineur) {
        $dict['message_mineur'] = '';
    } else {
        $dict['message_mineur'] = fixVariablesInProperties(
                        $dict['message_mineur'], $dict);
    }

    $message_email = fixVariablesInProperties($dict['message_email'], $dict);
    // pageAnswer($message_email);

    return $message_email;
}

// For testing from console


function testIt() {
    global $dict, $base, $argv;
    echo $argv[1];
    $dict = getLang($argv[1], "$base/bundle/");

    echo "\n\n*****Ack Insertion From Search:\n";
    echo messageAckInsertion('Luigi');
 
    echo "\n\n*****Ack Insertion From Scratch:\n";
    echo messageAckInsertion('Luigi','l.s.@lif.com');
 
    echo "\n\n*****Ack Confirmation:\n";
    echo messageAckConfirmation('Luigi', 'httP:url_liste');
    echo "\n\n*****Email:\n";
    echo message_email('Luigi', 'Regata del cavolo', 
            'http:confirmation_ici', 
            'http:payement_ici',
            'http:aut_parentale',
            TRUE);
    echo "\n\n\n";
}

$thisTesting = False;
if ($thisTesting) {
    testIt();
    exit(0);
}
$dict = getLang($lang);


