<h1>Liste de mes partenaires</h1>
[IF [!msg!]]
    <div class="alert alert-[!action!]">[!msg!]</div>
[/IF]
[!Client:=[!Module::Reservations::getCurrentClient()!]!]
[!Partenaire:=[!Client::getOneParent(Partenaire)!]!]


[STORPROC Reservations/Client/[!Client::Id!]/Partenaire|P]
<div class="alert alert-info">
    Mes partenaires
</div>
<div class="pitch">
    Vous trouverez ici tous vos partenaires déjà enregistrés. C'est ici que vous pourrez les délier de votre compte afin de ne plus les voir apparaître lors de la création de vos réservations.
</div>
<div id="mypartners">
[LIMIT 0|100]
<div class="btn-tennis del" data-person="[!P::Nom!] [!P::Prenom!]"  data-id="[!P::Id!]" data-details="[!P::Details!]" id="part-[!P::Id!]">
    <a class="btn btn-danger pull-right"><span class="glyphicon glyphicon-minus"></span></a>
    <h3>[!P::Nom!] [!P::Prenom!]</h3>
    <p>[!P::Details!]</p>
</div>
[/LIMIT]
</div>
[/STORPROC]


<div class="alert alert-info">
    Recherche de nouveaux partenaires parmi les membres
    <form method="POST" class="pull-right">
        <div class="form-group">
            <div class="col-sm12">
                <input type="text" id="partnersearch" placeholder="Recherche" value="[!partnersearch!]" name="partnersearch"/>
            </div>
        </div>
    </form>
</div>
<div class="pitch">
    Vous trouverez ici la liste des membres possiblement disponibles pour completer votre groupe. Vous pouvez filtrer ces membres grace au champ de recherche présent ci-dessus.
</div>
<div id="allpartners">
</div>

<div class="alert alert-info">
    Ajouter un nouveau partenaire
</div>
<div class="pitch">
    Vous pourrez ici ajouter de nouveau partenaires.  Il vous faudra renseigner au minimum un nom et un prénom. L'adresse Email est fortement recommandée car elle nous permettra d'envoyer un mail à votre partenaire qui pourra alors vous avertir de sa présence (ou non) lors de vos futures réservation.
</div>
<div id="newPartner" class="clearfix">
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


<script>
    function refreshDOM() {
        //reset events
        $('.btn-tennis.add').unbind('click');
        $('.btn-tennis.del').unbind('click');
        //add events
        $('.btn-tennis.add').click(function (ev) {
            //console.log('add', ev);
            bootbox.confirm('<h2>Êtes-vous sur de vouloir ajouter le partenaire ' + $(ev.currentTarget).attr('data-person') + ' ?</h2>', function (result) {
                if(!result)
                    return;

                //requete distante
                $.ajax({
                    url: '/Reservations/Partenaire/Add.json', // La ressource ciblée
                    type: 'POST',
                    dataType: 'json',
                    data: 'ID=' + $(ev.currentTarget).attr('data-id'),
                    complete: function () {
                        var part = $('#part-' + $(ev.currentTarget).attr('data-id')).remove();
                       // console.log('remove html',part);
                        //$('#mypartners').append($(part[0]));
                        addMyPartner({Id:$(ev.currentTarget).attr('data-id'),FullName: $(ev.currentTarget).attr('data-person'),Description: $(ev.currentTarget).attr('data-details')})
                        refreshDOM();
                    }
                });

            });
        });
        $('.btn-tennis.del').click(function (ev) {
            bootbox.confirm('<h2>Êtes-vous sur de vouloir supprimer le partenaire ' + $(ev.currentTarget).attr('data-person') + ' ?</h2>', function (result) {
                if(!result)
                    return;

                //requete distante
                $.ajax({
                    url: '/Reservations/Partenaire/Del.json', // La ressource ciblée
                    type: 'POST',
                    dataType: 'json',
                    data: 'ID=' + $(ev.currentTarget).attr('data-id'),
                    complete: function () {
                        var part = $('#part-' + $(ev.currentTarget).attr('data-id')).remove();
                        search(null);
                    }
                });
            });
        });
    }



    function search(){
        var query = $('#partnersearch').val();

        //recherche distante
        $.ajax({
            url : '/Reservations/Partenaire/Search.json', // La ressource ciblée
            type : 'POST',
            dataType: 'text',
            data : 'search=' + query,
            complete: function (out) {
                console.log('resultat recherche',out);
                out = JSON.parse(out.responseText);
                $('#allpartners').empty();
                for (var i in out.data){
                    addAllPartner(out.data[i]);
                }
                refreshDOM();
            }
        });
    }
    function addMyPartner(o) {
        var target = '#mypartners';
        var partners = $(target+' .btn-tennis')

        for(var n=0 ; n < partners.length; n++){
            if($(partners[n]).data('id') == o.Id){

                return false;
            }
        }

        $(target).append($('<div class="btn-tennis del" data-person="'+o.FullName+'" data-id="'+o.Id+'" data-details="'+o.Description+'" id="part-'+o.Id+'">'+
            '<a class="btn btn-danger pull-right"><span class="glyphicon glyphicon-minus"></span></a>'+
            '<h3>'+o.FullName+'</h3>'+
            '<p>'+o.Description+'</p>'+
            '</div>'));
    }
    function addAllPartner(o) {
        var target = '#allpartners';
        $(target).append($('<div class="btn-tennis add" data-person="'+o.FullName+'" data-id="'+o.Id+'" data-details="'+o.Description+'" id="part-'+o.Id+'">'+
            '<a class="btn btn-danger pull-right"><span class="glyphicon glyphicon-plus"></span></a>'+
            '<h3>'+o.FullName+'</h3>'+
            '<p>'+o.Description+'</p>'+
            '</div>'));
    }

    $('#PartenaireAddNew').on('click',function () {
        $('#partner_error').remove();
        //récupération du partenaire sélectionné

        var nom = $('#partenaireNom').val();
        var prenom = $('#partenairePrenom').val();
        var email = $('#partenaireEmail').val();
        $.ajax({
            url: "/[!Query!]/newPartner.json",
            data: {
                nom: nom,
                prenom: prenom,
                email: email
            },
            method: 'POST'
        }).success(function (response) {
            if (response.success) {
                var o ={
                    'FullName': response.data.nom+' '+response.data.prenom,
                    'Id': response.data.id,
                    'Description': (response.data.details != undefined ? response.data.details :'')
                }
                addMyPartner(o);

                $('#partenaireEmail').val('');
                $('#partenaireNom').val('');
                $('#partenairePrenom').val('');
                refreshDOM();
                search(null);
            } else {
                var errors = "";
                $.each(response.data.errors, function (k, v) {
                    errors += '<li>' + v + '</li>';
                })
                var html = '<div id="partner_error" class="alert alert-warning"> \
                            <h4>Impossible d\'ajouter le partenaire:</h4> \
                            <ul>' +
                    errors
                    + ' </ul> \
                        </div>';
                $('#newPartner').prepend(html);
            }

        }).fail(function (reponse) {
            console.log('erreur ajax', reponse);
        }).done(function (reponse) {

        });
    });



        var timeoutId = 0;
    $('#partnersearch').keyup(function () {
        clearTimeout(timeoutId); // doesn't matter if it's 0
        timeoutId = setTimeout(search, 500);
    });
    //$('#partnersearch').change(search);
    search(null);
</script>