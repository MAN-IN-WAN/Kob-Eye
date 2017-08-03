<div class="row">
    <div class="col-md-12">
        <h1>Nous attendons le retour de votre banque.</h1>
        <div class="alert alert-warning">Veuillez patienter, nous procédons au traitement de votre règlement...</div>
        <a href="/" class="btn btn-danger btn-large btn-block">Retour à l'accueil</a>
        <script>
            //[!DEBUG::PAI!]
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

            [IF [!P!]!=]
                [!PAIEMENT:=[!P!]!]
            [ELSE]
                //détection du paiement en cours.
                [!FAC:=[!CurrentClient::getCurrentFacture()!]!]
                [!PAIEMENT:=[!FAC::getPaiement()!]!]
            [/IF]
                //[!DEBUG::PAIEMENT!]
            [SWITCH [!PAIEMENT::Etat!]|=]
                [CASE 0]
                    //on refresh
                    var t = setTimeout(function () {
                        window.location.replace("[!Domaine!]/Reservations/Facture/Attente?Ref="+[!PAIEMENT::Id!]);
                    },1000);
                [/CASE]
                [CASE 1]
                    //page confirmation
                    var t = setTimeout(function () {
                        window.location.replace("[!Domaine!]/Reservations/Facture/[!FAC::Id!]/Confirmation?Ref="+[!PAIEMENT::Id!]);
                    },500);
                [/CASE]
                [DEFAULT]
                    //paiement à refaire
                    var t = setTimeout(function () {
                        window.location.replace("[!Domaine!]/Reservations/Facture/[!FAC::Id!]/Annulation?Ref="+[!PAIEMENT::Id!]);
                    },500);
                [/DEFAULT]
            [/SWITCH]
        </script>
    </div>
</div>
