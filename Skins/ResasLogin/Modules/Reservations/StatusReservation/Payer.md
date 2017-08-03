[STORPROC [!Query!]|S|0|1]
    [!Reserv:=[!S::getOneParent(Reservation)!]!]
    [!Partenaire:=[!S::getOneChild(Partenaire)!]!]
<div class="row">
    <div class="col-md-12">

        <h1>Redirection vers le terminal de paiement ...</h1>
        [IF [!S::MontantPaye!]>0]
            [!Paiement:=[!S::getPaiement()!]!]
            [!Paie:=[!Paiement::Id!]!]
            [COOKIE Set|PAI|Paie]
            [STORPROC Reservations/TypePaiement/Actif=1|TP]
                [!Plugin:=[!TP::getPlugin()!]!]
                [!Plugin::getCodeHTML([!Paiement!])!]
            [/STORPROC]
        [ELSE]
        [/IF]
    </div>
</div>
[/STORPROC]