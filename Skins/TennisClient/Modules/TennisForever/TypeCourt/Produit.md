[IF [!Action!]=Payer]

        //création de la réservation
        [!RES:=[!Module::TennisForever::createReservation([!Date!],[!Court!],0,0)!]!]
        //[!RES::setPartenaires([!Partenaire!])!]
        [!RES::setProduits([!Service!])!]

        [IF [!RES::Verify()!]]
<div class="alert alert-success">Création de la réservation en cours ....</div>
        [!RES::Save()!]
        [REDIRECT][!Sys::getMenu(TennisForever/Reservation)!]/[!RES::Id!][/REDIRECT]
        [ELSE]
        //gestion erreurs
        [IF [!RES::Error!]]
<div class="alert alert-warning">
<h4>Impossible d'enregistrer la réservation:</h4>
<ul>
    [STORPROC [!RES::Error!]|E]
    [!Error_[!E::Prop!]:=1!]
    <li>[!E::Message!]</li>
    [/STORPROC]
</ul>
</div>
        [/IF]
        [/IF]
        [/IF]


<div class="row">
<div class="col-md-12">
    <h3>Sélectionnez une date</h3>
    <div class="input-group date">
        <input type="text" class="form-control" id="datepicker" value="[DATE d/m/Y][!TMS::Now!][/DATE]"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
    </div>
</div>
</div>

<script>
$('#datepicker').datepicker({
    format:"dd/mm/yyyy",
    language: "fr"
}).on('changeDate',onChangeDate);

function onChangeDate(e) {
    //Mise à jour de la date
    var today = new Date();
    today.setHours(0);
    today.setMinutes(0);
    today.setSeconds(0);
    console.log('DATE '+Math.floor(today.getTime()/1000));
    $('.dateform').val((e)?Math.floor(new Date(e.date).getTime()/1000):Math.floor(today.getTime()/1000));
}
$(function () {
    onChangeDate();
});
</script>
<div class="row">
<form action="" method="POST">
    <input type="hidden" name="Date" value="" class="dateform" />
    [STORPROC [!Query!]/Court|C|0|1]
    <input type="hidden" name="Court" value="[!C::Id!]" id="courform" />
    <div class="col-md-12">
        <h3>Choisissez les services</h3>
        [STORPROC TennisForever/Court/[!C::Id!]/Service/Type=Reservation|S]
        [MODULE TennisForever/Service/Mini?S=[!S!]]
        [/STORPROC]
        <input type="submit" name="Action" value="Payer" class="btn btn-success btn-lg btn-block" />
    </div>
    [/STORPROC]
</form>
</div>