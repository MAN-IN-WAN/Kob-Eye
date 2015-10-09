
[MODULE Systeme/Utils/BreadCrumbs]
[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [MODULE Boutique/Commande/Fiche]
[ELSE]
    <h1>Commandes</h1>
    [MODULE Boutique/Commande/List]
[/IF]