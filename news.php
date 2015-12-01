<div  class="contenu">
    Version 3.0, décembre 2015.

    <h4>Nouveautés</h4>
    <h5>Page gestion de la régate</h5>
    <ul>
        <li>Le code pour la création des fiches d'accueil des participants a été amélioré.</li>
        <li>On a ajouté de la documentation en ligne sous la forme de "balloons" d'aide. </li>
        <li>On a ajouté un formulaire pour pouvoir contacter les développeurs.</li>
        <li>On peut ajouter maintenant un lien vers une page de paiement en ligne des frais d'inscriptions.
            Lors de l'envoie du courriel de confirmation, 
            on demande aussi de procéder au paiement en ligne.
        </li>
        <li>Les mots de passe ne sont plus stockés en clair dans la base de données 
            (de façon que l'on ne soit pas tenté de hacker le site 
            pour changer les liens de paiement en ligne).
        </li>
    </ul>

    <h5>Page pré-inscription à la régate</h5>
    <ul>
        <li>Un coureur ne peut plus se pré-inscrire à la même régate plusieurs fois.
            Attention, cela pose le problème suivant : 
            si on se pré-inscrit trop vite (en utilisant par exemple le premier formulaire)
            avec un courriel erroné, alors on ne peut plus se pré-inscrire.
            Toute idée pour résoudre ce problème est bienvenue.
            Pour l'instant, on a ajouté un NB pour décourager 
            à se pré-inscrire par FFV ou ISAF si on vient de changer courriel.
        </li>
        <li>Affichage de la liste des coureurs pré-inscrit amélioré.</li>
    </ul>

    <?php if(isset($administrateur)): ?>
    <h5>Page administrateur</h5>
    <ul>
        <li>Les mots de passe ne sont plus stockés en clair dans la base de données.</li>
        <li>Les identifiants sont envoyés automatiquement par courriel
            au club et à l'administrateur-créateur lors de la création de la régate.</li>
        <li>Destructions des régates anciennes implémentée.</li>
        <li>Un administrateur peut maintenant se logguer en tant que 
            Club pour gérer une régate.</li>
    </ul>
    <?php endif; ?>
    
</div>
