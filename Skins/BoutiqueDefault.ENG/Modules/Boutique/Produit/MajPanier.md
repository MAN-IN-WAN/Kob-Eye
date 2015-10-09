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

//switch commande
[SWITCH [!Action!]|=]
    [CASE Vider]
        [!Cli::viderPanier()!]
        [REDIRECT][!Lien!][/REDIRECT]
    [/CASE]
    [CASE Paiement]
        [IF [!Com!]]
            [!Cli::switchCommande([!Com!])!]
            [REDIRECT][!Sys::getMenu(Boutique/Commande/Etape4)!][/REDIRECT]
        [/IF]
    [/CASE]
    [CASE Annule]
        [IF [!Com!]]
            [!Cli::cancelCommande([!Com!])!]
            [REDIRECT][!Sys::getMenu(Boutique/Commande/Etape4)!][/REDIRECT]
        [/IF]
    [/CASE]
    [CASE Utiliser]
        [IF [!Com!]]
            [!Cli::switchCommande([!Com!])!]
            [REDIRECT][!Lien!][/REDIRECT]
        [/IF]
    [/CASE]
[/SWITCH]
