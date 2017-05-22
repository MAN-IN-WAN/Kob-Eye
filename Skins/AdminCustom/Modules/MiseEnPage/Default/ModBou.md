[STORPROC [!Query!]|Col|0|1][/STORPROC]
[!Con:=[!Col::getOneParent(Contenu)!]!]
[!Art:=[!Con::getOneParent(Article)!]!]
[!Bou:=[!Col::getOneChild(Bouton)!]!]
[!mod_saved:=0!]

[IF [!modBou_Mod!]]
    <div class="debug">
        //On enregistre les proprietes
        [METHOD Bou|Set]
            [PARAM]Label[/PARAM]
            [PARAM][!modBou_Label!][/PARAM]
        [/METHOD]
        [METHOD Bou|Set]
            [PARAM]Type[/PARAM]
            [PARAM][!modBou_Type!][/PARAM]
        [/METHOD]
        [METHOD Bou|Set]
            [PARAM]Parametres[/PARAM]
            [PARAM][!modBou_Parametres!][/PARAM]
        [/METHOD]
        [METHOD Bou|Set]
            [PARAM]Ordre[/PARAM]
            [PARAM][!modBou_Ordre!][/PARAM]
        [/METHOD]
    </div>

    //Sauvegarde l objet
    [IF [!Bou::Verify!]]
        [METHOD Bou|Save][/METHOD]
        [!mod_saved:=1!]
    [ELSE]
        <div class="error">
            <h2>Erreur d'enregistrement</h2>
            [STORPROC [!Bou::Error!]|E]
            //Generation d une variable d error pour informer le champ en question
            [!err_[!E::Prop!]:=1!]
            [/STORPROC]
        </div>
    [/IF]
[ELSE]
    [!modBou_Label:=[!Bou::Label!]!]
    [!modBou_Type:=[!Bou::Type!]!]
    [!modBou_Parametres:=[!Bou::Parametres!]!]
    [!modBou_Ordre:=[!Bou::Ordre!]!]
[/IF]

[IF [!mod_saved!]=1]
[REDIRECT]/MiseEnPage/Article/[!Art::Id!][/REDIRECT]
<div class="succes">

    <h2>Modification enregistrée avec succès</h2>
    <a href="/MiseEnPage/Article/[!Art::Id!]">Retour à l'article</a>
</div>
[ELSE]

<input type="hidden" name="modBou_Mod" id="modBou_Mod" value="1" />

<div class="inputWrap [IF [!err_Label!]=1]error[/IF]">
    <label for="modBou_Label">Label</label>
    <input type="text" name="modBou_Label" id="modBou_Label" value="[!modBou_Label!]" />
</div>
<div class="inputWrap [IF [!err_Type!]=1]error[/IF]">
    <label for="modBou_Type">Type</label>
    <select name="modBou_Type" id="modBou_Type" >
        <option value="contact" [IF [!newBou_Type!]=contact]selected="selected"[/IF]>Contact</option>
        <option value="espace-client"  [IF [!newBou_Type!]=espace-client]selected="selected"[/IF]>Espace Client</option>
    </select>
</div>
<div class="inputWrap [IF [!err_Parametres!]=1]error[/IF]">
    <label for="modBou_Parametres">Parametres</label>
    <input type="text" name="modBou_Parametres" id="modBou_Parametres" value="[!modBou_Parametres!]" />
</div>
<!--<div class="inputWrap [IF [!err_Ordre!]=1]error[/IF]">-->
    <!--<label for="modBou_Ordre">Ordre</label>-->
    <!--<input type="text" name="modBou_Ordre" id="modBou_Ordre" value="[!modBou_Ordre!]" />-->
<!--</div>-->


<a href="/MiseEnPage/Article/[!Art::Id!]" class="btnCancel">Annuler</a>
<input type="submit" name="modBou_Valider" id="modBou_Valider" value="Valider" class="btnSubmit">
<div class="clear"></div>
[/IF]
