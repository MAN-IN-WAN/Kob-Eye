        [STORPROC [!Query!]|Cat|0|1][/STORPROC]
        [!new_saved:=0!]
        
<a href="/[!Query!]" title="Retour à la catégorie" id="ModTitle">
	<h1><span class="glyphicon glyphicon-share-alt"> </span> Ajout d'un Article à la Catégorie <span id="objName">[!Cat::Nom!]</span></h1>
</a>
<div id="ModNav">
	[MODULE MiseEnPage/Nav]
</div>
<div id="ModContainer">
        [IF [!newArt_New!]]
                <div class="debug">
                        //Alors on enregistre les proprietes dans un nouvel objet
                        [OBJ MiseEnPage|Article|Art]
                        [METHOD Art|Set]
                                [PARAM]Titre[/PARAM]
                                [PARAM][!newArt_Titre!][/PARAM]
                        [/METHOD]
                        [METHOD Art|Set]
                                [PARAM]Chapo[/PARAM]
                                [PARAM][!newArt_Chapo!][/PARAM]
                        [/METHOD]
                        [METHOD Art|Set]
                                [PARAM]Date[/PARAM]
                                [PARAM][!newArt_Date!][/PARAM]
                        [/METHOD]
                        [METHOD Art|Set]
                                [PARAM]Auteur[/PARAM]
                                [PARAM][!newArt_Auteur!][/PARAM]
                        [/METHOD]
                        [IF [!newArt_AfficheTitre!]]
                                [!Art::Set(AfficheTitre,1)!]
                        [ELSE]
                                [!Art::Set(AfficheTitre,0)!]
                        [/IF]  
                        [METHOD Art|Set]
                                [PARAM]Contenu[/PARAM]
                                [PARAM][!newArt_Contenu!][/PARAM]
                        [/METHOD]
                        [IF [!newArt_ALaUne!]]
                                [!Art::Set(ALaUne,1)!]
                        [ELSE]
                                [!Art::Set(ALaUne,0)!]
                        [/IF]  
                        [IF [!newArt_Publier!]]
                                [!Art::Set(Publier,1)!]
                        [ELSE]
                                [!Art::Set(Publier,0)!]
                        [/IF] 
                        [!Art::Set(Ordre,[!newArt_Ordre!])!]
                        [!Art::addParent([!Cat!])!]
                </div>
        
                //Sauvegarde l objet
                [IF [!Art::Verify!]]
                        [METHOD Art|Save][/METHOD]
                        [!new_saved:=1!]
                [ELSE]
                        <div class="error">
                                <h2>Erreur d'enregistrement</h2>
                                [STORPROC [!Art::Error!]|E]
                                        //Generation d une variable d error pour informer le champ en question
                                        [!err_[!E::Prop!]:=1!]
                                [/STORPROC]
                        </div>		
                [/IF]
        [/IF]
        
        [IF [!new_saved!]=1]
                [REDIRECT]/MiseEnPage/Article/[!Art::Id!][/REDIRECT]
                <div class="succes">
                        <h2>Création d'article réalisée avec succès</h2>
                        <a href="/MiseEnPage/Article/[!Art::Id!]">Voir l'article</a>
                </div>
        [ELSE]
                <input type="hidden" name="newArt_New" id="newArt_New" value="1" />
                
                
                <div class="inputWrap [IF [!err_Titre!]=1]error[/IF]">
                        <label for="newArt_Titre">Titre</label>
                        <input type="text" name="newArt_Titre" id="newArt_Titre" value="[!newArt_Titre!]" />
                </div>
                <div class="inputWrap [IF [!err_Chapo!]=1]error[/IF]">
                        <label for="newArt_Chapo">Chapo</label>
                        <textarea name="newArt_Chapo" id="newArt_Chapo" class="EditorFull">[!newArt_Chapo!]</textarea>
                </div>
                <div class="inputWrap [IF [!err_Date!]=1]error[/IF]">
                        <label for="newArt_Date">Date</label>
                        <input type="text" name="newArt_Date" id="newArt_Date" [IF [!newArt_Date!]]value="[!newArt_Date!]"[/IF] class="datePicker"/>
                </div>
                <div class="inputWrap [IF [!err_Auteur!]=1]error[/IF]">
                        <label for="newArt_Auteur">Auteur</label>
                        <input type="text" name="newArt_Auteur" id="newArt_Auteur" value="[!newArt_Auteur!]" />
                </div>
                <div class="inputWrap [IF [!err_AfficheTitre!]=1]error[/IF]">
                        <label for="newArt_AfficheTitre">Afficher le titre</label>
                        <input type="checkbox" name="newArt_AfficheTitre" id="newArt_AfficheTitre"[IF [!newArt_AfficheTitre!]]checked=true[/IF]" />
                </div>
                <div class="inputWrap [IF [!err_Contenu!]=1]error[/IF]">
                        <label for="newArt_Contenu">Contenu Sans Colonne</label>
                        <textarea name="newArt_Contenu" id="newArt_Contenu" class="EditorFull">[!newArt_Contenu!]</textarea>
                </div>
                <div class="inputWrap [IF [!err_ALaUne!]=1]error[/IF]">
                        <label for="newArt_ALaUne">A la Une</label>
                        <input type="checkbox" name="newArt_ALaUne" id="newArt_ALaUne"[IF [!newArt_ALaUne!]]checked=true[/IF]" />
                </div>
                <div class="inputWrap [IF [!err_Publier!]=1]error[/IF]">
                        <label for="newArt_Publier">Publier</label>
                        <input type="checkbox" name="newArt_Publier" id="newArt_Publier"[IF [!newArt_Publier!]]checked=true[/IF]" />
                </div>
                <div class="inputWrap [IF [!err_Ordre!]=1]error[/IF]">
                        <label for="newArt_Ordre">Ordre</label>
                        <input type="text" name="newArt_Ordre" id="newArt_Ordre" value="[!newArt_Ordre!]" />
                </div>
                
                <a href="/MiseEnPage/Categorie/[!Cat::Id!]" class="btnCancel">Annuler</a>
                <input type="submit" name="newArt_Valider" id="newArt_Valider" value="Valider" class="btnSubmit"  onclick="optOutFile();">
                <div class="clear"></div>
        [/IF]
</div>