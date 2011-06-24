<?php
require "../partage.php";

html_pre("Création de la base de données");

execute_sql("createdb.sql");
html_post();
?>

