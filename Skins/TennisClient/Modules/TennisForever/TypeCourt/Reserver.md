[IF [!Action!]=Payer]

    //création de la réservation
    [!RES:=[!Module::TennisForever::createReservation([!Date!],[!Court!],[!HeureDebut!],[!ServiceDuree!])!]!]
    [!RES::setPartenaires([!Partenaire!])!]
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


[!DateDeb:=[!Date:+[!HeureDebut:*3600!]!]!]
[!Client:=[!Module::TennisForever::getCurrentClient()!]!]

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
            [STORPROC TennisForever/Court/[!Court!]/Service/Type=Reservation|S]
            <option value="[!S::Id!]" [IF [!ServiceDuree!]=[!S::Id!]]selected="selected"[/IF]>[!S::Titre!] ( [!Utils::getPrice([!S::getTarif(0,[!DateDeb!],[!DateDeb:+3600!])!])!] € )</option>
            [/STORPROC]
        </select>
        <h2>Le(s) partenaire</h2>
        <div class="form-inline" id="Partenaires">
        </div>
        <br />
        <button type="submit" class="btn btn-default" id="PartenaireAjout"><span class="glyphicon glyphicon-plus"></span>Ajouter un Partenaire</button>
        <h3>Choisissez les services annexes</h3>
        [STORPROC TennisForever/Court/[!Court!]/Service/Type=Produit|S]
            [MODULE TennisForever/Service/Mini?S=[!S!]]
        [/STORPROC]
        <input type="submit" name="Action" value="Payer" class="btn btn-success btn-lg btn-block" />
    </div>
</div>
</form>
<script>
    $('#PartenaireAjout').on('click',addPartenaire);
    var partenaire= 0;
    function addPartenaire(e,nom,email,prenom) {
        if (!nom)nom='';
        if (!email)email='';
        if (!prenom)prenom='';
        if (e)
            e.preventDefault();
        partenaire++;
        console.log('Ajout partenaire',partenaire);
        $('<div id="partenaire-'+partenaire+'" class="partenaire-wrapper" style="overflow: hidden;">'+
            '<h5>Partenaire '+partenaire+'</h5>'+
            '<div class="form-group">'+
                '<label class="sr-only" for="partenaireEmail'+partenaire+'">Email address</label>'+
                '<input type="email" class="form-control" id="partenaireEmail'+partenaire+'" placeholder="Adresse email" name="Partenaire['+partenaire+'][Email]" value="'+email+'" />'+
            '</div>'+
            '<div class="form-group">'+
                '<label class="sr-only" for="partenaireNom'+partenaire+'">Password</label>'+
                '<input type="text" class="form-control" id="partenaireNom'+partenaire+'" placeholder="Nom" name="Partenaire['+partenaire+'][Nom]" value="'+nom+'" />'+
            '</div>'+
            [IF [!Client::isSubscriber()!]]
            '<span style="color: #fff;"> OU </span>'+
            '<div class="form-group">'+
                '<label class="sr-only" for="partenaireNom'+partenaire+'">Membre</label>'+
                '<select class="form-control" id="partenaireNom'+partenaire+'" placeholder="Nom" name="Partenaire['+partenaire+'][Client]">'+
            '       <option value=""></option>'+
                    [STORPROC TennisForever/Client/Abonne=1|C]
                '       <option value="[!C::Id!]">[!C::Nom!] [!C::Prenom!]</option>'+
                    [/STORPROC]
                '</select>'+
            '</div>'+
            [/IF]
            '<div class="form-group pull-right">'+
                '<a class="btn btn-danger PartenaireSupp" onclick="suppPartenaire(this)"><span class="glyphicon glyphicon-minus"></span></a>'+
            '</div>'+
         '</div>').appendTo('#Partenaires');
    }
    function suppPartenaire(el) {
        console.log('supp partenaire',partenaire);
        $('#partenaire-'+partenaire).detach();
        partenaire--;
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
