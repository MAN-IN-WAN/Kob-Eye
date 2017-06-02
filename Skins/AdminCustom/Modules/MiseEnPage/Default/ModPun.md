[STORPROC [!Query!]|Pun|0|1][/STORPROC]
[!Art:=[!Pun::getOneParent(Article)!]!]
[!mod_saved:=0!]

[IF [!modPun_Mod!]]
    <div class="debug">
        //On enregistre les proprietes
        [METHOD Pun|Set]
            [PARAM]Contenu[/PARAM]
            [PARAM][!modPun_Contenu!][/PARAM]
        [/METHOD]
        [METHOD Pun|Set]
            [PARAM]Type[/PARAM]
            [PARAM][!modPun_Type!][/PARAM]
        [/METHOD]
        [METHOD Pun|Set]
            [PARAM]Ordre[/PARAM]
            [PARAM][!modPun_Ordre!][/PARAM]
        [/METHOD]
    </div>

    //Sauvegarde l objet
    [IF [!Pun::Verify!]]
        [METHOD Pun|Save][/METHOD]
        [!mod_saved:=1!]
    [ELSE]
        <div class="error">
            <h2>Erreur d'enregistrement</h2>
            [STORPROC [!Pun::Error!]|E]
            //Generation d une variable d error pour informer le champ en question
            [!err_[!E::Prop!]:=1!]
            [/STORPROC]
        </div>
    [/IF]
[ELSE]
    [!modPun_Contenu:=[!Pun::Contenu!]!]
    [!modPun_Type:=[!Pun::Type!]!]
    [!modPun_Ordre:=[!Pun::Ordre!]!]
[/IF]

[IF [!mod_saved!]=1]
[REDIRECT]/MiseEnPage/Article/[!Art::Id!][/REDIRECT]
<div class="succes">

    <h2>Modification enregistrée avec succès</h2>
    <a href="/MiseEnPage/Article/[!Art::Id!]">Retour à l'article</a>
</div>
[ELSE]

<input type="hidden" name="modPun_Mod" id="modPun_Mod" value="1" />

<div class="inputWrap [IF [!err_Contenu!]=1]error[/IF]">
    <label for="modPun_Contenu">Contenu</label>
    <textarea name="modPun_Contenu" id="modPun_Contenu" class="EditorFull">[!modPun_Contenu!]</textarea>
</div>
<div class="inputWrap [IF [!err_Type!]=1]error[/IF]">
    <label for="modPun_Type">Type</label>
    <select name="modPun_Type" id="modPun_Type" >
        <option value="info" [IF [!modPun_Type!]=info]selected="selected"[/IF]>Info</option>
        <option value="success"  [IF [!modPun_Type!]=success]selected="selected"[/IF]>Succes</option>
        <option value="warning"  [IF [!modPun_Type!]=warning]selected="selected"[/IF]>Warning</option>
        <option value="danger"  [IF [!modPun_Type!]=danger]selected="selected"[/IF]>Danger</option>
    </select>
</div>
<div class="inputWrap [IF [!err_Ordre!]=1]error[/IF]">
    <label for="modPun_Ordre">Ordre</label>
    <input type="text" name="modPun_Ordre" id="modPun_Ordre" value="[!modPun_Ordre!]" />
</div>


<a href="/MiseEnPage/Article/[!Art::Id!]" class="btnCancel">Annuler</a>
<input type="submit" name="modPun_Valider" id="modPun_Valider" value="Valider" class="btnSubmit">
<div class="clear"></div>
[/IF]
