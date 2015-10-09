<div class="row">
	<div class="ListeCategorie">
		[STORPROC [!Query!]/Categorie/Actif=1|Cat|||tmsCreate|ASC]
			<div class="col-md-3">
				<a href="/[!Cat::getUrl()!]" title="[!Cat::NomLong!]">
					<span class="Cat" style="background-image:url('/[!Cat::Image!]');"></span>
					<div class="TitreCategorie">
						<h2>[!Cat::NomLong!]</h2>
					</div>
				</a>
			</div>
		[/STORPROC]
	</div>
</div>
<div class="separator"></div>
<div class="row CentrageProduit">
	[STORPROC [!Query!]/Categorie/Actif=1|Cat|||tmsCreate|ASC]
		[STORPROC [!Query!]/Categorie/[!Cat::Id!]/Produit/Actif=1|Prod|||tmsCreate|ASC]
			[LIMIT 0|10]
				<div class="col-md-2">
					<a href="/[!Cat::getUrl()!]/Produit/[!Prod::Url!]" title="" style="position:relative;top:0" >
						<span class="Produit"></span>
						<img src="/[!Prod::Image!].mini.180x187.jpg" alt="[!Utils::noHtml([!Prod::Description!])!]" />
						<h3>Voir le produit</h3>
					</a>
				</div>
			[/LIMIT]
		[/STORPROC]
		[STORPROC [!Query!]/Categorie/[!Cat::Id!]/Categorie/Produit/Actif=1|Prod|||tmsCreate|ASC]
			[LIMIT 0|10]
				<div class="col-md-2">
					<a href="/[!Cat::getUrl()!]/Produit/[!Prod::Url!]" title="" style="position:relative;top:0" >
						<span class="Produit"></span>
						<img src="/[!Prod::Image!].mini.180x187.jpg" alt="[!Utils::noHtml([!Prod::Description!])!]" />
						<h3>Voir le produit</h3>
					</a>
				</div>
			[/LIMIT]
		[/STORPROC]
	[/STORPROC]
</div>
