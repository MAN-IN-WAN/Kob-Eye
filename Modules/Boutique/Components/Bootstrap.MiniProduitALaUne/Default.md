<div id="mycarouselHolder" align="center" class="block">
	<div class="row-fluid">
		<div class=" jcarousel-wrap">
			<div id="wrap">
				<ul id="lofjcarousel" class="jcarousel-skin-tango">
				[OBJ Boutique|Magasin|Mag]
				[!Mag:=[!Mag::getCurrentMagasin()!]!]
				[STORPROC Boutique/Magasin/[!Mag::Id!]/Categorie/Actif=1/Produit/Coeur=1|Prod|0|20|Ordre|ASC]
					<li class="lof-item">
						<a href="/[!Prod::getUrl()!]">
							<img src="/[!Prod::Image!].mini.143x94.jpg" alt="[!Prod::Titre!]" vspace="0" border="0" />
						</a>
					</li>
				[/STORPROC]
				</ul>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#lofjcarousel').jcarousel({
			auto : 0,
			animation : 2000,
			wrap : "circular",
			scroll : 1,
			buttonNextHTML : '<div></div>',
			buttonPrevHTML : '<div></div>'
		});
	});
</script>