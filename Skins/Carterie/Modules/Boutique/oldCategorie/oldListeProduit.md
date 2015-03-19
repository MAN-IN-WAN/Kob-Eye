[IF [!Chemin!]=]
	[!Chemin:=[!Query!]!]
[/IF]
[STORPROC [!Chemin!]|Cat][/STORPROC]
<div class="row-fluid CentrageProduit">
	<div class="ListeCategorie">
		[STORPROC [!Chemin!]|Cat|||tmsCreate|ASC]
			[IF [!Cat::Image!]!=]
				<div class="SPAN12">
					<img src="/[!Cat::Image!].limit.732x260.jpg" alt="[!Cat::Nom!]" title="[!Cat::Nom!]" />
				</div>
			[/IF]
		[/STORPROC]
	</div>
	<div class="SelectionProduits">
		<div class="Titre">Nos Produits</div>
		[!Cpt:=0!]
		<div class="ListeProduitsCat row-fluid">
			[STORPROC [!Chemin!]/Produit/Actif=1|Prod|||tmsCreate|ASC]
				[!Cpt+=1!]
				[IF [!Cpt!]=4]</div><div class="ListeProduitsCat row-fluid">[/IF]
				<div class="span4">
					<div class="NomProduit"><h2>[!Prod::Nom!]</h2></div>
					<div class="AccrocheProduit">[!Prod::Acrroche!]</div>
					<a href="/[!Cat::getUrl()!]/Produit/[!Prod::Url!]" title="[!Utils::noHtml([!Prod::Description!])!]">
						<img src="/[!Prod::Image!].mini.215x174.jpg" />
					</a>
					<div class="Details">
						<p class="Tarif">[!Math::PriceV([!Prod::getTarif!])!] €</p>
					</div>
					<div class="DetailsSous">
						<a href="/[!Cat::getUrl()!]/Produit/[!Prod::Url!]" title="[!Prod::Nom!]" class="loupelien" >Voir le détail</a>
						<a href="/[!Cat::getUrl()!]/Produit/[!Prod::Url!]#Qte" title="Panier" class="panierliste">Mettre au panier</a>
					</div>
				</div>
			[/STORPROC]
		</div>
	</div>


</div>
