
[MODULE Systeme/Utils/BreadCrumbs]
[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [STORPROC [!Query!]|D|0|1]
        [MODULE Boutique/Promotion/Fiche?D=[!D!]]
    [/STORPROC]
[ELSE]
<div class="row">
    <div class="col-md-8">
        <h1>Promotions</h1>
    </div>
    <div class="col-md-4">
        <a class="btn btn-success btn-block btn-lg" href="/[!Sys::CurrentMenu::Url!]/Ajouter">Ajouter une nouvelle promotion</a>
    </div>
</div>
    [MODULE Boutique/Promotion/List]
[/IF]