[!Mag:=[!CurrentMagasin!]!]
[!De:=[!CurrentDevise!]!]
// Acheteur connecté
[!CLCONN:=[!CurrentClient!]!]

// Vérification que l on est bien connecté à ce stade
[IF [!Systeme::User::Public!]][REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape2)!][/REDIRECT][/IF]

// Récupère la commande du client
[!CDE:=[!CLCONN::getLastCommande()!]!]

[IF [!CDE::Id!]=]
        <h1>PAS DE COMMANDE </h1>
	[IF [!Mag::EtapePaiement!]=0]
		[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape1)!]?Stop=1[/REDIRECT]
	[ELSE]
		[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape4)!]?Stop=1[/REDIRECT]
	[/IF]
[/IF]
[IF [!Mag::EtapePaiement!]]
	// Récupération du paiement
	[STORPROC [!CDE::getChildren(Paiement/Etat=1)!]|P|0|1]
            [NORESULT]
                [STORPROC [!CDE::getChildren(Paiement)!]|P|0|1]
                    [IF [!P=0!]]
                        [METHOD P|CheckPaiement][/METHOD]
                    [/IF]
                    [NORESULT]
                        [REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape4)!]?Stop=2[/REDIRECT]
                    [/NORESULT]
                [/STORPROC]
            [/NORESULT]
	[/STORPROC]

	[STORPROC Boutique/TypePaiement/Paiement/[!P::Id!]|TP|0|1|Id|DESC][/STORPROC]
	[!Plugin:=[!TP::getPlugin()!]!]
[/IF]
[IF [!Mag::EtapeLivraison!]]
	// Récupération de la livraison
	[!BL:=[!CDE::getBonLivraison!]!]
	[STORPROC Boutique/Adresse/Commande/[!CDE::Id!]|Adr]
		[IF [!Adr::Type!]=Livraison][!AdrLv:=[!Adr!]!][/IF]
		[IF [!Adr::Type!]=Facturation][!AdrFc:=[!Adr!]!][/IF]
	[/STORPROC]
[/IF]
<div class="CommandeEtape5">
	<div class="row"><div class="col-md-12">
		<h2 class="title_block">
			[IF [!Mag::EtapePaiement!]]
				[IF [!P::Etat!]=0]Commande [!CDE::RefCommande!] prise en compte - En attente de paiement[/IF]
				[IF [!P::Etat!]=4]Commande [!CDE::RefCommande!] prise en compte - En attente de paiement[/IF]
				[IF [!P::Etat!]=1]Commande [!CDE::RefCommande!] validée et en cours d'expédition[/IF]
				[IF [!P::Etat!]=3]Commande [!CDE::RefCommande!] annulée[/IF]
				[IF [!P::Etat!]=2]Commande [!CDE::RefCommande!] annulée - Paiement refusé[/IF]
			[ELSE]
				Confirmation de commande [!CDE::RefCommande!]
			[/IF]
		</h2>
	</div></div>
	[IF [!Mag::EtapePaiement!]]
		[IF [!P::Etat!]>1&&[!P::Etat!]<4]
			<div class="row"><div class="col-md-12">
				<div class="Desc">
					Votre commande n'a pas pu aboutir.<br />
					Si vous pensez qu'il s'agit d'une erreur, veuillez nous <a href="/Contact" class="Etape5Lien" >contacter</a> en rappelant le numéro de commande&nbsp;<strong>[!CDE::RefCommande!] ou alors en retournant sur l'étape de paiement en cliquant sur le bouton ci-dessous</strong>.
				</div>
			</div></div>
			[METHOD CDE|setUnValid][/METHOD]
		[/IF]
	[/IF]
	[IF [!Mag::EtapePaiement!]=0||[!P::Etat!]<=1||[!P::Etat!]>3]
		<div class="row"><div class="col-md-12">
			<div class="Desc">
				[!CLCONN::Civilite!] [!CLCONN::Prenom!] [!CLCONN::Nom!], merci pour votre commande. <br />
                Votre commande a été confirmée par nos services. Vous recevrez un email vous informant de l'état de préparation de votre commande.


			</div>
		</div></div>
		[IF [!Mag::EtapePaiement!]]
			<div class="row"><div class="col-md-12">
				<div class="Desc">
					<h2>Paiement par [!TP::Nom!]</h2>
					[!Plugin::affichageEtape5([!P!],[!CDE!])!]
					<br />[IF [!P::Montant!]>0]<br /><h3>Montant de votre commande [!Math::PriceV([!P::Montant!])!][!De::Sigle!]&nbsp;&nbsp;</h3>[/IF]
				</div>
			</div></div>
		[/IF]
		[IF [!Mag::EtapeLivraison!]&&[!CDE::getMontantLivrable()!]]
			<div class="row"><div class="col-md-12">
				<div class="Desc">
					<h2>Livraison via [!BL::TypeLivraison!]</h2>
					[IF [!L::NumColis!]!=]Le numéro de votre colis est [!L::NumColis!]<br />[/IF]
					[IF [!BL::AdresseLivraisonAlternative!]]
						Pour <span class="nom">[!AdrLv::Civilite!] [!AdrLv::Prenom!] [!AdrLv::Nom!]</span><br />
						à [!BL::ChoixLivraison!]<br />
					[ELSE]
						À <span class="nom">[!AdrLv::Civilite!] [!AdrLv::Prenom!] [!AdrLv::Nom!]</span><br />
						[!AdrLv::Adresse!] <br />
						[!AdrLv::CodePostal!] [!AdrLv::Ville!] [!AdrLv::Pays!]<br />
					[/IF]	
				</div>
			</div></div>
		[/IF]
	[/IF]

	<div class="row LigneBoutons"><div class="col-md-12">
		<div class="pull-right">
			<a href="/" class="btn btn-warning" >Je retourne à l'accueil</a>
		</div>
		[IF [!P::Etat!]=1]
		<div class="pull-right">
			[IF [!Mag::EtapePaiement!]]
				<a href="/[!Systeme::getMenu(Boutique/Commande/BonDeCommande)!]/[!CDE::Id!]" class="btn btn-success">
					J'imprime ma commande
				</a>
			[/IF]
		</div>
                [ELSE]
		<div class="pull-right">
			[IF [!Mag::EtapePaiement!]&&[!P::Etat!]>1&&[!P::Etat!]<4]
				<a href="/[!Systeme::getMenu(Boutique/Commande/Etape4)!]" class="btn btn-warning" >
					Je retourne à l'étape de paiement
				</a>
			[/IF]
		</div>
                [/IF]
		<div class="pull-right">
			<a href="/[!Systeme::getMenu(Boutique/Mon-compte)!]" class="btn btn-primary Espace" >
				J'accède à mon espace client
			</a>
		</div>
	</div></div>


</div>