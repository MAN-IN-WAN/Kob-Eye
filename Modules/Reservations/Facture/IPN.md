[LOG]IPN RECEUVECD[/LOG]
[STORPROC Reservations/TypePaiement/Actif=1|TP]
    [!Plg:=[!TP::getPlugin()!]!]
    [!PaiementID:=[!Plg::retrouvePaiementEtape4s()!]!]
    [LOG]PAIEMENT [!PaiementID!][/LOG]
    <h2>PAIEMENT [!PaiementID!]</h2>
    [IF [!PaiementID!]>0]
        [STORPROC Reservations/Paiement/[!PaiementID!]|P|0|1]
            <p>checkpaiement</p>
            [METHOD P|CheckPaiement][/METHOD]
        [/STORPROC]
    [/IF]
[/STORPROC]