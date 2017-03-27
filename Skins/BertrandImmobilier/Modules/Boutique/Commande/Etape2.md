[IF [!AnnulerPaiement!]]
    [!myReservation::Delete()!]
    [STORPROC [!myReservation::getChildren(Paiement)!]|P]
        [!P::Delete()!]
    [/STORPROC]
    [COOKIE Del|myReservation]
    [REDIRECT][/REDIRECT]
[/IF]
<div class="container">
    <h1>ERREUR DE PAIEMENT</h1>
    <a href="/Commande/EtapePaiement" class="btn btn-success">Reessayer le paiement</a>
    <a href="?AnnulerPaiement=1" class="btn btn-danger">Annuler le paiement</a>
</div>