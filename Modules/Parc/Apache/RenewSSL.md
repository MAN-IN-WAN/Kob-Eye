[STORPROC [!Query!]|O|0|1]
    [IF [!O::enableSsl(1)!]][/IF]
    [STORPROC [!O::Error!]|E]
        <div class="alert alert-danger">
            <ul>
                [LIMIT 0|100]
                <li>[!E::Message!]</li>
                [/LIMIT]
            </ul>
        </div>
        [NORESULT]
            <div class="alert alert-success">Renouvellement SSL de l'objet [!Query!] réussie. Si la méthode est "Letsencrypt" alors une tache sera crée et activée dans 1 minutes.</div>
        [/NORESULT]
    [/STORPROC]

    [NORESULT]
        <div class="alert alert-danger">Impossible de trouver l'objet [!Query!]</div>
    [/NORESULT]
[/STORPROC]