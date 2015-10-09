[OBJ Boutique|Magasin|Magasin]
[!Mag:=[!Magasin::getCurrentMagasin()!]!]
// On redirige automatiquement à l'étape 3 si on est déjà connecté
[IF [!Systeme::User::Public!]=0]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape3)!][/REDIRECT]
[/IF]

// Panier vide on redirige vers la 1
[OBJ Boutique|Client|Cli]
[!Panier:=[!Cli::getPanier()!]!]
[STORPROC [!Panier::LignesCommandes!]|Pan|0|1]
	[NORESULT]
		[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape1)!][/REDIRECT]
	[/NORESULT]
[/STORPROC]

[IF [!Mag::EtapeAffiche!]]
	<div class="EtapesCommande">
		<div class="col-md-3 FondStep1"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]"><span class="FondStep1">1 - Panier</span></a></div>
		<div class="col-md-3 FondStep2Active"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape2)!]"><span class="FondStep2Active">2 - Identification</span></a></div>
		[IF [!Mag::EtapeLivraison!]]<div class="col-md-3 FondStep3"><a href="#nogo"><span class="FondStep3">3 - Livraison</span></a></div>[/IF]
		[IF [!Mag::EtapePaiement!]]<div class="col-md-3 FondStep4"><a href="#nogo"><span class="FondStep4">4 - Paiement</span></a></div>[/IF]
	</div>
[/IF]

<div class="CommandeEtape2">
	[MODULE Systeme/Login/CreaCompte?Redirect=[!Systeme::getMenu(Boutique/Commande/Etape3)!]]
</div>

