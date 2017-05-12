<!--<div id="datepicker"></div>-->

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

    console.log('change date',e);
    $.ajax({
        url: "/[!Query!]/getDispo.json",
        data: {
            date: (e)?Math.floor(new Date(e.date).getTime()/1000):Math.floor(new Date().getTime()/1000)
        },
        method: 'POST'
    }).done(function (response) {
        //mise à jour des cours
        $('.horaire-tennis').removeClass('disabled');
        $('.horaire-tennis').removeClass('warning');
        console.log('RESPONSE',response);
        for ( var r in response.data){
            for (var j=response.data[r].HeureDebut; j<=response.data[r].HeureFin; j++ ){
                console.log('test time', r, j ,parseInt(response.data[r].HeureDebut),parseInt(response.data[r].MinuteDebut),'FIN',parseInt(response.data[r].HeureFin),parseInt(response.data[r].MinuteFin));
                //calcul 30 min avant
                if (parseInt(response.data[r].MinuteDebut)==0){
                    $('#date-' + response.data[r].Court + '-' + (parseInt(response.data[r].HeureDebut)-1) + '-30').addClass('warning');
                }else $('#date-' + response.data[r].Court + '-' + parseInt(response.data[r].HeureDebut) + '-00').addClass('warning');

                if ( (j==parseInt(response.data[r].HeureDebut) && parseInt(response.data[r].MinuteDebut)==0 )
                        || (j<parseInt(response.data[r].HeureFin)&&j>parseInt(response.data[r].HeureDebut))
                        || (j==parseInt(response.data[r].HeureFin) && parseInt(response.data[r].MinuteFin)==30)) {
                    console.log('desactivation '+'#date-' + response.data[r].Court + '-' + parseInt(j) + '-00')
                    $('#date-' + response.data[r].Court + '-' + parseInt(j) + '-00').addClass('disabled');
                }
                if (j < parseInt(response.data[r].HeureFin)){
                    console.log('desactivation '+'#date-' + response.data[r].Court + '-' + parseInt(j) + '-30')
                    $('#date-'+response.data[r].Court+'-'+parseInt(j)+'-30').addClass('disabled');
                }
            }
        }
    }).fail(function (reponse){
        console.log('erreur ajax',reponse);
    });

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

    [COUNT [!Query!]/Court|Nb]
    [IF [!Nb!]>=4]
        [!NbCol:=4!]
    [ELSE]
        [!NbCol:=[!Nb!]!]
    [/IF]
    [STORPROC [!Query!]/Court|C|0|10]
    <form action="/[!Lien!]/Reserver" method="POST">
    <input type="hidden" name="Date" value="" class="dateform" />
    <input type="hidden" name="Court" value="[!C::Id!]" id="courform" />
    <div class="col-md-[!12:/[!NbCol!]!]">
        <h3>[!C::Titre!]</h3>
        [STORPROC 12|H]
            <div class="row">
                <div class="col-xs-6 horaire-wrapper left">
                    <input type="submit" class="horaire-tennis" id="date-[!C::Id!]-[!H:+9!]-00" name="HeureDebut" value="[!H:+9!]:00" />
                </div>
                <div class="col-xs-6 horaire-wrapper right">
                    <input type="submit" class="horaire-tennis" id="date-[!C::Id!]-[!H:+9!]-30" name="HeureDebut" value="[!H:+9!]:30" />
                </div>
            </div>

        [/STORPROC]
    </div>
    </form>
    [/STORPROC]
</div>
<style>
    @media screen and (min-width: 768px) {
        .modal-dialog {
            width: [!NbCol:*2!]0%;
        }
    }
</style>
