<div  class="contenu" style="padding:20pt">
    <h3>WebRegatta, Version 3.0, décembre 2015.</h3>

    <h4>Description</h4>
    
    Logiciel web de pré-inscriptions aux régates en ligne.
    
    <h5>Ce logiciel permet au <em>Coureur</em> de :</h5>
    <ul>
        <li>Se pré-inscrire à une régate ouverte ; 
            cette pré-inscription prend d'habitude au plus 30 secondes.</li>
    </ul>
    
    <h5>Le <em>Club de Voile</em> peut : </h5>
    <ul>
        <li>Afficher les informations sur la régate 
            (date, lieu, coût, commentaires, site de payement en ligne).
        </li>
        
        <li>Disposer d'un formulaire web pour recueillir en ligne
            les pré-inscriptions à la régate.</li>
        <li>Disposer de la liste des pré-inscrits sur web mise 
            à jour automatiquement et en temps réel.
            </li>

        <li>Disposer de la liste des pré-inscrits et pouvoir la modifier 
            (par exemple, annuler la pré-inscription d'un concurrent).
        </li>

        <li>Disposer de feuilles d'accueil des concurrents 
            (à imprimer et utiliser le premier jour de la régata au comptoir
            des inscriptions).</li>

        <li>Exporter les listes et intégrer les donnés avec d'autres logiciels 
            (notamment, FREG, logiciel de la FFV)
        </li>    
        <li>Contacter tous les concurrents par courriel via un formulaire web 
            (par exemple, en cas de tempête, quand il faut annuler la régate).
        </li>
        <li>Contacter un administrateur en cas de problème 
            technique.
        </li>
    </ul>
    
    <!--
    <h4>au Club de Voile, de  </h4>
    <ul>
        <li>Completarer le informazioni sulla regata (data, luogo, costo, commentari, sito di pagamento web)
        <li>Mettere a disposizione un formulario web per la preiscrizione alla regata</li>
        <li>Mettere a disposizione la lista dei preiscritti in tempo reale, senza interazione umana</li>

        <li>Modificare la lista dei preiscritti (cancellare dalle preiscrizioni)
        </li>

        <li>Mettere a disposizione del club velico dei foglio xls (da stampare) p[er accogliere i concorrenti il primo giorno della regata)</li>

        <li>Esportazione dei dati e intergrazione con altri programmi (per adesso FREG, programma della Fed. Francaise Voile)</li>
        <li>Il circolo puo' contattare tutti i concorrenti via mail (esempiuo, cancellazione della regata per cattivo tempo)</li>

        <li>Il circolo puo' contattare un amministratore per aiuto tecnico.</li>

    </ul>
    -->
    
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
        <li>La connexion au serveur dure au plus 2 heures. 
            Après ce temps, il vous faudra vous identifier à nouveau.</li>
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
        <li>Affichage  amélioré de la liste des coureurs pré-inscrits.</li>
    </ul>

    <?php if (isset($administrateur)): ?>
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

    <h5>Concepteurs :
        Luigi Santocanale, Pierre Roche. </h5>

    <h5>Anciens concepteurs : David Kossovski.</h5>



</div>
