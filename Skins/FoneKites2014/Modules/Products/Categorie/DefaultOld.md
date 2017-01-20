[COUNT [!Query!]|NbCat]
[IF [!NbCat!]>1]
	[!Req:=[!Query!]/*!]
	ALL CATEGORIE
[ELSE]
	UNE CATEGORIE
	[!Req:=[!Query!]!]
[/IF]


<div class="featured">
	<div class="container" id="lesproduits">
		<h1>Products</h1>
		<div class="col-lg-6 col-sm-6 col-xs-12">
			<div class="produits">
				<a href="/[!Lien!]/Produit/" >
					<img class="img-responsive" src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/B7-green_white_2014.jpg">
					<div class="prod-1-50">
						<h2>Kite</h2>
						<h3>Bandit</h3>
					</div>
				</a>
			</div>
		</div>
		<div class="col-lg-3-3 col-sm-4 col-xs-12">
			<div class="produits">
				<img class="img-responsive"  src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/mitu-monteirp-top-bottom-rvb-100.jpg">
				<div class="prod-2-25">
					<h2>SURF 6.0</h2>
					<h3>MITU MONTEIRO</h3>
				</div>
			</div>
		</div>
		<div class="col-lg-3-3 col-sm-4 col-xs-12">
			<div class="col-lg-3-4 col-sm-12">
				<div class="produits-min-top">
					<img class="img-responsive" src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/products/Focus_FINS_2013.png">
					<div class="prod-3-25-1">
						<h2>ACCESSORIES</h2>
						<h3>TRAX HRD<br>SERIES</h3>
					</div>
				</div>
			</div>
			<div class="col-lg-3-4-1 col-sm-12">
				<div class="produits-min-bot">
					<img class="img-responsive" src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/products/Focus_FINS_2013.png">
					<div class="prod-3-25-1">
						<h2>ACCESSORIES</h2>
						<h3>TRAX HRD<br>SERIES</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3-3 col-sm-4 col-xs-12">
			<div class="produits">
				<img class="img-responsive"  src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/mitu-monteirp-top-bottom-rvb-100.jpg">
				<div class="prod-2-25">
					<h2>SURF 6.0</h2>
					<h3>MITU MONTEIRO</h3>
				</div>
			</div>
		</div>
		<div class="col-lg-3-3 col-sm-4 col-xs-12">
			<div class="produits">
				<img class="img-responsive" src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/TRAX-carbon2014-top-bottom-rvb-10.jpg">
				<div class="prod-3-25">
					<h2>TWIN-TIP</h2>
					<h3>TRAX HRD<br>SERIES</h3>
				</div>
			</div>
		</div>
		<div class="col-lg-6 col-sm-6 col-xs-12">
			<div class="produits">
				<img class="img-responsive" src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/B7-green_white_2014.jpg">
				<div class="prod-1-50">
					<h2>Kite</h2>
					<h3>Bandit</h3>
				</div>
			</div>
		</div>
		<div class="col-lg-3-3 col-sm-4 col-xs-12">
			<div class="produits">
				<a href=""><img class="img-responsive"  src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/mitu-monteirp-top-bottom-rvb-100.jpg"></a>
				<div class="prod-2-25">
					<h2>SURF 6.0</h2>
					<h3>MITU MONTEIRO</h3>
				</div>
			</div>	
		</div>
		<div class="col-lg-3-3 col-sm-4 col-xs-12">
			<div class="col-lg-3-4 col-sm-12">
				<div class="produits-min-top">
					<a href=""><img class="img-responsive" src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/products/Focus_FINS_2013.png"></a>
					<div class="prod-3-25-1">
						<h2>ACCESSORIES</h2>
						<h3>TRAX HRD<br>	SERIES</h3>
					</div>
				</div>
			</div>
			<div class="col-lg-3-4-1 col-sm-12">
				<div class="produits-min-bot">
					<a href=""><img class="img-responsive" src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/products/Focus_FINS_2013.png"></a>
					<div class="prod-3-25-1">
						<h2>ACCESSORIES</h2>
						<h3>TRAX HRD<br>SERIES</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3-3 col-sm-4 col-xs-12">
			<div class="produits">
				<a href=""><img class="img-responsive" src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/TRAX-carbon2014-top-bottom-rvb-10.jpg"></a>
				<div class="prod-3-25">
					<h2>TWIN-TIP</h2>
					<h3>TRAX HRD<br>SERIES</h3>
				</div>
			</div>
		</div>
		<div class="col-lg-3-3 col-sm-4 col-xs-12">
			<div class="produits">
				<a href=""><img class="img-responsive"  src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/mitu-monteirp-top-bottom-rvb-100.jpg"></a>
				<div class="prod-2-25">
					<h2>SURF 6.0</h2>
					<h3>MITU MONTEIRO</h3>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$("#lesproduits").masonry({ "columnWidth": 239, "itemSelector": ".item-menu" });
	}
</script>