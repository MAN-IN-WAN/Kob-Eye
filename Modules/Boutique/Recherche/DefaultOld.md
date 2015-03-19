[!REQUETE:=Boutique/Produit/Actif=1!]

// Mots cles
[IF [!RechercheMotCle!]!=]
	[!Re:=[!Utils::setSearch([!RechercheMotCle!])!]!]
	[!REQUETE+=&&MotClef.ProduitId(Canon~[!Re!])!]
[/IF]

// Tri
[!Order:=tmsEdit!]
[!OrderDir:=DESC!]
[SWITCH [!RechercheTri!]|=]
	[CASE Alphabetique]
		[!Order:=Nom!]
		[!OrderDir:=ASC!]
	[/CASE]
	[CASE PrixASC]
		[!Order:=Tarif!]
		[!OrderDir:=ASC!]
	[/CASE]
	[CASE PrixDESC]
		[!Order:=Tarif!]
	[/CASE]
	[CASE News]
		[!Order:=tmsCreate!]
	[/CASE]
[/SWITCH]

// Filtres
[IF [!RechercheFiltre!]=Promotions]
	[!REQUETE+=&&Promo=1!]
[/IF]

<h1>Résultats de votre recherche</h1>

// Données Pagination
[!Limit:=10!]
[COUNT [!REQUETE!]|Total]
[IF [!Page!]=][!Page:=1!][/IF]
[!Start:=[!Page:-1!]!][!Start*=[!Limit!]!]
[!NbPages:=[!Total:/[!Limit!]!]!]
[IF [!Math::Floor([!NbPages!])!]!=[!NbPages!]]
	[!NbPages:=[!Math::Floor([!NbPages!])!]!]
	[!NbPages+=1!]
[/IF]


<div class="ProduitsBoutique">
	[STORPROC [!REQUETE!]|Prod|[!Start!]|[!Limit!]|[!Order!]|[!OrderDir!]]
		[STORPROC Boutique/Categorie/Produit|Cat|0|1][/STORPROC]
		[!AVendre:=[!Prod::CheckStock!]!]

		// si on a choisi des declinaisons, il faut aller chercher la référence correspondante
		[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|RE|0|1][/STORPROC]
		[COUNT Boutique/Conditionnement/Produit/[!Prod::Id!]|NbCond]
		//[!Surcout:=0!]
		[!Emballage:=!]
		[!NbUnite:=1!]
		[STORPROC Boutique/Conditionnement/Produit/[!Prod::Id!]|COND|||Ordre|ASC]
			[NORESULT]
				[STORPROC Boutique/Conditionnement/ConditionnementDefaut=1|CONDD|0|1|tmsEdit|DESC]
					[!Emballage:= [!CONDD::TypeEmballage!]!]
					[!NbUnite:=[!CONDD::Colisage!]!]
					//[!Surcout:=[!CONDD::Surcout!]!]
				[/STORPROC]
			[/NORESULT]
			[!Emballage:= [!COND::TypeEmballage!]!]
			[!NbUnite:=[!COND::Colisage!]!]
		[/STORPROC]

		<div class="BlocProduit">
			<div class="PaveProduit">
				<div class="ImageProduit">
					<img src="/[!Prod::Image!].mini.100x230.jpg" alt="[!Prod::Nom!]" title="[!Prod::Nom!]" />
				</div>
				<div class="ContentProduit">
					<div class="IntituleProduit">
						<h2>[!Prod::Nom!]</h2>
						<h3>[!Prod::Accroche!]</h3>
					</div>
					<div class="DescrProduit">
						[IF [!Prod::Promo!]]
							[SUBSTR 50| [...]][!Prod::Description!][/SUBSTR]
							<div class="PromoArticle">
								[!Reduction:=[!Utils::Calc_Reduction(P,0,[!RE::Tarif!],[!RE::PrixPromotion!])!]!]
								PROMO [!Reduction!]% 
							</div>
						[ELSE]
							[SUBSTR 150| [...]][!Prod::Description!][/SUBSTR]
						[/IF]
					</div>
					<div class="PrixProduit">
						[IF [!Prod::Promo!]]
							[!PrixUniteHT:=[!RE::PrixPromotion!]!]
							[!PrixUnite:=[!Utils::getMontantTTC([!RE::PrixPromotion!],[!RE::TypeTva!])!]!]
							[!PrixUniteN:=[!Utils::getMontantTTC([!Prod::Tarif!],[!RE::TypeTva!])!]!]
							<div class="bb_strike">
								[!PrixUniteN!] € TTC
							</div>
						[ELSE]
							<br />
							[!PrixUniteHT:=[!Prod::Tarif!]!]
							[!PrixUnite:=[!Utils::getMontantTTC([!Prod::Tarif!],[!RE::TypeTva!])!]!]
						[/IF]
						<div class="FichPrix">
							[!PrixUnite!] € TTC
						</div>
						<div class="UniteArticle" >
							&nbsp;[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/TypeCaracteristique=Bouteille|B|0|1][!B::Valeur!][/STORPROC]
						</div>
						<div class="CalculPRCond" >
							<div class="FichCalculPRCond" ><br />Vendu en [!Emballage!]</div>
						</div>
					</div>
				</div>
			</div>
			<div class="LigneBoutons">
				<a href="/[!Systeme::getMenu(Boutique/Categorie)!]/[!Cat::Url!]/Produit/[!Prod::Url!]" title="Voir la fiche" class="VoirFiche">Voir la fiche</a>
				// AJUSTER QUANTITE
				<a href="/[!Lien!]?RefProduit=[!Prod::Reference!]&amp;Qte=1" title="Voir la fiche" class="AddToCart">Ajouter au panier</a>
			</div>
		</div>
	[/STORPROC]
</div>


// Pagination
[IF [!NbPages!]>1]
	<div class="Pagination">
		Page
		[STORPROC [!NbPages!]|P]
			<a href="/[!Lien!]?RechercheMotCle=[!RechercheMotCle!]&amp;RechercheTri=[!RechercheTri!]&amp;RechercheFiltre=[!RechercheFiltre!][IF [!Pos!]>1]&amp;Page=[!Pos!][/IF]" [IF [!Pos!]=[!Page!]]class="currentPage"[/IF]>[!Pos!]</a>[IF [!Pos!]!=[!NbResult!]]&nbsp;-&nbsp;[/IF]
		[/STORPROC]
	</div>
[/IF]

<a class="HautDePage" title="Retour en haut de la page" href="#">Haut de page</a>