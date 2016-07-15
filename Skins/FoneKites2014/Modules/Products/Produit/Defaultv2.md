[STORPROC [!Query!]|P|0|1][/STORPROC]
[!lelienS:=!][!lelienP:=!]
[!NomProdP:=!][!NomProdS:=!]
[STORPROC Products/Categorie/Produit/[!P::Id!]|CatP|0|1][/STORPROC]

[STORPROC Products/Categorie/[!CatP::Id!]/Produit/Id>[!P::Id!]|ProdS|0|1|Id|ASC]
	[STORPROC Products/Categorie/Produit/[!ProdS::Id!]|CatS|0|1][/STORPROC]
	[!lelienS:=Products_/[!CatS::Url!]/Produit/[!ProdS::Url!]!]
	[!NomProdS:=[!ProdS::Nom!]!]
[/STORPROC]
[STORPROC Products/Categorie/[!CatP::Id!]/Produit/Id<[!P::Id!]|ProdP|0|1|Id|DESC]
	[STORPROC Products/Categorie/Produit/[!ProdP::Id!]|CatP|0|1][/STORPROC]
	[!lelienP:=Products_/[!CatP::Url!]/Produit/[!ProdP::Url!]!]
	[!NomProdP:=[!ProdP::Nom!]!]
[/STORPROC]

<div class="titre-product gris-clair">
	<div class="container title-product">
		<div class="nav-product">
			<div class="nav-product-btn">
				[IF [!lelienP!]!=]
					//<a class="left" href="/[!lelienP!]">left</a>
					<a class="left" href="/[!lelienP!]" title="[!NomProdP!]"><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-left.png" class="img-responsive" alt="Fone"/></a>
				[/IF]
			</div>
			<div class="nav-product-btn">
				[IF [!lelienS!]!=]
					//<a class="right" href="/[!lelienS!]">right</a>
					<a class="right" href="/[!lelienS!]" title="[!NomProdS!]"><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-right.png" class="img-responsive" alt="Fone"/></a>
				[/IF]
			</div>
			[IF [!NomProdP!]!=&&[!NomProdS!]=]
				<div class="next-prod">
					[!ProdP::Nom!]
				</div>
			[/IF]
			[IF [!NomProdS!]!=]
				<div class="next-prod">
					[!ProdS::Nom!]
				</div>
			[/IF]
		</div>
		//<div class="caract">[!CatP::Nom!]</div>
		<h1 class="title_prod">[!P::Nom!]<span class="title">[!P::SousTitre!]</span></h1>
		<div class="caract">[IF [!P::Chapo!]!=][!P::Chapo!][ELSE]Remplir champ chapo du produit[/IF]</div>
	</div>
</div>
<div class="featured">
	<div class="container">
		[COUNT [!Query!]/Illustration|NbPill]
		[IF [!NbPill!]]
			<div class="row">
				<div id="big-photo" class=" col-lg-7 colborder col big-photo"> 
					<img src="/[!P::ProduitGrandFormat!]" class="img-responsive" alt="[!P::Nom!]" />
					<div class="pull-right" style="line-height:42px;">
						[MODULE Systeme/Social/Likes?Lurl=[!Lien!]]

					</div>
				</div>
				<div class="col-lg-5 colborder col2">
					<div class="photo-thumbnails">
						<div class="col-xs-4">
							<div class="thumbnail">
								<img src="/[!P::ProduitGrandFormat!]" class="img-responsive" alt="[!P::Nom!]" />
							</div>
						</div>
						[STORPROC [!Query!]/Illustration|Pill|||Ordre|ASC]
							<div class="col-xs-4">
								<div class="thumbnail">
									<img src="/[!Pill::Image!]" class="img-responsive" alt="[!P::Nom!]" />
								</div>
							</div>
						[/STORPROC]
					</div>
					<div class="info-produit">
						[STORPROC [!Query!]/Description|Des|||Ordre|ASC]
							<h3>[!Des::Titre!]<span> [!Des::SousTitre!]</span></h3>
							<p>[!Des::Contenu!]</p>
						[/STORPROC]
					</div>
					<div class="video-produit">
						[!P::Iframe!]
					</div>  
					[COUNT [!Query!]/Range|NbRg]
					[IF [!NbRg!]]
						<div class="range-use">
							<h4>Range of Use</h4>
							[STORPROC [!Query!]/Range|Rg|||Ordre|ASC]
								<div class="progress">
									<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="[!Rg::Note!]" aria-valuemin="0" aria-valuemax="100" style="width: [!Rg::Note!]%">
										<span class="sr-only-com">[!Rg::Nom!]</span>
									</div>
								</div>
							[/STORPROC]
						</div>
					[/IF]
				</div>
			</div>
		[ELSE]
			<div class="row">
				<div class="col-lg-6 colborder col big-photo">
					<img class="img-responsive" src="/[!P::ProduitGrandFormat!]" alt="[!P::Nom!]" />
				</div>
				<div class="col-lg-6 colborder col2">
					<div class=" info-produit">
						[STORPROC [!Query!]/Description|Des|||Ordre|ASC]
							<h3>[!Des::Titre!]<span> [!Des::SousTitre!]</span></h3>
							<p>[!Des::Contenu!]</p>
						[/STORPROC]
					</div>
					<div class="video-produit">
						[!P::Iframe!]
					</div>  
					[COUNT [!Query!]/Range|NbRg]
					[IF [!NbRg!]]
						<div class="range-use">
							<h4>Range of Use</h4>
							[STORPROC [!Query!]/Range|Rg|||Ordre|ASC]
								<div class="progress">
									<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="[!Rg::Note!]" aria-valuemin="0" aria-valuemax="100" style="width: [!Rg::Note!]%">
										<span class="sr-only-com">[!Rg::Nom!]</span>
									</div>
								</div>
							[/STORPROC]
						</div>
					[/IF]
				</div>
			</div>
		[/IF]
	</div>
	[COUNT [!Query!]/Sizes|Nbsiz]
	[IF [!Nbsiz!]]
		<div class="container">
			<div class="size">
				<ul>
					<div class="row">
						<li class="col-lg-1 col-sm-1-1 col-xs-3 active">SIZE (cm)<br>WIND RANGE </li>
						[STORPROC [!Query!]/Sizes|Si|||Ordre|ASC]
							<li class="col-lg-1 col-sm-1-1 col-xs-3"> [!Si::Contenu!]</li>
						[/STORPROC]
					</div>
				</ul>
			</div>
		</div>
	[/IF]
</div>
[COUNT [!Query!]/PunchText|NbPunch]
[IF [!NbPunch!]]
	<div class="gris-fonce">
		<div class="container">
			[STORPROC [!Query!]/PunchText|Pt|||Ordre|ASC]
				<h1>[!Pt::Texte!]</h1>
			[/STORPROC]
		</div>
	</div>
[/IF]
[COUNT [!Query!]/Technologie|NbTech]
[IF [!NbTech!]]
	<div class="gris">
		<div class="container">
			<h1>TECHNOLOGIES <br> CONSTRUCTION</h1>
			<div class="produits">
				<div class="marques">
					<div class="row mark">
						<div id="thumbnails"> 
							[STORPROC [!Query!]/Technologie|Tech|||Ordre|ASC]
								<div class="col-xs-2">
									<a href="/[!Tech::Image!]" [IF [!Pos!]=1]class="active"[/IF] ><img src="/[!Tech::Icone!]" class="img-responsive" alt="[!Tech::Titre!]" /></a> 
								</div>
							[/STORPROC]
						</div>
					</div>
				</div>
				<div class="details-technique produits">
					<div class="details-const">
						<div id="imageview"  > 
							[STORPROC [!Query!]/Technologie|Tech|0|1|Ordre|ASC]
								<img src="/[!Tech::Image!]" class="img-responsive" alt="[!Tech::Titre!]" />
							[/STORPROC]
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
[/IF]



[COUNT Products/Categorie/_Accessories/Produit/Id!=[!P::Id!]|NbProdAcc]
[IF [!NbProdAcc!]]
	<div class="gris-related">
		<div class="container">
			<h1>Related Gears</h1>
			[STORPROC Products/Categorie/_Accessories/Produit/Id!=[!P::Id!]|Pacc|0|2]
				[ORDER Id|RANDOM]
					[STORPROC Products/Categorie/Produit/[!Pacc::Id!]|CatP][/STORPROC]
					<div class="col-lg-6 colborder">
						<div class="produits">
							<img src="/[!Pacc::ProduitGrandFormat!]" class="img-responsive" alt="[!Pacc::Nom!]"/>
							<div class="prod-3-25-1">
								<h2>[!CatP::Nom!]</h2>
								<h3>[!Pacc::Nom!]</h3>
							</div>
						</div>
					</div>
				[/ORDER]
			[/STORPROC]
		</div>
	</div>
</div>
[IF [!P::Iframe!]!=]
	<div class="product-video">
		<div class="container">
			<h1>PRODUCT VIDEO</h1>
			<div class="video-large produits">
				[!P::Iframe!]
			</div>
		</div>
	</div>
[/IF]

