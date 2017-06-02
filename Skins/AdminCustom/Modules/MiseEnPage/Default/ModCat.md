[STORPROC [!Query!]|Cat|0|1][/STORPROC]
[!mod_saved:=0!]
[IF [!modCat_Mod!]]
        <div class="debug">
                //Alors on enregistre les proprietes
                [METHOD Cat|Set]
                        [PARAM]Nom[/PARAM]
                        [PARAM][!modCat_Nom!][/PARAM]
                [/METHOD]
                [METHOD Cat|Set]
                        [PARAM]Titre[/PARAM]
                        [PARAM][!modCat_Titre!][/PARAM]
                [/METHOD]
                [METHOD Cat|Set]
                        [PARAM]Description[/PARAM]
                        [PARAM][!modCat_Description!][/PARAM]
                [/METHOD]
                [IF [!modCat_Publier!]]
                        [!Cat::Set(Publier,1)!]
                [ELSE]
                        [!Cat::Set(Publier,0)!]
                [/IF]
                [!Cat::Set(Ordre,[!modCat_Ordre!])!]
        </div>


	//Sauvegarde l objet
	[IF [!Cat::Verify!]]
		[METHOD Cat|Save][/METHOD]
                [!mod_saved:=1!]
	[ELSE]
		<div class="error">
                        <h2>Erreur d'enregistrement</h2>
                        [STORPROC [!Cat::Error!]|E]
                                //Generation d une variable d error pour informer le champ en question
                                [!err_[!E::Prop!]:=1!]
                        [/STORPROC]
		</div>		
	[/IF]
[ELSE]
        [!modCat_Nom:=[!Cat::Nom!]!]
        [!modCat_Titre:=[!Cat::Titre!]!]
        [!modCat_Description:=[!Cat::Description!]!]
        [!modCat_Publier:=[!Cat::Publier!]!]
        [!modCat_Ordre:=[!Cat::Ordre!]!]
[/IF]

[IF [!mod_saved!]=1]
        <div class="succes">
                <h2>Modification enregistrée avec succès</h2>
                <a href="/MiseEnPage/Categorie/[!Cat::Id!]">Retour à la catégorie</a>
        </div>
[ELSE]
        <input type="hidden" name="modCat_Mod" id="modCat_Mod" value="1" />
        
        <div class="inputWrap [IF [!err_Nom!]=1]error[/IF]">
                <label for="modCat_Nom">Nom</label>
                <input type="text" name="modCat_Nom" id="modCat_Nom" value="[!modCat_Nom!]" />
        </div>
        <div class="inputWrap [IF [!err_Titre!]=1]error[/IF]">
                <label for="modCat_Titre">Titre</label>
                <input type="text" name="modCat_Titre" id="modCat_Titre" value="[!modCat_Titre!]" />
        </div>
        <div class="inputWrap [IF [!err_Description!]=1]error[/IF]">
                <label for="modCat_Description">Description</label>
                <textarea name="modCat_Description" id="modCat_Description" class="EditorFull" >[!modCat_Description!]</textarea>
        </div>
        <div class="inputWrap [IF [!err_Publier!]=1]error[/IF]">
                <label for="modCat_Publier">Publier</label>
                <input type="checkbox" name="modCat_Publier" id="modCat_Publier"[IF [!modCat_Publier!]]checked=true[/IF]" />
        </div>
        <div class="inputWrap [IF [!err_Ordre!]=1]error[/IF]">
                <label for="modCat_Ordre">Ordre</label>
                <input type="text" name="modCat_Ordre" id="modCat_Ordre" value="[!modCat_Ordre!]" />
        </div>
        
        <a href="/MiseEnPage/Categorie/[!Cat::Id!]" class="btnCancel">Annuler</a>
        <input type="submit" name="modCat_Valider" id="modCat_Valider" value="Valider" class="btnSubmit"  onclick="optOutFile();">
        <div class="clear"></div>
[/IF]