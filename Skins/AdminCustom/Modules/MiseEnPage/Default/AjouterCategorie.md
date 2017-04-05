[STORPROC [!Query!]|Cat|0|1][/STORPROC]
[!new_saved:=0!]
<a href="/MiseEnPage/Categorie" title="Retour à la liste des catégories" id="ModTitle">
	<h1><span class="glyphicon glyphicon-share-alt"> </span> Ajout d'une Catégorie</h1>
</a>
<div id="ModNav">
	[MODULE MiseEnPage/Nav]
</div>
<div id="ModContainer">        
        [IF [!newCat_New!]]
                <div class="debug">
                        //Alors on enregistre les proprietes dans un nouvel objet
                        [OBJ MiseEnPage|Categorie|Cat]
                        [METHOD Cat|Set]
                                [PARAM]Nom[/PARAM]
                                [PARAM][!newCat_Nom!][/PARAM]
                        [/METHOD]
                        [METHOD Cat|Set]
                                [PARAM]Titre[/PARAM]
                                [PARAM][!newCat_Titre!][/PARAM]
                        [/METHOD]
                        [METHOD Cat|Set]
                                [PARAM]Description[/PARAM]
                                [PARAM][!newCat_Description!][/PARAM]
                        [/METHOD]
                        [IF [!newCat_Publier!]]
                                [!Cat::Set(Publier,1)!]
                        [ELSE]
                                [!Cat::Set(Publier,0)!]
                        [/IF]
                        [!Cat::Set(Ordre,[!newCat_Ordre!])!]
                </div>
        
                //Sauvegarde l objet
                [IF [!Cat::Verify!]]
                        [METHOD Cat|Save][/METHOD]
                        [!new_saved:=1!]
                [ELSE]
                        <div class="error">
                                <h2>Erreur d'enregistrement</h2>
                                [STORPROC [!Cat::Error!]|E]
                                        //Generation d une variable d error pour informer le champ en question
                                        [!err_[!E::Prop!]:=1!]
                                [/STORPROC]
                        </div>		
                [/IF]
        [/IF]
        
        [IF [!new_saved!]=1]
                <div class="succes">
                        <h2>Création de catégorie réalisée avec succès</h2>
                        <a href="/MiseEnPage/Categorie/[!Cat::Id!]">Voir la catégorie</a>
                </div>
        [ELSE]
                <input type="hidden" name="newCat_New" id="newCat_New" value="1"/>
                
                <div class="inputWrap [IF [!err_Nom!]=1]error[/IF]">
                        <label for="newCat_Nom">Nom</label>
                        <input type="text" name="newCat_Nom" id="newCat_Nom" value="[!newCat_Nom!]"/>
                </div>
                <div class="inputWrap [IF [!err_Titre!]=1]error[/IF]">
                        <label for="newCat_Titre">Titre</label>
                        <input type="text" name="newCat_Titre" id="newCat_Titre" value="[!newCat_Titre!]"/>
                </div>
                <div class="inputWrap [IF [!err_Description!]=1]error[/IF]">
                     <label for="newCat_Description">Description</label>
	                <textarea name="newCat_Description" id="newCat_Description" class="EditorFull" >[!newCat_Description!]</textarea>
                </div>
                <div class="inputWrap [IF [!err_Publier!]=1]error[/IF]">
                        <label for="newCat_Publier">Publier</label>
                        <input type="checkbox" name="newCat_Publier" id="newCat_Publier"[IF [!newCat_Publier!]]checked=true[/IF]"/>
                </div>
                <div class="inputWrap [IF [!err_Ordre!]=1]error[/IF]">
                        <label for="newCat_Ordre">Ordre</label>
                        <input type="text" name="newCat_Ordre" id="newCat_Ordre" value="[!newCat_Ordre!]"/>
                </div>
                
                <a href="/MiseEnPage/Categorie" class="btnCancel">Annuler</a>
                <input type="submit" name="newCat_Valider" id="newCat_Valider" value="Valider" class="btnSubmit"  onclick="optOutFile();">
                <div class="clear"></div>
        [/IF]
</div>