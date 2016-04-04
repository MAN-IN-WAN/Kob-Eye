
[MODULE Systeme/Utils/BreadCrumbs]
[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::ObjectType!]|O]
[!Ocl:=[!O::getObjectClass()!]!]
[IF [!I::TypeSearch!]=Direct]
    [MODULE [!I::Module!]/[!I::ObjectType!]/Fiche]
[ELSE]
<div class="row">
    <div class="col-md-8">
        <h1>Liste des [!Ocl::Description!]s</h1>
    </div>
    <div class="col-md-4">
        <!--<a class="btn btn-success btn-block btn-lg" href="/[!Sys::CurrentMenu::Url!]/Ajouter">Ajouter un nouveau [!Ocl::Description!]</a>-->
    </div>
</div>
    [MODULE Systeme/Utils/List?Chemin=[!I::Module!]/[!I::ObjectType!]&NO_CONTROL=1]
[/IF]