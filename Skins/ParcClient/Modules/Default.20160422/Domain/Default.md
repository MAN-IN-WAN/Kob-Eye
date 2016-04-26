
[MODULE Systeme/Utils/BreadCrumbs]
[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [MODULE Parc/Domain/Fiche]
[ELSE]
    <h1>Domaines</h1>
    [MODULE Parc/Domain/List]
[/IF]