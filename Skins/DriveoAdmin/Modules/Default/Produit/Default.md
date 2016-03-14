
[MODULE Systeme/Utils/BreadCrumbs]
[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::ObjectType!]|O]
[!Ocl:=[!O::getObjectClass()!]!]
[IF [!I::TypeSearch!]=Direct]
    [MODULE [!I::Module!]/[!I::ObjectType!]/Fiche]
[ELSE]
    <a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/ResetProduitUne" class="btn btn-danger pull-right btn-lg"><span class="glyphicon glyphicon-reset" aria-hidden="true"></span> Reinitialiser les produits à la une</a>
    <h1>Liste des [!Ocl::Description!]</h1>
    [MODULE Systeme/Utils/List?Chemin=[!I::Module!]/[!I::ObjectType!]]
[/IF]