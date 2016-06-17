[IF [!SaveDate!]=Enregistrer]
        //PROMOTION
        [METHOD D|Set][PARAM]Intitule[/PARAM][PARAM][!Intitule!][/PARAM][/METHOD]
        [METHOD D|Set][PARAM]DateDebutPromo[/PARAM][PARAM][!DateDebutPromo!][/PARAM][/METHOD]
        [METHOD D|Set][PARAM]DateFinPromo[/PARAM][PARAM][!DateFinPromo!][/PARAM][/METHOD]
        [METHOD D|Set][PARAM]Image[/PARAM][PARAM][!Image!][/PARAM][/METHOD]
        [METHOD D|Set][PARAM]Display[/PARAM][PARAM]1[/PARAM][/METHOD]
        [METHOD D|Set][PARAM]SliderEnable[/PARAM][PARAM]1[/PARAM][/METHOD]
        [METHOD D|Set][PARAM]Panning[/PARAM][PARAM]0[/PARAM][/METHOD]
        [METHOD D|Save][/METHOD]

        //CALQUE
        [STORPROC Boutique/Promotion/[!D::Id!]/PromotionCalque|I|0|1]
            [NORESULT]
                [OBJ Boutique|PromotionCalque|I]
            [/NORESULT]
        [/STORPROC]
        [METHOD I|Set][PARAM]Texte[/PARAM][PARAM][!Intitule!][/PARAM][/METHOD]
        [METHOD I|Set][PARAM]Background[/PARAM][PARAM]largegreenbg[/PARAM][/METHOD]
        [METHOD I|Set][PARAM]PosX[/PARAM][PARAM]center[/PARAM][/METHOD]
        [METHOD I|Set][PARAM]PosY[/PARAM][PARAM]center[/PARAM][/METHOD]
        [METHOD I|Set][PARAM]TransitionType[/PARAM][PARAM]fromforeground[/PARAM][/METHOD]

        [METHOD I|AddParent][PARAM]Boutique/Promotion/[!D::Id!][/PARAM][/METHOD]
        [METHOD I|Save][/METHOD]
        [REDIRECT][!Sys::getMenu(Boutique/Promotion)!]/[!D::Id!][/REDIRECT]
[/IF]
<h1>Promotion [!D::Intitule!]</h1>
<form class="form-horizontal" method="POST">
  <div class="form-group">
    <label class="col-sm-4 control-label">Intitule</label>
    <div class="col-sm-8">
        <input type="text" name="Intitule" value="[!D::Intitule!]" class="form-control">
    </div>
  </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Image</label>
        <div class="col-sm-8">
            [IF [!D::Id!]&&[!D::Image!]]
                <img src="/[!D::Image!].mini.500x200.jpg" />
            [/IF]
            <input type="hidden" class="ImageInput" name="Image" value="[!D::Image!]"/>
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
    <label class="col-sm-4 control-label">Date de début de promotion</label>
    <div class="col-sm-8">
        <input type="text" class="form-control datepicker" id="inputDateDebut" placeholder="Sélectionnez une date" value="[IF [!D::DateDebutPromo!]][DATE d/m/Y][!D::DateDebutPromo!][/DATE][ELSE][DATE d/m/Y][!TMS::Now!][/DATE][/IF]"  name="DateDebutPromo">
    </div>
  </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Date de fin de promotion</label>
        <div class="col-sm-8">
            <input type="text" class="form-control datepicker" id="inputDateFin" placeholder="Sélectionnez une date" value="[IF [!D::DateFinPromo!]][DATE d/m/Y][!D::DateFinPromo!][/DATE][ELSE][DATE d/m/Y][!TMS::Now!][/DATE][/IF]"  name="DateFinPromo">
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
