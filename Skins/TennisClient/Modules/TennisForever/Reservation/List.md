<h1>Liste de mes réservations</h1>
<ul>
[!Client:=[!Module::TennisForever::getCurrentClient()!]!]

[STORPROC TennisForever/Client/[!Client::Id!]/Reservation|R]
    <li>Réservation du [DATE d/m/Y à H:i][!R::DateDebut!][/DATE]</li>
[/STORPROC]
</ul>