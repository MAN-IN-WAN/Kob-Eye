[MODULE Systeme/Utils/BreadCrumbs]

[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [MODULE Parc/Host/Fiche]
[ELSE]
    <h1>Hébergements</h1>
    [MODULE Parc/Host/List]
[/IF]