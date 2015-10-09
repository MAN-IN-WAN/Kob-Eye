[IF [!Systeme::User::Public!]]
    [MODULE Systeme/Login]
[ELSE]
    [STORPROC [!Query!]|P|0|1][/STORPROC]
    [TITLE]Admin Kob-Eye | Exportation de menus[/TITLE]
    [MODULE Systeme/Interfaces/FilAriane]
    <div id="Container">
        <div id="Data" style="padding:20px;">
            [BLOC Panneau]
            <h1>Clonage de [!P::Nom!]</h1>
            <h2>Personnalisation du nom du produit, de sa référence</h2>
            <h3>Personnalisation du nom des références, et de leur référence</h2>
                [IF [!Envoyer!]=1]
                    [!P::getCloneWithParams([!P_Nom!],[!P_Ref!],[!R_PrefNomRef!],[!R_PrefRefRef!])!]
                [ELSE]

                    <form method="post">
                        <div style="overflow:auto;background:white;height:80%;border: 1px solid gray;padding:20px;">
                            <h3>Si vous voulez cloner strictement à l'identique, merci de ne pas renseigner les champs de saisie</h3>
                            <label style="width:260px;display:inline-block;">Nouveau Nom Produit : </label><input type="text" value="" name="P_Nom" ><br />
                            <label style="width:260px;display:inline-block;">Nouvelle Référence Produit : </label><input type="text" value="" name="P_Ref"><br /><br /><br />
                            <label style="width:260px;display:inline-block;">Choisissez un prefixe pour le Nom des références  : </label><input type="text" value="" name="R_PrefNomRef"> <br />
                            (le nouveau nom des références sera : Préfixe - "Nom Produit" - nom attribut)<br /><br />
                            <label style="width:260px;display:inline-block;">Choisissez un prefixe pour la Référence des références  : </label><input type="text" value="" name="R_PrefRefRef"> <br />
                            (les  nouvelles références seront : Préfixe-CodeCouleur-ProduitId)<br /><br />
                            <input type="submit" class="KEBouton" name="generer" value="Générer le produit" style="margin:10px 0; width:100%" />
                            <input type="hidden" name="Envoyer" value="1" >
                        </div>
                    </form>
                [/IF]
            [/BLOC]
        </div>
    </div>

[/IF]