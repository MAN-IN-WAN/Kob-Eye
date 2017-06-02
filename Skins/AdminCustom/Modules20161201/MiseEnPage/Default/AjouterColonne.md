[STORPROC [!Query!]|Con|0|1][/STORPROC]
[STORPROC [!Query!]/Texte|Txt|0|1][/STORPROC]
[STORPROC [!Query!]/Image|Img|0|1][/STORPROC]
[!Art:=[!Con::getOneParent(Article)!]!]

[!new_saved:=0!]
[!sub_saved:=0!]

<a href="/MiseEnPage/Article/[!Art::Id!]" title="Retour à la catégorie" id="ModTitle">
	<h1><span class="glyphicon glyphicon-share-alt"> </span> Ajout d'une Colonne à l'Article <span id="objName">[!Art::Titre!]</span></h1>
</a>
<div id="ModNav">
	[MODULE MiseEnPage/Nav]
</div>
<div id="ModContainer">
        [IF [!newCol_New!]]
                <div class="debug">
                        //Alors on enregistre les proprietes
                        [OBJ MiseEnPage|Colonne|Col]
                        [METHOD Col|Set]
                                [PARAM]Titre[/PARAM]
                                [PARAM][!newCol_Titre!][/PARAM]
                        [/METHOD]
                        [!Col::Set(Ratio,[!newCol_Ratio!])!]
                        [!Col::Set(Ordre,[!newCol_Ordre!])!]
                        [!Col::addParent([!Con!])!]
                </div>
                //Sauvegarde l objet
                [IF [!Col::Verify!]]
                        [METHOD Col|Save][/METHOD]
                        [!new_saved:=1!]
                [ELSE]
                        <div class="error">
                                <h2>Erreur d'enregistrement</h2>
                                [STORPROC [!Col::Error!]|E]
                                        //Generation d une variable d error pour informer le champ en question
                                        [!err_[!E::Prop!]:=1!]
                                [/STORPROC]
                        </div>		
                [/IF]
                [IF [!new_saved!]=1]
                        [IF [!newCol_Type!]=Texte]
                        <div class="debug">                      
                                //Ajout du texte
                                [OBJ MiseEnPage|Texte|Txt]
                                [METHOD Txt|Set]
                                        [PARAM]Titre[/PARAM]
                                        [PARAM][!newData_Titre!][/PARAM]
                                [/METHOD]
                                [METHOD Txt|Set]
                                        [PARAM]Contenu[/PARAM]
                                        [PARAM][!newData_Contenu!][/PARAM]
                                [/METHOD]
                                [!Txt::addParent([!Col!])!]
                        </div>        
                                [IF [!Txt::Verify!]]
                                        [METHOD Txt|Save][/METHOD]
                                        [!sub_saved:=1!]
                                [ELSE]
                                        <div class="error">
                                                <h2>Erreur d'enregistrement du texte</h2>
                                                [STORPROC [!Txt::Error!]|E]
                                                        //Generation d une variable d error pour informer le champ en question
                                                        [!sub_err_[!E::Prop!]:=1!]
                                                [/STORPROC]
                                        </div>		
                                [/IF]
                        [ELSE]
                        <div class="debug">
                                //Ajout de l'image
                                [OBJ MiseEnPage|Image|Img]
                                [METHOD Img|Set]
                                        [PARAM]Titre[/PARAM]
                                        [PARAM][!newData_Titre!][/PARAM]
                                [/METHOD]
                                [METHOD Img|Set]
                                        [PARAM]Alt[/PARAM]
                                        [PARAM][!newData_Alt!][/PARAM]
                                [/METHOD]
                                [METHOD Img|Set]
                                        [PARAM]Title[/PARAM]
                                        [PARAM][!newData_Title!][/PARAM]
                                [/METHOD]
                                [METHOD Img|Set]
                                        [PARAM]Legende[/PARAM]
                                        [PARAM][!newData_Legende!][/PARAM]
                                [/METHOD]
                                [!Img::Set(URL,[!newData_URL!])!]
                                [!Img::addParent([!Col!])!]
                        </div>
                                [IF [!Img::Verify!]]
                                        [METHOD Img|Save][/METHOD]
                                        [!sub_saved:=1!]
                                [ELSE]
                                        <div class="error">
                                                <h2>Erreur d'enregistrement de l'image</h2>
                                                [STORPROC [!Img::Error!]|E]
                                                        //Generation d une variable d error pour informer le champ en question
                                                        [!sub_err_[!E::Prop!]:=1!]
                                                [/STORPROC]
                                        </div>		
                                [/IF]
                        [/IF]
                [/IF]
                
                [IF [!sub_saved!]=1]
                [ELSE]
                        [!Col::Delete()!]
                [/IF]
        [/IF]
        
        [IF [!new_saved!]=1]
                <div class="succes">
                        <h2>Création enregistrée avec succès</h2>
                        <a href="/MiseEnPage/Article/[!Art::Id!]">Retour à l'article</a>
                </div>
        [ELSE]
                <input type="hidden" name="newCol_New" id="newCol_New" value="1" />
                
                <div class="inputWrap [IF [!err_Titre!]=1]error[/IF]">
                        <label for="modCol_Titre">Nom</label>
                        <input type="text" name="newCol_Titre" id="newCol_Titre" value="[!newCol_Titre!]" />
                </div>
                <div class="inputWrap [IF [!err_Ratio!]=1]error[/IF]">
                        <label for="newCol_Ratio">Largeur en %</label>
                        <input type="text" name="newCol_Ratio" id="newCol_Ratio" value="[!newCol_Ratio!]" />
                </div>
                <div class="inputWrap [IF [!err_Ordre!]=1]error[/IF]">
                        <label for="newCol_Ordre">Ordre</label>
                        <input type="text" name="newCol_Ordre" id="newCol_Ordre" value="[!newCol_Ordre!]" />
                </div>
                <div class="inputWrap [IF [!err_Publier!]=1]error[/IF]">
                        [IF [!newCol_Type!]][ELSE][!newCol_Type:=Texte!][/IF]
                        <label for="newCol_Type">Type de colonne</label>
                        <select name="newCol_Type" id="newCol_Type">
                                <option value="Texte" [IF [!newCol_Type!]=Texte]selected="selected"[/IF]>Texte</option>
                                <option value="Image" [IF [!newCol_Type!]=Image]selected="selected"[/IF]>Image</option>
                        </select>
                </div>
                <div id="ColContent">
                        <div class="inputWrap [IF [!sub_err_Titre!]=1]error[/IF]">
                                <label for="newData_Titre">Titre</label>
                                <input type="text" name="newData_Titre" id="newData_Titre" value="[!newData_Titre!]" />
                        </div>
                        <div class="inputWrap txtCon [IF [!sub_err_Contenu!]=1]error[/IF]">
                                <label for="newData_Contenu">Contenu</label>
                                <textarea name="newData_Contenu" id="newData_Contenu" class="EditorFull">[!newData_Contenu!]</textarea>
                        </div>
                        <div class="inputWrap imgCon [IF [!sub_err_Alt!]=1]error[/IF]">
                                <label for="newData_Alt">Alt de l'image</label>
                                <input type="text" name="newData_Alt" id="newData_Alt" value="[!newData_Alt!]" />
                        </div>
                        <div class="inputWrap imgCon [IF [!sub_err_Titl!]=1]error[/IF]">
                                <label for="newData_Title">Title de l'image</label>
                                <input type="text" name="newData_Title" id="newData_Title" value="[!newData_Title!]" />
                        </div>
                        <div class="inputWrap imgCon [IF [!sub_err_Legende!]=1]error[/IF]">
                                <label for="newData_Legende">Légende</label>
                                <input type="text" name="newData_Legende" id="newData_Legende" value="[!newData_Legende!]" />
                        </div>
                        <div class="inputWrap imgCon [IF [!sub_err_URL!]=1]error[/IF]">
                                <label for="newData_URL">Fichier</label>
                                <input type="text" id="newData_URL" name="newData_URL" value="[!newData_URL!]">
                                <span class="btn btn-success fileinput-button">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        <span>Add files...</span>
                                        <!-- The file input field used as target for the file upload widget -->
                                        <input type="file" id="Filedata" name="Filedata" class="fileupload">
                                </span>
                                <div id="files" class="files"></div>
                                <div id="progress" class="progress">
                                        <div class="progress-bar progress-bar-success"></div>
                                </div>
                        </div>
                </div>
                <script type="text/javascript">
                       $(document).ready(function(){
                                function rebuild() {
                                        var type = $('#newCol_Type').val();
                                        if (type=='Texte') {
                                                $('.imgCon').hide();
                                                $('.txtCon').show();
                                        } else {
                                                $('.imgCon').show();
                                                $('.txtCon').hide();
                                        }
                                }
                                
                                $('#newCol_Type').on('change', rebuild);
                                
                                rebuild();  
                        }); 
                </script>
        
                <a href="/MiseEnPage/Article/[!Art::Id!]" class="btnCancel">Annuler</a>
                <input type="submit" name="newCol_Valider" id="newCol_Valider" value="Valider" class="btnSubmit"  onclick="optOutFile();">
                <div class="clear"></div>
        [/IF]
</div>