[STORPROC Reservations/TypePaiement/Actif=1|TP]
    [!Plg:=[!TP::getPlugin()!]!]
    [!PaiementID:=[!Plg::retrouvePaiementEtape4s()!]!]
    [LOG]PAIEMENT [!PaiementID!][/LOG]
    [IF [!PaiementID!]>0]
        [STORPROC Reservations/Paiement/[!PaiementID!]|P|0|1][/STORPROC]
    [/IF]
[/STORPROC]

[IF [!P!]]
    [!Status:=[!P::getOneParent(StatusReservation)!]!]
[/IF]


[IF [!Status!]]
    <div class="row">
        <div class="col-md-12">
            <h2>Votre paiement [!P::Reference!] a bien été pris en compte</h2>

            <h3><b>Total payé:</b><span class="label label-success" >[!Utils::getPrice([!P::Montant!])!] €</span></h3>
            <a href="/" class="btn btn-danger btn-lg btn-block">Retour à l'accueil</a>
        </div>
    </div>
[ELSE]
    [STORPROC [!Query!]|F|0|1]
    <div class="row">
        <div class="col-md-12">
                <h2>Votre facture [!F::NumFac!] a bien été payée</h2>
                [STORPROC Reservations/Facture/[!F::Id!]/LigneFacture|Lf]
                    <div class="alert alert-info">[!Lf::Libelle!] (x[!Lf::Quantite!]) <span class="label label-primary pull-right" >[!Utils::getPrice([!Lf::MontantTTC!])!] €</span></div>
                [/STORPROC]
            </ul></h3>
            <h3><b>Total payé:</b><span class="label label-success" >[!Utils::getPrice([!F::MontantTTC!])!] €</span></h3>
            <a href="/" class="btn btn-danger btn-lg btn-block">Retour à l'accueil</a>
        </div>
    </div>
    [/STORPROC]
[/IF]