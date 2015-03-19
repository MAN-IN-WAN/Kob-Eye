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
		<div class="span3 FondStep1Active"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape1)!]" class="btn btn-inverse btn-large btn-block"><span class="badge badge-protector">1</span> Panier</a></div>
		<div class="span3 FondStep2"><a href="/[!Systeme::getMenu(Boutique/Commande/Etape2)!]" class=" btn btn-inverse btn-block btn-large"><span class="badge badge-protector">2</span> Identification</a></div>
		[IF [!Mag::EtapeLivraison!]]<div class="span3 FondStep3"><a href="#nogo" class=" btn btn-inverse btn-block btn-large"><span class="badge badge-success">3</span> Livraison</a></div>[/IF]
		[IF [!Mag::EtapePaiement!]]<div class="span3 FondStep4"><a href="#nogo" class=" btn btn-inverse btn-block btn-large"><span class="badge badge-success">4</span> Paiement</a></div>[/IF]
	</div>

[/IF]

<div class="CommandeEtape2">
	[MODULE Systeme/Login/CreaCompte?Redirect=[!Systeme::getMenu(Boutique/Commande/Etape3)!]]
</div>

