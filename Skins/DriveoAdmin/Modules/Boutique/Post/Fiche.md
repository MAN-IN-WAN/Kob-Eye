[IF [!SaveDate!]=Enregistrer]
        //POST
        [METHOD D|Set][PARAM]Titre[/PARAM][PARAM][!Titre!][/PARAM][/METHOD]
        [METHOD D|Set][PARAM]Date[/PARAM][PARAM][!Date!][/PARAM][/METHOD]
        [METHOD D|Set][PARAM]Resume[/PARAM][PARAM][!Resume!][/PARAM][/METHOD]
        [METHOD D|Set][PARAM]Contenu[/PARAM][PARAM][!Contenu!][/PARAM][/METHOD]
        [METHOD D|Set][PARAM]Valide[/PARAM][PARAM]1[/PARAM][/METHOD]
        [METHOD D|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
        [METHOD D|AddParent][PARAM]Blog/Categorie/[!Categorie!][/PARAM][/METHOD]
        [METHOD D|Save][/METHOD]

        //IMAGE
        [STORPROC Blog/Post/[!D::Id!]/Donnees/Type=Image|I|0|1]
            [NORESULT]
                [OBJ Blog|Donnees|I]
            [/NORESULT]
        [/STORPROC]
        [METHOD I|Set][PARAM]Titre[/PARAM][PARAM]Image du post[/PARAM][/METHOD]
        [METHOD I|Set][PARAM]Fichier[/PARAM][PARAM][!Image!][/PARAM][/METHOD]
        [METHOD I|Set][PARAM]Type[/PARAM][PARAM]Image[/PARAM][/METHOD]
        [METHOD I|AddParent][PARAM]Blog/Post/[!D::Id!][/PARAM][/METHOD]
        [METHOD I|Save][/METHOD]
        //[REDIRECT][!Sys::getMenu(Blog/Post)!]/[!D::Id!][/REDIRECT]
[/IF]
<h1>Animation [!D::Titre!]</h1>
<form class="form-horizontal" method="POST">
  <div class="form-group">
    <label class="col-sm-2 control-label">Titre</label>
    <div class="col-sm-10">
        <input type="text" name="Titre" value="[!D::Titre!]" class="form-control">
    </div>
  </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Image</label>
        <div class="col-sm-10">
            [IF [!D::Id!]]
            [STORPROC Blog/Post/[!D::Id!]/Donnees/Type=Image|I|0|1]
                <img src="/[!I::Fichier!].mini.500x200.jpg" />
            [/STORPROC]
            [/IF]
            <input type="hidden" class="ImageInput" name="Image" value="[!I::Fichier!]"/>
            <input id="input-Image" type="file" multiple=false class="file-loading"/>
            <script>
                $(document).on('ready', function() {
                    $("#input-Image").fileinput({showCaption: false, showPreview: true, language: 'fr', uploadUrl: '/Blog/Post/Upload.htm', dropZoneEnabled: false});
                });
                $('#input-Image').on('fileuploaded', function(event, data, previewId, index) {
                    console.log('document upload ', data);
                    $('.ImageInput').val(data.response.url);
                });

            </script>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Categorie</label>
        <div class="col-sm-10">
            <select class="form-control" id="categorie"  name="Categorie">
                [STORPROC Blog/Categorie|C]
                <option value="[!C::Id!]">[!C::Titre!]</option>
                [/STORPROC]
            </select>
        </div>
    </div>
    <!--<div class="form-group">
        <label class="col-sm-2 control-label">Publier</label>
        <div class="col-sm-2">
            <input type="checkbox" class="form-control" value="[!D::Valide!]"  name="Publier">
        </div>
    </div>-->
  <div class="form-group">
    <label class="col-sm-2 control-label">Date</label>
    <div class="col-sm-10">
        <input type="text" class="form-control datepicker" id="inputDate" placeholder="Sélectionnez une date" value="[IF [!D::Date!]][DATE d/m/Y][!D::Date!][/DATE][ELSE][DATE d/m/Y][!TMS::Now!][/DATE][/IF]"  name="Date">
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Resume</label>
    <div class="col-sm-10">
        <textarea  class="form-control" name="Resume" style="min-height: 100px;;">[!D::Resume!]</textarea>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Contenu</label>
    <div class="col-sm-10">
        <textarea name="Contenu" rows="10" cols="80" id="ckeditor">
            [!D::Contenu!]
        </textarea>
    </div>
  </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"></label>
        <div class="col-sm-10">
            <input type="submit" name="SaveDate" value="Enregistrer" class="btn btn-success"/>
            <a href="/[!Sys::CurrentMenu::Url!]" class="btn btn-danger">Retour</a>
        </div>
    </div>
</form>

<script>
    $('.datepicker').datepicker({
        language: 'fr'
    });

    CKEDITOR.replace( 'ckeditor' );

    $('.submit').on('click',function () {
        $('#newSession').modal("hide");
        console.log('test form ',$('#newSessionForm').serialize());
        $.ajax({
            url: "/Formation/Session/Save.json",
            method: 'POST',
            data: $('#newSessionForm').serialize()
        }).done(function( data ) {
            if (data.success){
                //redirection vers la fiche de la session
                window.location.replace("/Sessions/"+data.id);
            }else{
                //affichage des erreurs
                $('#erreurPlace').html(data.errors);
            }
        }).fail(function () {
            console.log('FAILED');
        });
    });
</script>
