<?php
require "../partage.php";

html_pre("Rimotion de la base de données");

execute_sql("dropdb.sql");
html_post();
?>
