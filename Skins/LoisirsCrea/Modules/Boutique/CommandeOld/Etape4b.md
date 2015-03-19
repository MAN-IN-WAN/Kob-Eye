// Vérification que l on est bien connecté à ce stade
[IF [!Systeme::User::Public!]]
	[REDIRECT]Boutique/Commande/Etape2[/REDIRECT]
[/IF]

// Acheteur connecté
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC] 

// Récupère la commande du client
[!Com:=[!CLCONN::getCurrentCommande()!]!]
[IF [!Com::Id!]=]
	[REDIRECT]Boutique/Commande/Etape4[/REDIRECT]
[/IF]

// Récupère le paiement et son type
[STORPROC Boutique/Commande/[!Com::Id!]/Paiement/Etat=0|P]
	[STORPROC Boutique/TypePaiement/Paiement/[!P::Id!]|TP|0|1|Id|DESC][/STORPROC]
	[NORESULT]
		[REDIRECT]Boutique/Commande/Etape4[/REDIRECT]
	[/NORESULT]
[/STORPROC]


<div class="EtapesCommande">
	<a href="/Boutique/Commande/Etape1" class="FondStep1">1 - Panier</a>
	<a href="/Boutique/Commande/Etape2" class="FondStep2">2 - Identification</a>
	<a href="/Boutique/Commande/Etape3" class="FondStep3">3 - Livraison</a>
	<a href="/Boutique/Commande/Etape4" class="FondStep4Active">4 - Paiment</a>
</div>

<div class="CommandeEtape4">
	<h1>Mon paiement</h1>
	[!Plugin:=[!TP::getPlugin()!]!]
	[!Plugin::getCodeHTML([!P!])!]
</div>	

