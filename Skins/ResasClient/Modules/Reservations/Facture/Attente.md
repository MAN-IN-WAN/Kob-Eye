<div class="row">
    <div class="col-md-12">
        <h1>Nous attendons le retour de votre banque.</h1>
        <div class="alert alert-warning">Veuillez patienter, nous procédons au traitement de votre règlement...</div>
        <a href="/" class="btn btn-danger btn-large btn-block">Retour à l'accueil</a>
        <script>
            //détection du paiement en cours.
            [!FAC:=[!CurrentClient::getCurrentFacture()!]!]
            [!PAIEMENT:=[!FAC::getPaiement()!]!]
            [SWITCH [!PAIEMENT::Etat!]|=]
                [CASE 0]
                    //on refresh
                    var t = setTimeout(function () {
                        window.location.replace("[!Domaine!]/Reservations/Facture/Attente");
                    },1000);
                [/CASE]
                [CASE 1]
                    //page confirmation
                    var t = setTimeout(function () {
                        window.location.replace("[!Domaine!]/Reservations/Facture/[!FAC::Id!]/Confirmation");
                    },500);
                [/CASE]
                [DEFAULT]
                    //paiement à refaire
                    var t = setTimeout(function () {
                        window.location.replace("[!Domaine!]/Reservations/Facture/[!FAC::Id!]/Annulation");
                    },500);
                [/DEFAULT]
            [/SWITCH]
        </script>
    </div>
</div>
