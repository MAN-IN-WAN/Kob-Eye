[MODULE Systeme/Utils/BreadCrumbs]
[INFO [!Query!]|I]
[OBJ [!I::Module!]|[!I::ObjectType!]|O]
[!Ocl:=[!O::getObjectClass()!]!]
[COUNT [!O::getElementsByAttribute(form,,1)!]|NbP]
[IF [!I::TypeSearch!]=Direct]
    [MODULE [!I::Module!]/[!I::ObjectType!]/Fiche]
[ELSE]
    [IF [!Sys::User::isRole(PARC_REVENDEUR)!]&&[!I::ObjectType!]!=Client][ELSE]
        [IF [!NbP!]<=1]
            <a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/Form" data-title="Ajouter [!O::getDescription()!]" class="btn btn-danger pull-right btn-lg popup"><span class="glyphicon glyphicon-plus" aria-hidden="true" ></span> Ajouter un(e) [!Ocl::Description!]</a>
        [ELSE]
        <a href="/[!Sys::getMenu([!I::Module!]/[!I::ObjectType!])!]/Form" data-title="Ajouter [!O::getDescription()!]" class="btn btn-danger pull-right btn-lg popup"><span class="glyphicon glyphicon-plus" aria-hidden="true" ></span> Ajouter un(e) [!Ocl::Description!]</a>
        [/IF]
    [/IF]

    <h1>[!Sys::CurrentMenu::Titre!]</h1>
    <i><b>[!Sys::CurrentMenu::SousTitre!]</b></i>

    [MODULE Systeme/Utils/List?Chemin=[!ParcClient::get[!I::ObjectType!]Query()!]]
[/IF]