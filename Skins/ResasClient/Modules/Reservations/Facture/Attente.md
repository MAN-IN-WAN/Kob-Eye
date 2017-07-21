[STORPROC [!Query!]|F|0|1]
<div class="row">
    <div class="col-md-12">
        <form action="" method="POST">
            <h1>Votre facture [!F::FacNum!] est en attente de paiement. Vous recevrez un email vous donnant les le statut de votre paiement.</h1>
            <h3><b>Total à payer:</b><span class="label label-success" >[!Utils::getPrice([!F::MontantTTC!])!] €</span></h3>
            <a href="/" class="btn btn-danger btn-large btn-block">Retour à l'accueil</a>
        </form>
    </div>
</div>
[/STORPROC]
