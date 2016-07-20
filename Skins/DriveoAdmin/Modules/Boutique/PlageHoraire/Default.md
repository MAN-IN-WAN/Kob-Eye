
[MODULE Systeme/Utils/BreadCrumbs]
[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Direct]
    [STORPROC [!Query!]|D|0|1]
        [MODULE Pharmacie/PlageHoraire/Fiche?D=[!D!]]
    [/STORPROC]
[ELSE]
<div class="row">
    <div class="col-md-8">
        <h1>Plages horaires de préparation</h1>
    </div>
    <div class="col-md-4">
        <a class="btn btn-success btn-block btn-lg" href="/[!Sys::CurrentMenu::Url!]/Ajouter">Ajouter une nouvelle plage horaire</a>
    </div>
</div>
    [MODULE Pharmacie/PlageHoraire/List]
[/IF]