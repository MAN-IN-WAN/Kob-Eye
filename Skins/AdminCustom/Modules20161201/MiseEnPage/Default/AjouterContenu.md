[STORPROC [!Query!]|Art|0|1][/STORPROC]
[!new_saved:=0!]

<a href="/MiseEnPage/Article/[!Art::Id!]" title="Retour à la catégorie" id="ModTitle">
	<h1><span class="glyphicon glyphicon-share-alt"> </span> Ajout d'un Contenu à l'Article <span id="objName">[!Art::Titre!]</span></h1>
</a>
<div id="ModNav">
	[MODULE MiseEnPage/Nav]
</div>
<div id="ModContainer">
        [IF [!newCon_New!]]
                <div class="debug">
                        //On enregistre les proprietes
                        [OBJ MiseEnPage|Contenu|Con]
                        [METHOD Con|Set]
                                [PARAM]Titre[/PARAM]
                                [PARAM][!newCon_Titre!][/PARAM]
                        [/METHOD]
                        [METHOD Con|Set]
                                [PARAM]SousTitre[/PARAM]
                                [PARAM][!newCon_SousTitre!][/PARAM]
                        [/METHOD]
                        [IF [!newCon_AfficheTitre!]]
                                [!Con::Set(AfficheTitre,1)!]
                        [ELSE]
                                [!Con::Set(AfficheTitre,0)!]
                        [/IF]
                        [IF [!newCon_Publier!]]
                                [!Con::Set(Publier,1)!]
                        [ELSE]
                                [!Con::Set(Publier,0)!]
                        [/IF]
                        [!Con::Set(Ordre,[!newCon_Ordre!])!]
                        [!Con::addParent([!Art!])!]
                </div>
                
                //Sauvegarde l objet
                [IF [!Con::Verify!]]
                        [METHOD Con|Save][/METHOD]
                        [!new_saved:=1!]
                [ELSE]
                        <div class="error">
                                <h2>Erreur d'enregistrement</h2>
                                [STORPROC [!Con::Error!]|E]
                                        //Generation d une variable d error pour informer le champ en question
                                        [!err_[!E::Prop!]:=1!]
                                [/STORPROC]
                        </div>		
                [/IF]
        [/IF]
        
        [IF [!new_saved!]=1]
                <div class="succes">
                        <h2>Modification enregistrée avec succès</h2>
                        <a href="/MiseEnPage/Article/[!Art::Id!]">Retour à l'article</a>
                </div>
        [ELSE]
                <input type="hidden" name="newCon_New" id="newCon_New" value="1" />
                
                <div class="inputWrap [IF [!err_Titre!]=1]error[/IF]">
                        <label for="newCon_Titre">Titre</label>
                        <input type="text" name="newCon_Titre" id="newCon_Titre" value="[!newCon_Titre!]" />
                </div>
                <div class="inputWrap [IF [!err_SousTitre!]=1]error[/IF]">
                        <label for="newCon_SousTitre">Sous-titre</label>
                        <input type="text" name="newCon_SousTitre" id="newCon_SousTitre" value="[!newCon_SousTitre!]" />
                </div>
                <div class="inputWrap [IF [!err_AfficheTitre!]=1]error[/IF]">
                        <label for="newCon_AfficheTitre">Afficher le titre</label>
                        <input type="checkbox" name="newCon_AfficheTitre" id="newCon_AfficheTitre"[IF [!newCon_AfficheTitre!]]checked=true[/IF]" />
                </div>
                <div class="inputWrap [IF [!err_Publier!]=1]error[/IF]">
                        <label for="newCon_Publier">Publier</label>
                        <input type="checkbox" name="newCon_Publier" id="newCon_Publier"[IF [!newCon_Publier!]]checked=true[/IF]" />
                </div>
                <div class="inputWrap [IF [!err_Ordre!]=1]error[/IF]">
                        <label for="newCon_Ordre">Ordre</label>
                        <input type="text" name="newCon_Ordre" id="newCon_Ordre" value="[!newCon_Ordre!]" />
                </div>
                
                <a href="/MiseEnPage/Article/[!Art::Id!]" class="btnCancel">Annuler</a>
                <input type="submit" name="newCon_Valider" id="newCon_Valider" value="Valider" class="btnSubmit"  onclick="optOutFile();">
                <div class="clear"></div>
        [/IF]
</div>