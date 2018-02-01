//Liste des produits
// Calcul Pagination
[!UrlPage:=[!Lien!]!]
[!BorneDe:=0!]
[!BorneA:=9!]
[!Total:=0!]
[!Requete:=[!Query!]!]
[COUNT [!Query!]/Produit|NbP]
[IF [!NbP!]]
[ELSE]
	[!Requete:=[!Query!]/Categorie/*!]
	[COUNT [!Requete!]/Produit|NbProd]
	[IF [!NbProd!]]
	[ELSE]
		[!Requete:=[!Query!]/Categorie/*/Categorie/*!]
	[/IF]
[/IF]

// on vérifie les critères de sélection
[!LesCriteresDeRecherche:=!]
[IF [!RechercheMotCle!]!=Rechercher...&&[!RechercheMotCle!]!=]
	[!Re:=[!Utils::setSearch([!RechercheMotCle!])!]!]
	[!LesCriteresDeRecherche+=&&MotClef.ProduitId(Canon~[!Re!])!]
[/IF]
[!OrderCh:=tmsEdit!]
[!OrderDir:=DESC!]
[SWITCH [!RechercheTri!]|=]
	[CASE Alphabetique]
		[!OrderCh:=Titre!]
		[!OrderDir:=ASC!]
	[/CASE]
	[CASE PrixASC]
		[!OrderCh:=PPHT!]
		[!OrderDir:=ASC!]
	[/CASE]
	[CASE PrixDESC]
		[!OrderCh:=PPHT!]
		[!OrderDir:=DESC!]
	[/CASE]
	[CASE News]
		[!OrderCh:=tmsCreate!]
		[!OrderDir:=DESC!]
	[/CASE]
[/SWITCH]

[IF [!RechercheFiltre!]!=]
	[!LesCriteresDeRecherche+=&&Fabricant=[!RechercheFiltre!]!]
[/IF]


[COUNT [!Requete!]/Produit/Publier=1[!LesCriteresDeRecherche!]|Total]
<h2>Nos produits</h2>
<div class="Filtres">
	[MODULE Catalogue/Recherche]	
</div>

[IF [!Total!]]
	[IF [!Page!]=][!Page:=1!][/IF]
	[!IdxPage:=[!Page:-1!]!]
	[!BorneDe:=[!IdxPage:*[!BorneA!]!]!]
	[!NbPages:=[!Total:/[!BorneA!]!]!]
	[IF [!Math::Floor([!NbPages!])!]!=[!NbPages!]]
		[!NbPages:=[!Math::Floor([!NbPages!])!]!]
		[!NbPages+=1!]
	[/IF]
	[!Prev:=[!Page:-1!]!]
	[IF [!Prev!]<1][!Prev:=1!][/IF]
	[!Next:=[!Page:+1!]!]
	[IF [!Next!]>[!NbPages!]][!Next:=[!NbPages!]!][/IF]
	[STORPROC [!Requete!]/Produit/Publier=1[!LesCriteresDeRecherche!]|P|[!BorneDe!]|[!BorneA!]|[!OrderCh!]|[!OrderDir!]]
		[IF [!NbPages!]!=1&&[!NbPages!]!=0]
			<div class="Pagination">
				<div class="PaginationBody">
					<a class="PagiFirst" href="/[!UrlPage!]">&nbsp;</a>
					<a class="PagiPrev" href="/[!UrlPage!]?[IF [!Prev!]>1]Page=[!Prev!][/IF]">&nbsp;</a>
					[STORPROC [!NbPages!]|P]
						<a href="/[!UrlPage!][IF [!Pos!]>1]?Page=[!Pos!][/IF]" [IF [!Pos!]=[!Page!]] class="currentPage" [/IF]>[!Pos!]</a>
					[/STORPROC]
					<a class="PagiNext" href="/[!UrlPage!]?Page=[!Next!]">&nbsp;</a>
					<a class="PagiLast" href="/[!UrlPage!]?Page=[!NbPages!]">&nbsp;</a>
				</div>
			</div>
		[/IF]	
		// Affichage de la liste
		<div class="ListeProduits">
			[LIMIT 0|9]
				[STORPROC Catalogue/Categorie/Produit/[!P::Id!]|Cat|0|1][/STORPROC]
				[!CataTester:=/[!Cat::Url!]!]
				<a href="/[!Lien!][IF [!Lien!]~[!CataTester!]][ELSE]/[!Cat::Url!][/IF]/Produit/[!P::Url!]">
					<div class="UnProduit">
						<div class="TitreProduit">[!Cat::Nom!]</div>
						<div class="DetailsProduit">
							<div class="ImgProduit">
								<img src="[IF [!P::Image!]!=]/[!P::Image!].limit.78x117.jpg[ELSE][!Domaine!]/Skins/[!Systeme::Skin!]/Img/defautProd.jpg.limit.78x117.jpg[/IF]" title="[!P::Titre!]" alt="[!P::Titre!]" />
							</div>
							
							<div class="DescProduit">
								[IF [!P::Fabricant!]!=]
									[STORPROC Catalogue/Fabricant/[!P::Fabricant!]|Fab|0|1][/STORPROC]
									<div class="UneInfoTitre">[!Fab::Nom!]</div>
								[/IF]
								[IF [!P::Titre!]!=]<div class="UneInfoTitre [IF [!P::Chapo!]=]MargB10[/IF]" >[SUBSTR 30|...][!P::Titre!][/SUBSTR]</div>[/IF]
								[IF [!P::DescriptionDansListe!]]
									<div class="UneInfo" style="display: block;height: 70px;overflow: hidden;">[!P::Description!]</div>
								[ELSE]
									[IF [!P::Chapo!]!=]<div class="UneInfoSousTitre " >[!P::Chapo!]</div>[/IF]
									[IF [!P::Dimensions!]!=]<div class="UneInfo" >- [!P::Dimensions!]</div>[/IF]
									[IF [!P::SolMurale!]!=]<div class="UneInfo">- [!P::SolMurale!]</div>[/IF]
									[IF [!Cat::Nom!]!=]<div class="UneInfo">- [!Cat::Nom!]</div>[/IF]
									[IF [!P::Service!]!=]<div class="UneInfo">- [!P::Service!]</div>[/IF]
									[IF [!P::Evacuation!]!=]
										<div class="UneInfo">
											[SWITCH [!P::Evacuation!]|=]
												[CASE CF]
													- Conduit Fumée
												[/CASE]
												[CASE FF]
													- Flux forcé
												[/CASE]
												[CASE VMC_Gaz]
													- VMC Gaz
												[/CASE]
											[/SWITCH]
										</div>
									[/IF]
								[/IF]
							</div>
						</div>
						<div class="LienEtPrixProduit">
							<div class="FichePrix">
								Détail
							</div>
							<div class="FicheProduit">
								[IF [!P::Tva!]=0]
								[ELSE]
//									[!TauxTva:=[!P::TauxTva!]!]
									[!TauxTva:=!]
									[STORPROC Catalogue/TypeTaux/Nom=[!P::TypeTaux!]|Ttva|0|1]
										[STORPROC Catalogue/TypeTaux/[!Ttva::Id!]/Tauxtva/Publier=1&DateApplication<=[!TMS::Now!]&DateFin>=[!TMS::Now!]|Tx|0|1]
											[!TauxTva:=[!Tx::Taux!]!]
										[/STORPROC]
									[/STORPROC]
									[IF [!P::Tva!]=1]
										<div class="PPIProduit" ><span class="dontTva" >(dont TVA [!TauxTva!]%)</span>
										[!Utils::getPrice([!P::PrixTTC([!P::PPHT!],[!TauxTva!])!])!] €</div>
									[ELSE]
										[IF [!P::PPHT!]!=&&[!P::PPHT!]!=0]			
											<div class="PPIProduit" >
												<span class="dontTva" >(dont TVA [!TauxTva!]%)</span>
												[!Utils::getPrice([!P::PrixTTC([!P::PPHT!],[!TauxTva!])!])!] €
											</div>
											[STORPROC Catalogue/TypeTaux/Nom=[!P::TypeTaux2!]|Ttva|0|1]
												<div class="PPIProduit" >
													[STORPROC Catalogue/TypeTaux/[!Ttva::Id!]/Tauxtva/Publier=1&DateApplication<=[!TMS::Now!]&DateFin>=[!TMS::Now!]|Tx|0|1]
														[!TauxTva:=[!Tx::Taux!]!]
														<span class="dontTva" >(dont TVA [!TauxTva!]%)</span>
														<span style="font-size: 12px;">[!Utils::getPrice([!P::PrixTTC([!P::PPHT!],[!TauxTva!])!])!] €</span>
													[/STORPROC]
												</div>
											[/STORPROC]
										[/IF]
									[/IF]
								[/IF]
							</div>
							
						</div>
					</div>
				</a>
				
			[/LIMIT]
		</div>
		[IF [!NbPages!]!=1&&[!NbPages!]!=0]
			<div class="Pagination"  >
				<div class="PaginationBody">
					<a class="PagiFirst" href="/[!UrlPage!]">&nbsp;</a>
					<a class="PagiPrev" href="/[!UrlPage!]?[IF [!Prev!]>1]Page=[!Prev!][/IF]">&nbsp;</a>
					[STORPROC [!NbPages!]|P]
						<a href="/[!UrlPage!][IF [!Pos!]>1]?Page=[!Pos!][/IF]" [IF [!Pos!]=[!Page!]] class="currentPage" [/IF]>[!Pos!]</a>
					[/STORPROC]
					<a class="PagiNext" href="/[!UrlPage!]?Page=[!Next!]">&nbsp;</a>
					<a class="PagiLast" href="/[!UrlPage!]?Page=[!NbPages!]">&nbsp;</a>
				</div>
			</div>
		[/IF]	
	[/STORPROC]
[ELSE]
	<div class="BlocError" style="width:300px;">Aucun produit ne correspond à votre recherche</div>
[/IF]