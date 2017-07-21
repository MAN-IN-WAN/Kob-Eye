[!DateDeb:=[!Date:+[!HeureDebut:*3600!]!]!]
[!Client:=[!Module::Reservations::getCurrentClient()!]!]
[STORPROC Reservations/TypeCourt/Court/[!Court!]|TC|0|1][/STORPROC]
[STORPROC Reservations/Court/[!Court!]|Co|0|1][/STORPROC]
[IF [!Action!]=Réserver]

    //création de la réservation
    [!RES:=[!Module::Reservations::createReservation([!Date!],[!Court!],[!HeureDebut!],[!ServiceDuree!])!]!]
    [SWITCH [!TC::GestionInvite!]|=]
        [CASE Quantitatif]
            [!RES::setNombrePartenaires([!NombreParticipant:-1!])!]
        [/CASE]
        [CASE Nominatif]
            [IF [!PaiementParticipant!]=1]
            [METHOD RES|Set]
                [PARAM]PaiementParticipant[/PARAM]
                [PARAM]1[/PARAM]
            [/METHOD]
            [/IF]
            [!RES::setPartenairesBis([!Partenaire!])!]
        [/CASE]
    [/SWITCH]


    [!RES::setProduits([!Service!])!]

    [IF [!RES::Verify()!]]
        [COOKIE Set|RES|RES]
        <div class="alert alert-success">Création de la réservation en cours ....</div>
        //[!RES::Save()!]
        [REDIRECT][!Sys::getMenu(Reservations/Reservation)!]/Reserver[/REDIRECT]
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



<form action="" method="POST">
<input type="hidden" name="Date" value="[!Date!]" />
<input type="hidden" name="Court" value="[!Court!]" />
<input type="hidden" name="HeureDebut" value="[!HeureDebut!]" />
<div class="row">
    <div class="col-md-12">
        <h5>Votre réservation pour le [DATE d/m/Y][!Date!][/DATE] A partir  de [!HeureDebut!]</h5>
        <h2>Sélectionnez la durée</h2>
        <select name="ServiceDuree"  class="form-control">
            <option value=""> -- choisissez une durée -- </option>
            [STORPROC Reservations/Court/[!Court!]/Service/Type=Reservation|S]
                <option value="[!S::Id!]" [IF [!ServiceDuree!]=[!S::Id!]]selected="selected"[/IF]>[!S::Titre!] -  [!Utils::getPrice([!S::getTarif([!Client!],[!DateDeb!],[!DateDeb:+3600!])!])!] €</option>
            [/STORPROC]
            [STORPROC Reservations/TypeCourt/Court/[!Court!]|TC]
                [STORPROC Reservations/TypeCourt/[!TC::Id!]/Service/Type=Reservation|S]
                    <option value="[!S::Id!]" [IF [!ServiceDuree!]=[!S::Id!]]selected="selected"[/IF]>[!S::Titre!] -  [!Utils::getPrice([!S::getTarif([!Client!],[!DateDeb!],[!DateDeb:+3600!])!])!] €</option>
                [/STORPROC]
            [/STORPROC]
    </select>
    <h2>Les participants</h2>
[SWITCH [!TC::GestionInvite!]|=]
    [CASE Quantitatif]
    <div class="well" style="overflow:hidden">
        <div class="row">
            <div class="col-xs-7">
                <h4>Sélectionnez le nombre de participants total</h4>
            </div>
            [IF [!NombreParticipant!]=][!NombreParticipant:=1!][/IF]
            <div class="col-xs-5">
                <a class="btn btn-danger pull-right" onclick="on[!S::Id!]Plus()"><span class="glyphicon glyphicon-plus"></span></a>
                <input type="text" class=" pull-right" style="width: 34px;height: 34px;text-align: center;" name="NombreParticipant" id="NombreParticipant" value="[!NombreParticipant!]"/>
                <a class="btn btn-danger pull-right" onclick="on[!S::Id!]Moins()"><span class="glyphicon glyphicon-minus"></span></a>
                <script>
                    function on[!S::Id!]Plus(){
                        if ($('#NombreParticipant').val()<100)
                            $('#NombreParticipant').val(parseInt($('#NombreParticipant').val())+1);
                    }
                    function on[!S::Id!]Moins(){
                        if ($('#NombreParticipant').val()>1)
                            $('#NombreParticipant').val(parseInt($('#NombreParticipant').val())-1);
                    }
                </script>
            </div>
        </div>
    </div>
    [/CASE]
    [CASE Nominatif]
    <div class="form-group group-PaiementParticipant row">
        <label class="col-sm-7 control-label">Paiement des participants
            <p style="font-weight: normal;">Chaque particpant recevra un email lui permettant de régler sa propre participation.</p>
        </label>
        <div class="col-sm-5" style="text-align: right;">
            <input type="checkbox" name="PaiementParticipant" [IF [!PaiementParticipant!]]checked="checked"[/IF] class="switch pull-right" value="1">
        </div>
    </div>
    <h5>Sélectionnez les participants</h5>
    <div id="Partenaires">
    </div>

    <div class="form-group row">
        <div class="col-xs-10">
        <label class="sr-only" for="SelectPartenaire">Membre</label>
        <select class="form-control" id="SelectPartenaire" >
           <option value="">-- Liste des partenaires enregistrés --</option>
            [STORPROC Reservations/Client/[!Client::Id!]/Partenaire|P|0|500|Nom|ASC]
            <option value="[!P::Id!]" data-nom="[!P::Nom!]" data-prenom="[!P::Prenom!]" data-details="[!P::Details!]" data-email="[!P::Email!]">[!P::Nom!] [!P::Prenom!]</option>
            [/STORPROC]
        </select>
        </div>
        <div class="col-xs-2">
        <div class="form-group pull-right">
        <a class="btn btn-danger" id="PartenaireAdd"><span class="glyphicon glyphicon-plus"></span></a>
        </div>
        </div>
    </div>

    [/CASE]
[/SWITCH]
    [STORPROC Reservations/Court/[!Court!]/Service/Type=Produit|S]
        <h3>Choisissez les services annexes / compléter votre réservation</h3>
        [LIMIT 0|100]
            [MODULE Reservations/Service/Mini?S=[!S!]]
        [/LIMIT]
    [/STORPROC]
    <input type="submit" name="Action" value="Réserver" class="btn btn-success btn-lg btn-block" />
</div>
</div>
        </form>
<script>
$('#PartenaireAdd').on('click',function () {
    //récupération du partenaire sélectionné

    var id = $('#SelectPartenaire option:selected').val();
    if (!id||$('#part-'+id)[0])return;
    var nom = $('#SelectPartenaire option:selected').attr('data-nom');
    var prenom = $('#SelectPartenaire option:selected').attr('data-prenom');
    var details = $('#SelectPartenaire option:selected').attr('data-details');
    var email = $('#SelectPartenaire option:selected').attr('data-email');
    addPartenaire(id,nom,email,prenom,details);
});
var partenaire= 0;
function addPartenaire(id,nom,email,prenom,details) {
    [IF [!Co::Capacite!]]
        [!Part:=[!Co::Capacite!]-1!]
        if(partenaire >= [!Part!]) return false;
        if(partenaire == [!Part!] - 1) $('#PartenaireAjout').hide();
    [/IF]
    if (!nom)nom='';
    if (!email)email='';
    if (!prenom)prenom='';
    if (!details)details='';
    partenaire++;
    console.log('Ajout partenaire',partenaire);
    $('#Partenaires').append($('<div class="btn-tennis del" data-nom="'+nom+'" data-id="'+id+'" data-details="'+details+'" id="part-'+id+'" onclick="suppPartenaire('+id+')">'+
        '<input type="hidden" name="Partenaire['+partenaire+'][Details]" value="'+details+'" />'+
        '<input type="hidden" name="Partenaire['+partenaire+'][Id]" value="'+id+'" />'+
        '<input type="hidden" name="Partenaire['+partenaire+'][Nom]" value="'+nom+'" />'+
        '<input type="hidden" name="Partenaire['+partenaire+'][Prenom]" value="'+prenom+'" />'+
        '<input type="hidden" name="Partenaire['+partenaire+'][Email]" value="'+email+'" />'+
        '<a class="btn btn-danger pull-right"><span class="glyphicon glyphicon-minus"></span></a>'+
        '<h3>'+nom+' '+prenom+'</h3>'+
        '<p>'+details+'</p>'+
        '</div>'));'[!P::Id!]'
}
function suppPartenaire(id) {
    console.log('supp partenaire',id);
    $('#part-'+id).detach();
    partenaire--;
    $('#PartenaireAjout').show();
}
$(
        function () {
            [IF [!Partenaire!]]
                [STORPROC [!Partenaire!]|P]
                    addPartenaire('[!P::Id!]', '[UTIL ADDSLASHES][!P::Nom!][/UTIL]', '[UTIL ADDSLASHES][!P::Email!][/UTIL]', '[UTIL ADDSLASHES][!P::Prenom!][/UTIL]','[UTIL ADDSLASHES][!P::Details!][/UTIL]');
                [/STORPROC]
            [/IF]
        }
);
</script>