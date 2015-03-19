// Vérification que l on est bien connecté à ce stade
[IF [!Systeme::User::Public!]][REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape2)!][/REDIRECT][/IF]
[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]
[STORPROC Boutique/Magasin|Mag|0|1][/STORPROC]

// Récupère la commande du client
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|CLCONN|0|1][/STORPROC] 
[!CDE:=[!CLCONN::getLastCommande()!]!]
[IF [!CDE::Id!]=]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape4)!]?Stop=1[/REDIRECT]
[/IF]

// Récupération du paiement
[STORPROC Boutique/Commande/[!CDE::Id!]/Paiement|P]
	[STORPROC Boutique/TypePaiement/Paiement/[!P::Id!]|TP|0|1|Id|DESC][/STORPROC]
	[NORESULT]
		[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape4)!]?Stop=2[/REDIRECT]
	[/NORESULT]
[/STORPROC]

// Récupération de la livraison
[!BL:=[!CDE::getBonLivraison!]!]
[STORPROC Boutique/Adresse/Commande/[!CDE::Id!]|Adr]
	[IF [!Adr::Type!]=Livraison][!AdrLv:=[!Adr!]!][/IF]
	[IF [!Adr::Type!]=Facturation][!AdrFc:=[!Adr!]!][/IF]
[/STORPROC]

[!Plugin:=[!TP::getPlugin()!]!]

<div class="CommandeEtape5">
	<h1>
		[IF [!P::Etat!]=0]Commande [!CDE::RefCommande!] prise en compte - En attente de paiement[/IF]
		[IF [!P::Etat!]=1]Confirmation de commande [!CDE::RefCommande!][/IF]
		[IF [!P::Etat!]>1]Commande [!CDE::RefCommande!] annulée[/IF]
	</h1>
	[IF [!P::Etat!]>1]
		<div class="Desc">
			Votre commande n'a pas pu aboutir.<br />
			Si vous pensez qu'il s'agit d'une erreur, veuillez nous <a href="/Contact" class="Etape5Lien" >contacter</a> en rappelant le numéro de commande&nbsp;<strong>[!CDE::RefCommande!]</strong>.
		</div>
		[METHOD CDE|setUnValid][/METHOD]
	[ELSE]
		<div class="Desc">
			[!CLCONN::Civilite!] [!CLCONN::Prenom!] [!CLCONN::Nom!], merci pour votre commande.
		</div>
		<div class="Desc">
			<h2>Paiement par [!TP::Nom!]</h2>
			[!Plugin::affichageEtape5([!P!],[!CDE!])!]
		</div>
		<div class="Desc">
			<h2>Livraison via [!BL::TypeLivraison!]</h2>
			[IF [!L::NumColis!]!=]Le numéro de votre colis est [!L::NumColis!]<br />[/IF]
			[IF [!BL::AdresseLivraisonAlternative!]]
				Pour <span class="nom">[!AdrLv::Civilite!] [!AdrLv::Prenom!] [!AdrLv::Nom!]</span><br />
				à [!BL::ChoixLivraison!]<br />
			[ELSE]
				À <span class="nom">[!AdrLv::Civilite!] [!AdrLv::Prenom!] [!AdrLv::Nom!]</span><br />
				[!AdrLiv::Adresse!] <br />
				[!AdrLv::CodePostal!] [!AdrLv::Ville!] [!AdrLv::Pays!]<br />
			[/IF]	
		</div>
	[/IF]

	<div class="LigneBoutons">
		<div class="BoutonsGauche RetourAccueilCdeEtape5">
			<a href="/" class="btn btn-info" >Je retourne à l'accueil</a>
		</div>
		[IF [!P::Etat!]<2]
			<div class="BoutonsGauche ImpressionCdeEtape5" >
				<a href="/[!Systeme::getMenu(Boutique/Commande/BonDeCommande)!]/[!CDE::RefCommande!]" class="btn btn-info" target="_blank" >
					J'imprime ma commande
				</a>
			</div>
		[/IF]
		<div class="BoutonsGauche AccesClientEtape5" >
			<a href="/[!Systeme::getMenu(Mon-compte)!]" class="btn btn-info" >
				J'accède à mon espace client
			</a>
		</div>
	</div>

</div>