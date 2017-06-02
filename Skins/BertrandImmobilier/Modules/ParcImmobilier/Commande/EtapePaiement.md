
[IF [!myReservation!]=]
    [REDIRECT][/REDIRECT]
[/IF]

[STORPROC Boutique/TypePaiement/Actif=1|TP|0|1]

    [OBJ Boutique|Paiement|Paiement]
    [!myReservation::Save()!]
    [!Paiement::AddParent([!myReservation!])!]
    [!Paiement::AddParent([!TP!])!]
    [!Paiement::Montant:=[!myReservation::Prix!]!]
    [!Paiement::Save()!]
    [COOKIE Set|myReservation|myReservation]

        <div id="content">
            <div class="container">
                <div id="main">

                    <div class="row">
                        <div class="span9">
                            <h1 class="page-header">Paiement de votre réservation</h1>
                            [STORPROC [!myReservation::LotQuery!]|Lot|0|1]
                                <img src="/[!Lot::Photo1!].mini.135x75.jpg" alt="" class="miniature-paiement">
                                <h5>[!Lot::Titre!]</h5>
                            <p>
                                [!Lot::TypeBien!] à [STORPROC [!Lot::getParents(Ville)!]|Ville][!Ville::Nom!][/STORPROC] d'une surface de [!Lot::SuperficieCarrez!] m² avec [!Lot::Chambres!] chambre(s).<br>
                                Location du samedi [DATE d/m/Y][!myReservation::Debut!][/DATE] au samedi [DATE d/m/Y][!myReservation::Fin!][/DATE].
                            </p>
                            [/STORPROC]
                            <h2>Accéder au paiement sécurisé</h2>
                            [!Plugin:=[!TP::getPlugin()!]!]
                            [!Plugin::getCodeHTML([!Paiement!])!]
                        </div>
                        <div class="span3 margin-top-60">
                            <h3>Résumé de votre réservation</h3>
                            <h5>Date d'arrivée</h5>
                            <p>Samedi [DATE d/m/Y][!myReservation::Fin!][/DATE]</p>
                            <h5>Date de départ</h5>
                            <p>Samedi [DATE d/m/Y][!myReservation::Debut!][/DATE]</p>
                            <h5>Montant de votre réservation</h5>
                            <p>[!myReservation::Prix!] €</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
[/STORPROC]

