<div class="row-fluid">
	<div class="ListeCategorie">
		[STORPROC [!Query!]|Cat|||tmsCreate|ASC]
			[IF [!Cat::Image!]!=]
				<div class="SPAN12">
					<img src="/[!Cat::Image!].limit.732x260.jpg" alt="[!Cat::Nom!]" title="[!Cat::Nom!]" />
				</div>
			[/IF]
		[/STORPROC]
	</div>
	<div class="SelectionProduits">
		<div class="Titre">Notre sélection</div>
		[!Cpt:=0!]
		<div class="ListeProduitsCat row-fluid">
			[STORPROC [!Query!]/Categorie/*/Produit/Actif=1|Prod|||tmsCreate|ASC]
				[STORPROC Boutique/Categorie/Produit/[!Prod::Id!]|CatUrl|0|1][/STORPROC]
				[!Cpt+=1!]
				[IF [!Cpt!]=4]</div><div class="ListeProduitsCat row-fluid">[/IF]
				<div class="span4">
					<div class="NomProduit"><h2>[!Prod::Nom!]</h2></div>
					<div class="AccrocheProduit">[!Prod::Acrroche!]</div>
					<a href="/[!CatUrl::getUrl()!]/Produit/[!Prod::Url!]" title="[!Utils::noHtml([!Prod::Description!])!]">
						<img src="/[!Prod::Image!].mini.215x174.jpg" />
					</a>
					<div class="Details">
						<p class="Tarif">[!Math::PriceV([!Prod::getTarif!])!] €</p>
					</div>
					<div class="DetailsSous">
						<a href="/[!CatUrl::getUrl()!]/Produit/[!Prod::Url!]" title="[!Prod::Nom!]" class="loupelien" >Voir le détail</a>
						<a href="/[!CatUrl::getUrl()!]/Produit/[!Prod::Url!]#Qte" title="Panier" class="panierliste">Mettre au panier</a>
					</div>
				</div>
			[/STORPROC]
		</div>
	</div>
</div>
