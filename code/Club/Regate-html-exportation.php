<div class="contenu">
    <script type="text/javascript">

        $(document).ready(function () {

            $('div.explication').accordion({
                collapsible: true,
                active: 0,
                heightStyle: "content",
                event: "click hoverintent",
//                event: "mouseover",
                header: "h4"
            });
        });

    </script>

    <h3>Exportation des données</h3>

    <ul>
        <li>
            Télécharger la liste des <strong>inscrits ayant confirmé</strong> au <a href="Liste_inscrits_xls.php?confirme=1">format xls</a> (pour Excel, OpenOffice).
        </li>
        <li>
            Télécharger la liste des <strong>tous les inscrits</strong> au <a href="Liste_inscrits_xls.php">format xls</a>  (pour Excel, OpenOffice).
        </li>
    </ul>

    <h3>Intégration avec le logiciel FREG</h3>

    <h5>Nouvelle méthode d'importation dans FREG (depuis 2014) :</h5>
    <ul>
        <li>
            Télécharger la liste des <strong>inscrits ayant confirmé</strong> au <a href="Liste_inscrits_csv.php?confirme=1">format csv</a> ;
        </li>
        <li>
            Télécharger la liste des <strong>tous les inscrits</strong> au <a href="Liste_inscrits_csv.php">format csv</a>.
        </li>
    </ul>
    <div class="explication">
        <h4>Comment intégrer le fichier csv sous FREG ?</h4>
        <div class="contenu">
            <p>
                Sous FREG, après avoir créé la régate et au moins un Groupe/Série, importez le <a href="Liste_inscrits_csv.php?confirme=1">fichier csv</a> via le menu<br/>
                <span  class="menu">Inscrits->Fiches d'inscription à la régate->31. Solitaires->Importer Format CSV 2014
                </span>
                <img class="displayed" src="img/FREGCSV.jpg"/>
            </p>

            <p>
                Par le même menu, importez les Groupes/Série qui figurent maintenant dans le fichier CSV.
                Ces groupes sont LA4,RAD,STD, et les séries M(F)MIN à M(F)GGM définis selon l'âge des concurrents.        
            </p>

            <p>
                Notez bien que le menu <span class="menu">Inscrits->Fiches d'inscription à la régate->31. Solitaires->Importer Format CSV 2014</span>
                n'est accessible que si vous avez déjà créé au moins un Groupe/Série dans votre régate.
            </p>

            <p>Pour vous simplifier, vous pouvez télécharger puis restaurer dans FREG cette régate :  
                <a href="docs/Regate_Laser_type_pour_import_csv.WDZ">Regate_Laser_type_pour_import_csv.WDZ</a>.
                Tous les Groupes/séries y sont déjà créés.
            </p>

            <!--
            <p>
                Importez ce fichier dans FREG via le menu<br/>
                <span  class="menu">Inscrits->Fiches d'inscription à la régate->31. Solitaires->Importer Format CSV 2014
                </span>
            </p>

            <p>
                Importez par le même menu les Groupes/Série qui figurent maintenant dans le fichier CSV.
                Ces groupes sont LA4,RAD,STD, et les séries M(F)MIN a M(F)GGM définis selon l'âge des concurrents.        
            </p>

            <p>
                Notez que ce menu <span class="menu">Inscrits->Fiches d'inscription à la régate->31. Solitaires->Importer Format CSV 2014</span>
                n'est accessible que si vous avez déjà créé au moins un Groupe/Série dans votre régate.
            </p>

            <p>Vous pouvez télécharger puis restaurer dans FREG cette régate :  
                <a href="docs/Regate_Laser_type_pour_import_csv.WDZ">Regate_Laser_type_pour_import_csv.WDZ"</a>.
                Tous les Groupes/séries y sont déjà créés.
            </p>-->
            <!--
            <p>
                Pour vous simplifier, vous pouvez restaurer puis utiliser le fichier 
                <a href="docs/Regate_Laser_type_pour_import_csv.WDZ">Regate_Laser_type_pour_import_csv.WDZ"</a>
            </p>
            -->
        </div>
    </div>

    <br />
    On peut lire ces fichiers aussi avec Excel, OpenOffice, ou un editeur de texte.
    <!--
        <h5>Ancienne méthode d'importation :</h5>
        <ul>
            <li>
                Télécharger la liste des <strong>inscrits ayant confirmé</strong> au <a href="Liste_inscrits_dbf.php?confirme=1">format dbf</a> ;
            </li>
            <li>
                Télécharger la liste des <strong>tous les inscrits</strong> au <a href="Liste_inscrits_dbf.php">format dbf</a>.
            </li>
        </ul>
        Pour importer le fichier ins_dbf.dbf vers votre régate dans FREG, vous devez disposer du module FF_PRE_INS.EXE, à demander par courriel au support de FREG.
    
        <br/>
        On peut lire ces fichiers aussi avec Excel, OpenOffice, XBase.
    -->
</div>
