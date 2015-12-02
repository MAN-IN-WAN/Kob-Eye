
[MODULE Systeme/Utils/BreadCrumbs]
[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [MODULE Blog/Post/Fiche]
[ELSE]
    <h1>Animations</h1>
    [MODULE Blog/Post/List]
[/IF]