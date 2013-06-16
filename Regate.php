<?php
	session_start();
	if(!isset($_SESSION["ID_regate"]))
	{
	   
		header("Location: LoginClub.php");
		//$_SESSION["ID_regate"]=2;
	}

   require_once "partage.php";
   require_once('php/mailer.php');

include 'Regate-logique.php';
include 'Regate-html.php';

