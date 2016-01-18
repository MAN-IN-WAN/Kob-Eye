
[MODULE Systeme/Utils/BreadCrumbs]
[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::ObjectType!]|O]
[!Ocl:=[!O::getObjectClass()!]!]
[IF [!I::TypeSearch!]=Direct]
    [MODULE [!I::Module!]/[!I::ObjectType!]/Fiche]
[ELSE]
    <!--<a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/Form" class="btn btn-danger pull-right btn-lg"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Ajouter [!Ocl::Description!]</a>-->
    <h1>Liste des [!Ocl::Description!]</h1>
    [MODULE Systeme/Utils/List?Chemin=[!I::Module!]/[!I::ObjectType!]]
[/IF]