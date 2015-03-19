// Pas connecté
[IF [!Systeme::User::Public!]=1][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]

// Mauvais type
[IF [!Type!]!=Livraison&&[!Type!]!=Facturation][REDIRECT][!Systeme::CurrentMenu::Url!][/REDIRECT][/IF]

<div class="user">
	<h1 class="moncompte">Mon Compte</h1>
	[MODULE Boutique/Mon-compte/Home]

	<h2 class="moncompte">Mes adresses de [!Type!]</h2>
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]/Adresse/Type=[!Type!]|Adr]
		<div class="Adresse">
			<table class="Adresse">
				//<tr><td colspan="3">[IF [!Adr::Societe!]!=][<td>Société : [!Adr::Societe!]<br /> Siret : [!Adr::Siret!]</td>[/IF]</td></tr>
				<tr>
					<td>[!Adr::Civilite!] [!Adr::Nom!] [!Adr::Prenom!]</td>
					
				</tr>
				<tr><td colspan="3">[!Adr::Adresse!]</td></tr>
				<tr><td colspan="3"><strong>[!Adr::CodePostal!] [!Adr::Ville!]</strong></td></tr>
				<tr><td >[!Adr::Pays!]</td><td class="bouton">
						<a class="btn btn-red  ModifierAdresse" href="/[!Systeme::getMenu(Boutique/Mon-compte)!]/ModifierAdresse?Id=[!Adr::Id!]">Modifier</a>
					</td>
					<td class="bouton">
						<a class="btn btn-gris SupprimerAdresse" href="/[!Systeme::getMenu(Boutique/Mon-compte)!]/SupprimerAdresse?Id=[!Adr::Id!]" onclick="return confirm('Etes-vous sûr de vouloir supprimer cette adresse de [!Type!] ?')">Supprimer</a>
					</td></tr>
			</table>
		</div>
	[/STORPROC]
	<div class="buttons">
		<a class="btn btn-red AjouterAdresse" href="/[!Systeme::getMenu(Boutique/Mon-compte)!]/AjouterAdresse?Type=[!Type!]">Ajouter une adresse <br />de [!Type!]</a>
	</div>
	<div class="buttons">
		<a class="btn btn-grisfonce RetourCompte" href="/[!Systeme::getMenu(Boutique/Mon-compte)!]">Retour à mon <br />compte</a>
	</div>

	// Acheteur connecté
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC]

	// Récupère le panier du client
	[!Panier:=[!CLCONN::getPanier()!]!]

	// Si rien dans le panier redirection etape 1
	[COUNT [!Panier::LignesCommandes!]|NbLig]
	[IF [!NbLig!]]
		<div class="buttons">
			<a class="btn btn-gris Retourvalidation" href="/[!Systeme::getMenu(Boutique/Commande)!]/Etape3">Retour à la validation <br />de commande</a>
		</div>
	[/IF]

</div>