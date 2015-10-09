[STORPROC [!Query!]|D]
<h1>Animation [!D::Titre!]</h1>
<form class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-2 control-label">Titre</label>
    <div class="col-sm-10">
        <input type="text" name="Titre" value="[!D::Titre!]" class="form-control">
    </div>
  </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Image</label>
        <div class="col-sm-10">
            [IF [!D::Image!]]
            <img src="/[!D::Image!].mini.500x200.jpg" />
            [/IF]
            <input id="input-Image" type="file" multiple=false class="file-loading" name="Image" />
            <script>
                $(document).on('ready', function() {
                    $("#input-Image").fileinput({showCaption: false, showPreview: true, language: 'fr', uploadUrl: '/Blog/Post/Upload.htm', dropZoneEnabled: false});
                });
                $('#input-Image').on('filebatchuploadcomplete', function(event, file, previewId, index) {
                    console.log('document upload ', file);
                });

            </script>
        </div>
    </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">Date</label>
    <div class="col-sm-10">
        <input type="text" class="form-control datepicker" id="inputDate" placeholder="Sélectionnez une date" value="[DATE d/m/Y][!D::Date!][/DATE]"  name="Date">
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
            <input type="submit" name="Enregistrer" value="Enregistrer" class="btn btn-success"/>
            <a href="/[!Sys::CurrentMenu::Url!]" class="btn btn-danger">Retour</a>
        </div>
    </div>
</form>
[/STORPROC]

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
