[STORPROC [!Query!]|O|0|1]
    [!O::enableSsl()!]
    [STORPROC [!O::Error!]|E]
        <div class="alert alert-danger">
            <ul>
                [LIMIT 0|100]
                <li>[!E::Message!]</li>
                [/LIMIT]
            </ul>
        </div>
        [NORESULT]
            <div class="alert alert-success">Activation SSL de l'objet [!Query!] réussie. Une tache sera crée et activée dans 2 minutes.</div>
        [/NORESULT]
    [/STORPROC]

    [NORESULT]
        <div class="alert alert-danger">Impossible de trouver l'objet [!Query!]</div>
    [/NORESULT]
[/STORPROC]