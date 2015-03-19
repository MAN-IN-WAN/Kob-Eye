[IF [!Chemin!]=]
	[!Chemin:=[!Query!]!]
[/IF]
[STORPROC [!Chemin!]|Cat][/STORPROC]
<div class="row-fluid CentrageProduit">
	<div class="ListeCategorie">
		[STORPROC [!Chemin!]|Cat|||tmsCreate|ASC]
			[IF [!Cat::Image!]!=]
				<div class="span12">
					<img src="/[!Cat::Image!].limit.732x260.jpg" alt="[!Cat::Nom!]" title="[!Cat::Nom!]" />
				</div>
			[ELSE]
				[STORPROC Boutique/Categorie/Categorie/[!Cat::Id!]|CatP]
					[IF [!CatP::Image!]!=]
						<div class="span12">
							<img src="/[!CatP::Image!].limit.732x260.jpg" alt="[!CatP::Nom!]" title="[!CatP::Nom!]" />
						</div>
					[/IF]
				[/STORPROC]
			[/IF]
		[/STORPROC]
	</div>
	<div class="row-fluid SelectionProduits ">
		<div class="Titre">Nos Produits</div>
		[!Cpt:=0!]
		<div class="ListeProduitsCat">
			[STORPROC [!Chemin!]/Produit/Actif=1|Prod|||tmsCreate|ASC]
				[!Cpt+=1!]
				[IF [!Cpt!]=4][!Cpt:=1!]</div><div class="ListeProduitsCat row-fluid">[/IF]
				<div class="span4">
					<div class="NomProduit"><h2>[!Prod::Nom!]</h2></div>
					<div class="AccrocheProduit">[SUBSTR 35|...][!Prod::Accroche!][/SUBSTR]</div>
					<a href="/[!Prod::getUrl()!]" title="[!Utils::noHtml([!Prod::Description!])!]">
						<img src="/[!Prod::Image!].mini.215x174.jpg" />
					</a>
					<div class="LesDetails">
						<div class="Details">
							<p class="Tarif">[!Math::PriceV([!Prod::getTarif!])!] €</p>
						</div>
						<div class="DetailsSous">
							<a href="/[!Prod::getUrl()!]" title="[!Prod::Nom!]" class="loupelien" >Voir le détail</a>
							<a href="/[!Prod::getUrl()!]#Qte" title="Panier" class="panierliste">Mettre au panier</a>
						</div>
					</div>
				</div>
			[/STORPROC]
			[IF [!Cpt!]!=1]</div>[/IF]

		</div>
	</div>


</div>
