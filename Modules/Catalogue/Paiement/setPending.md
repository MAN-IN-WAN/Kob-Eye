[LOG]SET COMMANDE PENDING [!Paiement!][/LOG]
[IF [!Paiement!]>0]
	//recupération du paiement
	[STORPROC Catalogue/Paiement/[!Paiement!]|P|0|1]
		[!P::setPending()!]
	[/STORPROC]
[/IF]
