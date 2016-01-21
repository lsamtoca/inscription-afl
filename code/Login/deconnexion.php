<?php
	session_destroy();
        $message='Vous êtes maintenant déconnecté';
        $goback='index';
        unset($_SESSION);
        pageAnswer($message, $goback);
//	header('Location:index');
