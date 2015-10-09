[STORPROC [!Query!]|Prod|0|1][/STORPROC]
// calcule le prix du produit dans une variable [!LePrix!]

[!Req:=[!Query!]/Reference/!]
[STORPROC [!Query!]/Attribut|Att]
	[IF [!Pos!]>1][!Req+=&!][/IF]
	[!Req+=Declinaison.DeclinaisonId([!P[!Prod::Id!]A[!Att::Id!]!])!]
[/STORPROC]
[STORPROC [!Req!]|Ref|0|1][/STORPROC]
// changement d'appel de fonction pour tenir compte des promos avec unité d'achat mini
[!PrixTot:=0!]
[!PrixUn:=1!]
[IF [!Prod::TypeProduit!]=10]
	// ajout md : ajout idref en vu de chgt pour travailler sur id, non fait dans les class
	{
		"price":"[!Ref::getTarifSpe([!quantite!],[!config!],[!PrixTot!])!]",
		"stock":"Ok",
		"reference":"[!Ref::Reference!]",
		"idreference":"[!Ref::Id!]",
		"attribut":"[!Att::Id!]",
		"StockAvailable":"[IF [!Ref::getStockReference()!]>=[!quantite!]]1[ELSE]0[/IF]",
		"promo":"[!Prod::GetPromo!]",
		"QTE":"[!quantite!]",
		"priceUnit":"[!Ref::getTarifSpe([!quantite!],[!config!],[!PrixUn!])!]"
	
	}

[ELSE]

	// ajout md : ajout idref en vu de chgt pour travailler sur id, non fait dans les class
	{
		"price":"[!Ref::getTarif([!quantite!],[!config!])!]",
		"stock":"Ok",
		"reference":"[!Ref::Reference!]",
		"idreference":"[!Ref::Id!]",
		"attribut":"[!Att::Id!]",
		"StockAvailable":"[IF [!Ref::getStockReference()!]>=[!quantite!]]1[ELSE]0[/IF]",
		"promo":"[!Prod::GetPromo!]",
		"QTE":"[!quantite!]",
		"priceUnit":"[!Ref::getTarif([!quantite!],[!config!],[!PrixUn!])!]"
	
	}

[/IF]
