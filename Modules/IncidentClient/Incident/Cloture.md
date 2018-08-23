[STORPROC [!Query!]|I|0|1]
    [STORPROC IncidentClient/ParametresEtat/Cloture=1|P|0|1]
        [!I::addParent(IncidentClient/ParametresEtat/[!P::Id!])!]
    [/STORPROC]
    [IF [!I::Save()!]]
        <div class="alert alert-success">Votre incident est clôturé</div>
    [ELSE]
        <div class="alert alert-error">Une erreur est survenu lors de la clôture de votre incident. Merci de contacter votre prestataire Océan</div>
    [/IF]
[/STORPROC]