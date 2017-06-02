[STORPROC [!Query!]|Srv][/STORPROC]
[IF [!Srv::Mail!]=1]
        [!Srv::getMails()!]
[ELSE]
        <p class="error">Impossible de récupérer les mails car ce n'est pas uns serveur de mail</p>
[/IF]
