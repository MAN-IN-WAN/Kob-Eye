
[MODULE Systeme/Utils/BreadCrumbs]
[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [MODULE Pharmacie/Ordonnance/Fiche]
[ELSE]
    <h1>Ordonnances</h1>
    [MODULE Pharmacie/Ordonnance/List]
[/IF]