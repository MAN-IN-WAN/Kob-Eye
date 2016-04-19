[STORPROC [!Query!]|R|0|1]
<div class="row">
    <div class="col-md-12">
        <h1>Détail de votre réservation</h1>
        [!Service:=[!R::getService()!]!]
        [!Court:=[!R::getCourt()!]!]
        <h3><b>Description: </b>[!Service::Titre!] pour [!Court::Titre!]</h3>
        <h3><b>Date: </b>le [DATE d/m/Y][!R::DateDebut!][/DATE] de [DATE H:i][!R::DateDebut!][/DATE] à [DATE H:i][!R::DateFin!][/DATE]</h3>
        <h3><b>Partenaire(s):</b>
        <ul>
        [STORPROC TennisForever/Reservation/[!R::Id!]/Partenaire|Pa]
            <li>[!Pa::Nom!] <span class="label label-primary" >[!Pa::Email!]</span></li>
        [/STORPROC]
        </ul></h3>
        <h3><b>Total :</b></h3>
            [STORPROC TennisForever/Reservation/[!R::Id!]/LigneFacture|Lf]
            <div class="alert alert-info">[!Lf::Libelle!] (x[!Lf::Quantite!]) <span class="label label-primary pull-right" >[!Utils::getPrice([!Lf::MontantTTC!])!] €</span></div>
        [/STORPROC]
        </ul></h3>
        <h3><b>Total à payer:</b><span class="label label-success" >[!Utils::getPrice([!R::getTotal()!])!] €</span></h3>
        [IF [!R::getTotal()!]>0]
            <a href="" class="btn btn-success btn-large btn-block">Payer</a>
        [ELSE]
            <a href="" class="btn btn-success btn-large btn-block">Valider la réservation</a>
        [/IF]
    </div>
</div>
[/STORPROC]