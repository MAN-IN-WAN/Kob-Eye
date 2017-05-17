[STORPROC [!Query!]|F|0|1]
<div class="row">
    <div class="col-md-12">
        <form action="" method="POST">
            <h1>Votre facture [!F::FacNum!] n'est pas payée</h1>
            <h3><b>Total à payer:</b><span class="label label-success" >[!Utils::getPrice([!F::MontantTTC!])!] €</span></h3>

            [IF [!F::Valide!]=]
            <a href="Payer" class="btn btn-success btn-large btn-block">Payer en carte bleue</a>
            <a href="/" class="btn btn-danger btn-large btn-block">Retour à l'accueil</a>
            [/IF]
        </form>
    </div>
</div>
[/STORPROC]