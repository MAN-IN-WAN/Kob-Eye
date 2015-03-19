//affichage des produits en liste
[!LIENURL:=/[!Lien!]?RechercheMotCle=[!RechercheMotCle!]&amp;RechercheTri=[!RechercheTri!]&amp;RechercheFiltre=[!RechercheFiltre!]&amp;TitreListe=[!TitreListe!]!]
[!REQUETE:=Boutique/Produit/Actif=1&&StockReference>0!]
//[!REQUETE!]
// Mots cles
[IF [!RechercheMotCle!]!=Rechercher...&&[!RechercheMotCle!]!=]
	[!Re:=[!Utils::setSearch([!RechercheMotCle!])!]!]
	[!REQUETE+=&&~[!Re!]!]
[/IF]
//[!REQUETE!]
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
		[!OrderDir:=DESC!]
	[/CASE]
	[CASE News]
		[!Order:=tmsCreate!]
		[!OrderDir:=DESC!]
	[/CASE]
	[CASE PlusVisites]
		[!Order:=Visites!]
		[!OrderDir:=DESC!]
	[/CASE]
[/SWITCH]

// Filtres
<div class="RechercheProduit">

	[SWITCH [!RechercheFiltre!]|=]
		[CASE Promotions]
			[!REQUETE+=&&Promotion.ProduitId(DateDebutPromo<=[!TMS::Now!]&&DateFinPromo>=[!TMS::Now!])!]
		[/CASE]
		[CASE Coeur]
			[!REQUETE+=&&Coeur=1!]
		[/CASE]
		[CASE IdKdo]
			[!REQUETE+=&&IdKdo=1!]
		[/CASE]


	[/SWITCH]

	[IF [!RechercheFiltre!]=Promotions]
		[IF [!RechercheMotCle!]!=Rechercher...&&[!RechercheMotCle!]!=]
			<h1 >[!TitreListe!]- [IF [!Cat::Nom!]] "[!Cat::Nom!]"[/IF]</h1> en promotion
		[/IF]
	[ELSE]
		<h1>[!TitreListe!] [IF [!Cat::Nom!]] "[!Cat::Nom!]"[/IF]</h1>
	[/IF]
	
	// Données Pagination
	[!TypeEnf:=Recherche!]
	//Definition des elements a afficher
	[IF [!PagePos!]!=]
		[!Page[!TypeEnf!]:=[!PagePos!]!]
	[ELSE]
		[!Page[!TypeEnf!]:=1!]
	[/IF]
	
	[!MaxLine:=5!] // nb d'éléments à afficher
	[COUNT [!REQUETE!]|Total]

	[IF [!Total!]>0]
		//On compte le nombre total d element a affciher
		[!TotalPage:=[!Total:/[!MaxLine!]!]!]
		//On calcule le nombre total de page
		[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]
			//On arrondit au chiffre superieur le nombre total de page
			[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
		[/IF]
		//PAGINATION
		[IF [!TotalPage!]>1]
			[MODULE Boutique/Recherche/Pagination?lelien=[!LIENURL!]&PageCourante=[!Page[!TypeEnf!]!]&TotalP=[!TotalPage!]]
		[/IF]	

		<table class="RechercheProduit">
			<tr>
				<th colspan="2">Produit</th>
				<th>Prix Unitaire<br /><span style="font-size:11px;font-style:italic;text-transform:none;font-weight:normal;">(à partir de)</span> </th>
				<th style="border-right:none;">Actions</th>
			</tr>
			[STORPROC [!REQUETE!]|Prod|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|[!Order!]|[!OrderDir!]]
				[STORPROC Boutique/Categorie/Produit/[!Prod::Id!]|Cat|0|1][/STORPROC]
				[!Promo:=[!Prod::EstenPromo()!]!]
				[IF [!Prod::getTarif!]>0]
					<tr>
						<td class="Imgrecherche" style="border-right:none;">
							<a href="/[!Prod::getUrl()!]" title="Voir la fiche de [!Prod::Nom!]"><img src="/[IF [!Prod::Image!]!=][!Prod::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].mini.55x55.jpg" width="55" height="55" alt ="[!Prod::Nom!]" title="[!Prod::Nom!]" /></a>
						</td>
						<td class="Nom">
							<a href="/[!Prod::getUrl()!]" title="Voir la fiche"><h2>[!Prod::Nom!]</h2></a><h3>[!Prod::Accroche!]</h3>
							//[IF [!Promo!]>0&&[!Promotion::APartirNbUnite!]>1]<span class="promoquantite">* Promo à partir de [!Promotion::APartirNbUnite!] produits achetés</span>[/IF]
						</td>
						<td class="Prix" style="text-align:center;font-weight:bold;vertical-align:middle;">
							[IF [!Promo!]]
								<div class="Fichprixbarre" style="text-decoration:line-through;">
									[!Math::PriceV([!Prod::getTarifHorsPromo!])!]</span> [!De::Sigle!] TTC		
								</div>
								<div class="PrixAchat">[!Math::PriceV([!Prod::getTarif!])!][!De::Sigle!] TTC</div>
							[ELSE]
								<div class="PrixAchat">[!Math::PriceV([!Prod::getTarif!])!][!De::Sigle!] TTC</div>
			
							[/IF]
						</td>
						<td class="Achat" style="border-right:none;vertical-align:middle;text-align:left;">
							[IF [!Prod::TypeProduit!]=3]
								//Produit unique 
								[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|Re|0|1][/STORPROC]
									<form method="post" action="/Boutique/Commande/Etape1" name="achatresultat[!Prod::Id!]">
										<input type="hidden" name="Qte" value="1">
										<input type="hidden" name="Reference" value="[!Re::Reference!]">
										<input type="submit" name="Acheter[!Prod::Id!]" value="Acheter"  class="AddToCartRecherche" />
									</form>
									<a href="/[!Prod::getUrl()!]" title="Voir la fiche" class="voirDetailRecherche">Voir</a>
							[ELSE]
	
								<a href="/[!Prod::getUrl()!]" title="Voir la fiche" class="AddToCartRecherche">Acheter</a></<br />
								<a href="/[!Prod::getUrl()!]" title="Voir la fiche" class="voirDetailRecherche">Voir</a>
							[/IF]
						</td>
					</tr>
				[/IF]
			[/STORPROC]
		</table>
		//PAGINATION
		[IF [!TotalPage!]>1]
			[MODULE Boutique/Recherche/Pagination?lelien=[!LIENURL!]&PageCourante=[!Page[!TypeEnf!]!]&TotalP=[!TotalPage!]]
		[/IF]
	[ELSE]
		<p class="PasDeResultat">Aucun résultat pour vos critères de recherche, essayez d'autres mots clés ou supprimez le filtre...</p>
	[/IF]
</div>