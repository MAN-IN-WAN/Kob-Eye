[STORPROC [!Query!]|P]
		<div id="product-detail" class="block">
			<h3 class="title_block">[!P::Nom!]</h3>
			<div id="primary_block" class="row">
				<div>
					<!-- right infos-->
					<div id="pb-right-column" class="col-md-5">
						<div class="images-block">
							<!-- product img-->
							<div id="image-block">
								<a href="/[!P::Image!]" class="gallery" rel="gal"> <img src="/[!P::Image!]" class="img-responsive" alt="/[!P::Image!]" id="bigpic" /> </a>
							</div>
							<!-- thumbnails -->
							<div id="views_block" class="clearfix ">
                                <a href="/[!P::Image!]" rel="other-views" class="thickbox shown" title=""> <img id="thumb_0" src="/[!P::Image!].mini.58x58.jpg" alt="[!Utils::noHtml([!P::Description!])!]" /> </a>
                            [STORPROC Boutique/Produit/[!P::Id!]/Donnee/Type=Image|i]
                                <a href="/[!i::Fichier!]" rel="gal" class="gallery"> <img id="thumb_[!Pos!]" src="/[!i::Fichier!].mini.58x58.jpg" alt="[!Utils::noHtml([!P::Description!])!]" /> </a>
                            [/STORPROC]
							</div>
                            <script>
                                $('a.gallery').colorbox({rel:'gal'});
                            </script>
						</div>
						<p class="resetimg clear">
							<span id="wrapResetImages" style="display: none;"><img src="/Skins/Paranature/img/cancel_11x13.gif" alt="Cancel" width="11" height="13"/> <a id="resetImages" href="/[!Lien!]" onclick="$('span#wrapResetImages').hide('slow');return (false);">__DISPLAY_ALL_PICTURES__</a></span>
						</p>
					</div>

					<!-- left infos-->
					<div id="pb-left-column" class="col-md-7">
						<!-- usefull links-->
						<!-- <ul id="usefull_link_block">

							<li id="left_share_fb">
								<a href="http://www.facebook.com/sharer.php?u=[!Domaine!][!Lien!]" class="js-new-window">__SHARE_ON_FACEBOOK__</a>
							</li>

							<li id="favoriteproducts_block_extra_added">
								__REMOVE_THIS_PRODUCT_FROM_MY_FAVORITE_LIST__
							</li>
							<li id="favoriteproducts_block_extra_removed">
								__ADD_THIS_PRODUCT_TO_MY_FAVORITE_LIST__
							</li>
							<li class="print">
								<a href="javascript:print();">__PRINT__</a>
							</li>
						</ul>-->
						<!-- end usefull links-->

						<!-- description short -->
						<div id="short_description_block">
							<h3>__QUICK_OVERVIEW__</h3>
							<div id="short_description_content" class="rte align_justify">
                                [!P::Description!]
							</div>
							<p class="buttons_bottom_block">
								<a href="javascript:{}" class="button">__MORE_DETAILS__</a>
							</p>
						</div>
						<!-- end description short -->

						<!-- add to cart form-->
						<form id="FicheProduit"  action="" method="post">

							<!-- content prices -->
							<div class="content_prices clearfix row">
                                <div class="col-md-6" style="text-align: center">
                                    <!-- prices -->
                                    <div class="price">

                                        <p class="our_price_display">
                                            <span id="tarif">[!Math::PriceV([!P::getTarif!])!] [!CurrentDevise::Sigle!]</span>
                                            <!---->
                                        </p>

                                    </div>
                                    [!Promo:=[!Prod::GetPromo!]!]
                                    [IF [!Promo!]]
                                        [IF [!Promo::TypeVariation!]=1]
                                            <p id="reduction_percent">
                                                <span id="reduction_percent_display">- [!Promo::PrixVariation!]%</span>
                                            </p>
                                        [/IF]
                                        [IF [!Promo::TypeVariation!]=2]
                                            <p id="reduction_amount">
                                                <span id="reduction_amount_display">- [!Promo::PrixVariation!] [!De::Sigle!]</span>
                                            </p>
                                        [/IF]
                                        <p id="old_price">
                                            <span class="bold">
                                                <span id="old_price_display">[!Math::PriceV([!Prod::getTarifHorsPromo!])!][!De::Sigle!]</span>
                                            </span>
                                        </p>
                                    [/IF]
								//******************************
								// AFFICHAGE PANIER + QUANTITE
								//******************************
								[IF [!Prod::StockReference!]>0]
                                        <div class="buttons_bottom_block row">
										[IF [!Prod::TypeProduit!]=2]
                                              <div class="control-group">
                                                <!--<label class="control-label" for="Qte">Quantité</label>-->
                                                <div class="controls">
                                                    <div class="FichQuantite">
                                                        <input type="button" class="btn btn-default" value="-" onclick="CalculQte(-1);">
                                                        <input name="Qte" id="Qte" value="1" size="2" onchange="VerifieSelection();" class="input-small" />
                                                        <input type="button" class="btn btn-default" value="+" onclick="CalculQte(+1);">
                                                    </div>
                                                </div>
                                              </div>
										[/IF]
	    								</div>
    							[/IF]
                                </div>
                                <div class="col-md-6">
                                    [IF [!Prod::TypeProduit!]!=1&&[!Prod::StockReference!]>0]
                                    <input type="submit" name="Submit" value="__ADD_TO_CART__" class="btn btn-success btn-block" />
                                    [/IF]
                                </div>
							</div>
							<!-- content prices -->

							<!-- attributes -->
							<div class="product_attributes">
							[SWITCH [!Prod::TypeProduit!]|=]
								[CASE 1]
									//******************************
									// Cas produit reference unique 
									//******************************
									
								[/CASE]
								[CASE 2]
									//******************************
									// Cas produit reference declinées
									//******************************
									[IF [!Prod::StockReference!]>0]
										[!LaPos:=0!]
										[STORPROC Boutique/Produit/[!Prod::Id!]/Attribut|Att|||Ordre|ASC]
											<div class="BlocFichDeclinaisons">
												<label class="attribute_label" for="group_1">[IF [!Att::NomPublic!]=][!Att::Nom!][ELSE][!Att::NomPublic!][/IF] </label>
												<div class="attribute_list">
													[LIMIT 0|100]
														[SWITCH [!Att::TypeAttribut!]|=]
															[CASE 1]
																//Type attribut texte
																<select name="P[!Prod::Id!]A[!Att::Id!]" class="attribute_select AttributTexte CalculPrix" onchange="VerifieSelection();" >
																	<option value="-1">Sélectionnez une valeur</option>
																	[STORPROC Boutique/Attribut/[!Att::Id!]/Declinaison|Decli]
																		[COUNT Boutique/Produit/[!Prod::Id!]/Reference/Declinaison.DeclinaisonId([!Decli::Id!])&&Quantite>0&&Tarif>0|Rdec]
																		[IF [!Rdec!]>0]
																			[!LaPos+=1!]
																			<option value="[!Decli::Id!]"  >[IF [!Decli::NomPublic!]=][!Decli::Nom!][ELSE][!Decli::NomPublic!][/IF]</option>
																		[/IF]
																	[/STORPROC]
																</select>
															[/CASE]
															[CASE 2]
																[STORPROC Boutique/Attribut/[!Att::Id!]/Declinaison|Decli]
																	//Type attribut graphique
																	[COUNT Boutique/Produit/[!Prod::Id!]/Reference/Declinaison.DeclinaisonId([!Decli::Id!])&&Quantite>0&&Tarif>0|Rdec]
																	[IF [!Rdec!]>0]
																		[!LaPos+=1!]
																		<div class="AttributGraphique ">
																			<div class="AttributGraphiqueNom">[IF [!Decli::NomPublic!]=][!Decli::Nom!][ELSE][!Decli::NomPublic!][/IF]</div>
																			<div class="AttributGraphiqueImg">
																				<a class="mb" href="[!Domaine!]/[IF [!Decli::Image!]!=][!Decli::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].limit.560x533.jpg" style="margin:0;" title="[!Decli::NomPublic!]" ><img src="[!Domaine!]/[IF [!Decli::Image!]!=][!Decli::Image!].mini.53x49.jpg[ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg.mini.53x49.jpg[/IF]" /></a>
																			</div>
									
																			<div class="AttributGraphiqueChoix"><input type="radio" name="P[!Prod::Id!]A[!Att::Id!]"  value="[!Decli::Id!]"  id="A[!Att::Id!]D[!Decli::Id!]" class="CalculPrix" onchange="VerifieSelection();" />
									// on ne change plus l'image principal quand on clique sur une déclinaison
									//onchange="VerifieSelection();return apercu('[!Domaine!]/[!Decli::Image!].mini.295x281.jpg','[UTIL SANSCOTEESPACE][!Decli::NomPublic!][/UTIL]','[!Domaine!]/[!Decli::Image!]');
																			</div>
																		</div>
																	[/IF]
																[/STORPROC]
															[/CASE]
														[/SWITCH]
													[/LIMIT]
												</div>
											</div>
											
											[NORESULT]
												// Pas d'attribut donc on prend la référence directement
												[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|Re|0|1][/STORPROC]
												<input type="hidden" name="Reference" value="[!Re::Reference!]" >
												<input type="hidden" name="StockAvailable" value="1" >
												<input type="hidden" name="IdReference" value="[!Re::Id!]" >
											[/NORESULT]
										[/STORPROC]
									[/IF]
								[/CASE]
								[CASE 3]
									[IF [!Prod::StockReference!]>0]
										//******************************
										// Cas produit unique
										//******************************
										[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|Re|0|1][/STORPROC]
										<input type="hidden" name="Reference" value="[!Re::Reference!]" >
										<input type="hidden" name="IdReference" value="[!Re::Id!]" >
										<input type="hidden" name="StockAvailable" value="1" >
									[/IF]
								[/CASE]
							[/SWITCH]

								<!-- minimal quantity wanted -->
                                                                <!--
								<p id="minimal_quantity_wanted_p" style="display: none;">
									This product is not sold individually. You must select at least <b id="minimal_quantity_label">1</b> quantity for this product.
								</p>-->

								<!-- availability -->
								<!--<p id="availability_statut" style="display: none;">
									<span id="availability_label">Availability:</span>
									<span id="availability_value"> </span>
								</p>-->

								<!-- number of item in stock -->
								<!--<p id="pQuantityAvailable">
									<span id="quantityAvailable">114</span>
									<span  style="display: none;" id="quantityAvailableTxt">item in stock</span>
									<span  id="quantityAvailableTxtMultiple">items in stock</span>
								</p>-->

								<!-- Out of stock hook -->
                                                                <!--
								<p id="oosHook" style="display: none;">

								</p>
                    
								<p class="warning_inline" id="last_quantities" style="display: none" >
									Warning: Last items in stock!
								</p>-->
							</div>
							<!-- end attributes -->

							<!--<p class="buttons_bottom_block">
								<a href="#" id="wishlist_button" onclick="WishlistCart('wishlist_block_list', 'add', '5', $('#idCombination').val(), document.getElementById('quantity_wanted').value); return false;">&raquo; __ADD_TO_MY_WISHLIST__</a>
							</p>-->
						</form>



                    </div>
				</div>
			</div>
			[COUNT Boutique/Produit/[!Prod::Id!]/Donnee/Type!=Image|NbDo]
			[IF [!NbDo!]]
			<!-- description and features -->
            <div>
                <ul class="nav nav-tabs" role="tablist">
                    [STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Caracteristique+Type=Documentation+Type=Descriptif|CAR|0|10|Ordre|ASC]
                        <li role="presentation" [IF [!Pos!]=1]class="active"[/IF]>
							<a href="#idTab[!Pos!]" aria-controls="home" role="tab" data-toggle="tab">[!CAR::TypeCaracteristique!]</a>
//                            <a href="#idTab1">__DESCRIPTIF__</a>
                        </li>
                    [/STORPROC]
                    //[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Fichier+Type=Lien|CAR|0|1|Ordre|ASC]
                     //   <li role="presentation" >
                     //       <a href="#idTab2" aria-controls="home" role="tab" data-toggle="tab">__FICHIERS__</a>
                    //    </li>
                    //[/STORPROC]
                    //[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Video|CAR|0|1|Ordre|ASC]
                    //    <li role="presentation" >
                    //        <a href="#idTab3" aria-controls="home" role="tab" data-toggle="tab">__MEDIAS__</a>
                    //    </li>
                    //[/STORPROC]
                </ul>
                <div class="tab-content">
                    <!-- full description -->
                    [STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Caracteristique+Type=Documentation+Type=Descriptif|CAR|0|10|Ordre|ASC]
                        <div role="tabpanel" id="idTab[!Pos!]"  class="tab-pane [IF [!Pos!]=1]active[/IF]">
                            [!CAR::Valeur!]
                        </div>
                    [/STORPROC]
                    //[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Fichier+Type=Lien|CAR|0|1|Ordre|ASC]
                     //   <div role="tabpanel" id="idTab2"  class="tab-pane">
                    //        [LIMIT 0|100]
                    //        <a href="/[!CAR::Fichier!]">[!CAR::Valeur!]</a>
                    //        [/LIMIT]
                    //    </div>
                    //[/STORPROC]
                    //[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Video|CAR|0|1|Ordre|ASC]
                    //    <div role="tabpanel" id="idTab3"  class="tab-pane">
                    //        [LIMIT 0|100]
                    //        [!CAR::Fichier!]
                    //        [/LIMIT]
                    //    </div>
                    //[/STORPROC]
                    <!-- Customizable products -->

                </div>
            </div>
			[/IF]

		</div>

		<!-- MODULE Block specials -->
		[STORPROC Boutique/Categorie/Produit/[!P::Id!]|Cat|0|1]
			[STORPROC Boutique/Categorie/[!Cat::Id!]/Produit/Actif=1&Id!=[!P::Id!]|Prod]
		<div id="relatedproducts" class="block products_block exclusive blockleorelatedproducts">
			<h3 class="title_block">__RELATED_PRODUCTS__</h3>
			<div class="block_content">
				<div class=" carousel slide" id="leorelatedcarousel">

					<div class="button-carousel">
						<a class="carousel-control left" href="#leorelatedcarousel"   data-slide="prev">&lsaquo;</a>
						<a class="carousel-control right" href="#leorelatedcarousel"  data-slide="next">&rsaquo;</a>
					</div>
					<div class="carousel-inner">
					[ORDER Id|RANDOM]
						<div class="item active">
							<div class="row">
							[LIMIT 0|3]
                                                        [!LePrix:=[!Prod::getTarif!]!]
                                                        [!Promo:=[!Prod::GetPromo!]!]
                                                        <!-- Product item -->
                                                        <div class="p-item col-md-4 product_block ajax_block_product [IF [!Pos!]=1]first_item[/IF][IF [!Pos!]=[!NbResult!]]last_item[/IF] [IF [!Utils::isPair([!Pos!])!]] alternate_item[ELSE] item[/IF]  ">
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
                
                                                                                        <a class="button ajax_add_to_cart_btn btn-protector" rel="ajax_id_product_1" href="http://demo4leotheme.com/prestashop/leo_beauty_store/index.php?controller=cart&add=&id_product=1&token=c72340620966cb9ae8dcccbd3dd03a3b" title="__ADD_TO_CART__">__ADD_TO_CART__</a>
                
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
							[/LIMIT]
							</div>
						</div>
					[/ORDER]
					</div>
				</div>

			</div>
		</div>
		<!-- /MODULE Block specials -->
		<script>
			$(document).ready(function() {
				$('.blockleorelatedproducts .carousel').each(function() {
					$(this).carousel({
						pause : true,
						interval : false
					});
				});

			});
		</script>
			[/STORPROC]
		[/STORPROC]

	</div>
	<!-- end div block_home -->

[/STORPROC]
