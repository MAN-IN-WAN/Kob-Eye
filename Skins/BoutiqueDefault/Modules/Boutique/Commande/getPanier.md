[OBJ Boutique|Magasin|Magasin]
[!Mag:=[!Magasin::getCurrentMagasin()!]!]
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

[!TotGene:=0!]

// Ajout au panier
[IF [!Reference!]!=&&[!Qte!]!=0]
        [!ACTION:=ajout!]
        [METHOD Cli|ajouterAuPanier]
            [PARAM][!Reference!][/PARAM]
            [PARAM][!Qte!][/PARAM]
        [/METHOD]
//	[!Cli::ajouterAuPanier([!Reference!],[!Qte!])!]
[/IF]

// Vider le panier
[IF [!Action!]=Vider mon panier]
        [!ACTION:=vider!]
        [METHOD Cli|viderPanier][/METHOD]
//	[!Cli::viderPanier()!]
[/IF]

// Récupère le panier du client
[!Panier:=[!Cli::getPanier()!]!]

// Enlever du panier
[IF [!Sup!]]
        [!ACTION:=supprime!]
	[STORPROC [!Sup!]|S]
            [METHOD Cli|enleverDuPanier]
                [PARAM][!S!][/PARAM]
            [/METHOD]
//		[!Cli::enleverDuPanier([!S!])!]
	[/STORPROC]
[/IF]


[IF [!Continue!]]
	[!Men:=[!Systeme::getMenu(Boutique/Magasin/[!Mag::Id!])!]!]
	[REDIRECT][!Domaine!]/[!Men!][/REDIRECT]
[/IF]

[IF [!Valider!]]
	[REDIRECT][!Systeme::getMenu(Boutique/Commande/Etape2)!][/REDIRECT]
[/IF]

//Magasin


//Message
[STORPROC [!Panier::getErrors()!]|E][/STORPROC]
[!Panier::resetErrors()!]
[STORPROC [!Panier::getSuccess()!]|S][/STORPROC]
[!Panier::resetSuccess()!]


[!Panier::recalculer()!]

//RETOUR JSON
{
    "success":"[!S::Message!]",
    "error":"[!!E::Message!]",
    "action":"[!ACTION!]",
    "total":"[!Math::PriceV([!Panier::MontantTTC!])!]  [!CurrentDevise::Sigle!]",
    "panier":[
[STORPROC [!Panier::LignesCommandes!]|Pan]
        [IF [!Pos!]>1],[/IF]
        // Colisage
        [STORPROC Boutique/Produit/Reference/[!Pan::Reference!]|Prod|0|1][/STORPROC]
        [!RefStock:=0!]
        [STORPROC Boutique/Reference/Reference=[!Pan::Reference!]|Re|0|1][/STORPROC]
        [!RefStock:=[!Re::getStockReference!]!]
        [!Emballage:=[!Prod::GetEmballage()!]!]
        [!refObj:=[!Pan::getReference()!]!]
        [!NbUnite:=[!Prod::GetColisage()!]!]
        [IF [!Pan::MontantRemiseTTC!]>0]
                [!montantReduc:=[!Pan::MontantTTC!]!]
                [!montantReduc/=[!Pan::MontantHorsPromoTTC!]!]
                [!montantReduc-=1!]
                [!montantReduc*=100!]
        [/IF]
        
        {
            "title":"[!Pan::Titre!]",
            "url":"[!Prod::getUrl()!]",
            "quantite":[!Pan::Quantite!],
            "price":"[!Math::PriceV([!Pan::MontantHorsPromoTTC!])!] [!CurrentDevise::Sigle!]",
            "reduction":"[IF [!Pan::MontantRemiseTTC!]>0][!Math::PriceV([!montantReduc!])!] %<br /> soit <br /> - [!Math::PriceV([!Pan::MontantRemiseTTC!])!] [!CurrentDevise::Sigle!][/IF]",
            "topay":"[!Math::PriceV([!Pan::MontantTTC!])!] [!CurrentDevise::Sigle!]",
            "ref": "[!Pan::Reference!]",          
            "conf": "[!Pan::Config!]"            
        }
[/STORPROC]
]}
