[STORPROC [!Query!]|R|0|1]

    [IF [!Valider!]=Valider la réservation]
        <div class="alert alert-success">
            La réservation a été validée avec succès.
        </div>
        //Validation de la réservation
        [!R::setValide()!]
    [/IF]

    [IF [!Valider!]=Payer en carte bleue]
        [!R::Save()!]
        [COOKIE Set|RES|R]
        <div class="alert alert-success">
        La réservation a étée validée avec succès.
        </div>
        [REDIRECT][!Sys::getMenu(Reservations/Reservation)!]/[!R::Id!]/Payer[/REDIRECT]
    [/IF]

    <div class="row">
    <div class="col-md-12">
        <form action="" method="POST">
            <h1>Détail de votre réservation</h1>
            [!Service:=[!R::getService()!]!]
            [!Court:=[!R::getCourt()!]!]
            <h3><b>Description: </b>[!Service::Titre!] pour [!Court::Titre!]</h3>
            <h3><b>Date: </b>le [DATE d/m/Y à H:i:s][!R::DateDebut!][/DATE]</h3>

            [!Client:=[!Module::Reservations::getCurrentClient()!]!]
            [STORPROC Reservations/TypeCourt/Court/[!Court::Id!]|TC|0|1][/STORPROC]

            [SWITCH [!TC::GestionInvite!]|=]
                [CASE Quantitatif]
                    [COUNT Reservations/Reservation/[!R::Id!]/Partenaire|Pa]
                    <h3><b>Partenaire(s): [!Pa!]</b>
                [/CASE]
                [CASE Nominatif]
                    [STORPROC Reservations/Reservation/[!R::Id!]/StatusReservation|SR]
                    <h3><b>Partenaire(s):</b>
                        <ul>
                            [LIMIT 0|100]
                            [!Pa:=[!SR::getOneChild(Partenaire)!]!]
                            [IF [!R::PaiementParticipant!]]
                            <li style="overflow:hidden;">[!Pa::Nom!] [!Pa::Prenom!] : <span class="label label-primary" >[!Pa::Email!]</span>  <span class="pull-right label-custom [IF [!SR::Present!]=Oui]label-success[ELSE][IF[!SR::Present!]=Non]label-warning[ELSE]label-default[/IF][/IF]" >[!SR::Present!]</span><span class="pull-right">Présent : </span><div class="clearfix"></div><span class="pull-right label-custom [IF[!SR::Paye!]]label-success[ELSE]label-danger[/IF]" >[!Utils::getPrice([!SR::MontantPaye!])!] € [IF[!SR::Paye!]]le [DATE d/m/Y H:i:s][!SR::tmsEdit!][/DATE][ELSE] non payé [/IF]</span><span class="pull-right">Paiement : </span></li>
                            [ELSE]
                                <li>[!Pa::Nom!] [!Pa::Prenom!] : <span class="label label-primary" >[!Pa::Email!]</span>  <span class="pull-right label-custom [IF [!SR::Present!]=Oui]label-success[ELSE][IF[!SR::Present!]=Non]label-warning[ELSE]label-default[/IF][/IF]" >[!SR::Present!]</span><span class="pull-right">Présent : </span></li>
                            [/IF]
                            [/LIMIT]
                        </ul></h3>
                    [/STORPROC]
                [/CASE]
            [/SWITCH]
            [IF [!R::PaiementParticipant!]]
                <h3><b>Montant restant</b></h3>
                [!F:=[!R::getFacture()!]!]
                [!Paiement:=[!F::getOneChild(Paiement/PaiementFractionne=1)!]!]
                [IF [!Paiement::DebitEffectue!]]
                    <div class="alert alert-success">Débité le [DATE d/m/Y à H:i:s][!R::DateDebut:-1800!][/DATE] <span class="label label-primary pull-right" >[!Utils::getPrice([!Paiement::Montant!])!] €</span></div>
                [ELSE]
                    <div class="alert alert-warning">Sera débité le [DATE d/m/Y à H:i:s][!R::DateDebut:-1800!][/DATE] <span class="label label-primary pull-right" >[!Utils::getPrice([!Paiement::Montant!])!] €</span></div>
                [/IF]
            [/IF]
            <h3><b>Total :</b></h3>
            [STORPROC Reservations/Reservation/[!R::Id!]/LigneFacture|Lf]
            <div class="alert alert-info">[!Lf::Libelle!] (x[!Lf::Quantite!]) <span class="label label-primary pull-right" >[!Utils::getPrice([!Lf::MontantTTC!])!] €</span></div>
            [/STORPROC]
            </ul></h3>
        <h3><b>Total à payer:</b><span class="label label-success" >[!Utils::getPrice([!R::getTotal()!])!] €</span></h3>

            [IF [!R::Valide!]=]
                [IF [!R::getTotal()!]>0]
                    [IF [!R::DateFin!]>[!TMS::Now!]]
                        <input type="submit" class="btn btn-success btn-large btn-block" name="Valider" value="Payer en carte bleue" />
                    [/IF]
                    <a href="/[!Sys::getMenu(Reservations/Reservation)!]/[!R::Id!]/Supprimer" class="btn btn-warning btn-large btn-block" >Annuler la réservation</a>
                [ELSE]
                    [IF [!R::DateFin!]>[!TMS::Now!]]
                        <input type="submit" class="btn btn-success btn-large btn-block" name="Valider" value="Valider la réservation">
                    [/IF]
                    <a href="/[!Sys::getMenu(Reservations/Reservation)!]/[!R::Id!]/Supprimer" class="btn btn-warning btn-large btn-block" >Annuler la réservation</a>
                [/IF]
            [ELSE]
                <div class="alert alert-success">
                    Cette réservation est validée.
                </div>
                [IF [!R::getTotal()!]>0]
                    <div class="alert alert-success">
                        Cette réservation est payée.
                    </div>
                    [!limit:=[!R::DateDebut!]!]
                    [!limit-=86400!]
                    [IF [!limit!]>[!TMS::Now!]]
                    <a href="/[!Sys::getMenu(Reservations/Reservation)!]/[!R::Id!]/Supprimer" class="btn btn-warning btn-large btn-block" >Annuler la réservation</a>
                    [/IF]
                [ELSE]
                    [IF [!R::DateFin!]>[!TMS::Now!]]
                        <a href="/[!Sys::getMenu(Reservations/Reservation)!]/[!R::Id!]/Supprimer" class="btn btn-warning btn-large btn-block" >Annuler la réservation</a>
                    [/IF]
                [/IF]
            [/IF]
                <a href="/" class="btn btn-danger btn-large btn-block">Retour à l'accueil</a>
        </form>
    </div>
    </div>
[/STORPROC]