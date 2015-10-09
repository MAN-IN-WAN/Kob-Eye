<!-- Block Viewed products -->
<div id="viewed-products_block_left" class="block products_block hidden-phone">
	<h3 class="title_block title_block_green">Déjà vus	</h3>
	<div class="block_content">
		<ul class="products clearfix">
			[STORPROC Boutique/Magasin/[!CurrentMagasin::Id!]/Categorie/*/Produit/Actif=1&Tarif>0&Coeur=1|Prod|0|1]
			<li class="clearfix last_item">
				<a href="[!Prod::getUrl()!]" title="En savoir plus sur	[!Prod::Nom!]" class="content_img">
					<img src="/[!Prod::Image!].mini.50x50.jpg" alt="[!Prod::Nom!]" />
				</a>
				<div class="text_desc">
					<p class="s_title_block">
						<a href="[!Prod::getUrl()!]" title="En savoir plus sur	[!Prod::Nom!]">[!Prod::Nom!]</a></p>
					<p>
					<a href="[!Prod::getUrl()!]" title="En savoir plus sur	[!Prod::Nom!]">[SUBSTR 20|...][!Prod::Description!][/SUBSTR]</a></p>
				</div>
			</li>
			[/STORPROC]
		</ul>
	</div>
</div>
