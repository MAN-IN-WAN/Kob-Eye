[STORPROC [!Query!]|R|0|1]
<div class="row">
    <div class="col-md-12">

        <h1>Redirection vers le terminal de paiement ...</h1>
        [IF [!R::getTotal()!]>0]
            //génération de la facture et du paiement
            [!Facture:=[!R::getFacture()!]!]
            [!Paiement:=[!Facture::getPaiement()!]!]
            [STORPROC TennisForever/TypePaiement/Actif=1|TP]
                [!Plugin:=[!TP::getPlugin()!]!]
                [!Plugin::getCodeHTML([!Paiement!])!]
            [/STORPROC]

        [ELSE]
        [/IF]
    </div>
</div>
[/STORPROC]