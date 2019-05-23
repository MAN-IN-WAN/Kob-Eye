[STORPROC [!Query!]|O|0|1]
[IF [!O::Reset()!]]
    <div class="alert alert-success">La tache [!O::Nom!] devrait démarrer sous peu.</div>
[ELSE]
    <div class="alert alert-danger">L'éxécution de la tache [!O::Nom!] a echouée.</div>
[/IF]
[/STORPROC]
