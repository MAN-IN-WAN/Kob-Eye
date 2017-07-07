<h1>Liste de mes partenaires</h1>
[IF [!msg!]]
    <div class="alert alert-[!action!]">[!msg!]</div>
[/IF]
[!Client:=[!Module::Reservations::getCurrentClient()!]!]
[!Partenaire:=[!Client::getOneParent(Partenaire)!]!]


[STORPROC Reservations/Client/[!Client::Id!]/Partenaire|P]
<div class="alert alert-info">
    Mes partenaires
</div>
[LIMIT 0|10]
<div class="btn-tennis">
    <a class="btn btn-danger pull-right" onclick="onMinus([!P::Id!])"><span class="glyphicon glyphicon-minus"></span></a>
    <span class="label label-primary pull-right" style="margin-right:50px;">[!P::Email!]</span>
    [!P::Nom!] - [!P::Prenom!]
    <p>[!P::Details!]</p>
</div>
[/LIMIT]
[/STORPROC]


<div class="alert alert-warning">
    Recherche de nouveaux partenaires
</div>
    <form method="POST" class="horizontal-form">
        <div class="form-group">
            <label>Recherche</label>
            <div class="col-sm7">
                <input type="text">
            </div>
        <div>    
    </form>
    
[STORPROC Reservations/Partenaire/Disponible=1&Id!=[!Partenaire::Id!]|P]
[LIMIT 0|10]
<div class="btn-tennis">
    <a class="btn btn-danger pull-right" onclick="onPlus([!P::Id!])"><span class="glyphicon glyphicon-plus"></span></a>
    <span class="label label-primary pull-right" style="margin-right:50px;">[!P::Email!]</span>
    <b>[!P::Nom!] - [!P::Prenom!]</b>
    <p>[!P::Details!]</p>
</div>
[/LIMIT]
[/STORPROC]