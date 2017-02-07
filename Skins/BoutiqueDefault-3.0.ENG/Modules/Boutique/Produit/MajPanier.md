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
	[REDIRECT][!Sys::getMenu(Boutique/Commande/Etape1)!][/REDIRECT]
[/IF]
[IF [!Action!]=Valider ma commande]
	[REDIRECT][!Sys::getMenu(Boutique/Commande/Etape2)!][/REDIRECT]
[/IF]

// Ajout au panier
[IF [!Reference!]!=&&[!Qte!]>0]
	[STORPROC Boutique/Produit/Reference/[!Reference!]|Prod|0|1][/STORPROC]

	// en commentaires car sur cete boutique on ne gère pas de colisage 
	//[IF [!Prod::GetColisage()!]][!Qte*=[!Prod::GetColisage()!]!][/IF]
	[!T:=[!Cli::ajouterAuPanier([!Reference!],[!Qte!],[!config!],[!options!])!]!]

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
			[REDIRECT][!Sys::getMenu(Boutique/Commande/Etape4)!][/REDIRECT]
		[/CASE]
		[CASE annule]
			[!Cli::cancelCommande([!Com!])!]
			[REDIRECT][!Sys::getMenu(Boutique/Commande/Etape4)!][/REDIRECT]
		[/CASE]
		[DEFAULT]
			[!Cli::switchCommande([!Com!])!]
			[REDIRECT][!Lien!][/REDIRECT]
        [/DEFAULT]
    [/SWITCH]
[/IF]