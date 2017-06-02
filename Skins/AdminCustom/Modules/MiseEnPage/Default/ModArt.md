[STORPROC [!Query!]|Art|0|1][/STORPROC]
[!mod_saved:=0!]

[IF [!modArt_Mod!]]
        <div class="debug">
                             
                
                //Alors on enregistre les proprietes
                [METHOD Art|Set]
                        [PARAM]Titre[/PARAM]
                        [PARAM][!modArt_Titre!][/PARAM]
                [/METHOD]
                [METHOD Art|Set]
                        [PARAM]Chapo[/PARAM]
                        [PARAM][!modArt_Chapo!][/PARAM]
                [/METHOD]
                [METHOD Art|Set]
                        [PARAM]Date[/PARAM]
                        [PARAM][!modArt_Date!][/PARAM]
                [/METHOD]
                [METHOD Art|Set]
                        [PARAM]Auteur[/PARAM]
                        [PARAM][!modArt_Auteur!][/PARAM]
                [/METHOD]
                [IF [!modArt_AfficheTitre!]]
                        [!Art::Set(AfficheTitre,1)!]
                [ELSE]
                        [!Art::Set(AfficheTitre,0)!]
                [/IF]
                [METHOD Art|Set]
                        [PARAM]Contenu[/PARAM]
                        [PARAM][!modArt_Contenu!][/PARAM]
                [/METHOD]
                [IF [!modArt_ALaUne!]]
                        [!Art::Set(ALaUne,1)!]
                [ELSE]
                        [!Art::Set(ALaUne,0)!]
                [/IF]  
                [IF [!modArt_Publier!]]
                        [!Art::Set(Publier,1)!]
                [ELSE]
                        [!Art::Set(Publier,0)!]
                [/IF]   
                [!Art::Set(Ordre,[!modArt_Ordre!])!]
        </div>
	//Sauvegarde l objet
	[IF [!Art::Verify!]]
		[METHOD Art|Save][/METHOD]
                [!mod_saved:=1!]
	[ELSE]
		<div class="error">
                        <h2>Erreur d'enregistrement</h2>
                        [STORPROC [!Art::Error!]|E]
                                //Generation d une variable d error pour informer le champ en question
                                [!err_[!E::Prop!]:=1!]
                        [/STORPROC]
		</div>		
	[/IF]
[ELSE]
        [!modArt_Titre:=[!Art::Titre!]!]
        [!modArt_Chapo:=[!Art::Chapo!]!]
        [!modArt_Date:=[!Art::Date!]!]
        [!modArt_Auteur:=[!Art::Auteur!]!]
        [!modArt_AfficheTitre:=[!Art::AfficheTitre!]!]
        [!modArt_Contenu:=[!Art::Contenu!]!]
        [!modArt_ALaUne:=[!Art::ALaUne!]!]
        [!modArt_Publier:=[!Art::Publier!]!]
        [!modArt_Ordre:=[!Art::Ordre!]!]
[/IF]

[IF [!mod_saved!]=1]
        [REDIRECT]/MiseEnPage/Article/[!Art::Id!][/REDIRECT]
        <div class="succes">
                <h2>Modification enregistrée avec succès</h2>
                <a href="/MiseEnPage/Article/[!Art::Id!]">Retour à l'article</a>
        </div>
[ELSE]
        <input type="hidden" name="modArt_Mod" id="modArt_Mod" value="1" />
        
        
        <div class="inputWrap [IF [!err_Titre!]=1]error[/IF]">
                <label for="modArt_Titre">Titre</label>
                <input type="text" name="modArt_Titre" id="modArt_Titre" value="[!modArt_Titre!]" />
        </div>
        <div class="inputWrap [IF [!err_Chapo!]=1]error[/IF]">
                <label for="modArt_Chapo">Chapo</label>
                <textarea name="modArt_Chapo" id="modArt_Chapo" class="EditorFull">[!modArt_Chapo!]</textarea>
        </div>
        <div class="inputWrap [IF [!err_Date!]=1]error[/IF]">
                <label for="modArt_Date">Date</label>
                <input type="text" name="modArt_Date" id="modArt_Date" value="[!Utils::getDate(d/m/Y H:i:s,[!modArt_Date!])!]" class="datePicker"/>
        </div>
        <div class="inputWrap [IF [!err_Auteur!]=1]error[/IF]">
                <label for="modArt_Auteur">Auteur</label>
                <input type="text" name="modArt_Auteur" id="modArt_Auteur" value="[!modArt_Auteur!]" />
        </div>v>
        <div class="inputWrap [IF [!err_AfficheTitre!]=1]error[/IF]">
                <label for="modArt_AfficheTitre">Afficher le titre</label>
                <input type="checkbox" name="modArt_AfficheTitre" id="modArt_AfficheTitre"[IF [!modArt_AfficheTitre!]]checked=true[/IF]" />
        </div>
         <div class="inputWrap [IF [!err_Contenu!]=1]error[/IF]">
                <label for="modArt_Contenu">Contenu Sans Colonne</label>
                <textarea name="modArt_Contenu" id="modArt_Contenu" class="EditorFull">[!modArt_Contenu!]</textarea>
        </div>
         <div class="inputWrap [IF [!err_ALaUne!]=1]error[/IF]">
                <label for="modArt_ALaUne">A la Une</label>
                <input type="checkbox" name="modArt_ALaUne" id="modArt_ALaUne"[IF [!modArt_ALaUne!]]checked=true[/IF]" />
        </div>
        <div class="inputWrap [IF [!err_Publier!]=1]error[/IF]">
                <label for="modArt_Publier">Publier</label>
                <input type="checkbox" name="modArt_Publier" id="modArt_Publier"[IF [!modArt_Publier!]]checked=true[/IF]" />
        </div>
        <div class="inputWrap [IF [!err_Ordre!]=1]error[/IF]">
                <label for="modArt_Ordre">Ordre</label>
                <input type="text" name="modArt_Ordre" id="modArt_Ordre" value="[!modArt_Ordre!]" />
        </div>
        
        <a href="/MiseEnPage/Article/[!Art::Id!]" class="btnCancel">Annuler</a>
        <input type="submit" name="modArt_Valider" id="modArt_Valider" value="Valider" class="btnSubmit"  onclick="optOutFile();">
        <div class="clear"></div>
[/IF]