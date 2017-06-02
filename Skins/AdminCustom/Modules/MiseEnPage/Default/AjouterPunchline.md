[STORPROC [!Query!]|Art|0|1][/STORPROC]
[!new_saved:=0!]

<a href="/MiseEnPage/Article/[!Art::Id!]" title="Retour à l'article" id="ModTitle">
    <h1><span class="glyphicon glyphicon-share-alt"> </span> Ajout d'une Punchline à l'Article <span id="objName">[!Art::Titre!]</span></h1>
</a>
<div id="ModNav">
    [MODULE MiseEnPage/Nav]
</div>
<div id="ModContainer">
    [IF [!newPun_New!]]
        <div class="debug">
            //On enregistre les proprietes
            [OBJ MiseEnPage|Punchline|Pun]
            [METHOD Pun|Set]
                [PARAM]Contenu[/PARAM]
                [PARAM][!newPun_Contenu!][/PARAM]
            [/METHOD]
            [METHOD Pun|Set]
                [PARAM]Type[/PARAM]
                [PARAM][!newPun_Type!][/PARAM]
            [/METHOD]
            [METHOD Pun|Set]
                [PARAM]Ordre[/PARAM]
                [PARAM][!newPun_Ordre!][/PARAM]
            [/METHOD]
            [!Pun::addParent([!Art!])!]
        </div>

        //Sauvegarde l objet
        [IF [!Pun::Verify!]]
            [METHOD Pun|Save][/METHOD]
            [!new_saved:=1!]
        [ELSE]
            <div class="error">
                <h2>Erreur d'enregistrement</h2>
                [STORPROC [!Pun::Error!]|E]
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

        <input type="hidden" name="newPun_New" id="newPun_New" value="1" />

        <div class="inputWrap [IF [!err_Contenu!]=1]error[/IF]">
            <label for="newPun_Contenu">Contenu</label>
            <textarea name="newPun_Contenu" id="newPun_Contenu" class="EditorFull">[!newPun_Contenu!]</textarea>
        </div>
        <div class="inputWrap [IF [!err_Type!]=1]error[/IF]">
            <label for="newPun_Type">Type</label>
            <select name="newPun_Type" id="newPun_Type" >
                <option value="info" [IF [!newPun_Type!]=||[!newPun_Type!]=info]selected="selected"[/IF]>Info</option>
                <option value="success"  [IF [!newPun_Type!]=success]selected="selected"[/IF]>Succes</option>
                <option value="warning"  [IF [!newPun_Type!]=warning]selected="selected"[/IF]>Warning</option>
                <option value="danger"  [IF [!newPun_Type!]=danger]selected="selected"[/IF]>Danger</option>
            </select>
        </div>
        <div class="inputWrap [IF [!err_Ordre!]=1]error[/IF]">
            <label for="newPun_Ordre">Ordre</label>
            <input type="text" name="newPun_Ordre" id="newPun_Ordre" value="[!newPun_Ordre!]" />
        </div>

        <a href="/MiseEnPage/Article/[!Art::Id!]" class="btnCancel">Annuler</a>
        <input type="submit" name="newPun_Valider" id="newPun_Valider" value="Valider" class="btnSubmit">
        <div class="clear"></div>
    [/IF]
</div>