<?php
require "partage.php";
html_pre("Inscription aux regates AFL : page de test");
?>

<h2>Role : administrateur système</h2>

<ul>
   <li> <A href="admin/init.php">initialiser la base des données</A> </li>
   <li> <A href="admin/clear.php">nettoyage de la base des données</A> </li>
 </ul>
 

<h2>Rôle : administrateur des clubs (Martine ?)</h2>

<ul>
   <li> <A href="Admin.php">gérer les clubs (et/où les évenements ?)</A> </li>
</ul>

<h2>Rôle : club </h2>

<ul>
   <li> <A href="Club.php">gérer les régates</A> </li>
   <li> <A href="Regate.php">gérer une régate (II ?)</A> </li>
</ul>

<h2>Tests regatiers</h2>

<ul>
   <li> <A href="Formulaire.php">se pré-inscrire (à une régate?)</A> </li>
   <li> <A href="Confirmation.php">se confirmer (à une régate?)</A> </li>
   <li> <A href="Annullation.php">annuler sa participation (à une régate?)</A> </li>
 </ul>

<?php
html_post();
?>