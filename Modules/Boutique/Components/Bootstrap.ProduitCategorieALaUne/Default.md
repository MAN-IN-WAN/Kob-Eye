<!-- MODULE Block specials -->
[OBJ Boutique|Magasin|Magasin]
[!Magasin:=[!Magasin::getCurrentMagasin()!]!]
<div id="categoriesprodtabs" class="block products_block exclusive blockleocategoriestabs">
	<h3 class="title_block">Catégorie onglets </h3>
	<div class="block_content">
		<div class="row-fluid">
			<div class="htabs-title">
				<ul id="catProductsTabs" class="htabs">
					//Liste des catégories
					[STORPROC [!Magasin::getTopCategories()!]|Cat|0|5]
					<li>
						<a href="#cattab[!Cat::Id!]" data-toggle="tab">[!Cat::Nom!]</a>
					</li>
					[/STORPROC]
				</ul>
			</div>
			<div class="htabs-content">
				<div id="catProductsTabsContent" class="tab-content">

					//Liste des catégories
					[STORPROC Boutique/Magasin/[!Magasin::Id!]/Categorie|Cat]
					<div class="tab-pane" id="cattab[!Cat::Id!]">
						<div class=" carousel slide" id="carousel-[!Cat::Id!]">
							<a class="carousel-control left" href="#carousel-[!Cat::Id!]"   data-slide="prev">&lsaquo;</a>
							<a class="carousel-control right" href="#carousel-[!Cat::Id!]"  data-slide="next">&rsaquo;</a>
							<div class="carousel-inner">
								[STORPROC Boutique/Categorie/[!Cat::Id!]/Produit|Prod|0|6]
								<div class="item active">
									<div class="row-fluid">
										[LIMIT 0|3]
										<div class="p-item span4 product_block ajax_block_product  [IF [!Pos!]=1]first_item[/IF]">
											<div class="product-container">
												<a href=/[!Prod::getUrl()!]" title="iPod Nano" class="product_image">
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
								[IF [!NbResult!]>3]
								<div class="item">
									<div class="row-fluid">
										[LIMIT 3|3]
										<div class="p-item span4 product_block ajax_block_product [IF [!Pos!]=1]first_item[/IF]">
											<div class="product-container">
												<a href=/[!Prod::getUrl()!]" title="iPod Nano" class="product_image">
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
					[/STORPROC]
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /MODULE Block specials -->

<script>
	$(document).ready(function() {
		$('.carousel').each(function() {
			$(this).carousel({
				pause : true,
				interval : false
			});
		});
		$(".blockleocategoriestabs").each(function() {
			$(".htabs li", this).first().addClass("active");
			$(".tab-content .tab-pane", this).first().addClass("active");
		});
	});
</script>
