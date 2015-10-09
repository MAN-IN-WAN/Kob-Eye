<h1>synchronisation des sessions en attente de synchro</h1>
[STORPROC Formation/Session|S]
    <h2>Synchro [!S::Nom!]</h2>
    [!S::checkReponse()!]
[/STORPROC]

<h1>synchronisation des sessions en attente de synchro</h1>
[STORPROC Formation/Session/Termine=1&Synchro=0|S]
    <h2>Synchro [!S::Nom!]</h2>
        [!S::Synchro()!]
[/STORPROC]