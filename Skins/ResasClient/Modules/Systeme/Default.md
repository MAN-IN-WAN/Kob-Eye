<div class="alert alert-warning">
    Bienvenue sur l'espace client du Dôme du Foot. <br />
    Consultez vos réservations, modifiez vos informations et réservez vos activités.
</div>
[!Client:=[!Module::Reservations::getCurrentClient()!]!]
[STORPROC Systeme/User/Id=[!Client::UserId!]|Us|0|1][/STORPROC]
//[IF [!Us::Id!]=18&&[!Client::Abonne!]=1]
//   <a href="/[!Sys::getMenu(Reservations/TypeCourt)!]" class="btn-tennis">Réserver une activité</a>
//[ELSE]
// [IF [!SERVER::REMOTE_ADDR!]=185.71.149.9||[!SERVER::REMOTE_ADDR!]=31.35.56.84]
       <a href="/[!Sys::getMenu(Reservations/TypeCourt)!]" class="btn-tennis">Réserver une activité</a>
// [/IF]
//[/IF]
[!Client:=[!Module::Reservations::getCurrentClient()!]!]
[STORPROC Reservations/Client/[!Client::Id!]/Reservation/Valide=0|RES]
<div class="alert alert-danger">
Mes réservations non complètes
</div>
    [LIMIT 0|10]
        <a href="/[!Sys::getMenu(Reservations/Reservation)!]/[!RES::Id!]" class="btn-tennis">
        <span class="label label-danger pull-right">[!Utils::getPrice([!RES::getTotal()!])!] €</span>
        Complèter ma réservation<br/>
            [IF [!RES::Service!]]
            <small>De [DATE H:i][!RES::DateDebut!][/DATE] pour une durée de [!RES::Duree!] minutes et avec [!RES::NbParticipant!] participants.</small>
            [/IF]
            <ul>
                [STORPROC [!RES::getLigneFacture()!]|Lf]
                <li>[!Lf::Quantite!] x [!Lf::Libelle!]</li>
                [/STORPROC]
            </ul>
        </a>
    [/LIMIT]
[/STORPROC]

        [STORPROC Reservations/Client/[!Client::Id!]/Reservation/Valide=1&DateFin>[!TMS::Now!]|RES]
<div class="alert alert-success">
Mes réservations à venir
</div>
        [LIMIT 0|10]
<a href="/[!Sys::getMenu(Reservations/Reservation)!]/[!RES::Id!]" class="btn-tennis">
<span class="label label-success pull-right">[!Utils::getPrice([!RES::getTotal()!])!] €</span>
Consulter ma réservation Du [DATE d/m/Y][!RES::DateDebut!][/DATE]<br/>
    [IF [!RES::Service!]]
        <small>De [DATE H:i][!RES::DateDebut!][/DATE] pour une durée de [!RES::Duree!] minutes et avec [!RES::NbParticipant!] participants.</small>
    [/IF]
    <ul>
    [STORPROC [!RES::getLigneFacture()!]|Lf]
        <li>[!Lf::Quantite!] x [!Lf::Libelle!]</li>
    [/STORPROC]
    </ul>
</a>
        [/LIMIT]
        [/STORPROC]