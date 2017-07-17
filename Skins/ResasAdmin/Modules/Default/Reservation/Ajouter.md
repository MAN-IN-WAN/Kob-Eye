//validation du formulaire
[IF [!action!]=Enregistrer]
    [OBJ Reservations|Reservation|O]
    //Engregistrement des champs
    [STORPROC [!O::getElementsByAttribute(form,,1)!]|P]
        [SWITCH [!P::type!]|=]
            [CASE fkey]
                [!O::resetParents([!P::objectName!])!]
                [STORPROC [!Form_[!P::name!]!]|V]
                    [METHOD O|AddParent]
                        [PARAM][!P::objectModule!]/[!P::objectName!]/[!V!][/PARAM]
                    [/METHOD]
                [/STORPROC]
            [/CASE]
            [DEFAULT]
                [METHOD O|Set]
                    [PARAM][!P::name!][/PARAM]
                    [PARAM][!Form_[!P::name!]!][/PARAM]
                [/METHOD]
            [/DEFAULT]
        [/SWITCH]
    [/STORPROC]
    //enregistrement de la position
    [METHOD O|AddParent]
        [PARAM][!Query!][/PARAM]
    [/METHOD]
    //verfication de la saisie
    [IF [!O::Verify()!]]
        <div class="alert alert-success">OK bien enregistré</div>
        [!O::setPartenairesBis([!Partenaire!])!]
        [METHOD O|Save][/METHOD]
        [REDIRECT][!Sys::getMenu(Reservations/Reservation)!][/REDIRECT]
    [ELSE]
        <div class="alert alert-danger">
            <b>La saisie est incorrecte:</b>
            <ul>
                [STORPROC [!O::Error!]|E]
                <li>[!E::Message!]</li>
                [!Error_[!E::Prop!]:=1!]
                [/STORPROC]
            </ul>
        </div>
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

        $('.horaire-wrapper:last-child .horaire-tennis').addClass('warning');
        console.log('RESPONSE',response);

        for ( var r in response.data){
            if(response.data[r].HeureFin == 0) response.data[r].HeureFin = 24;
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

    [COUNT Reservations/Court|Nb]
    [IF [!Nb!]>=4]
        [!NbCol:=6!]
    [ELSE]
        [!NbCol:=[!Nb!]!]
    [/IF]
    [STORPROC Reservations/Court|C|0|10]
    [STORPROC Reservations/TypeCourt/Court/[!C::Id!]|TC][/STORPROC]
    [!S:=[!TC::getOneChild(Service)!]!]

    <input type="hidden" name="Date" value="" class="dateform" />
    <input type="hidden" name="Court" value="[!C::Id!]" id="courform" />
    <div class="col-md-[!12:/[!NbCol!]!]">
        <h3>[!C::Titre!]</h3>
//        [STORPROC 12|H]
//           <div class="row">
//                <div class="col-xs-6 horaire-wrapper left">
//                    <input type="submit" class="horaire-tennis" id="date-[!C::Id!]-[!H:+9!]-00" name="HeureDebut" value="[!H:+9!]:00" />
 //               </div>
 //               <div class="col-xs-6 horaire-wrapper right">
 //                   <input type="submit" class="horaire-tennis" id="date-[!C::Id!]-[!H:+9!]-30" name="HeureDebut" value="[!H:+9!]:30" />
 //               </div>
 //           </div>
//
//        [/STORPROC]
        <div class="row">
        [STORPROC [!S::getHoraires()!]|H]
            [!splitH:=[!Utils::explode(:,[!H!])!]!]
            <div class="col-xs-6 horaire-wrapper">
                <a class="horaire-tennis" id="date-[!C::Id!]-[!splitH::0!]-[!splitH::1!]" name="HeureDebut" href="#form-resa" onclick="setResa('[!splitH::0!]','[!splitH::1!]',[!C::Id!])">[!H!]</a>
            </div>
            [IF [!Utils::modulo([!Key!],2)!]=1]
            </div>
            <div class="row">
            [/IF]
        [/STORPROC]
        </div>
    </div>
    [/STORPROC]
    <script>
        function setResa(heure,minute,court){
            //sélection du court
            $('#Form_CourtId').val(court);
            //défintion de la date
            var datedeb = $('#datepicker').val();
            $('#datetimepicker1 input').val(datedeb+' '+heure+':'+minute);
            $('#datetimepicker2 input').val(datedeb+' '+(parseInt(heure)+1)+':'+minute);
            //définition du client
            $('#Form_ClientId').val(10);
            //définition de la durée
            $('#Form_ServiceId').val(2);
        }
    </script>
</div>
<style>
    @media screen and (min-width: 768px) {
        .modal-dialog {
            width: [!NbCol:*2!]0%;
        }
    }
</style>
<form method="post" id="form-form" class="standard">
<div id="form-resa">

    <div class="form-group group-ServiceId ">
        <label class="col-sm-5 control-label">Type de réservation</label>
        <div class="col-sm-7">
            <select class="form-control" id="Form_ServiceId" name="Form_ServiceId[]">
                [STORPROC Reservations/Service|C]
                <option value="[!C::Id!]" data-duration="[!C::Duree!]">[!C::Titre!]</option>
                [/STORPROC]
            </select>
        </div>
    </div>

    <div class="form-group group-DateDebut ">
            <label class="col-sm-5 control-label">Date de début</label>
            <div class="col-sm-7">
                <div class="input-group date" id='datetimepicker1'>
                    <input type="text" class="form-control datepicker" value="" name="Form_DateDebut"  id="Form_DateDebut" />
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>

            </div>
        </div>
        <script type="text/javascript">
            $(function () {
                $('#datetimepicker1').datetimepicker({
                    locale: 'fr'
                });
            });
        </script>

        <div class="form-group group-DateFin ">
            <label class="col-sm-5 control-label">Date de fin</label>
            <div class="col-sm-7">
                <div class="input-group date" id='datetimepicker2'>
                    <input type="text" class="form-control datepicker" value="" name="Form_DateFin"  id="Form_DateFin" />
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>

            </div>
        </div>
        <script type="text/javascript">
            $(function () {
                $('#datetimepicker2').datetimepicker({
                    locale: 'fr'
                });
            });
        </script>

        <div class="form-group group-Valide ">
            <label class="col-sm-5 control-label">Validité</label>
            <div class="col-sm-7">
                <input type="checkbox" name="Form_Valide"  class="switch" value="1"  checked="checked">
            </div>
        </div>

        <div class="form-group group-ClientId ">
            <label class="col-sm-5 control-label">Client</label>
            <div class="col-sm-7">
                <select class="form-control" id="Form_ClientId" name="Form_ClientId[]">
                    [STORPROC Reservations/Client/Actif=1|C]
                    <option value="[!C::Id!]">[!C::Nom!] [!C::Prenom!]</option>
                    [/STORPROC]
                </select>
            </div>
        </div>

        <div class="form-group group-CourtId ">
            <label class="col-sm-5 control-label">Terrain</label>
            <div class="col-sm-7">
                <select class="form-control" id="Form_CourtId" name="Form_CourtId[]">
                    [STORPROC Reservations/Court|C]
                    <option value="[!C::Id!]">[!C::Titre!]</option>
                    [/STORPROC]
                </select>
            </div>
        </div>

</div>
<h5>Indiquez les nom des participants</h5>
<div class="form-inline" id="Partenaires">
    <p class="precision">Si vous ne sélectionnez pas de partenaire déjà créé, merci de renseigner au moins le nom et le prénom.</p>
</div>
<br />
<button type="submit" class="btn btn-default" id="PartenaireAjout"><span class="glyphicon glyphicon-plus"></span>Ajouter un Participant</button>
<script>
    //valeurs par défaut
    var datenow = new Date();
    $('#Form_DateDebut').val(pad2(datenow.getUTCDate())+'/'+pad2(datenow.getUTCMonth()+1)+'/'+pad2(datenow.getUTCFullYear())+' '+pad2(datenow.getHours())+':'+pad2(0));
    $('#Form_DateFin').val(pad2(datenow.getUTCDate())+'/'+pad2(datenow.getUTCMonth()+1)+'/'+pad2(datenow.getUTCFullYear())+' '+pad2(datenow.getHours()+1)+':'+pad2(0));

    function pad2(number) {
        return (number < 10 ? '0' : '') + number
    }

    $('#Form_ServiceId').on('click',function () {
        var duration = $('#Form_ServiceId option:selected') ? $('#Form_ServiceId option:selected').attr('data-duration'):0;
        //calcul de la date de fin
        Date.prototype.fromString = function(str, ddmmyyyy) {
            var m = str.match(/(\d+)(-|\/)(\d+)(?:-|\/)(?:(\d+)\s+(\d+):(\d+)(?::(\d+))?(?:\.(\d+))?)?/);
            console.log('regexp',m);
            if(m[2] == "/"){
                if(ddmmyyyy === false)
                    return new Date(+m[4], +m[1] - 1, +m[3], m[5] ? +m[5] : 0, m[6] ? +m[6] : 0, m[7] ? +m[7] : 0, m[8] ? +m[8] * 100 : 0);
                return new Date(+m[4], +m[3] - 1, +m[1], m[5] ? +m[5] : 0, m[6] ? +m[6] : 0, m[7] ? +m[7] : 0, m[8] ? +m[8] * 100 : 0);
            }
            return new Date(+m[1], +m[3] - 1, +m[4], m[5] ? +m[5] : 0, m[6] ? +m[6] : 0, m[7] ? +m[7] : 0, m[8] ? +m[8] * 100 : 0);
        }

        var datedebut = new Date();
        datedebut = datedebut.fromString($('#Form_DateDebut').val());
        datedebut.setMinutes(datedebut.getMinutes()+duration);
        if (datedebut.getHours()<4)datedebut.setUTCDate(datedebut.getUTCDate()+1);
        console.log('date debut',datedebut);
        $('#Form_DateFin').val(pad2(datedebut.getUTCDate())+'/'+pad2(datedebut.getUTCMonth()+1)+'/'+pad2(datedebut.getUTCFullYear())+' '+pad2(datedebut.getHours())+':'+pad2(datedebut.getMinutes()));
        console.log('date debut',datedebut);
    });
    $('#PartenaireAjout').on('click',addPartenaire);
    var partenaire= 0;
    function addPartenaire(e,nom,email,prenom) {
        [IF [!Co::Capacite!]]
        [!Part:=[!Co::Capacite!]-1!]
        if(partenaire >= [!Part!]) return false;
        if(partenaire == [!Part!] - 1) $('#PartenaireAjout').hide();
        [/IF]
        if (!nom)nom='';
        if (!email)email='';
        if (!prenom)prenom='';
        if (e)
            e.preventDefault();
        partenaire++;
        console.log('Ajout partenaire',partenaire);
        $('<div id="partenaire-'+partenaire+'" class="partenaire-wrapper" style="overflow: hidden;">'+
            '<h5>Paticipant '+partenaire+'</h5>'+
            '<div class="form-group">'+
            '<label class="sr-only" for="partenaireEmail'+partenaire+'">Email address</label>'+
            '<input type="email" class="form-control" id="partenaireEmail'+partenaire+'" placeholder="Adresse email" name="Partenaire['+partenaire+'][Email]" value="'+email+'" />'+
            '</div><br/>'+
            '<div class="form-group">'+
            '<label class="sr-only" for="partenaireNom'+partenaire+'">Nom</label>'+
            '<input type="text" class="form-control" id="partenaireNom'+partenaire+'" placeholder="Nom" name="Partenaire['+partenaire+'][Nom]" value="'+nom+'" />'+
            '</div>'+
            '<div class="form-group">'+
            '<label class="sr-only" for="partenaireNom'+partenaire+'">Prenom</label>'+
            '<input type="text" class="form-control" id="partenairePrenom'+partenaire+'" placeholder="Prenom" name="Partenaire['+partenaire+'][Prenom]" value="'+prenom+'" />'+
            '</div>'+

            '<span style="color: #fff;"> OU </span>'+
            '<div class="form-group">'+
            '<label class="sr-only" for="partenaireNom'+partenaire+'">Membre</label>'+
            '<select class="form-control" id="partenaireNom'+partenaire+'" placeholder="Nom" name="Partenaire['+partenaire+'][Client]">'+
            '       <option value="">-- Liste des partenaires enregistrés --</option>'+
//                        [STORPROC Reservations/Client/Actif=1|C|0|500|Nom|ASC]
//                            [IF [!C::Id!]!=[!Client::Id!]]
//                        '<option value="[!C::Id!]">[!C::Nom!] [!C::Prenom!]</option>'+
//                            [/IF]
//                        [/STORPROC]
            [STORPROC Reservations/Client/10/Partenaire|P|0|500|Nom|ASC]
        '<option value="[!P::Id!]">[!P::Nom!] [!P::Prenom!]</option>'+
        [/STORPROC]

        '</select>'+
        '</div>'+

        '<div class="form-group pull-right">'+
        '<a class="btn btn-danger PartenaireSupp" onclick="suppPartenaire(this)"><span class="glyphicon glyphicon-minus"></span></a>'+
        '</div>'+
        '</div>').appendTo('#Partenaires');
    }
    function suppPartenaire(el) {
        console.log('supp partenaire',partenaire);
        $('#partenaire-'+partenaire).detach();
        partenaire--;
        $('#PartenaireAjout').show();
    }
    $(
        function () {
            [IF [!Partenaire!]]
            [STORPROC [!Partenaire!]|P]
            addPartenaire(null, '[!P::Nom!]', '[!P::Email!]', '[!P::Prenom!]');
            [/STORPROC]
            [ELSE]
            addPartenaire();
            [/IF]
        }
    );
</script>
    <div class="btn-group" role="group">
        <a  class="btn btn-danger" data-dismiss="modal" id="form-annuler" href="/GestionReservation">Annuler</a>
        <input type="submit" class="btn btn-success" data-form="" id="form-save" value="Enregistrer" name="action"/>
    </div>

</form>
