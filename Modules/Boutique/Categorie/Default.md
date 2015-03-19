// Devise en cours
[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]
[INFO [!Query!]|I]
[STORPROC [!Query!]|Cat]
	[COUNT [!Query!]/Produit/Actif=1&&Tarif>0|NbProd]
	// Affichage de la liste des produits de la catégorie en cours	
	<div class="BlocCategories">
		<div class="BandeauTitreBlocCategorie">
			<h1 class="TitreBlocCategorie">[IF [!Cat::NomLong!]!=][!Cat::NomLong!][ELSE][!Cat::Nom!][/IF]</h1>
		</div>
		[IF [!NbProd!]]
			<div class="toutesCategories">
				// calcul pour savoir si on met le border-bottom ou pas
				[STORPROC [!Query!]/Produit/Actif=1|Prod|||Ordre|ASC]
					[!LePrix:=[!Prod::getTarif!]!]
					
					[!Promo:=0!]
					[!Promo:=[!Prod::GetPromo!]!]
					[IF [!LePrix!]>0]
						<div class="BlocCateg">
							<div class="Categorie " >
								<div class="CategorieTitreBlock " >
									<div class="CategoriePrix">
										[IF [!Prod::MultiTarif!]=1]<span class="BlocProduitApartir">à partir de</span>[/IF]
										<br />
										<span class="BlocProduitPrix">[!Math::PriceV([!LePrix!])!][!De::Sigle!]
										</span>
									</div>
									[IF [!Promo!]!=0]
										<div style="display:block;color:#fff;font-size:13px;position:absolute;right:32px;text-decoration:line-through;top:0;" id="tarifNonPromo">[!Math::PriceV([!Prod::getTarifHorsPromo!])!][!De::Sigle!]</div>
									[/IF]
									<h2><a  class="LienCategorieTitre"  href="/[!Prod::getUrl()!]" >[!Prod::Nom!]</a></h2>
								</div>
								<div class="CategorieContenu">
									<div class="ImageCategorie">
										<a  class="LienCategorieTitre" href="/[!Lien!]/Produit/[!Prod::Url!]" >[IF [!Promo!]!=0]<div class="PromoCatProd"></div>[/IF]
										<img src="/[IF [!Prod::Image!]!=][!Prod::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].limit.175x166.jpg" width="175" height="166" alt="[!Prod::Nom!]" title="[!Prod::Nom!]" /></a>
									</div>
									<div class="DescCategorieProduit">
										[!Prod::Accroche!]
										<a  class="LienBtnCategorie" href="/[!Prod::getUrl()!]" >Voirledetail</a>
									</div>
								</div>
							</div>
						</div>
					[/IF]
				[/STORPROC]
			</div>
		</div>
	[ELSE]
			<div class="toutesCategories"><h3>Produits disponibles très bientôt</h3></div>
	[/IF]
[/STORPROC]
// Affichage de la liste des catégories de la catégorie en cours	

[COUNT [!Query!]/Categorie/Actif=1|NbCat]
[IF [!NbCat!]]
	<div class="BlocCategories">
		<div class="BandeauTitreBlocCategorie">
			<h1 class="TitreBlocCategorie">[IF [!Cat::NomLong!]!=][!Cat::NomLong!][ELSE][!Cat::Nom!][/IF]</h1>
		</div>
		<div class="toutesCategories">
			// calcul pour savoir si on met le border-bottom ou pas
			[STORPROC [!Query!]/Categorie/Actif=1|Cato|||Ordre|ASC]
				<div class="BlocCateg">
					<div class="Categorie " >
						<div class="CategorieTitreBlock " >
							<h2><a  class="LienCategorieDetail" href="[!Domaine!]/BoutiqueSableEtJasmin/Categorie/[!Cato::Url!]" >voir <br />le détail</a>
							<a  class="LienCategorieTitre"  href="[!Domaine!]/BoutiqueSableEtJasmin/Categorie/[!Cato::Url!]" >[IF [!Cato::NomLong!]!=][!Cato::NomLong!][ELSE][!Cato::Nom!][/IF]</a></h2>

						</div>
						<div class="CategorieContenu">
							<div class="ImageCategorieRight">
								<a  class="LienCategorieTitre"  href="/[!Lien!]/Categorie/[!Cato::Url!]" ><img src="/[IF [!Cato::Image!]!=][!Cato::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].limit.175x166.jpg" width="175" height="166" alt="[!Cato::Nom!]" title="[!Cato::Nom!]" /></a>
							</div>
							<div class="DescCategorieCat">
								[SUBSTR 320|[...]][!Cato::Description!][/SUBSTR]
							</div>
						</div>
						
					</div>
				</div>
			[/STORPROC]
		</div>
	</div>
[/IF]
