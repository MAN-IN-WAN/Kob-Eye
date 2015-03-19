	<!-- Bottom Module Position -->
	<div id="bottom" class="row-fluid">
	[COUNT Boutique/Magasin/1/Categorie/*/Produit|N]
	[!N-=3!][!N:=[!Utils::random([!N!])!]!]
[STORPROC Boutique/Magasin/1/Categorie/*/Produit|P|[!N!]|3]
		<div class="custom  span4">
			<p><a href="/[!P::getUrl!]"><img src="/[!P::Image!].mini.350x265.jpg" alt="[!P::Titre!]"></a></p>
		</div>
[/STORPROC]
	</div>
