//
//	Recupere les choix détaillés possibles pour le type de livraison
//

// Acheteur connecté
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Cli|0|1][/STORPROC]

// Commande
[!Panier:=[!Cli::getPanier()!]!]

// Adresse Livraison obligatoire
[
	[STORPROC Boutique/Client/[!Cli::Id!]/Adresse/Id=[!Livraison!]&&Type=Livraison|AdrLiv|0|1]
		[STORPROC LivraisonStock/TypeLivraison/[!TypeLivraison!]|TypeLiv]
			[!Plugin:=[!TypeLiv::getPlugin()!]!]
			[!TarifLivraison:=[!Plugin::getTarif([!Panier!],[!AdrLiv!])!]!]
			[IF [!TarifLivraison::ObjectType!]=TarifLivraison]
				[STORPROC [!Plugin::getChoix([!Panier!],[!AdrLiv!])!]|Choix]
					[IF [!Pos!]>1],[/IF]{
						"Uid": "[!Choix::Uid!]",
						"Libelle": "[!Choix::Libelle!]"
					}
				[/STORPROC]
			[/IF]
		[/STORPROC]
	[/STORPROC]
]