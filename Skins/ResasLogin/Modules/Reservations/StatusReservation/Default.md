

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
                //Envoi de mail
                //A la personne qui viens de valider
                [!Parten::sendConfirmationMail(1,[!Reserv!])!]

                //Au client qui a reservé
                [!Cli::sendConfirmationPartenaireMail(1,[!Parten!],[!Reserv!])!]
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
            <input type="submit" id="confirme" name="status" value="Oui">
            <input type="submit" id="infirme" name="status" value="Non">
        </div>
    </div>
</form>
[ELSE]
<div class="row">
    <div class="col-md-12">
        <h2>Merci</h2>
        <a href="/" alt="retour à l'accueil" title="Retour à l'accueil">Retour à l'accueil</a>
    </div>
</div>
[/IF]
