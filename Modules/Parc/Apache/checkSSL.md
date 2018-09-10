<h1>Vérification du certificat</h1>
[STORPROC [!Query!]|O|0|1]
    [IF [!O::checkCertificate()!]]
        <div class="alert alert-success">Le certificat est bien valide</div>
    [ELSE]
        <div class="alert alert-danger">Le certificat n'est pas valide.</div>
    [/IF]
    [STORPROC [!O::Error!]|Err]
        <div class="alert alert-warning">[!Err::Message!]</div>
    [/STORPROC]
[/STORPROC]