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
	[!Panier::setUnValid!]
	[REDIRECT]Boutique/Commande/Etape1[/REDIRECT]
[/IF]
[IF [!Action!]=Valider ma commande]
	[REDIRECT]Boutique/Commande/Etape2[/REDIRECT]
[/IF]

// Ajout au panier
[IF [!Reference!]!=&&[!Qte!]>0]
	[STORPROC Boutique/Produit/Reference/[!Reference!]|Prod|0|1][/STORPROC]
	//[IF [!Prod::GetColisage()!]][!Qte*=[!Prod::GetColisage()!]!][/IF]
	[!T:=[!Cli::ajouterAuPanier([!Reference!],[!Qte!])!]!]
	[IF [!T!]]
		[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape1)!][/REDIRECT]
	[ELSE]
		[REDIRECT][!Lien!][/REDIRECT]
	[/IF]
[/IF]
// je recalcule systématiquement
[IF [!Continue!]||[!Valider!]||[!Recalcul!]]
	[STORPROC [!Panier::LignesCommandes!]|Pan]
		// Changement de la quantité
		[!refObj:=[!Pan::getReference()!]!]
		[!Cli::ajusterQtePanier([!Pan::Reference!],[!Qte[!refObj::Id!]!])!]
	[/STORPROC]
[/IF]
// Enlever du panier
[IF [!Sup!]]
	[STORPROC [!Sup!]|S]
		[!Cli::enleverDuPanier([!S!])!]
	[/STORPROC]
	[REDIRECT][!Lien!][/REDIRECT]
[/IF]

// Vider le panier
[IF [!Action!]=Vider mon panier||[!Action!]=Annuler ma commande]
	[!Cli::viderPanier()!]
	[REDIRECT][!Lien!][/REDIRECT]
[/IF]

//switch commande
[IF [!Com!]]
	[SWITCH [!action!]|=]
		[CASE paiement]
			[!Cli::switchCommande([!Com!])!]
			[REDIRECT]Boutique/Commande/Etape4[/REDIRECT]
		[/CASE]
		[CASE annule]
			[!Cli::cancelCommande([!Com!])!]
			[REDIRECT]Boutique/Commande/Etape4[/REDIRECT]
		[/CASE]
		[DEFAULT]
			[!Cli::switchCommande([!Com!])!]
			[REDIRECT][!Lien!][/REDIRECT]
		[/DEFAULT]
	[/SWITCH]
[/IF]


