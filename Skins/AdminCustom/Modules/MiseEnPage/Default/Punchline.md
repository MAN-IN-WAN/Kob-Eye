[STORPROC [!Query!]/Punchline|Punch]
    <h3>Punchlines</h3>
    [LIMIT 0|10000]
        <div class="punchMEP">
            <div class="alert alert-[!Punch::Type!]" role="alert">
                [!Punch::Contenu!]
            </div>
            <div class="mods">
                <a href="/MiseEnPage/Punchline/[!Punch::Id!]/Modifier" title="Modifier la punchline" class="modButton ">Modifier</a>
                <a href="/MiseEnPage/Punchline/[!Punch::Id!]/Supprimer" title="Supprimer la punchline"  class="delButton ">supprimer</a>
                <div class="clear"></div>
            </div>
        </div>
    [/LIMIT]
    [NORESULT]
        <p>Aucune punchline pour cet Article.</p>
    [/NORESULT]
[/STORPROC]
<a href="[!I::LastId!]/AjouterPunchline" class="addButton" title="Ajouter une punchline">Ajouter</a>