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
    Recherche de nouveaux partenaires
    <form method="POST" class="pull-right">
        <div class="form-group">
            <div class="col-sm12">
                <input type="text" id="partnersearch" placeholder="Recherche" value="[!partnersearch!]" name="partnersearch"/>
            </div>
        </div>
    </form>
</div>
<div id="allpartners">
</div>

<script>
    function refreshDOM() {
        //reset events
        $('.btn-tennis.add').unbind('click');
        $('.btn-tennis.del').unbind('click');
        //add events
        $('.btn-tennis.add').click(function (ev) {
            console.log('add', ev);
            bootbox.confirm('<h2>Êtes-vous sur de vouloir ajouter le partenaire ' + $(ev.currentTarget).attr('data-person') + ' ?</h2>', function () {
                //requete distante
                $.ajax({
                    url: '/Reservations/Partenaire/Add.json', // La ressource ciblée
                    type: 'POST',
                    dataType: 'json',
                    data: 'ID=' + $(ev.currentTarget).attr('data-id'),
                    complete: function () {
                        var part = $('#part-' + $(ev.currentTarget).attr('data-id')).remove();
                        console.log('remove html',part);
                        //$('#mypartners').append($(part[0]));
                        addMyPartner({Id:$(ev.currentTarget).attr('data-id'),FullName: $(ev.currentTarget).attr('data-person'),Description: $(ev.currentTarget).attr('data-details')})
                        refreshDOM();
                    }
                });

            });
        });
        $('.btn-tennis.del').click(function (ev) {
            bootbox.confirm('<h2>Êtes-vous sur de vouloir supprimer le partenaire ' + $(ev.currentTarget).attr('data-person') + ' ?</h2>', function () {
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
    $('#partnersearch').change(search);
    search(null);
</script>