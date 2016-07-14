//REQUETE
[IF [!Chemin!]=]
    [INFO [!Query!]|I]
    [OBJ [!I::Module!]|[!I::ObjectType!]|O]
    [!REQ:=[!I::Module!]/[!I::ObjectType!]!]
[ELSE]
    [INFO [!Chemin!]|I]
    [OBJ [!I::Module!]|[!I::ObjectType!]|O]
    [!REQ:=[!Chemin!]!]
[/IF]
[!O::setView()!]

//DATA
[IF [!Data!]]
    [!REQ:=[!Data!]!]
[/IF]

//RECHERCHE
[!FILTER:=!]
[IF [!search!]!=]
    [!FILTER:=~[!search!]!]
[/IF]
[IF [!FILTER!]!=]
    [!REQ:=[!REQ!]/[!FILTER!]!]
[/IF]

//PAGINATION
[IF [!Page!]=][!Page:=1!][/IF]
[COUNT [!REQ!]|Nb]
[!NbParPage:=30!]
[IF [!Limit!]>0]
    [!NbParPage:=[!Limit!]!]
[/IF]
[!NbNumParPage:=3!]
[!NbPage:=[!Math::Floor([!Nb:/[!NbParPage!]!])!]!]
[IF [!NbPage!]!=[!Nb:/[!NbParPage!]!]][!NbPage+=1!][/IF]

[COUNT [!O::getElementsByAttribute(form,,1)!]|NbP]


//ACTIONS
[SWITCH [!action!]|=]
    [CASE edit]
        [STORPROC [!I::Module!]/[!I::TypeChild!]/[!id!]|P|0|1]
            [!P::[!prop!]:=[!value!]!]
            [IF [!P::Save()!]=]
                <div class="alert alert-success">L'élément [!P::getDescription()!] [!P::getFirstSearchOrder()!] a bien été mis à jour.</div>
            [ELSE]
                <div class="alert alert-danger">Un problème est survenu lors de la mise à jour de l'élément [!P::getDescription()!] [!P::getFirstSearchOrder()!].
                    <ul>
                        [STORPROC [!P::Error!]|E]
                            <li>[!E::Message!]</li>
                        [/STORPROC]
                    </ul>
                </div>
            [/IF]
        [/STORPROC]
    [/CASE]
[/SWITCH]

[IF [!Mini!]=]
<div class="row ">
    <div class="col-md-4">
        <form method="GET">
        <div class="btn-toolbar" role="toolbar">
            <div class="input-group">
                <input name="search" type="text" class="form-control" placeholder="Titre, Mot-clef ..." value="[!search!]">
                  <span class="input-group-btn">
                    <input class="btn btn-primary" type="submit" value="Recherche" />
                  </span>
            </div><!-- /input-group -->
        </div>
            <input type="hidden" name="Page" value="[!Page!]">
        </form>
    </div>
    <div class="col-md-8">
        <div class="btn-toolbar pull-right" role="toolbar">
            <div class="btn-group" role="group">
                <button class="btn btn-default" disabled="disabled">Page 1 sur [!NbPage!] </button>
                [IF [!Page!]>1]
                <a href="/[!Lien!]?search=[!search!]" class="btn btn-default"><span>&laquo;</span></a>
                <a href="[IF [!Page!]=2]/[!Lien!]?search=[!search!][ELSE]?Page=[!Page:-1!]&search=[!search!][/IF]" class="btn btn-default">&lsaquo;</a>
                [IF [!Page!]>[!Math::Round([!NbNumParPage:/2!])!]]
                <a href="/[!Lien!]?search=[!search!]" class="btn btn-default"><span>1</span></a>
                <a href="#" class="btn btn-default" disabled="disabled"><span>...</span></a>
                [/IF]
                [/IF]
                [!start:=1!]
                [IF [!Page!]>[!start:+[!NbNumParPage:/[!NbParPage!]!]!]][!start:=[!Math::Round([!Page:-[!NbNumParPage:/2!]!])!]!][/IF]
                [STORPROC [!NbPage:+1!]|P|[!start!]|[!NbNumParPage!]]
                <a href="[IF [!P!]!=1]?Page=[!P!]&search=[!search!][ELSE]/[!Lien!]?search=[!search!][/IF]" class="btn btn-default [IF [!P!]=[!Page!]]active[/IF]">[!P!]</a>
                [/STORPROC]
                [IF [!Page!]<[!NbPage!]]
                [IF [!Page:+[!NbNumParPage:/2!]!]<[!NbPage!]]
                <a href="#" class="btn btn-default"><span>...</span></a>
                <a href="?Page=[!NbPage!]&search=[!search!]" class="btn btn-default">[!NbPage!]</a>
                [/IF]
                <a href="?Page=[!Page:+1!]&search=[!search!]" class="btn btn-default"><span>&rsaquo;</span></a>
                <a href="?Page=[!NbPage!]&search=[!search!]" class="btn btn-default">&raquo;</a>
                [/IF]
            </div>
        </div>
    </div>
</div>
[/IF]

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>

            [STORPROC [!O::getElementsByAttribute(list,,1)!]|E]
            <th>[IF [!E::listDescr!]][!E::listDescr!][ELSE][!E::description!][/IF]</th>
            [/STORPROC]
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="bodylist-[!O::Module!]-[!O::ObjectType!]">
//        [STORPROC [!REQ!]|C|[!NbParPage:*[!Page:-1!]!]|[!NbParPage!]|tmsCreate|DESC]
        [STORPROC [!REQ!]|C|0|1|tmsCreate|DESC]
        <script id="list-[!O::Module!]-[!O::ObjectType!]" type="text/x-jQuery-tmpl">
        <tr>
            [STORPROC [!O::getElementsByAttribute(list,,1)!]|E]
                [MODULE Systeme/Utils/List/getElementTypeTemplate?E=[!E!]&C=[!C!]&Popup=[!Popup!]]
            [/STORPROC]
            <!--<td width="250">
                <div class="small">Créé le [DATE d/m/Y H:i:s][!C::tmsCreate!][/DATE] [STORPROC Systeme/User/[!C::userCreate!]|U|0|1] par [!U::Nom!] [!U::Prenom!] ([!U::Login!])[/STORPROC]</div>
                <div class="small">Modifié le [DATE d/m/Y H:i:s][!C::tmsCreate!][/DATE] [STORPROC Systeme/User/[!C::userEdit!]|U|0|1] par [!U::Nom!] [!U::Prenom!] ([!U::Login!])[/STORPROC]</div>
            </td>-->
            <td width="250">
                <div class="btn-group" role="group">

                    <a class="btn btn-warning [IF [!Popup!]]popup[/IF]" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/${id}[IF [!Popup!]]/Form[/IF]" data-title="Modification [!C::getFirstSearchOder()!]">[IF [!Popup!]]Modifier[ELSE]Détails[/IF]</a>
                    <a class="btn btn-danger confirm" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/${id}/Supprimer" data-title="Suppression [!C::getFirstSearchOder()!]" data-confirm="Etes vous sur de vouloir supprimer [!C::getFirstSearchOrder()!] ?">Supprimer</a>
                    <a class="btn btn-info confirm" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/${id}Cloner" data-title="CLonage ${label}" data-confirm="Etes vous sur de vouloir cloner ${label} ?">Cloner</a>
                </div>
            </td>
        </tr>
        </script>
        <script type="text/javascript">
            console.log('execution list');
            function refreshList() {
                console.log('refresh list ');
                $.ajax({
                    url: '/[!Chemin!]/getJson.json',
                    type: "POST",
                    dataType: 'json',
                    context: document.body,
                    error: function (xhr, status, thrown) {
                        alert('Problème de connexion... Veuillez rafraichir la pagae.');
                    },
                    success: function (data) {
                        // Render the books using the template
                        $("#list-[!O::Module!]-[!O::ObjectType!]").tmpl(data.results).appendTo("#bodylist-[!O::Module!]-[!O::ObjectType!]");
                    }
                });
            }

            refreshList();

            function formatPrice(price) {
                return "$" + price.toFixed(2);
            }

        </script>
        [/STORPROC]
        </tbody>
    </table>
</div>
[IF [!Mini!]=]
<div class="row ">
    <div class="col-md-4">
    </div>
    <div class="col-md-8">
        <div class="btn-toolbar pull-right" role="toolbar">
            <div class="btn-group" role="group">
                <button class="btn btn-default" disabled="disabled">Page 1 sur [!NbPage!] </button>
                [IF [!Page!]>1]
                <a href="/[!Lien!]?search=[!search!]" class="btn btn-default"><span>&laquo;</span></a>
                <a href="[IF [!Page!]=2]/[!Lien!]?search=[!search!][ELSE]?Page=[!Page:-1!]&search=[!search!][/IF]" class="btn btn-default">&lsaquo;</a>
                [IF [!Page!]>[!Math::Round([!NbNumParPage:/2!])!]]
                <a href="/[!Lien!]?search=[!search!]" class="btn btn-default"><span>1</span></a>
                <a href="#" class="btn btn-default" disabled="disabled"><span>...</span></a>
                [/IF]
                [/IF]
                [!start:=1!]
                [IF [!Page!]>[!start:+[!NbNumParPage:/[!NbParPage!]!]!]][!start:=[!Math::Round([!Page:-[!NbNumParPage:/2!]!])!]!][/IF]
                [STORPROC [!NbPage:+1!]|P|[!start!]|[!NbNumParPage!]]
                <a href="[IF [!P!]!=1]?Page=[!P!]&search=[!search!][ELSE]/[!Lien!]?search=[!search!][/IF]" class="btn btn-default [IF [!P!]=[!Page!]]active[/IF]">[!P!]</a>
                [/STORPROC]
                [IF [!Page!]<[!NbPage!]]
                [IF [!Page:+[!NbNumParPage:/2!]!]<[!NbPage!]]
                <a href="#" class="btn btn-default"><span>...</span></a>
                <a href="?Page=[!NbPage!]&search=[!search!]" class="btn btn-default">[!NbPage!]</a>
                [/IF]
                <a href="?Page=[!Page:+1!]&search=[!search!]" class="btn btn-default"><span>&rsaquo;</span></a>
                <a href="?Page=[!NbPage!]&search=[!search!]" class="btn btn-default">&raquo;</a>
                [/IF]
            </div>
        </div>
    </div>
</div>
[/IF]
