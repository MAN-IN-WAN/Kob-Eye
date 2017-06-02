[STORPROC [!Query!]|Con|0|1][/STORPROC]
[!Art:=[!Con::getOneParent(Article)!]!]
[!mod_saved:=0!]

[IF [!modCon_Mod!]]
        <div class="debug">
                //Alors on enregistre les proprietes
                [METHOD Con|Set]
                        [PARAM]Titre[/PARAM]
                        [PARAM][!modCon_Titre!][/PARAM]
                [/METHOD]
                [METHOD Con|Set]
                        [PARAM]SousTitre[/PARAM]
                        [PARAM][!modCon_SousTitre!][/PARAM]
                [/METHOD]
                [IF [!modCon_AfficheTitre!]]
                        [!Con::Set(AfficheTitre,1)!]
                [ELSE]
                        [!Con::Set(AfficheTitre,0)!]
                [/IF]
                [IF [!modCon_Publier!]]
                        [!Con::Set(Publier,1)!]
                [ELSE]
                        [!Con::Set(Publier,0)!]
                [/IF]
                [!Con::Set(Ordre,[!modCon_Ordre!])!]
        </div>
	//Sauvegarde l objet
	[IF [!Con::Verify!]]
		[METHOD Con|Save][/METHOD]
                [!mod_saved:=1!]
	[ELSE]
		<div class="error">
                        <h2>Erreur d'enregistrement</h2>
                        [STORPROC [!Con::Error!]|E]
                                //Generation d une variable d error pour informer le champ en question
                                [!err_[!E::Prop!]:=1!]
                        [/STORPROC]
		</div>		
	[/IF]
[ELSE]
        [!modCon_Titre:=[!Con::Titre!]!]
        [!modCon_SousTitre:=[!Con::SousTitre!]!]
        [!modCon_AfficheTitre:=[!Con::AfficheTitre!]!]
        [!modCon_Publier:=[!Con::Publier!]!]
        [!modCon_Ordre:=[!Con::Ordre!]!]
[/IF]

[IF [!mod_saved!]=1]
        [REDIRECT]/MiseEnPage/Article/[!Art::Id!][/REDIRECT]
        <div class="succes">
                <h2>Modification enregistrée avec succès</h2>
                <a href="/MiseEnPage/Article/[!Art::Id!]">Retour à l'article</a>
        </div>
[ELSE]
        <input type="hidden" name="modCon_Mod" id="modCon_Mod" value="1" />
        
        <div class="inputWrap [IF [!err_Titre!]=1]error[/IF]">
                <label for="modCon_Titre">Titre</label>
                <input type="text" name="modCon_Titre" id="modCon_Titre" value="[!modCon_Titre!]" />
        </div>
        <div class="inputWrap [IF [!err_SousTitre!]=1]error[/IF]">
                <label for="modCon_SousTitre">Sous-titre</label>
                <input type="text" name="modCon_SousTitre" id="modCon_SousTitre" value="[!modCon_SousTitre!]" />
        </div>
        <div class="inputWrap [IF [!err_AfficheTitre!]=1]error[/IF]">
                <label for="modCon_AfficheTitre">Afficher le titre</label>
                <input type="checkbox" name="modCon_AfficheTitre" id="modCon_AfficheTitre" [IF [!modCon_AfficheTitre!]]checked=true[/IF]" />
        </div>
        <div class="inputWrap [IF [!err_Publier!]=1]error[/IF]">
                <label for="modCon_Publier">Publier</label>
                <input type="checkbox" name="modCon_Publier" id="modCon_Publier" [IF [!modCon_Publier!]]checked=true[/IF]" />
        </div>
        <div class="inputWrap [IF [!err_Ordre!]=1]error[/IF]">
                <label for="modCon_Ordre">Ordre</label>
                <input type="text" name="modCon_Ordre" id="modCon_Ordre" value="[!modCon_Ordre!]" />
        </div>
        
        <a href="/MiseEnPage/Article/[!Art::Id!]" class="btnCancel">Annuler</a>
        <input type="submit" name="modCon_Valider" id="modCon_Valider" value="Valider" class="btnSubmit"  onclick="optOutFile();">
        <div class="clear"></div>
[/IF]