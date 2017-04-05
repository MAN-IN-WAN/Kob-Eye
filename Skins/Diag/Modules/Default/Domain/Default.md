[MODULE Systeme/Utils/BreadCrumbs]
[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [STORPROC [!Query!]|O|0|1]
        [MODULE [!I::Module!]/[!I::ObjectType!]/Fiche]
    [/STORPROC]
[ELSE]
<div class="row">
    <div class="col-md-8">
        <h1>Gestion du [!I::ObjectType!]</h1>
    </div>
    <div class="col-md-4">
        //<a class="btn btn-success btn-block btn-lg" href="/[!Sys::CurrentMenu::Url!]/Ajouter">Ajouter un nouveau [!I::ObjectType!]</a>
    </div>
</div>
        
        //j'ai déplacé la gestion des colonnes dans le fichier Modules/Default/Domain/List.md
    [MODULE [!I::Module!]/[!I::ObjectType!]/List]
[/IF]