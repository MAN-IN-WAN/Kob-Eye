[MODULE Systeme/Interfaces/FilAriane]
<div id="Container">
    <div id="Arbo">
            [BLOC Panneau]
                    //Bouton ajouter
                    <a href="/[!Query!]" class="KEBouton">Retour à la fiche</a>
                //Commentaires
                [BLOC Panneau|background:white;position:relative;overflow:hidden;padding:0px;padding-bottom:5px;]
                    <p>
                        Exportation complête des enregistrements de produits.
                        CHoisissez un lien ci-contre pour définir le type d'exportation que vous souhaitez.
                    </p>
                [/BLOC]
            [/BLOC]
    </div>
    <div id="Data">
        <div style="overflow:hidden;width:74%;float:left;">
                //Liens
                [BLOC Panneau|background:#BABAD5;position:relative;overflow:hidden;padding:0px;padding-bottom:5px;]
                    <h2>Exportation des enregistrements</h2>
                    <ul>
                        <li><a href="/Products/Registration/ExportCsv.csv" rel="link">Téléchargez le fichier au format CSV.</a></li>
                        <li>Téléchargez le fichier au format PDF. (Pas encore disponible)</li>
                        <li>Téléchargez le fichier au format XLS. (Pas encore disponible)</li>
                    </ul>
                [/BLOC]
        </div>
    </div>
</div>
