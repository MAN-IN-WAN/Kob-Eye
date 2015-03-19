//COMPOSANT UTILISE POUR VOIE ROMAINE A REVOIR QUAND ON AURA FINI CAR LES FONCTIONS POUR LES PRIX DOIVENT ETRE REVU
<div class="ALaUne">
[!OnATrouve:=0!]

[STORPROC Boutique/Produit/UneAccueil=1|Prod|||tmsEdit|DESC]
	[ORDER Id|RANDOM]	
	[COUNT Boutique/Produit/[!Prod::Id!]/Reference|NbRef]
	[IF [!NbRef!]&&[!OnATrouve!]=0]
		[!OnATrouve:=1!]
		[STORPROC Boutique/Categorie/Produit/[!Prod::Id!]|Cat|0|1][/STORPROC]
		[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|RE|0|1][/STORPROC]
	
		[!TxTva:=[!Prod::TypeTva!]!]
		[!Emballage:=!]
		[!NbUnite:=1!]
		[!Promotion:=[!Prod::GetPromo()!]!]
		[!Emballage:=[!Prod::GetEmballage()!]!]
		[!NbUnite:=[!Prod::GetColisage()!]!]	
		[!PrixNormal:=[!Prod::Tarif!]!]
		[!PrixNormal*=[!NbUnite!]!]
		// Détection du vrai prix (HT)
		[IF [!Promotion!]]
			[!EnPromo:=1!]
			[!Reduction:=[!Promotion::GetNiveauReduction([!NbUnite!]],[!Prod::Tarif!])!]!]
			[!PrixPromo:=[!Promotion::GetTarifPromo([!NbUnite!],[!Prod::Tarif!])!]!]
		[ELSE]
			[!EnPromo:=0!]
		[/IF]

		<h1>A decouvir en ce moment sur [!CONF::GENERAL::INFO::SITE_NAME!]</h1>
	
		<div class="BlocProduitUne">
	
			// IMAGE
			<div class="BlocProduitUneImage">
				[IF [!Prod::Image!]!=]<a href="/[!Prod::getUrl()!]"><img src="/[!Prod::Image!].mini.150x179.jpg" alt="[!Prod::Nom!]" title="[!Prod::Nom!]"  /></a>[/IF]
			</div>
	
			// DESCRIPTION
			<div class="BlocProduitUneContenu">
				<div class="CategorieProduitUne">[IF [!Cat::NomCourt!]!=][!Cat::NomCourt!][ELSE][!Cat::Nom!][/IF]</div>
				<div class="NomProduitUne">[!Prod::Nom!]</div>
				<div class="AccrocheProduitUne">
					[!Prod::Accroche!]
					[IF [!EnPromo!]=1&&[!Promotion::APartirNbUnite!]>1]<span class="promoquantite">* Promo à partir de [!Promotion::APartirNbUnite!] produits achetés</span>[/IF]

				</div>
			</div>
	
			// PRIX
			<div class="BlocProduitUnePrix">
				<div class="DescriptionPrixUne">
					[!Emballage::TypeEmballage!]<br />
					[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/TypeCaracteristique=Bouteille|B|0|1]
						[!B::Valeur!]
					[/STORPROC]
				</div>
	
				// Détection du vrai prix (HT)
				[IF [!EnPromo!]=1]
					<div class="PrixBarreUne">
						*[!Utils::getMontantTTC([!PrixNormal!],[!TxTva!])!] € TTC
					</div>
					<div class="PrixUne">
						[!Utils::getMontantTTC([!PrixPromo!],[!TxTva!])!] € TTC
					</div>
				[ELSE]
					<div class="PrixUne">[!Utils::getMontantTTC([!PrixNormal!],[!TxTva!])!] € TTC</div>
				[/IF]

				<div class="PrixUneUnite">
					[IF [!NbUnite!]>1]
						[!PrixUnitaire:=[!PrixNormal!]!]
						[!PrixUnitaire/=[!NbUnite!]!]
						soit [!Utils::getMontantTTC([!PrixUnitaire!],[!TxTva!])!] € TTC l'unité
					[/IF]
					
				</div>
				
	
				<a href="/[!Prod::getUrl()!]" class="ALaUneProfiter">J'en profite</a>
			</div>
		</div>
	[/IF]
	[/ORDER]
[/STORPROC]

</div>