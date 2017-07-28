[INFO [!Query!]|I]

[IF [!I::TypeSearch!]=Direct]
    [STORPROC [!Query!]|Status][/STORPROC]
    [IF [!Status!]!=]
        [LIB Mail|M]

        [!Reserv:=[!Status::getOneParent(Reservation)!]!]
        [!Parten:=[!Status::getOneChild(Partenaire)!]!]
        [!Cli:=[!Reserv::getOneParent(Client)!]!]
//[!DEBUG::Status!]
//[!DEBUG::Reserv!]
//[!DEBUG::Parten!]
        [IF [!valide!]=1]
            [METHOD Status|set]
                [PARAM]Present[/PARAM]
                [PARAM][!status!][/PARAM]
            [/METHOD]
            [!Status::Save()!]
            [IF [!status!]=Oui]
                [IF [!MontantPaye!]>0]
                    [IF [!Paye!]]
                        //Envoi de mail
                        //A la personne qui viens de valider
                        [!Parten::sendConfirmationMail(1,[!Reserv!])!]

                        //Au client qui a reservé
                        [!Cli::sendConfirmationPartenaireMail(1,[!Parten!],[!Reserv!])!]
                    [ELSE]

                    [/IF]
                [ELSE]
                    //Envoi de mail
                    //A la personne qui viens de valider
                    [!Parten::sendConfirmationMail(1,[!Reserv!])!]

                    //Au client qui a reservé
                    [!Cli::sendConfirmationPartenaireMail(1,[!Parten!],[!Reserv!])!]
                [/IF]
            [ELSE]
                //Envoi de mail
                //A la personne qui viens de valider
                //A la personne qui viens de valider
                [!Parten::sendConfirmationMail(0,[!Reserv!])!]

                //Au client qui a reservé
                [!Cli::sendConfirmationPartenaireMail(0,[!Parten!],[!Reserv!])!]
            [/IF]
        [/IF]
    [ELSE]
        [REDIRECT][/REDIRECT]
    [/IF]
[ELSE]
    [REDIRECT][/REDIRECT]
[/IF]

[IF [!Status::Present!]=NC]
<form action="" method="POST">
    <div class="row">
        <div class="col-md-12">
            <h5>Vous avez été invité à participer à un match le [DATE d/m/Y][!Reserv::DateDebut!][/DATE] A partir  de [DATE H:i:s][!Reserv::DateDebut!][/DATE]</h5>
            <h2>Souhaitez-vous y participer ?</h2>
            <input type="hidden" id="valide" name="valide" value="1">
            <input type="submit" class="btn btn-info" id="confirme" name="status" value="Oui">
            <input type="submit" class="btn btn-info" id="infirme" name="status" value="Non">
        </div>
    </div>
</form>
[ELSE]
    [IF [!Status::Present!]=Oui]
        [IF [!Status::Paye!]]
            <div class="row">
                <div class="col-md-12">
                    <h2>Merci d'avoir confirmé votre présence</h2>
                    <a href="/" alt="retour à l'accueil" class="btn btn-success btn-lg btn-block" title="Retour à l'accueil">Retour à l'accueil</a>
                </div>
            </div>
        [ELSE]
            [IF [!Status::MontantPaye!]>0]
                <div class="row">
                    <div class="col-md-12">
                        <h2>Merci de bien vouloir régler votre part</h2>
                        <a href="/[!Sys::getMenu(Reservations/StatusReservation)!]/[!Status::Id!]/Payer" class="btn btn-info btn-lg btn-block" alt="Payer en carte bleue" title="Payer en carte bleue">Payer en carte bleue</a>
                    </div>
                </div>
            [ELSE]
                <div class="row">
                    <div class="col-md-12">
                        <h2>Merci d'avoir confirmé votre présence</h2>
                        <a href="/" alt="retour à l'accueil" class="btn btn-success btn-lg btn-block" title="Retour à l'accueil">Retour à l'accueil</a>
                    </div>
                </div>
            [/IF]
        [/IF]
    [ELSE]
    <div class="row">
        <div class="col-md-12">
            <h2>Merci de votre retour</h2>
            <a href="/" alt="retour à l'accueil" class="btn btn-success btn-lg btn-block" title="Retour à l'accueil">Retour à l'accueil</a>
        </div>
    </div>
    [/IF]
[/IF]
