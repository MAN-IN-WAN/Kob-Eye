[IF [!Chemin!]=]
	[!Chemin:=[!Query!]!]
[/IF]
[STORPROC [!Chemin!]|Cat][/STORPROC]
<div class="row CentrageProduit">
	<div class="ListeProduitsCat">
	[STORPROC [!Chemin!]/Produit/Actif=1|Prod|||tmsCreate|ASC]
		
		<div class="col-md-4">
			<a href="/[!Cat::getUrl()!]/Produit/[!Prod::Url!]" title="[!Utils::noHtml([!Prod::Description!])!]">
				<span class="ProduitCat" style="background-image: url('/[!Prod::Image!].mini.327x285.jpg');"></span>
			</a>
			<div class="Details">
				<div class="DetailsGauche">
					<h2>[!Prod::Nom!]ffffff</h2>
					<a href="/[!Cat::getUrl()!]/Produit/[!Prod::Url!]" title="">En savoir plus</a>
				</div>
				<p class="Tarif">[!Math::PriceV([!Prod::getTarif!])!] €</p>
			</div>
		</div>
	[/STORPROC]
	</div>
</div>
