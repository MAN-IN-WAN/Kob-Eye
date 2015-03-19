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
					[STORPROC Boutique/Magasin/[!Magasin::Id!]/Categorie/*/Categorie/AlaUne=1&Display=1|Cat|0|5]
					<li>
						<a href="#cattab[!Cat::Id!]" data-toggle="tab">[!Cat::Nom!]</a>
					</li>
					[/STORPROC]
				</ul>
			</div>
			<div class="htabs-content">
				<div id="catProductsTabsContent" class="tab-content">

					//Liste des catégories
					[STORPROC Boutique/Magasin/[!Magasin::Id!]/Categorie/*/Categorie/AlaUne=1&Display=1|Cat]
					<div class="tab-pane" id="cattab[!Cat::Id!]">
						<div class=" carousel slide" id="carousel-[!Cat::Id!]">
							<a class="carousel-control left" href="#carousel-[!Cat::Id!]"   data-slide="prev">&lsaquo;</a>
							<a class="carousel-control right" href="#carousel-[!Cat::Id!]"  data-slide="next">&rsaquo;</a>
							<div class="carousel-inner">
								[STORPROC Boutique/Categorie/[!Cat::Id!]/Produit/Display=1|Prod|0|6]
								<div class="item active">
									<div class="row-fluid">
										[LIMIT 0|3]
										[!LePrix:=[!Prod::getTarif!]!]
										[!Promo:=[!Prod::GetPromo!]!]
										<div class="p-item span4 product_block ajax_block_product [IF [!Pos!]=1]first_item[/IF][IF [!Pos!]=[!NbResult!]]last_item[/IF] [IF [!Utils::isPair([!Pos!])!]] alternate_item[ELSE] item[/IF]  ">
											<div class="list-products">
												<div class="product-container clearfix">
													<div class="center_block">
														<a href="[!Prod::getUrl()!]" class="product_img_link" title="iPod Nano"> <img src="/[IF [!Prod::Image!]!=][!Prod::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].mini.180x200.jpg" alt=""  /> <span class="new">__NEW__</span> </a>
														[IF [!Promo!]!=0]
														<span class="discount">__PROMO__</span>
														[/IF]
					
													</div>
													<div class="right_block">
														<h3 class="s_title_block"><a href="[!Prod::getUrl()!]" title="iPod Nano">[!Prod::Nom!]</a></h3>
					
														<div class="price_container">
															</span>
															[IF [!Promo!]!=0]
															<div style="display:block;color:#fff;font-size:13px;position:absolute;right:32px;text-decoration:line-through;top:0;" id="tarifNonPromo">
																[!Math::PriceV([!Prod::getTarifHorsPromo!])!][!De::Sigle!]
															</div>
															[/IF]
															[IF [!Prod::MultiTarif!]=1]<span class="BlocProduitApartir">__A_PARTIR_DE__</span>[/IF] <span class="price" style="display: inline;">[!Math::PriceV([!LePrix!])!][!De::Sigle!]</span>
															<br />
															[IF [!Prod::CheckStock!]]
															<span class="availability">__AVAILABLE__</span>
															[/IF]
														</div>
														<span class="online_only"></span>
					
														<a class="button ajax_add_to_cart_button exclusive" rel="ajax_id_product_1" href="http://demo4leotheme.com/prestashop/leo_beauty_store/index.php?controller=cart&add=&id_product=1&token=c72340620966cb9ae8dcccbd3dd03a3b" title="__ADD_TO_CART__">__ADD_TO_CART__</a>
					
													</div>
					<!--
													<div class="content-bottom">
														<div class="product_desc">
															<a href="[!Prod::getUrl()!]" title="[!Prod::Description!]" >[SUBSTR 75|...][!Prod::Description!][/SUBSTR]</a>
														</div>
														<p class="compare">
															<input type="checkbox" class="comsparator" id="comparator_item_1" value="comparator_item_1"  />
															<label for="comparator_item_1">__SELECT_TO_COMPARE__</label>
														</p>
					
														<a href="#" id="wishlist_button1" title="Add to wishlist" class="btn-add-wishlist btn" onclick="LeoWishlistCart('wishlist_block_list', 'add', '1', $('#idCombination').val(), 1 ); return false;"><i class="icon-heart icon-white">&nbsp;</i></a>
					
														<a class="lnk_more btn" href="[!Prod::getUrl()!]" title="View"><i class="icon-file">&nbsp;</i></a>
													</div>
					-->
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
										[!LePrix:=[!Prod::getTarif!]!]
										[!Promo:=[!Prod::GetPromo!]!]
										<div class="p-item span4 product_block ajax_block_product [IF [!Pos!]=1]first_item[/IF][IF [!Pos!]=[!NbResult!]]last_item[/IF] [IF [!Utils::isPair([!Pos!])!]] alternate_item[ELSE] item[/IF]  ">
											<div class="list-products">
												<div class="product-container clearfix">
													<div class="center_block">
														<a href="[!Prod::getUrl()!]" class="product_img_link" title="iPod Nano"> <img src="/[IF [!Prod::Image!]!=][!Prod::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].mini.180x200.jpg" alt=""  /> <span class="new">__NEW__</span> </a>
														[IF [!Promo!]!=0]
														<span class="discount">__PROMO__</span>
														[/IF]
					
													</div>
													<div class="right_block">
														<h3 class="s_title_block"><a href="[!Prod::getUrl()!]" title="iPod Nano">[!Prod::Nom!]</a></h3>
					
														<div class="price_container">
															</span>
															[IF [!Promo!]!=0]
															<div style="display:block;color:#fff;font-size:13px;position:absolute;right:32px;text-decoration:line-through;top:0;" id="tarifNonPromo">
																[!Math::PriceV([!Prod::getTarifHorsPromo!])!][!De::Sigle!]
															</div>
															[/IF]
															[IF [!Prod::MultiTarif!]=1]<span class="BlocProduitApartir">__A_PARTIR_DE__</span>[/IF] <span class="price" style="display: inline;">[!Math::PriceV([!LePrix!])!][!De::Sigle!]</span>
															<br />
															[IF [!Prod::CheckStock!]]
															<span class="availability">__AVAILABLE__</span>
															[/IF]
														</div>
														<span class="online_only"></span>
					
														<a class="button ajax_add_to_cart_button exclusive" rel="ajax_id_product_1" href="http://demo4leotheme.com/prestashop/leo_beauty_store/index.php?controller=cart&add=&id_product=1&token=c72340620966cb9ae8dcccbd3dd03a3b" title="__ADD_TO_CART__">__ADD_TO_CART__</a>
					
													</div>
					<!--
													<div class="content-bottom">
														<div class="product_desc">
															<a href="[!Prod::getUrl()!]" title="[!Prod::Description!]" >[SUBSTR 75|...][!Prod::Description!][/SUBSTR]</a>
														</div>
														<p class="compare">
															<input type="checkbox" class="comsparator" id="comparator_item_1" value="comparator_item_1"  />
															<label for="comparator_item_1">__SELECT_TO_COMPARE__</label>
														</p>
					
														<a href="#" id="wishlist_button1" title="Add to wishlist" class="btn-add-wishlist btn" onclick="LeoWishlistCart('wishlist_block_list', 'add', '1', $('#idCombination').val(), 1 ); return false;"><i class="icon-heart icon-white">&nbsp;</i></a>
					
														<a class="lnk_more btn" href="[!Prod::getUrl()!]" title="View"><i class="icon-file">&nbsp;</i></a>
													</div>
					-->
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
