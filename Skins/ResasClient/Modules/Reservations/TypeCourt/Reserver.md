<form action="" method="POST">
    //[IF [!Date!]=+[!Court!]=+[!HeureDebut!]=]
    //    [REDIRECT]Reserver[/REDIRECT]
    //[/IF]
    [IF [!Date!]=]
        [REDIRECT]Reserver[/REDIRECT]
    [/IF]
    [IF [!Court!]=]
        [REDIRECT]Reserver[/REDIRECT]
    [/IF]
    [IF [!HeureDebut!]=]
        [REDIRECT]/Reserver[/REDIRECT]
    [/IF]


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

    <div id="regisPart" class="form-group row mode active">
        <div class="col-xs-12 pitch">
                Choisissez parmi les partenaires avec lesquels vous avez déjà joué <a href="#notes">(*)</a>. Si vous souhaitez créer un nouveau partenaire ou completer votre groupe avec un autre membre du Dome du Foot, utilisez les boutons prévus à cet effet.
        </div>
        <div class="col-xs-10">
        <label class="sr-only" for="SelectPartenaire">Partenaire enregistré</label>
        <select class="form-control" name="SelectPartenaire" id="SelectPartenaire" >
           <option value="">-- Liste de mes partenaires déjà enregistrés --</option>
            [STORPROC Reservations/Client/[!Client::Id!]/Partenaire|P|0|500|Nom|ASC]
            <option value="[!P::Id!]" data-nom="[!P::Nom!]" data-prenom="[!P::Prenom!]" data-details="[!P::Details!]" data-email="[!P::Email!]" data-type="partner" [IF [!SelectPartenaire!]="[!P::Id!]"]selected[/IF]>[!P::Nom!] [!P::Prenom!]</option>
            [/STORPROC]
        </select>
        </div>
        <div class="col-xs-2">
        <div class="form-group pull-right">
        <a class="btn btn-danger" id="PartenaireAdd"><span class="glyphicon glyphicon-plus"></span></a>
        </div>
        </div>
    </div>

    <div id="newPart" class="form-group row mode">
        <div class="col-xs-12 pitch">
            Créez un nouveau partenaire <a href="#notes">(*)(**)</a>. Si vous souhaitez completer votre groupe avec un partenaire avec lequel vous avez déjà joué ou un autre membre du Dome du Foot, utilisez les boutons prévus à cet effet.
        </div>
        <div class="col-xs-10">
            <div class="form-group">
                <label class="sr-only" for="partenaireEmail">Email address</label>
                <input type="email" class="form-control" id="partenaireEmail" placeholder="Adresse email" name="Temp[Email]" value="" />
                </div><br/>
            <div class="form-group">
                <label class="sr-only" for="partenaireNom">Nom</label>
                <input type="text" class="form-control" id="partenaireNom" placeholder="Nom" name="Temp[Nom]" value="" />
            </div>
            <div class="form-group">
                <label class="sr-only" for="partenairePrenom">Prenom</label>
                <input type="text" class="form-control" id="partenairePrenom" placeholder="Prenom" name="Temp[Prenom]" value="" />
            </div>
        </div>
        <div class="col-xs-2">
            <div class="form-group pull-right">
                <a class="btn btn-danger" id="PartenaireAddNew"><span class="glyphicon glyphicon-plus"></span></a>
            </div>
        </div>
    </div>

    <div id="otherPart" class="form-group row mode">
        <div class="col-xs-12 pitch">
            Choisissez parmi les membres du dome du foot <a href="#notes">(*)</a>. Si vous souhaitez créer un nouveau partenaire ou completer votre groupe avec un partenaire avec lequel vous avez déjà joué, utilisez les boutons prévus à cet effet.
        </div>
        <div class="col-xs-10">
            <label class="sr-only" for="SelectPartenaire">Autre membre inscrit</label>
            <select class="form-control" name="SelectMember" id="SelectMember" >
                <option value="">-- Liste des membres éventuellement disponibles --</option>
                [STORPROC Reservations/Partenaire/Client/Disponible=1|P|0|500|Nom|ASC]
                <option value="[!P::Id!]" data-nom="[!P::Nom!]" data-prenom="[!P::Prenom!]" data-details="[!P::Details!]" data-email="[!P::Email!]" data-type="member" [IF [!SelectMember!]="[!P::Id!]"]selected[/IF]>[!P::Nom!] [!P::Prenom!]</option>
                [/STORPROC]
            </select>
        </div>
        <div class="col-xs-2">
            <div class="form-group pull-right">
                <a class="btn btn-danger" id="PartenaireAddMember"><span class="glyphicon glyphicon-plus"></span></a>
            </div>
        </div>
    </div>


    <div id="modeSelect">
        <a class="btn btn-info modeSelect active" id="regisPartBtn">
            Je choisis parmi mes partenaires enregistrés
        </a>
        <a class="btn btn-info modeSelect" id="newPartBtn">
            Je crée un nouveau partenaire
        </a>
        <a class="btn btn-info modeSelect" id="otherPartBtn">
            Je choisis parmi les membres disponibles
        </a>
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

    <div id="notes">
        <p><span>(*)</span> Veuillez noter que le fait de choisir ou de renseigner les champs textes ne suffit pas à ajouter un partenaire à votre réservation. Pour celà, il vous faudra cliquer sur le petit bouton rouge "+" à droite.</p>
        <p><span>(**)</span> En cas de création de nouveau partenaire, il vous faudra renseigner au minimum un nom et un prénom. L'adresse Email est fortement recommandée car elle nous permettra d'envoyer un mail à votre partenaire qui pourra alors vous avertir de sa présence (ou non) lors de votre réservation.</p>
    </div>
</div>
</div>
        </form>
<script type="text/javascript">
var detached = new Array();
//var button ='<a class="btn btn-info modeSelect active" id="regisPartBtn">' +
//    '            Je choisi parmi mes partenaires enregistrés' +
//    '        </a>';

function addListener(){
    $('#modeSelect .btn').on('click',function(e){
        e.preventDefault();
        e.stopPropagation();

        //$('#modeSelect .btn').off('click');

        $(this).addClass('active');
        //$('#modeSelect').append(button);
        $(this).siblings().removeClass('active');
        //button = $(this).detach();

        //addListener();

        switch ($(this).attr('id')){
            case 'newPartBtn':
                $('#newPart').addClass('active');
                $('#newPart').siblings('.mode').removeClass('active');
                break;
            case 'otherPartBtn':
                $('#otherPart').addClass('active');
                $('#otherPart').siblings('.mode').removeClass('active');
                break;
            default:
                $('#regisPart').addClass('active');
                $('#regisPart').siblings('.mode').removeClass('active');
        }
    });
}


$('#PartenaireAdd').on('click',function () {
    //récupération du partenaire sélectionné

    var id = $('#SelectPartenaire option:selected').val();
    if (!id||$('#part-'+id)[0])return;
    var nom = $('#SelectPartenaire option:selected').attr('data-nom');
    var prenom = $('#SelectPartenaire option:selected').attr('data-prenom');
    var details = $('#SelectPartenaire option:selected').attr('data-details');
    var email = $('#SelectPartenaire option:selected').attr('data-email');
    addPartenaire(id,nom,email,prenom,details,'partner');
    detached.push($('#SelectPartenaire option:selected').detach());

});

$('#PartenaireAddNew').on('click',function () {
    //récupération du partenaire sélectionné

    var nom = $('#partenaireNom').val();
    var prenom = $('#partenairePrenom').val();
    var email = $('#partenaireEmail').val();
    $.ajax({
        url: "/[!Query!]/newPartner.json",
        data: {
           nom:nom,
            prenom:prenom,
            email:email
        },
        method: 'POST'
    }).success(function (response) {
        if(response.success){
            var opts = $('option[data-nom]');
            var type = 'new'
            $.each(opts,function(k,opt){
                var d = $(opt).data();
                if($(opt).val() == response.data.id && d.email == response.data.email && d.nom == response.data.nom && d.prenom == response.data.prenom ){
                    type = d.type;
                    detached.push($(opt).detach());
                }
            });
            addPartenaire(response.data.id,response.data.nom,response.data.email,response.data.prenom,response.data.details,type);
            $('#partenaireEmail').val('');
            $('#partenaireNom').val('');
            $('#partenairePrenom').val('');
        }else{
            var errors ="";
            $.each(response.data.errors,function(k,v){
                errors += '<li>'+v+'</li>';
            })
            var html = '<div id="partner_error" class="alert alert-warning"> \
                            <h4>Impossible d\'ajouter le partenaire:</h4> \
                            <ul>'+
                            errors
                            +' </ul> \
                        </div>';
            $('#Partenaires').append(html);
        }

    }).fail(function (reponse){
        console.log('erreur ajax',reponse);
    }).done(function (reponse){

    });





});

$('#PartenaireAddMember').on('click',function () {
    //récupération du partenaire sélectionné

    var id = $('#SelectMember option:selected').val();
    if (!id||$('#part-'+id)[0])return;
    var nom = $('#SelectMember option:selected').attr('data-nom');
    var prenom = $('#SelectMember option:selected').attr('data-prenom');
    var details = $('#SelectMember option:selected').attr('data-details');
    var email = $('#SelectMember option:selected').attr('data-email');
    addPartenaire(id,nom,email,prenom,details,'member');
    detached.push($('#SelectMember option:selected').detach());
});

var partenaire= 0;
function addPartenaire(id,nom,email,prenom,details,type) {
    $('#partner_error').remove();

    var alr = $('#Partenaires .btn-tennis');
    console.log(alr);
    for (var i = 0; i < alr.length; i++){
        var v = alr[i];
        if ($(v).data('id') == id) {
            var html = '<div id="partner_error" class="alert alert-warning"> \
                            <h4>Impossible d\'ajouter le partenaire:</h4> \
                            <ul>\
                                <li>Ce partenaire est déjà inscrit</li> \
                            </ul> \
                        </div>';
            $('#Partenaires').append(html);
            return false;
        }
    }



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
    //console.log('Ajout partenaire',partenaire);
    $('#Partenaires').append($('<div class="btn-tennis del" data-nom="'+nom+'" data-id="'+id+'" data-details="'+details+'" id="part-'+id+'" onclick="suppPartenaire('+id+',\''+type+'\')">'+
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
function suppPartenaire(id,type) {
    $('#partner_error').remove();

    //console.log('supp partenaire',id);
    $('#part-'+id).detach();
    partenaire--;
    $('#PartenaireAjout').show();


    if(type == 'partner'){
        $.each(detached,function(k,v){
            if($(v).val()==id) $('#SelectPartenaire').append($(v));
            return;
        });
    }
    if(type == 'member'){
        $.each(detached,function(k,v){
            if($(v).val()==id) $('#SelectMember').append($(v));
            return;
        });
    }
    if(type == 'new'){
        var option = ' <option value="'+$(this).data('id')+'" data-nom="'+$(this).data('nom')+'" data-prenom="'+$(this).data('prenom')+'" data-details="'+$(this).data('details')+'" data-email="'+$(this).data('email')+'" data-type="partner">[!P::Nom!] [!P::Prenom!]</option>'
        $('#SelectPartenaire').append(option);
    }
}

$(
        function () {

            addListener();


            [STORPROC Reservations/Client/UserId=[!Sys::User::Id!]|TCli][/STORPROC]
            [!TPart:=[!TCli::getOneParent(Partenaire)!]!]
            var thisId = [!TPart::Id!];
            //Clean des doublons partenaire et membre dispo à la fois
            var parts = $('option[data-type=partner]');
            var membs = $('option[data-type=member]');
            for(var n = 0; n < parts.length; n++){
                var part = parts[n];
                for(var m = 0; m < membs.length; m++) {
                    var memb = membs[m];
                    if($(part).val() == thisId) {
                        $(part).remove();
                    }
                    if($(part).val() == $(memb).val() || $(memb).val() == thisId ){
                        $(memb).remove();
                        membs.splice(m,1);
                    }
                }
            }


            var added = new Array();
            [IF [!Partenaire!]]
                [STORPROC [!Partenaire!]|P]
                    addPartenaire('[!P::Id!]', '[UTIL ADDSLASHES][!P::Nom!][/UTIL]', '[UTIL ADDSLASHES][!P::Email!][/UTIL]', '[UTIL ADDSLASHES][!P::Prenom!][/UTIL]','[UTIL ADDSLASHES][!P::Details!][/UTIL]');
                    added.push([!P::Id!]);
            [/STORPROC]
            [/IF]

            $.each(parts,function(k1,v1){
                $.each(added,function(k2,v2){
                    if($(v1).val() == v2){
                        detached.push($(v1).detach());
                        $('.btn-tennis[data-id='+v2+']').data('type','partner');
                    }
                });
            });

            $.each(membs,function(k1,v1){
                $.each(added,function(k2,v2){
                    if($(v1).val() == v2){
                        detached.push($(v1).detach());
                        $('.btn-tennis[data-id='+v2+']').data('type','member');
                    }
                });
            });
        }
);
</script>