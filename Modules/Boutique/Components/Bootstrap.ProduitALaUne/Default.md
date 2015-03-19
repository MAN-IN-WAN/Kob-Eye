<!-- MODULE Home Featured Products -->
<div id="featured-products_block_center" class="block products_block clearfix ">
	<h3 class="title_block">[!TITRE!]</h3>
	<div class="block_content">
	
		<div style="min-height:140px;" class=" carousel slide" id="homefeatured" >
	
			<a class="carousel-control left" href="#homefeatured"   data-slide="prev">&lsaquo;</a>
			<a class="carousel-control right" href="#homefeatured"  data-slide="next">&rsaquo;</a>
			<div class="carousel-inner">
				[STORPROC Boutique/Produit/Coeur=1|Prod|0|8]
				<div class="item active">
					<div class="row-fluid">
						[LIMIT 0|4]
						<div class="p-item span3 product_block ajax_block_product  [IF [!Pos!]=1]first_item[/IF]">
							<div class="product-container">
								<a href=/[!Prod::getUrl()!]" title="iPod Nano" class="product_image">
								<img src="/[!Prod::Image!].mini.300x300.jpg" alt="iPod Nano" />
								[IF [!Prod::isNew()!]]
									<span class="new">Nouveau	</span>
								[/IF]
								</a>
								<h5 class="s_title_block">
									<a href="/[!Prod::getUrl()!]" title="[!Prod::Nom!]">[!Prod::Nom!]</a>
								</h5>
								<p class="price_container">
									<span class="price"> [!Prod::getTarif()!] </span>
								</p>
	
								<a class="exclusive ajax_add_to_cart_button" rel="ajax_id_product_9" href=/Skins/Paranature/index.php?controller=cart?qty=1&amp;id_product=9&amp;token=32f4b0bcca875efdf13c8b259373d259&amp;add" title="Add to cart">Add to cart</a>
								<div class="content-bottom">
									<div class="product_desc">
										<a href="/[!Prod::getUrl()!]" title="Plus	">[SUBSTR 0|25][!Prod::Descripion!][/SUBSTR]</a>
									</div>
									<a href="#" id="wishlist_button9"  title="Add to wishlist" class="btn-add-wishlist btn" onclick="LeoWishlistCart('wishlist_block_list', 'add', '9', $('#idCombination').val(), 1 ); return false;"><i class="icon-heart icon-white">&nbsp;</i></a>
									<a class="lnk_more btn" href="/[!Prod::getUrl()!]" title="Voir	"><i class="icon-file">&nbsp;</i></a>
								</div>
							</div>
						</div>
						[/LIMIT]
					</div>
				</div>
				[IF [!NbResult!]>4]
				<div class="item">
					<div class="row-fluid">
						[LIMIT 0|4]
						<div class="p-item span3 product_block ajax_block_product  [IF [!Pos!]=1]first_item[/IF]">
							<div class="product-container">
								<a href=/[!Prod::getUrl()!].mini.300x300.jpg" title="iPod Nano" class="product_image">
								<img src="/[!Prod::Image!]" alt="iPod Nano" />
								[IF [!Prod::isNew()!]]
									<span class="new">Nouveau	</span>
								[/IF]
								</a>
								<h5 class="s_title_block">
									<a href="/[!Prod::getUrl()!]" title="[!Prod::Nom!]">[!Prod::Nom!]</a>
								</h5>
								<p class="price_container">
									<span class="price"> [!Prod::getTarif()!] </span>
								</p>
	
								<a class="exclusive ajax_add_to_cart_button" rel="ajax_id_product_9" href=/Skins/Paranature/index.php?controller=cart?qty=1&amp;id_product=9&amp;token=32f4b0bcca875efdf13c8b259373d259&amp;add" title="Add to cart">Add to cart</a>
								<div class="content-bottom">
									<div class="product_desc">
										<a href="/[!Prod::getUrl()!]" title="Plus	">[SUBSTR 0|25][!Prod::Descripion!][/SUBSTR]</a>
									</div>
									<a href="#" id="wishlist_button9"  title="Add to wishlist" class="btn-add-wishlist btn" onclick="LeoWishlistCart('wishlist_block_list', 'add', '9', $('#idCombination').val(), 1 ); return false;"><i class="icon-heart icon-white">&nbsp;</i></a>
									<a class="lnk_more btn" href="/[!Prod::getUrl()!]" title="Voir	"><i class="icon-file">&nbsp;</i></a>
								</div>
							</div>
						</div>
						[/LIMIT]
					</div>
				</div>
				[/IF]
				[/STORPROC]
			</div>
		</div>
	</div>
</div>
<!-- /MODULE Home Featured Products -->
