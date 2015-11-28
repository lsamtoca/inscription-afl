<?php

if (isset($post['lang']) && $post['lang'] == 'en') {
    $lang = 'en';
} else {
    $lang = 'fr';
}

switch ($lang) {

    case 'en':
        // Insertion
        $titleModeInsert = 'You are (almost) pre-registered';
        // Answer to insertion
        $dear = 'Dear';
        $ask = 'we ask you to confirm your pre-registration.';
        $sentyou = 'We have sent you a message';
        $lastemail = 'at your email address '
                . '(the last address you used with this system).';
        $youremail = 'at the email address';
        $modifyit = 'The message contains a link '
                . 'by which you will be able to '
                . 'confirm and/or modify your pre-registration.';
        $checkitout = 'If you do not receive this message within the next few minutes, '
                . 'please verify the Spam folder of your email account '
                . 'before trying to preregister once more.';

        //  Answer to confirmation
        $hello = 'Hello';
        $regok = 'your pre-registration is now completed.';
        $verifier = 'You can verify it on the';
        $liste = 'list of preregistered sailors';

        // Email asking confirmation
        function message_email($prenom, $titre_regate, $url_confirmation, $url_paiement = '') {
            $message_paiement = '';
            if ($url_paiement != '') {
                //Should we verify once more that this is a vaild address ?
                $message_paiement = 
                        "\n\n"
                        . 'You can pay the inscriptions fees by following this link: '
                        . "\n"
                        . $url_paiement . "\n"
                        . 'Your registration will be considered complete once the payment is received by the organizing authority. '
                        . "\n"
                        . 'If you do not pay the fees before the deadline for pre-registration '
                        . 'you might be asked to pay additional fees.'
                ;
            }

            return "Hello  $prenom,\n\n"
                    . "please confirm your registration to the race '$titre_regate' by clicking on the following link:\n"
                    . $url_confirmation
                    . "\n"
                    . "You'll be able to modify your pre-registration "
                    . "by using the link above, until the deadline for pre-registrations."
                    . "\n\n"
                    . "If you wish to cancel your pre-registration, "
                    . "please contact the organizing club by replying to this email."
                    . $message_paiement
                    . "\n\n"
                    . "Bon vent,\n\t the AFL (for the organizing club)";
        }

        // Erreurs
        $messageErrAlreadyThere = 'A sailor, with the same licence number of same ISAF number, but a different email, '
                . 'is already preregistered.'
                . "Please contact the organizing club to join this race.";

        $messagePreregClosed = 'This race is not  anymore open to preregistration. '
                . 'Therefore, you cannot modify your preregistration.';
        $messageInvalidEmail = 'Invalid email address';

        break;

    case 'fr':
    default:
        // Insertion
        $titleModeInsert = 'Vous êtes (presque) préinscrit';
        $dear = 'Cher(e)';
        $ask = 'nous vous demandons de confirmer votre pré-inscription.';
        $sentyou = 'Nous avons envoyé un courriel';
        $lastemail = 'à votre adresse email (le dernier utilisé sur ce système).';
        $youremail = 'à l\'adresse';
        $modifyit = 'Le courriel contient un lien '
                . 'qui vous permettra de confirmer '
                . 'et/ou modifier votre pré-inscription.';
        $checkitout = 'Si vous ne recevez pas ce courriel dans des brefs délais, '
                . 'vérifiez le répertoire Spam de votre compte email '
                . 'avant essayer de vous pré-inscrire une nouvelle fois.';


        // Confirmation
        $titleModeConfirm = 'Confirmation';
        $hello = 'Bonjour';
        $regok = 'votre pré-inscription est maintenant confirmée';
        $verifier = 'Vous pouvez vérifier votre inscription sur la';
        $liste = 'liste des pré-inscrits';

        // Courriel to confirm
        function message_email($prenom, $titre_regate, $url_confirmation, $url_paiement = '') {

            $message_paiement = '';
            if ($url_paiement != '') {
                //Should we verify once more that this is a vaild address ?
                $message_paiement = 
                        "\n\n"
                        .'Vous pouvez payer les droits d\'inscriptions à l\'adresse suivant : '
                        . "\n"
                        . $url_paiement . "\n"
                        . 'Votre inscription sera complète de que votre paiement est perçu par le club. '
                        . "\n"
                        . 'Si vous ne payez pas ces droits avant la date limite des pré-inscriptions, '
                        . 'vous pouvez être assujetti à des frais additionnelles.'
                ;
            }

            return "Bonjour " . $prenom . ",\n\n"
                    . "veuillez confirmer votre pré-inscription à la régate '$titre_regate' en cliquant le lien suivant :"
                    . "\n"
                    . $url_confirmation
                    . "\n"
                    . "Vous pouvez modifier les données concernant votre pré-inscription "
                    . "à l'aide du lien ci-dessus, jusqu'à la date limite des pré-inscriptions."
                    . "\n\n"
                    . "Si vous souhaitez annuler votre pré-inscription, "
                    . "veuillez contacter le club organisateur en répondant à ce courriel."
                    . $message_paiement
                    . "\n\n"
                    . "Bon vent,\n\t l'AFL (pour le club organisateur)";
        }

        // Err messages
        $messageErrAlreadyThere = "Un coureur est déjà pré-inscrit à cette régate,"
                . " avec le même numéro de licence ou le même numéro ISAF, mais avec un
 courriel différent."
                . "Veuillez demander directement au club organisateur de vous pré-inscrire.";

        $messageErrPreregClosed = 'Cette régate n\'est plus ouverte à la pré-inscription ; '
                . 'vous ne pouvez plus modifier votre pré-inscription.';

        $messageInvalidEmail = 'Le courriel n\'est pas valide';
}




