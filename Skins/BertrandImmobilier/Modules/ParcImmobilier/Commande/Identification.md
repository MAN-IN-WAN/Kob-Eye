[IF [!myReservation!]=]
    [REDIRECT][/REDIRECT]
[/IF]


[IF [!Nom!]]
    [!myReservation::Nom:=[!Nom!]!]
    [!myReservation::Prenom:=[!Prenom!]!]
    [!myReservation::Email:=[!Email!]!]
    [!myReservation::Telephone:=[!Telephone!]!]
    [!myReservation::Adresse:=[!Adresse!]!]
    [!myReservation::CodePostal:=[!CodePostal!]!]
    [!myReservation::Ville:=[!Ville!]!]

    [COOKIE Set|myReservation|myReservation]

    [REDIRECT]Commande/EtapePaiement[/REDIRECT]
[/IF]

<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
                <div class="span9">
                    <h1 class="page-header">Confirmation de votre réservation</h1>
                    <form method="post" class="contact-form" action="?">
                        <div class="control-group">
                            <label class="control-label" for="Nom">
                                Nom
                                <span class="form-required" title="Ce champs est obligatoire.">*</span>
                            </label>
                            <div class="controls">
                                <input type="text" id="Nom" name="Nom" required="required">
                            </div><!-- /.controls -->
                        </div><!-- /.control-group -->

                        <div class="control-group">
                            <label class="control-label" for="Prenom">
                                Prénom
                                <span class="form-required" title="Ce champs estobligatoire.">*</span>
                            </label>
                            <div class="controls">
                                <input type="text" id="Prenom" name="Prenom" required="required">
                            </div><!-- /.controls -->
                        </div><!-- /.control-group -->

                        <div class="control-group">
                            <label class="control-label" for="Email">
                                Email
                                <span class="form-required" title="Ce champs estobligatoire.">*</span>
                            </label>
                            <div class="controls">
                                <input type="email" id="Email" name="Email" required="required">
                            </div><!-- /.controls -->
                        </div><!-- /.control-group -->

                        <div class="control-group">
                            <label class="control-label" for="Telephone">
                                Téléphone
                                <span class="form-required" title="Ce champs estobligatoire.">*</span>
                            </label>
                            <div class="controls">
                                <input type="text" id="Telephone" name="Telephone" required="required">
                            </div><!-- /.controls -->
                        </div><!-- /.control-group -->

                        <div class="control-group">
                            <label class="control-label" for="Adresse">
                                Adresse
                                <span class="form-required" title="Ce champs estobligatoire.">*</span>
                            </label>
                            <div class="controls">
                                <input type="text" id="Adresse" name="Adresse" required="required">
                            </div><!-- /.controls -->
                        </div><!-- /.control-group -->
                        
                        <div class="control-group">
                            <label class="control-label" for="CodePostal">
                                Code postal
                                <span class="form-required" title="Ce champs estobligatoire.">*</span>
                            </label>
                            <div class="controls">
                                <input type="text" id="CodePostal" name="CodePostal" required="required">
                            </div><!-- /.controls -->
                        </div><!-- /.control-group -->
                        
                        <div class="control-group">
                            <label class="control-label" for="Ville">
                                Ville
                                <span class="form-required" title="Ce champs estobligatoire.">*</span>
                            </label>
                            <div class="controls">
                                <input type="text" id="Ville" name="Ville" required="required">
                            </div><!-- /.controls -->
                        </div><!-- /.control-group -->

                        <div class="form-actions">
                            <input type="submit" class="btn btn-primary" value="Confirmer ma réservation de [!myReservation::Prix!] € et accéder au paiment sécurisé">
                        </div><!-- /.form-actions -->
                    </form>
                </div>
                <div class="span3 margin-top-60">
                    <h3>Résumé de votre réservation</h3>
                                        <h5>Date d'arrivée</h5>
                        <p>Samedi [!myReservation::Debut!]</p>
                    <h5>Date de départ</h5>
                        <p>Samedi [!myReservation::Fin!]</p>
                    <h5>Montant de votre réservation</h5>
                        <p>[!myReservation::Prix!] €</p>
                    <span style="display:inline-block;background-color:#E63434;color:#FFD700;padding:0px 30px;font-weight:700;margin-bottom:5px">Important</span>
                    <p style="text-align: justify">
                        Les [!myReservation::Prix!] € à payer pour enregistrer votre réservation représentent un acompte d'environ 25 % du montant de votre location.
                    </p>
                     <p style="text-align: justify">
                        Votre réservation sera pleinement effective lors de la réception par Bertrand Immobilier des contrats de location signés.
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>
