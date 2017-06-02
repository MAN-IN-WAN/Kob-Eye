[STORPROC [!Query!]|Col|0|1][/STORPROC]
[STORPROC [!Query!]/Texte|Txt|0|1][/STORPROC]
[STORPROC [!Query!]/Image|Img|0|1][/STORPROC]
[!Con:=[!Col::getOneParent(Contenu)!]!]
[!Art:=[!Con::getOneParent(Article)!]!]

[!mod_saved:=0!]
[!sub_saved:=0!]

[IF [!modCol_Mod!]]
        <div class="debug">
                //Alors on enregistre les proprietes
                [METHOD Col|Set]
                        [PARAM]Titre[/PARAM]
                        [PARAM][!modCol_Titre!][/PARAM]
                [/METHOD]
                [IF [!modCol_AfficheTitre!]]
                    [!Col::Set(AfficheTitre,1)!]
                [ELSE]
                    [!Col::Set(AfficheTitre,0)!]
                [/IF]
                [!Col::Set(Ratio,[!modCol_Ratio!])!]
                [!Col::Set(Ordre,[!modCol_Ordre!])!]
        </div>
        
        [IF [!modCol_Type!]=Texte]
        <div class="debug">
                [IF [!Txt!]]
                        //On avait deja un texte on change juste ses propriétés
                        [METHOD Txt|Set]
                                [PARAM]Titre[/PARAM]
                                [PARAM][!modData_Titre!][/PARAM]
                        [/METHOD]
                        [METHOD Txt|Set]
                                [PARAM]Contenu[/PARAM]
                                [PARAM][!modData_Contenu!][/PARAM]
                        [/METHOD]
                [ELSE]
                        //On avait une image ou rien et on veut du texte à la place
                        [OBJ MiseEnPage|Texte|Txt]
                        [METHOD Txt|Set]
                                [PARAM]Titre[/PARAM]
                                [PARAM][!modData_Titre!][/PARAM]
                        [/METHOD]
                        [METHOD Txt|Set]
                                [PARAM]Contenu[/PARAM]
                                [PARAM][!modData_Contenu!][/PARAM]
                        [/METHOD]
                        [!Txt::addParent([!Col!])!]
                [/IF]
        </div>
                [IF [!Txt::Verify!]]
                        [METHOD Txt|Save][/METHOD]
                        [!sub_saved:=1!]
                [ELSE]
                        <div class="error">
                                <h2>Erreur d'enregistrement</h2>
                                [STORPROC [!Txt::Error!]|E]
                                        //Generation d une variable d error pour informer le champ en question
                                        [!sub_err_[!E::Prop!]:=1!]
                                [/STORPROC]
                        </div>		
                [/IF]
                [IF [!sub_saved!]=1]
                        [!Img::Delete()!]
                [/IF]
        [ELSE]
        <div class="debug">
                [IF [!Img!]]
                        //On avait deja une image on change juste ses propriétés
                        [METHOD Img|Set]
                            [PARAM]Hauteur[/PARAM]
                            [PARAM][!modData_Hauteur!][/PARAM]
                        [/METHOD]
                        // [METHOD Img|Set]
                        //         [PARAM]Titre[/PARAM]
                        //         [PARAM][!modData_Titre!][/PARAM]
                        // [/METHOD]
                        [METHOD Img|Set]
                                [PARAM]Alt[/PARAM]
                                [PARAM][!modData_Alt!][/PARAM]
                        [/METHOD]
                        [METHOD Img|Set]
                                [PARAM]Title[/PARAM]
                                [PARAM][!modData_Title!][/PARAM]
                        [/METHOD]
                        [METHOD Img|Set]
                                [PARAM]Legende[/PARAM]
                                [PARAM][!modData_Legende!][/PARAM]
                        [/METHOD]
                        [!Img::Set(URL,[!modData_URL!])!]
                [ELSE]
                        //On avait un texte ou rien et on veut une image à la place
                        [OBJ MiseEnPage|Image|Img]
                        [METHOD Img|Set]
                            [PARAM]Hauteur[/PARAM]
                            [PARAM][!modData_Hauteur!][/PARAM]
                        [/METHOD]
                        // [METHOD Img|Set]
                        //         [PARAM]Titre[/PARAM]
                        //         [PARAM][!modData_Titre!][/PARAM]
                        // [/METHOD]
                        [METHOD Img|Set]
                                [PARAM]Alt[/PARAM]
                                [PARAM][!modData_Alt!][/PARAM]
                        [/METHOD]
                        [METHOD Img|Set]
                                [PARAM]Title[/PARAM]
                                [PARAM][!modData_Title!][/PARAM]
                        [/METHOD]
                        [METHOD Img|Set]
                                [PARAM]Legende[/PARAM]
                                [PARAM][!modData_Legende!][/PARAM]
                        [/METHOD]
                        [!Img::Set(URL,[!modData_URL!])!]
                        [!Img::addParent([!Col!])!]
                [/IF]
        </div>
                [IF [!Img::Verify!]]
                        [METHOD Img|Save][/METHOD]
                        [!sub_saved:=1!]
                [ELSE]
                        <div class="error">
                                <h2>Erreur d'enregistrement</h2>
                                [STORPROC [!Img::Error!]|E]
                                        //Generation d une variable d error pour informer le champ en question
                                        [!sub_err_[!E::Prop!]:=1!]
                                [/STORPROC]
                        </div>		
                [/IF]
                [IF [!sub_saved!]=1]
                        [!Txt::Delete()!]
                [/IF]
        [/IF]
        
        [IF [!sub_saved!]=1]
                //Sauvegarde l objet
                [IF [!Col::Verify!]]
                        [METHOD Col|Save][/METHOD]
                        [!mod_saved:=1!]
                [ELSE]
                        <div class="error">
                                <h2>Erreur d'enregistrement</h2>
                                [STORPROC [!Col::Error!]|E]
                                        //Generation d une variable d error pour informer le champ en question
                                        [!err_[!E::Prop!]:=1!]
                                [/STORPROC]
                        </div>		
                [/IF]
        [/IF]
[ELSE]
        [!modCol_Titre:=[!Col::Titre!]!]
        [!modCol_Ratio:=[!Col::Ratio!]!]
        [!modCol_Ordre:=[!Col::Ordre!]!]
        
        [IF [!Txt!]]
                [!modCol_Type:=Texte!]
                [!modData_Titre:=[!Txt::Titre!]!]
                [!modData_Contenu:=[!Txt::Contenu!]!]
        [ELSE]
                [!modCol_Type:=Image!]
                [!modData_Hauteur:=[!Img::Hauteur!]!]
                [!modData_Alt:=[!Img::Alt!]!]
                [!modData_Legende:=[!Img::Legende!]!]
                [!modData_Title:=[!Img::Title!]!]
                [!modData_URL:=[!Img::URL!]!]
        [/IF]
        
[/IF]

[IF [!mod_saved!]=1]
        [REDIRECT]/MiseEnPage/Article/[!Art::Id!][/REDIRECT]
        <div class="succes">
                <h2>Modification enregistrée avec succès</h2>
                <a href="/MiseEnPage/Article/[!Art::Id!]">Retour à l'article</a>
        </div>
[ELSE]
        <input type="hidden" name="modCol_Mod" id="modCol_Mod" value="1" />
        
        <div class="inputWrap [IF [!err_Titre!]=1]error[/IF]">
                <label for="modCol_Titre">Titre de la colonne</label>
                <input type="text" name="modCol_Titre" id="modCol_Titre" value="[!modCol_Titre!]" />
        </div>
        <div class="inputWrap ">
            <label for="modCol_AfficheTitre">Afficher titre</label>
            <input type="checkbox" name="modCol_AfficheTitre" id="modCol_AfficheTitre" [IF [!modCol_AfficheTitre!]]checked=true[/IF]/>
        </div>
        <div class="inputWrap [IF [!err_Ratio!]=1]error[/IF]">
                <label for="modCol_Ratio">Largeur en %</label>
                <input type="text" name="modCol_Ratio" id="modCol_Ratio" value="[!modCol_Ratio!]" />
        </div>
        <div class="inputWrap [IF [!err_Ordre!]=1]error[/IF]">
                <label for="modCol_Ordre">Ordre</label>
                <input type="text" name="modCol_Ordre" id="modCol_Ordre" value="[!modCol_Ordre!]" />
        </div>
        <div class="inputWrap [IF [!err_Type!]=1]error[/IF]">
                [IF [!modCol_Type!]][ELSE][!modCol_Type:=Texte!][/IF]
                <label for="modCol_Type">Type de colonne</label>
                <select name="modCol_Type" id="modCol_Type">
                        <option value="Texte" [IF [!modCol_Type!]=Texte]selected="selected"[/IF]>Texte</option>
                        <option value="Image" [IF [!modCol_Type!]=Image]selected="selected"[/IF]>Image</option>
                </select>
        </div>
        <div id="ColContent">
                <!--<div class="inputWrap [IF [!sub_err_Titre!]=1]error[/IF]">-->
                        <!--<label for="modData_Titre">Titre</label>-->
                        <!--<input type="text" name="modData_Titre" id="modData_Titre" value="[!modData_Titre!]" />-->
                <!--</div>-->
                <div class="inputWrap txtCon [IF [!sub_err_Contenu!]=1]error[/IF]">
                        <label for="modData_Contenu">Contenu</label>
                        <textarea name="modData_Contenu" id="modData_Contenu" class="EditorFull">[!modData_Contenu!]</textarea>
                </div>
                <div class="inputWrap imgCon [IF [!sub_err_Hauteur!]=1]error[/IF]">
                        <label for="modData_Hauteur">Hauteur de l'image</label>
                        <input type="text" name="modData_Hauteur" id="modData_Hauteur" value="[!modData_Hauteur!]" />
                </div>
                <div class="inputWrap imgCon [IF [!sub_err_Alt!]=1]error[/IF]">
                        <label for="modData_Alt">Alt de l'image</label>
                        <input type="text" name="modData_Alt" id="modData_Alt" value="[!modData_Alt!]" />
                </div>
                <div class="inputWrap imgCon [IF [!sub_err_Title!]=1]error[/IF]">
                        <label for="modData_Title">Title de l'image</label>
                        <input type="text" name="modData_Title" id="modData_Title" value="[!modData_Title!]" />
                </div>
                <div class="inputWrap imgCon [IF [!sub_err_Legende!]=1]error[/IF]">
                        <label for="modData_Legende">Légende</label>
                        <input type="text" name="modData_Legende" id="modData_Legende" value="[!modData_Legende!]" />
                </div>
                <div class="inputWrap imgCon [IF [!sub_err_URL!]=1]error[/IF]">
                        <label for="modData_URL">Fichier</label>
                        <input type="text" id="modData_URL" name="modData_URL" value="[!modData_URL!]">
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
                                var type = $('#modCol_Type').val();
                                if (type=='Texte') {
                                        $('.imgCon').hide();
                                        $('.txtCon').show();
                                } else {
                                        $('.imgCon').show();
                                        $('.txtCon').hide();
                                }
                        }
                        
                        $('#modCol_Type').on('change', rebuild);
                        
                        rebuild();  
                }); 
        </script>

       
        <a href="/MiseEnPage/Article/[!Art::Id!]" class="btnCancel">Annuler</a>
        <input type="submit" name="modCol_Valider" id="modCol_Valider" value="Valider" class="btnSubmit" onclick="optOutFile();">
        <div class="clear"></div>
[/IF]