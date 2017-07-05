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
    <h5>Indiquez les nom des paticipants</h5>
    <div class="form-inline" id="Partenaires">
    </div>
    <br />
    <button type="submit" class="btn btn-default" id="PartenaireAjout"><span class="glyphicon glyphicon-plus"></span>Ajouter un Participant</button>
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
                        [STORPROC Reservations/Client/[!Client::Id!]/Partenaire|P|0|500|Nom|ASC]
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