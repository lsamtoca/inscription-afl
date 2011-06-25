<?php
require "partage.php";
html_pre("Inscription aux regates AFL : page de test");
?>

<h2>Rôle : administrateur du programme (Luigi, David ?)</h2>

<ul>
   <li> <A href="admin/init.php">initialiser la base des données</A> </li>
   <li> <A href="admin/clear.php">nettoyage de la base des données</A> </li>
 </ul>
 

<h2>Rôle : administrateur AFL (Martine, Pierre ?)</h2>

<ul>
   <li> <A href="Admin.php">gérer  les évenements et les clubs</A> </li>
</ul>

<h2>Rôle : club (organisateur d'une régate)</h2>

<ul>
   <li> <A href="Regate.php">gérer sa régate</A> </li>
</ul>

<h2>Rôle : coureur</h2>

<ul>
   <li> <A href="Liste_regates.php">liste des régates</A>  ouvertes à l'inscription. <br>
      Cette liste n'est pas nécessairement accessible sur le site AFL, mais ici sert pour tester.
    </li>
<!--   <li> <A href="Formulaire.php">se pré-inscrire (à une régate?)</A> </li>-->
   <li> <A href="Confirmation.php">se confirmer (à une régate?)</A> </li>
   <li> <A href="Annullation.php">annuler sa participation (à une régate?)</A> </li>
 </ul>

<?php
html_post();
?>