// Utilisateur (Connecté ou non ?)
[IF [!Systeme::User::Public!]=1]
	[OBJ Boutique|Client|Cli]
[ELSE]
	[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|Cli|0|1]
		[NORESULT]
			[OBJ Boutique|Client|Cli]
		[/NORESULT]
	[/STORPROC]
[/IF]

//Recupération du panier
[!Panier:=[!Cli::getPanier()!]!]

// Valider ou modifier ma commande
[IF [!Action!]=Modifier ma commande]
	[REDIRECT]Boutique/Commande/Etape1[/REDIRECT]
[/IF]
[IF [!Action!]=Valider ma commande]
	[REDIRECT]Boutique/Commande/Etape2[/REDIRECT]
[/IF]

// Ajout au panier
[IF [!Reference!]!=&&[!Qte!]>0]
	[STORPROC Boutique/Produit/Reference/[!Reference!]|Prod|0|1][/STORPROC]
	[IF [!Prod::GetColisage()!]][!Qte*=[!Prod::GetColisage()!]!][/IF]
	[!Cli::ajouterAuPanier([!Reference!],[!Qte!])!]
	[REDIRECT][!Lien!][/REDIRECT]
[/IF]

// Enlever du panier
[IF [!Sup!]]
	[STORPROC [!Sup!]|S]
		[!Cli::enleverDuPanier([!S!])!]
	[/STORPROC]
	[REDIRECT][!Lien!][/REDIRECT]
[/IF]

// Vider le panier
[IF [!Action!]=Vider mon panier]
	[!Cli::viderPanier()!]
	[REDIRECT][!Lien!][/REDIRECT]
[/IF]


