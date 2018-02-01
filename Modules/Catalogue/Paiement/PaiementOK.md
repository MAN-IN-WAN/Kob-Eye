<h3>Votre paiement a été enregistré</h3>
[LOG]paiementOK[/LOG]
[STORPROC Catalogue/TypePaiement/Actif=1|TP]

	[!Plg:=[!TP::getPlugin()!]!]
	[!PaiementID:=[!Plg::retrouvePaiementEtape4s()!]!]
	[LOG]id du paiement : [!PaiementID!][/LOG]
	[IF [!PaiementID!]>0]
		[STORPROC Catalogue/Paiement/[!PaiementID!]|P|0|1]
			[LOG]verification du paiement[/LOG]
			[METHOD P|CheckPaiement][/METHOD]
		[/STORPROC]
	[/IF]
[/STORPROC]
