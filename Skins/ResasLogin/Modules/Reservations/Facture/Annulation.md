
[IF [!PAI!]>0]  //PAI extrait des variables sessions setté via COOKIE
    [STORPROC Reservations/Paiement/[!PAI!]|P|0|1][/STORPROC]
[ELSE]
    [STORPROC Reservations/TypePaiement/Actif=1|TP]
        [!Plg:=[!TP::getPlugin()!]!]
        [!PaiementID:=[!Plg::retrouvePaiementEtape4s()!]!]
        [LOG]PAIEMENT [!PaiementID!][/LOG]
        [IF [!PaiementID!]>0]
            [STORPROC Reservations/Paiement/[!PaiementID!]|P|0|1][/STORPROC]
        [/IF]
    [/STORPROC]
[/IF]

[IF [!P!]]
    [!Status:=[!P::getOneParent(StatusReservation)!]!]
[/IF]


[IF [!Status!]]
    <div class="row">
        <div class="col-md-12">
            <h2>Votre paiement [!Utils::sprintf(%06d,[!P::Id!])!] n'a pas abouti</h2>

            <h3><b>Total à payer:</b><span class="label label-success" >[!Utils::getPrice([!P::Montant!])!] €</span></h3>
            <a href="/[!Sys::getMenu(Reservations/StatusReservation)!]/[!Status::Id!]/Payer" class="btn btn-success btn-large btn-block">Payer en carte bleue</a>
            <a href="/" class="btn btn-danger btn-lg btn-block">Retour à l'accueil</a>
        </div>
    </div>
[ELSE]
    [!F:=[!P::getOneParent(Facture)!]!]
    //[STORPROC [!Query!]|F|0|1]
    <div class="row">
        <div class="col-md-12">
            <form action="" method="POST">
                <h1>Votre facture [!F::NumFac!] n'est pas payée</h1>
                <h3><b>Total à payer:</b><span class="label label-success" >[!Utils::getPrice([!F::MontantTTC!])!] €</span></h3>
                [STORPROC Reservations/Reservation/Facture/[!F::Id!]|R|0|1][/STORPROC]
                [IF [!F::Valide!]=]
                <a href="/Reservation/[!R::Id!]/Payer" class="btn btn-success btn-large btn-block">Payer en carte bleue</a>
                <a href="/" class="btn btn-danger btn-large btn-block">Retour à l'accueil</a>
                [/IF]
            </form>
        </div>
    </div>
    //[/STORPROC]
[/IF]