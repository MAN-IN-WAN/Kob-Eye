
        [!REQ:=[!Chemin!]!]
        [INFO [!REQ!]|I]
        [OBJ [!I::Module!]|[!I::ObjectType!]|OO]
<div class="table-responsive">
    <table class="table table-striped tree">
        <thead>
        <tr>
            <td></td>
            [STORPROC [!OO::getElementsByAttribute(list,,1)!]|E|0|5]
            <th>[IF [!E::listDescr!]][!E::listDescr!][ELSE][!E::description!][/IF]</th>
            [/STORPROC]
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>

        [STORPROC [!REQ!]|C|0|1000]
        <tr class="treeitem" data-id="[!C::Id!]" data-name="[!I::TypeChild!]" id="[!I::TypeChild!]-[!C::Id!]">
            <td width="10"><i class="fa fa-arrow-down"></i></td>
            [STORPROC [!OO::getElementsByAttribute(list,,1)!]|E|0|5]
            [MODULE Systeme/Utils/List/getElementType?E=[!E!]&C=[!C!]&NOLINK=1]
            [!NbField:=[!NbResult!]!]
            [/STORPROC]
            <td width="250">
                <div class="btn-group" role="group">

                    <a class="btn btn-warning [IF [!Popup!]]popup[/IF]" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!C::Id!][IF [!Popup!]]/Form[/IF]" data-title="Modification [!C::getFirstSearchOder()!]">[IF [!Popup!]]Modifier[ELSE]Détails[/IF]</a>
                    <a class="btn btn-danger confirm" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!C::Id!]/Supprimer" data-title="Suppression [!C::getFirstSearchOder()!]" data-confirm="Etes vous sur de vouloir supprimer [!C::getFirstSearchOrder()!] ?">Supprimer</a>
                    <a class="btn btn-info confirm" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!C::Id!]/Cloner" data-title="CLonage [!C::getFirstSearchOder()!]" data-confirm="Etes vous sur de vouloir cloner [!C::getFirstSearchOrder()!] ?">Cloner</a>
                </div>
            </td>
        </tr>
        <tr style="background-color:#D3D3d3;">
            <td colspan="[!NbField:+3!]" style="padding:0;padding-left:30px;" id="[!I::TypeChild!]-[!C::Id!]-sub"></td>
        </tr>
        [/STORPROC]
        </tbody>
    </table>
</div>
<script id="head-[!I::Module!]-[!I::TypeChild!]" type="text/x-jQuery-tmpl">
<div class="table-responsive"><table class="table table-striped tree" style="margin:10px 0 10px 0;">
<thead>
    <tr>
        <td></td>
        [STORPROC [!OO::getElementsByAttribute(list,,1)!]|E|0|5]
        <th>[IF [!E::listDescr!]][!E::listDescr!][ELSE][!E::description!][/IF]</th>
        [/STORPROC]
        <th>Actions</th>
    </tr>
</thead>
<tbody class="tree-content">
</tbody></table></div>
</script>
<script id="list-[!I::Module!]-[!I::TypeChild!]" type="text/x-jQuery-tmpl">
    <tr class="treeitem ${tail}" data-id="${id}" data-name="[!I::TypeChild!]" id="[!I::TypeChild!]-${id}">
        <td width="10"><i class="fa ${downicon}"></i></td>
        [STORPROC [!OO::getElementsByAttribute(list,,1)!]|E|0|5]
            [MODULE Systeme/Utils/ListAjax/getElementTypeTemplate?E=[!E!]&C=[!C!]]
            [!NbField:=[!NbResult!]!]
        [/STORPROC]
    <td width="250">
            <div class="btn-group" role="group">

            <a class="btn btn-warning [IF [!Popup!]]popup[/IF]" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/${id}/[IF [!Popup!]]Form[/IF]" data-title="Modification ${label}">[IF [!Popup!]]Modifier[ELSE]Détails[/IF]</a>
    <a class="btn btn-danger confirm" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/${id}/Supprimer" data-title="Suppression [!C::getFirstSearchOder()!]" data-confirm="Etes vous sur de vouloir supprimer ${label} ?">Supprimer</a>
            <a class="btn btn-info confirm" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/${id}/Cloner" data-title="Clonage [!C::getFirstSearchOder()!]" data-confirm="Etes vous sur de vouloir cloner ${label} ?">Cloner</a>
            </div>
            </td>
    </tr>
    <tr style="background-color:#D3D3d3;">
        <td colspan="[!NbField:+3!]" style="padding:0;padding-left:30px;" id="[!I::TypeChild!]-${id}-sub"></td>
    </tr>
</script>
<script>
function getData(query, handler, err) {
    console.log('getdata:',query);
    $.ajax({
        url: '/'+query+'/getJson.json',
        type: "POST",
        dataType: 'json',
        context: document.body,
        error: function (xhr, status, thrown) {
            err();
            //alert('Problème de connexion... Veuillez rafraichir la pagae.');
        },
        success: function (data) {
            handler(data);
        }
    });
}

function scan(target) {
    //ajout des évenements
    target.find('tr.treeitem').click(function () {
        opennode($(this),function () {});
    });
    target.find('input.switch').bootstrapSwitch({
        onColor: 'success',
        offColor: 'danger',
        size: 'normal',
        handleWidth: 50
    });
}
function opennode(target, handler){
    if (target.hasClass('tail')) return;
    //test de l'ouverture
    if (target.hasClass('opened')) {
        $('#'+target.attr('id')+'-sub').empty();
        target.removeClass('opened');
        return;
    }
    target.addClass('opened');
    //indice de chargement
    var subcont = '#'+target.attr('id')+'-sub';
    $(subcont).html('<span class="loadingSpinner"></span>');

    //chargement des données
    getData('[!I::Module!]/'+target.attr('data-name')+'/'+target.attr('data-id')+'/'+target.attr('data-name'), function (data) {
        console.log('data loaded:',data);
        //on ajoute certaines variable sur la liste des données
        var out = [];
        for (var d in data.results){
            if (data.results[d].tail!='tail'){
                data.results[d].downicon = 'fa-arrow-down';
            }
            out.push(data.results[d]);
        }
        console.log('resultats', data.results);

        $(subcont).empty();
        if (data.results.length){
            $("#head-[!OO::Module!]-[!I::TypeChild!]").tmpl({}).appendTo(subcont);
            $("#list-[!OO::Module!]-[!I::TypeChild!]").tmpl(out).appendTo(subcont+' .tree-content');
        }
        scan($(subcont+' .tree-content'));

        //launch handler
        handler();
    }, function () {
        $(subcont).empty();
    });
}

scan($('.tree'));
</script>