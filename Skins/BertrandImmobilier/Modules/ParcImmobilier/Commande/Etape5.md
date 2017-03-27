[IF [!myReservation!]=]
[REDIRECT][/REDIRECT]
[/IF]


[IF [!Nom!]]
[!myReservation::Nom:=[!Nom!]!]
[!myReservation::Prenom:=[!Prenom!]!]
[!myReservation::Email:=[!Email!]!]
[!myReservation::Telephone:=[!Telephone!]!]

[COOKIE Set|myReservation|myReservation]

[REDIRECT]Commande/EtapePaiement[/REDIRECT]
[/IF]

<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
                <div class="span12">
                    <h1 class="page-header">Confirmation de votre réservation</h1>
                    [STORPROC ParcImmobilier/Reservation/[!myReservation::Id!]|mR]
                    <p>
                        Le paiement de pour votre réservation du [DATE m/d/Y][!mR::Debut!][/DATE] au [DATE m/d/Y][!mR::Fin!][/DATE] a bien été pris en compte.
                    </p>
                    <p>
                        Toute l'équipe de Bertrand Immobilier vous remercie pour votre confiance. Nous vous contacterons dans les plus brefs délais afin de vous indiquer les modalités inhérentes à cette réservation.
                    </p>
                    [/STORPROC]
                </div>
            </div>
        </div>
    </div>
</div>