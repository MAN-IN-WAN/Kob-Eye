[STORPROC [!Query!]|I|0|1]
    [METHOD I|addParent][PARAM]IncidentClient/ParametresEtat/5[/PARAM][/METHOD]
    [IF [!I::Save()!]]
        <div class="alert alert-success">Votre incident est clôturé</div>
    [ELSE]
        <div class="alert alert-error">Une erreur est survenu lors de la clôture de votre incident. Merci de contacter votre prestataire Océan</div>
    [/IF]
[/STORPROC]