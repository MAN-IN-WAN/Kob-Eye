//
//	Recupere les modes de livraison possibles
//


// Acheteur connecté
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Cli|0|1][/STORPROC]

// Commande
[!Panier:=[!Cli::getPanier()!]!]

// Adresse Livraison obligatoire
[
	[!First:=1!]
	[STORPROC Boutique/Client/[!Cli::Id!]/Adresse/Id=[!Livraison!]&&Type=Livraison|AdrLiv|0|1]
			[STORPROC LivraisonStock/TypeLivraison/Actif=1|TypeLiv]
				[!Plugin:=[!TypeLiv::getPlugin()!]!]
				[!TarifLivraison:=[!Plugin::getTarif([!Panier!],[!AdrLiv!])!]!]
				[IF [!TarifLivraison::ObjectType!]=TarifLivraison]
					[IF [!First!]=1]
						[!First:=0!]
					[ELSE]
						,
					[/IF]
					{
						"Id":"[!TypeLiv::Id!]",
						"Titre":"[!TypeLiv::Nom!]",
						"Desc":"[JSON][!TypeLiv::Description!][/JSON]",
						"Delai":"[!TypeLiv::LivreEn!]",
						"Prix": "[!Math::Price([!TypeLiv::getTTC([!TarifLivraison::Tarif!])!])!]"
					}
				[/IF]
			[/STORPROC]
	[/STORPROC]
]