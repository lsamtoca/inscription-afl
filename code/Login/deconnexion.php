<?php
	session_destroy();
        $message='Vous etes maintenant deconnecté';
        $goback='index';
        unset($_SESSION);
        pageAnswer($message, $goback);
//	header('Location:index');
