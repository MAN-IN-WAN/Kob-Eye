
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
<div class="row well">
        <div class="col-md-4">
                <form method="GET">
                        <div class="btn-toolbar" role="toolbar">
                                <div class="input-group">
                                        <input name="search" type="text" class="form-control" placeholder="Recherche..." value="[!search!]">
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

[!Clefs:=[!Array::newArray()!]!]
[!Ids:=[!Array::newArray()!]!]

<div class="table-responsive">
        <table class="table table-striped">
                <thead>
                        <tr>
                                <th>Id</th>
                                [STORPROC [!O::getElementsByAttribute(diag,,1)!]|E]
                                <th>[!E::description!]</th>
                                [/STORPROC]
                                [STORPROC [!ACols!]|C]
                                <th>[!C!]</th>
                                [/STORPROC]
                                <th>Informations</th>
                //              <th>Actions</th>
                        </tr>
                </thead>
                <tbody>
                [STORPROC [!REQ!]|C|[!NbParPage:*[!Page:-1!]!]|[!NbParPage!]|tmsCreate|DESC]
                        [!Ids:=[!Array::push([!Ids!],[!C::Id!])!]!]
                        <tr id="elem_[!C::Id!]">
                                <td>[!C::Id!]</td>    
                                [STORPROC [!O::getElementsByAttribute(diag,,1)!]|E]
                                        [SWITCH [!E::type!]|=]
                                                [CASE boolean]
                                                        <td>
                                                        [IF [!C::[!E::name!]!]]
                                                                <h4><a href="?search=[!search!]&Page=[!Page!]&action=edit&prop=[!E::name!]&value=0&id=[!C::Id!]"><span class="label label-success"><i class="fa fa-check"></i></span></a></h4>
                                                        [ELSE]
                                                                <h4><a href="?search=[!search!]&Page=[!Page!]&action=edit&prop=[!E::name!]&value=1&id=[!C::Id!]"><span class="label label-danger"><i class="fa fa-times"></i></span></a></h4>
                                                        [/IF]
                                                        </td>
                                                [/CASE]
                                                [CASE price]
                                                        <td><h4><span class="label label-primary">[!Utils::getPrice([!C::[!E::name!]!])!] € HT</span></h4></td>
                                                [/CASE]
                                                [CASE int]
                                                        <td><h4><span class="label label-warning">[!C::[!E::name!]!]</span></h4></td>
                                                [/CASE]
                                                [CASE image]
                                                        <td><img src="/[!C::[!E::name!]!].mini.200x50.jpg" class="img-responsive" /></td>
                                                [/CASE]
                                                [DEFAULT]
                                                        <td>
                                                                <a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!C::Id!]">
                                                                        [IF [!Pos!]=1]<strong>[/IF]
                                                                        [!C::[!E::name!]!]
                                                                        [IF [!Pos!]=1]</strong>[/IF]
                                                                </a>
                                                        </td>
                                                [/DEFAULT]
                                        [/SWITCH]
                                [/STORPROC]
                                [STORPROC [!ACols!]|AC]
                                        [!Clefs:=[!Array::push([!Clefs!],[!Key!])!]!]
                                        <td class="dynamic [!Key!]">
                                                <img src="https://d13yacurqjgara.cloudfront.net/users/82092/screenshots/1073359/spinner.gif" style="width: 40px; height: 30px;">
                                        </td>
                                        // ---> Guillaume
                                        //Vu que nous avons la pagination et les filtres de recherche, il vaut mieux faire une requete par ligne / par col.
                                [/STORPROC]
                
                                <td>
                                        <div class="small">Créé le [DATE d/m/Y H:i:s][!C::tmsCreate!][/DATE] [STORPROC Systeme/User/[!C::userCreate!]|U] par [!U::Nom!] [!U::Prenom!] ([!U::Login!])[/STORPROC]</div>
                                        <div class="small">Modifié le [DATE d/m/Y H:i:s][!C::tmsCreate!][/DATE] [STORPROC Systeme/User/[!C::userEdit!]|U] par [!U::Nom!] [!U::Prenom!] ([!U::Login!])[/STORPROC]</div>
                                </td>
                                <td>
                                        <div class="btn-group" role="group">
                                                <!--<a class="btn btn-info" href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!C::Id!]">Détails</a>-->
                                        </div>
                                </td>
                        </tr>
                        [NORESULT]
                                <tr class="noResult">
                                        <td colspan="30">Aucun résultat pour les critères fournis.</td>
                                </tr>
                        [/NORESULT]
                [/STORPROC]
                </tbody>
        </table>
</div>

[!Clefs:=[!Array::unique([!Clefs!])!]!]
<script type="text/javascript">
        [COUNT [!Clefs!]|cCount]
        var keys = new Array(
                             [STORPROC [!Clefs!]|clef]
                                        "[!clef!]"
                                        [IF [!Pos!]<[!cCount!]],[/IF]
                             [/STORPROC]
                             );
        
        [COUNT [!Ids!]|iCount]
        var ids = new Array(
                             [STORPROC [!Ids!]|id]
                                        [!id!]
                                        [IF [!Pos!]<[!iCount!]],[/IF]
                             [/STORPROC]
                            );
        
        $(document).on('ready',function(){
                for( var i in keys){
                        $.getJSON( "/[!I::Module!]/[!I::ObjectType!]/"+keys[i]+".json",{"ids":ids},function(data) {
                                $.each(data.data,function(j,v){
                                        $("#elem_"+j+" ."+data.type ).html(v);
                                });   
                        });
                }
        });
</script>

[IF [!Mini!]=]
<div class="row well">
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


