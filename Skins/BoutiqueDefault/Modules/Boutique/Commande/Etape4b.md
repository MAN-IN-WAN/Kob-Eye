// Vérification que l on est bien connecté à ce stade
[IF [!Systeme::User::Public!]]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape2)!][/REDIRECT]
[/IF]

// Acheteur connecté
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC] 

// Récupère la commande du client
[!Com:=[!CLCONN::getPanier()!]!]
[IF [!Com::Id!]=]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape4)!][/REDIRECT]
[/IF]

// Récupère le paiement et son type
[!P:=[!Com::getPaiement()!]!]
[IF [!P!]]
	[STORPROC Boutique/TypePaiement/Paiement/[!P::Id!]|TP|0|1|Id|DESC][/STORPROC]
	[NORESULT]
		[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape4)!][/REDIRECT]
	[/NORESULT]
[/IF]


[IF [!Mag::EtapeAffiche!]]
	
	<div class="EtapesCommande">
		<div class="col-md-3 FondStep1"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]"><span class="FondStep1">1 - Panier</span></a></div>
		<div class="col-md-3 FondStep2"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape2)!]"><span class="FondStep2">2 - Identification</span></a></div>
		[IF [!Mag::EtapeLivraison!]]<div class="col-md-3 FondStep3"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape3)!]"><span class="FondStep3">3 - Livraison</span></a></div>[/IF]
		[IF [!Mag::EtapePaiement!]]<div class="col-md-3 FondStep4Active"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape4)!]"><span class="FondStep4Active">4 - Paiement</span></a></div>[/IF]
	</div>
[/IF]
<div class="CommandeEtape4">
	<h1>Mon paiement</h1>
	[!Plugin:=[!TP::getPlugin()!]!]
	[!Plugin::getCodeHTML([!P!])!]
</div>	

