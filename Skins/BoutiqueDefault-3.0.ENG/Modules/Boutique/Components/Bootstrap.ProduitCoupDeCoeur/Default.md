<!-- MODULE Block specials -->
[OBJ Boutique|Magasin|Magasin]
[!Magasin:=[!Magasin::getCurrentMagasin()!]!]
[!REQ:=Boutique/Produit/Coeur=1&Actif=1!]
<div id="categoriesprodtabs" class="block products_block exclusive blockleocategoriestabs">
	<h3 class="title_block">[!TITRE!]</h3>
	<div class="block_content">
		<!-- Products list -->
		<div id="product_list" class="products_block view-grid">
			<div class="row">
				[STORPROC [!REQ!]|Prod|0|9|tmsEdit|DESC]
				[!LePrix:=[!Prod::getTarif!]!]
				[!Promo:=[!Prod::GetPromo!]!]
				<!-- Product item -->
				<div class="p-item col-md-4 product_block ajax_block_product [IF [!Pos!]=1]first_item[/IF][IF [!Pos!]=[!NbResult!]]last_item[/IF] [IF [!Utils::isPair([!Pos!])!]] alternate_item[ELSE] item[/IF]  ">
					<div class="list-products">
						<div class="product-container clearfix">
							<div class="center_block">
								<a href="[!Prod::getUrl()!]" class="product_img_link" title="[!Prod::Nom!]"> <img src="/[IF [!Prod::Image!]!=][!Prod::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF]" alt="[!Prod::Nom!]" class="img-responsive" style="max-height:200px;margin: auto;" /> <span class="new">__NEW__</span> </a>
								[IF [!Promo!]!=0]
								<span class="discount">__PROMO__</span>
								[/IF]

							</div>
							<div class="right_block">
								<h3 class="s_title_block"><a href="[!Prod::getUrl()!]" title="[!Prod::Nom!]">[!Prod::Nom!]</a></h3>

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

								<a class="btn btn-success" href="[!Prod::getUrl()!]" title="__ADD_TO_CART__">__ADD_TO_CART__</a>

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
				<!-- /Product item -->
				[IF [!Pos:%3!]=0&&[!Pos!]!=[!NbResult!]]
			</div>
			<div class="row">
				[/IF]
				[/STORPROC]

			</div>
		</div>
		<!-- /Products list -->
	</div>
</div>
<!-- /MODULE Block specials -->

