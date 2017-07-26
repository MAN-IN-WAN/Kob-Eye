<div class="row">
    <div class="col-md-12">
        <h1>Nous attendons le retour de votre banque.</h1>
        <h3><b>Total à payer:</b><span class="label label-success" >[!Utils::getPrice([!F::MontantTTC!])!] €</span></h3>
        <p>Veuillez patienter, nous procédons au traitement de votre règlement...</p>
        <a href="/" class="btn btn-danger btn-large btn-block">Retour à l'accueil</a>
        <script>
            var t = setTimeout(2000,function () {
                alert('redirection vers la confirmation de paiement.');
            });
        </script>
    </div>
</div>
