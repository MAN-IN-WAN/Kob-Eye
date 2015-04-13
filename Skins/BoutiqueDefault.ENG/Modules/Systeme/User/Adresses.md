// Pas connecté
[IF [!Systeme::User::Public!]=1][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]

// Mauvais type
[IF [!Type!]!=Livraison&&[!Type!]!=Facturation][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]

<div class="user">
	<h1 class="moncompte">Mon Compte</h1>
	[MODULE Systeme/User/Home]

	<h2  class="moncompte">Mes adresses de [!Type!]</h2>
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]/Adresse/Type=[!Type!]|Adr]
		<div class="Adresse">
				<strong>[!Adr::Civilite!] [!Adr::Nom!] [!Adr::Prenom!]</strong><br />
				[!Adr::Adresse!]<br />
				[!Adr::CodePostal!] [!Adr::Ville!]<br />
				[!Adr::Pays!]
				<a class="ModifierAdresse" href="/[!Systeme::getMenu(Systeme/User)!]/ModifierAdresse?Id=[!Adr::Id!]">Modifier</a>
				<a class="ModifierAdresse" href="/[!Systeme::getMenu(Systeme/User)!]/SupprimerAdresse?Id=[!Adr::Id!]" onclick="return confirm('Etes-vous sûr de vouloir supprimer cette adresse de [!Type!] ?')">Supprimer</a>
		</div>
	[/STORPROC]
	<div class="buttons">
		<a href="/[!Systeme::getMenu(Systeme/User)!]/AjouterAdresse?Type=[!Type!]">Ajouter une adresse de [!Type!]</a>
	</div>
	<div class="buttons">
		<a href="/[!Systeme::getMenu(Systeme/User)!]">Retour à mon compte</a>
	</div>

	// Acheteur connecté
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC] 

	// Récupère le panier du client
	[!Panier:=[!CLCONN::getPanier()!]!]

	// Si rien dans le panier redirection etape 1
	[COUNT [!Panier::LignesCommandes!]|NbLig]
	[IF [!NbLig!]]
		<div class="buttons">
			<a href="/Boutique/Commande/Etape3">Retour à la validation de commande</a>
		</div>
	[/IF]

</div>