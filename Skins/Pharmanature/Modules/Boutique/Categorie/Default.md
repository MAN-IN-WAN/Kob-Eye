// Devise en cours

//recuperation du magasin
[STORPROC [!Systeme::MenusFromUrl!]|M|0|1]
	[STORPROC [!M::Alias!]|Mag][/STORPROC]
[/STORPROC]
//CONFIG
[!NbProdParLigne:=3!]
[!NbProdParPage:=15!]

[INFO [!Query!]|I]
[STORPROC [!Query!]|Cat]
		<div class="well">
			<div class=" row-fluid">
				[IF [!Cat::Image!]]
					<div class="span2">
							<img src="/[!Cat::Image!].mini.100x100.jpg">
					</div>
				[/IF]
				<div class="[IF [!Cat::Image!]]span10[ELSE]span12[/IF]">
					<h1>[IF [!Cat::NomLong!]!=][!Cat::NomLong!][ELSE][!Cat::Nom!][/IF]</h1>
					<blockquote>
						[!Cat::Description!]
					</blockquote>
				</div>
			</div>
		</div>
		
		// Affichage de la liste des catégories de la catégorie en cours	
		[COUNT [!Query!]/Categorie/Actif=1|NbCat]
		[IF [!NbCat!]]
			[!NbLigne:=[!Math::Floor([!NbCat:/2!])!]!]
			[IF [!NbLigne!]!=[!NbCat:/2!]][!NbLigne++!][/IF]
			[STORPROC [!NbLigne!]|L]
			<div class="row-fluid">
				// calcul pour savoir si on met le border-bottom ou pas
				[STORPROC [!Query!]/Categorie/Actif=1|Cato|[!L:*[!NbLigne!]!]|2|Ordre|ASC]
					<div class="BlocCateg span6 well">
						<div class="row-fluid">
							<div class="span6">
								<a href="/[!Systeme::getMenu(Boutique/Magasin/[!Mag::Id!])!]/Categorie/[!Cato::Url!]" ><img src="/[IF [!Cato::Image!]!=][!Cato::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].mini.200x200.jpg" width="175" height="166" alt="[!Cato::Nom!]" title="[!Cato::Nom!]" /></a>
							</div>
							<div class="span6">
								<h4><a  class="LienCategorieTitre"  href="/[!Systeme::getMenu(Boutique/Magasin/[!Mag::Id!])!]/Categorie/[!Cato::Url!]" >[IF [!Cato::NomLong!]!=][!Cato::NomLong!][ELSE][!Cato::Nom!][/IF]</a></h4>
								<blockquote>[SUBSTR 100|[...]][!Cato::Description!][/SUBSTR]</blockquote>
								<a style="margin-top:5px;" class="btn btn-primary btn-block" href="/[!Systeme::getMenu(Boutique/Magasin/[!Mag::Id!])!]/Categorie/[!Cato::Url!]" >voir le détail</a>
							</div>
						</div>
					</div>
				[/STORPROC]
			</div>
			[/STORPROC]
		[/IF]
		
		//Affichage des produits
		[COUNT [!Query!]/Produit/Actif=1|NBPROD]
		[!NbLigne:=[!Math::Floor([!NBPROD:/[!NbProdParLigne!]!])!]!]
		[!NbLigne+=1!]
		[STORPROC [!NbLigne!]|L]
			<div class="row-fluid">
			[STORPROC [!Query!]/Produit/Actif=1|Prod|[!L:*[!NbProdParLigne!]!]|3|Ordre|ASC]
				//calcul des tarifs
				[!LePrix:=[!Prod::getTarif!]!]
				[!Promo:=0!]
				[!Promo:=[!Prod::GetPromo!]!]
				
				<div class="span4">
					<div class="well" >
						[IF [!Prod::MultiTarif!]=1]<span class="BlocProduitApartir">à partir de</span>[/IF]
						<span class="badge badge-warning pull-right price"style="margin-top:5px;">[!Math::PriceV([!LePrix!])!][!CurrentDevise::Sigle!]</span>
						<h4><a  class="LienCategorieTitre"  href="/[!Prod::getUrl()!]" >[!Prod::Nom!]</a></h4>
						<a  class="LienCategorieTitre" href="/[!Lien!]/Produit/[!Prod::Url!]" >[IF [!Promo!]!=0]<div class="PromoCatProd"></div>[/IF]
						<img src="/[IF [!Prod::Image!]!=][!Prod::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].limit.300x300.jpg" width="300" height="300" alt="[!Prod::Nom!]" title="[!Prod::Nom!]" /></a>
						<p >[!Prod::Accroche!]</p>
						[IF [!Promo!]!=0]
							<div style="display:block;color:#fff;font-size:13px;position:absolute;right:32px;text-decoration:line-through;top:0;" id="tarifNonPromo">[!Math::PriceV([!Prod::getTarifHorsPromo!])!][!CurrentDevise::Sigle!]</div>
						[/IF]
						<a  class="btn btn-primary btn-block" href="/[!Prod::getUrl()!]" >Voir le detail</a>
					</div>
				</div>
			[/STORPROC]
			</div>
		[/STORPROC]
	[NORESULT]
			<div class="well"><h3>Produits disponibles très bientôt</h3></div>
	[/NORESULT]
[/STORPROC]
