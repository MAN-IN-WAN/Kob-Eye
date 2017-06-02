[STORPROC [!Query!]|Col|0|1][/STORPROC]
[!Con:=[!Col::getOneParent(Contenu)!]!]
[!Art:=[!Con::getOneParent(Article)!]!]
[!new_saved:=0!]


<a href="/MiseEnPage/Article/[!Art::Id!]" title="Retour à l'article" id="ModTitle">
    <h1><span class="glyphicon glyphicon-share-alt"> </span> Ajout d'un Bouton à la colonne <span id="objName">[!Art::Titre!]</span></h1>
</a>
<div id="ModNav">
    [MODULE MiseEnPage/Nav]
</div>
<div id="ModContainer">
    [IF [!newBou_New!]]
        <div class="debug">
            //On enregistre les proprietes
            [OBJ MiseEnPage|Bouton|Bou]
            [METHOD Bou|Set]
                [PARAM]Label[/PARAM]
                [PARAM][!newBou_Label!][/PARAM]
            [/METHOD]
            [METHOD Bou|Set]
                [PARAM]Type[/PARAM]
                [PARAM][!newBou_Type!][/PARAM]
            [/METHOD]
            [METHOD Bou|Set]
                [PARAM]Parametres[/PARAM]
                [PARAM][!newBou_Parametres!][/PARAM]
            [/METHOD]
            [METHOD Bou|Set]
                [PARAM]Ordre[/PARAM]
                [PARAM][!newBou_Ordre!][/PARAM]
            [/METHOD]
            [!Bou::addParent([!Col!])!]
        </div>

        //Sauvegarde l objet
        [IF [!Bou::Verify!]]
            [METHOD Bou|Save][/METHOD]
            [!new_saved:=1!]
        [ELSE]
            <div class="error">
                <h2>Erreur d'enregistrement</h2>
                [STORPROC [!Bou::Error!]|E]
                    //Generation d une variable d error pour informer le champ en question
                    [!err_[!E::Prop!]:=1!]
                [/STORPROC]
            </div>
        [/IF]
    [/IF]

    [IF [!new_saved!]=1]
        [REDIRECT]/MiseEnPage/Article/[!Art::Id!][/REDIRECT]
        <div class="succes">
            <h2>Modification enregistrée avec succès</h2>
            <a href="/MiseEnPage/Article/[!Art::Id!]">Retour à l'article</a>
        </div>
    [ELSE]

        <input type="hidden" name="newBou_New" id="newBou_New" value="1" />

        <div class="inputWrap [IF [!err_Label!]=1]error[/IF]">
            <label for="newBou_Label">Label</label>
            <input type="text" name="newBou_Label" id="newBou_Label" value="[!newBou_Label!]" />
        </div>
        <div class="inputWrap [IF [!err_Type!]=1]error[/IF]">
            <label for="newBou_Type">Type</label>
            <select name="newBou_Type" id="newBou_Type" >
                <option value="contact" [IF [!newBou_Type!]=||[!newBou_Type!]=contact]selected="selected"[/IF]>Contact</option>
                <option value="espace-client"  [IF [!newBou_Type!]=espace-client]selected="selected"[/IF]>Espace Client</option>
            </select>
        </div>
        <div class="inputWrap [IF [!err_Parametres!]=1]error[/IF]">
            <label for="newBou_Parametres">Parametres</label>
            <input type="text" name="newBou_Parametres" id="newBou_Parametres" value="[IF [!newBou_Parametres!]=]?sujet=[ELSE][!newBou_Parametres!][/IF]" />
        </div>
        <!--<div class="inputWrap [IF [!err_Ordre!]=1]error[/IF]">-->
            <!--<label for="newBou_Ordre">Ordre</label>-->
            <!--<input type="text" name="newBou_Ordre" id="newBou_Ordre" value="[!newBou_Ordre!]" />-->
        <!--</div>-->

        <a href="/MiseEnPage/Article/[!Art::Id!]" class="btnCancel">Annuler</a>
        <input type="submit" name="newBou_Valider" id="newBou_Valider" value="Valider" class="btnSubmit">
        <div class="clear"></div>
    [/IF]
</div>