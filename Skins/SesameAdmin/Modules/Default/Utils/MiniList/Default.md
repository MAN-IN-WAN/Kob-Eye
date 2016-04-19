
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



<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>

            [STORPROC [!O::getElementsByAttribute(list,,1)!]|E]
            <th>[!E::description!]</th>
            [/STORPROC]
            <th>Informations</th>
        </tr>
        </thead>
        <tbody>
        [STORPROC [!REQ!]|C|0|5|tmsCreate|DESC]
        <tr>
            [STORPROC [!O::getElementsByAttribute(list,,1)!]|E]
                [SWITCH [!E::type!]|=]
                    [CASE boolean]
                        <td>[IF [!C::[!E::name!]!]]
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
                            //<a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/[!C::Id!]">
                            [IF [!Pos!]=1]<strong>[/IF]
                            [!C::[!E::name!]!]
                            [IF [!Pos!]=1]</strong>[/IF]
                            //</a>
                        </td>
                    [/DEFAULT]
                [/SWITCH]
            [/STORPROC]

            <td>
                <div class="small">Créé le [DATE d/m/Y H:i:s][!C::tmsCreate!][/DATE] [STORPROC Systeme/User/[!C::userCreate!]|U] par [!U::Nom!] [!U::Prenom!] ([!U::Login!])[/STORPROC]</div>
                <div class="small">Modifié le [DATE d/m/Y H:i:s][!C::tmsCreate!][/DATE] [STORPROC Systeme/User/[!C::userEdit!]|U] par [!U::Nom!] [!U::Prenom!] ([!U::Login!])[/STORPROC]</div>
            </td>
        </tr>
        [/STORPROC]
        </tbody>
    </table>
</div>
