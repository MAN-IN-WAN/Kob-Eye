<div class="alert alert-warning">
    Bienvenue sur l'espace client Tennis Forever. <br />
    Consultez vos réservations, modifiez vos informations et réservez vos activités.
</div>
<a href="/[!Sys::getMenu(TennisForever/TypeCourt)!]" class="btn-tennis">Réserver une activité</a>

[STORPROC TennisForever/Reservation/Valide=0|RES]
<div class="alert alert-danger">
Mes réservations non complètes
</div>
    [LIMIT 0|10]
        <a href="/[!Sys::getMenu(TennisForever/Reservation)!]/[!RES::Id!]" class="btn-tennis">
        <span class="label label-danger pull-right">[!Utils::getPrice([!RES::getTotal()!])!] €</span>
        Complèter ma réservation<br/>
        <small>Du [DATE d/m/Y à H:i][!RES::DateDebut!][/DATE] pour une durée de [!RES::Duree!] minutes et avec [!RES::NbParticipant!] participants.</small>
        </a>
    [/LIMIT]
[/STORPROC]

        [STORPROC TennisForever/Reservation/Valide=1&DateFin>[!TMS::Now!]|RES]
<div class="alert alert-success">
Mes réservations à venir
</div>
        [LIMIT 0|10]
<a href="/[!Sys::getMenu(TennisForever/Reservation)!]/[!RES::Id!]" class="btn-tennis">
<span class="label label-success pull-right">[!Utils::getPrice([!RES::getTotal()!])!] €</span>
Consulter ma réservation<br/>
<small>Du [DATE d/m/Y à H:i][!RES::DateDebut!][/DATE] pour une durée de [!RES::Duree!] minutes et avec [!RES::NbParticipant!] participants.</small>
</a>
        [/LIMIT]
        [/STORPROC]