[IF [!SaveDate!]=Enregistrer]
        //PLAGE HORAIRE
        [METHOD D|Set][PARAM]HeureDebut[/PARAM][PARAM][!HeureDebut!][/PARAM][/METHOD]
        [METHOD D|Set][PARAM]HeureFin[/PARAM][PARAM][!HeureFin!][/PARAM][/METHOD]
        [METHOD D|Save][/METHOD]

    [REDIRECT][!Sys::getMenu(Pharmacie/PlageHoraire)!][/REDIRECT]
[/IF]
<h1>Plage Horaire [!D::HeureDebut!] - [!D::HeureFin!]</h1>
<form class="form-horizontal" method="POST">
  <div class="form-group">
    <label class="col-sm-4 control-label">HeureDebut</label>
    <div class="col-sm-8">
        <select class="form-control" id="HeureDebut"  name="HeureDebut">
            [IF [!D::HeureDebut!]]<option value="[!D::HeureDebut!]">[!D::HeureDebut!]h00</option>[/IF]
            <option value="6">6h00</option>
            <option value="7">7h00</option>
            <option value="8">8h00</option>
            <option value="9">9h00</option>
            <option value="10">10h00</option>
            <option value="11">11h00</option>
            <option value="12">12h00</option>
            <option value="13">13h00</option>
            <option value="14">14h00</option>
            <option value="15">15h00</option>
            <option value="16">16h00</option>
            <option value="17">17h00</option>
            <option value="18">18h00</option>
            <option value="19">19h00</option>
            <option value="20">20h00</option>
        </select>
    </div>
  </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">HeureFin</label>
        <div class="col-sm-8">
            <select class="form-control" id="HeureFin"  name="HeureFin">
                [IF [!D::HeureFin!]]<option value="[!D::HeureFin!]">[!D::HeureFin!]h00</option>[/IF]
                <option value="6">6h00</option>
                <option value="7">7h00</option>
                <option value="8">8h00</option>
                <option value="9">9h00</option>
                <option value="10">10h00</option>
                <option value="11">11h00</option>
                <option value="12">12h00</option>
                <option value="13">13h00</option>
                <option value="14">14h00</option>
                <option value="15">15h00</option>
                <option value="16">16h00</option>
                <option value="17">17h00</option>
                <option value="18">18h00</option>
                <option value="19">19h00</option>
                <option value="20">20h00</option>
            </select>
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
