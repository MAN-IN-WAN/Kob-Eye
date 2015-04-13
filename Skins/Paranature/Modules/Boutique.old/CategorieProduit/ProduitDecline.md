[STORPROC [!Query!]|P]
	<div class="contenttop row-fluid">
		<script type="text/javascript">
			// <![CDATA[

			// PrestaShop internal settings
			/*var currencySign = '$';
			var currencyRate = '1';
			var currencyFormat = '1';
			var currencyBlank = '0';
			var taxRate = 0;*/
			var jqZoomEnabled = true;

			//JS Hook
			/*var oosHookJsCodeFunctions = new Array();

			// Parameters
			var id_product = '1';
			var productHasAttributes = true;
			var quantitiesDisplayAllowed = true;
			var quantityAvailable = 154;
			var allowBuyWhenOutOfStock = false;
			var availableNowValue = 'In stock';
			var availableLaterValue = '';
			var productPriceTaxExcluded = 124.58 - 0.000000;
			var reduction_percent = 5;
			var reduction_price = 0;
			var specific_price = 124.580000;
			var product_specific_price = new Array();
				product_specific_price['id_specific_price'] = '1';
				product_specific_price['id_specific_price_rule'] = '0';
				product_specific_price['id_cart'] = '0';
				product_specific_price['id_product'] = '1';
				product_specific_price['id_shop'] = '0';
				product_specific_price['id_shop_group'] = '0';
				product_specific_price['id_currency'] = '0';
				product_specific_price['id_country'] = '0';
				product_specific_price['id_group'] = '0';
				product_specific_price['id_customer'] = '0';
				product_specific_price['id_product_attribute'] = '0';
				product_specific_price['price'] = '124.580000';
				product_specific_price['from_quantity'] = '1';
				product_specific_price['reduction'] = '0.050000';
				product_specific_price['reduction_type'] = 'percentage';
				product_specific_price['from'] = '0000-00-00 00:00:00';
				product_specific_price['to'] = '0000-00-00 00:00:00';
				product_specific_price['score'] = '32';
			var specific_currency = false;
			var group_reduction = '1';
			var default_eco_tax = 0.000000;
			var ecotaxTax_rate = 0;
			var currentDate = '2013-09-07 05:53:11';
			var maxQuantityToAllowDisplayOfLastQuantityMessage = 3;
			var noTaxForThisProduct = true;
			var displayPrice = 0;
			var productReference = '';
			var productAvailableForOrder = '1';
			var productShowPrice = '1';
			var productUnitPriceRatio = '0.000000';
			var idDefaultImage = 15;
			var stock_management = 1;
					
			var productPriceWithoutReduction = '166.38602';
			var productPrice = '158.07';
			// Customizable field
			var img_ps_dir = 'http://demo4leotheme.com/prestashop/leo_beauty_store/img/';
			var customizationFields = new Array();
			customizationFields[0] = new Array();
			customizationFields[0][0] = 'img0';
			customizationFields[0][1] = 0;

			// Images
			var img_prod_dir = 'http://demo4leotheme.com/prestashop/leo_beauty_store/img/p/';
			var combinationImages = new Array();

			combinationImages[0] = new Array();
			combinationImages[0][0] = 0;

			combinationImages[0] = new Array();
			combinationImages[0][0] = 7;
			combinationImages[0][1] = 8;
			combinationImages[0][2] = 9;
			combinationImages[0][3] = 10;
			combinationImages[0][4] = 11;
			combinationImages[0][5] = 12;
			combinationImages[0][6] = 32;

			// Translations
			var doesntExist = 'This combination does not exist for this product. Please choose another.';
			var doesntExistNoMore = 'This product is no longer in stock';
			var doesntExistNoMoreBut = 'with those attributes but is available with others';
			var uploading_in_progress = 'Uploading in progress, please wait...';
			var fieldRequired = 'Please fill in all required fields, then save the customization.';
			// Combinations
			var specific_price_combination = new Array();
			specific_price_combination['reduction_percent'] = 0;
			specific_price_combination['reduction_price'] = 0;
			specific_price_combination['price'] = 0;
			specific_price_combination['reduction_type'] = '';
			addCombination(9, new Array('15'), 34, 0, 0, -1, '', 0.00, 1, '', specific_price_combination);
			var specific_price_combination = new Array();
			specific_price_combination['reduction_percent'] = 0;
			specific_price_combination['reduction_price'] = 0;
			specific_price_combination['price'] = 0;
			specific_price_combination['reduction_type'] = '';
			addCombination(10, new Array('16'), 40, 75.250836, 0, -1, '', 0.00, 1, '', specific_price_combination);
			var specific_price_combination = new Array();
			specific_price_combination['reduction_percent'] = 0;
			specific_price_combination['reduction_price'] = 0;
			specific_price_combination['price'] = 0;
			specific_price_combination['reduction_type'] = '';
			addCombination(11, new Array('17'), 40, 150.501672, 0, -1, '', 0.00, 1, '', specific_price_combination);

			// Combinations attributes informations
			var attributesCombinations = new Array();
			tabInfos = new Array();
			tabInfos['id_attribute'] = '15';
			tabInfos['attribute'] = '8gb';
			tabInfos['group'] = 'disk_space';
			tabInfos['id_attribute_group'] = '1';
			attributesCombinations.push(tabInfos);
			tabInfos = new Array();
			tabInfos['id_attribute'] = '16';
			tabInfos['attribute'] = '16gb';
			tabInfos['group'] = 'disk_space';
			tabInfos['id_attribute_group'] = '1';
			attributesCombinations.push(tabInfos);
			tabInfos = new Array();
			tabInfos['id_attribute'] = '17';
			tabInfos['attribute'] = '32gb';
			tabInfos['group'] = 'disk_space';
			tabInfos['id_attribute_group'] = '1';
			attributesCombinations.push(tabInfos);*/
			//]]>
		</script>
		<div id="product-detail" class="block">
			<h3 class="title_block">[!P::Nom!]</h3>
			<div id="primary_block" class="row-fluid">
				<div>
					<!-- right infos-->
					<div id="pb-right-column" class="span5">
						<div class="images-block">
							<!-- product img-->
							<div id="image-block">
								<span id="view_full_size"> <img src="/[!P::Image!].mini.280x320.jpg" class="jqzoom" alt="/[!P::Image!]" id="bigpic" /> <span class="span_link">__VIEW_FULL_SIZE__</span> </span>
							</div>
							<!-- thumbnails -->
							<div id="views_block" class="clearfix ">
								<div id="thumbs_list">
									<ul id="thumbs_list_frame">
										<li id="thumbnail_7">
											<a href="/[!P::Image!]" rel="other-views" class="thickbox shown" title=""> <img id="thumb_0" src="/[!P::Image!].mini.58x58.jpg" alt="[!Utils::noHtml([!P::Description!])!]" /> </a>
										</li>
										[STORPROC Boutique/Produit/[!P::Id!]/Donnee/Type=Image|i]
										<li id="thumbnail_7">
											<a href="/[!i::Fichier!]" rel="other-views" class="thickbox shown" title=""> <img id="thumb_[!Pos!]" src="/[!i::Fichier!].mini.58x58.jpg" alt="[!Utils::noHtml([!P::Description!])!]" /> </a>
										</li>
										[/STORPROC]
									</ul>
								</div>
								<div class="scroll_lr">
									<span class="view_scroll_spacer"><a id="view_scroll_left" class="hidden" title="Other views" href="javascript:{}">__PREVIOUS__</a></span><a id="view_scroll_right" title="Other views" href="javascript:{}">__NEXT__</a>
								</div>
							</div>
						</div>
						<p class="resetimg clear">
							<span id="wrapResetImages" style="display: none;"><img src="/Skins/Paranature/img/cancel_11x13.gif" alt="Cancel" width="11" height="13"/> <a id="resetImages" href="/[!Lien!]" onclick="$('span#wrapResetImages').hide('slow');return (false);">__DISPLAY_ALL_PICTURES__</a></span>
						</p>
					</div>

					<!-- left infos-->
					<div id="pb-left-column" class="span7">
						<!-- usefull links-->
						<ul id="usefull_link_block">

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
						</ul>
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
						<form id="buy_block"  action="http://demo4leotheme.com/prestashop/leo_beauty_store/index.php?controller=cart" method="post">

							<!-- hidden datas -->
							<p class="hidden">
								<input type="hidden" name="token" value="c72340620966cb9ae8dcccbd3dd03a3b" />
								<input type="hidden" name="id_product" value="5" id="product_page_product_id" />
								<input type="hidden" name="add" value="1" />
								<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
							</p>
							<!-- content prices -->
							<div class="content_prices clearfix">
								<!-- prices -->

								<div class="price">

									<p class="our_price_display">
										<span id="our_price_display">[!Math::PriceV([!P::getTarif!])!] [!CurrentDevise::Sigle!]</span>
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
											<span id="reduction_amount_display">- [!Promo::PrixVariation!] [!CurrentDevise::Sigle!]</span>
										</p>
									[/IF]
									<p id="old_price">
										<span class="bold">
											<span id="old_price_display">[!Math::PriceV([!Prod::getTarifHorsPromo!])!][!CurrentDevise::Sigle!]</span>
										</span>
									</p>
								[/IF]
								//******************************
								// AFFICHAGE PANIER + QUANTITE
								//******************************
								[IF [!Prod::StockReference!]>0]
								<p id="add_to_cart" class="buttons_bottom_block">
										[IF [!Prod::TypeProduit!]=2]
											<div class="GestionQuantite"  >
												<div class="FichLibelle LibQte" >Quantité</div>
												<div class="FichQuantite">
													<input name="Qte" id="Qte" value="1" size="2" onchange="VerifieSelection();" class="input-small" />
												</div>
												<div class="LesBoutons">
													<input type="button" class="InputBtnMoins" value="-" onclick="CalculQte(-1);">
													<input type="button" class="InputBtnPlus" value="+" onclick="CalculQte(+1);">
												</div>
											</div>
										[/IF]
										[IF [!Prod::TypeProduit!]!=1&&[!Prod::StockReference!]>0]
											<input type="submit" name="Submit" value="__ADD_TO_CART__" class="exclusive" />
										[/IF]
									</div>
								</p>
								[/IF]
								<div class="clear"></div>
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
												<div class="BlocFichDeclinaisonsLibelle">[IF [!Att::NomPublic!]=][!Att::Nom!][ELSE][!Att::NomPublic!][/IF] </div>
												<div class="BlocFichDeclinaisonsLibelle">
													[LIMIT 0|100]
														[SWITCH [!Att::TypeAttribut!]|=]
															[CASE 1]
																//Type attribut texte
																<select name="P[!Prod::Id!]A[!Att::Id!]" class="AttributTexte CalculPrix" onchange="VerifieSelection();" >
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
								<!-- attributes -->
								<div id="attributes">
									<fieldset class="attribute_fieldset">
										<label class="attribute_label" for="group_1">Disk space :</label>
										<div class="attribute_list">
											<select name="group_1" id="group_1" class="attribute_select" onchange="findCombination();getProductAttribute();$('#wrapResetImages').show('slow');;">
												<option value="15" selected="selected" title="8GB">8GB</option>
												<option value="16" title="16GB">16GB</option>
												<option value="17" title="32GB">32GB</option>
											</select>
										</div>
									</fieldset>
								</div>
								<p id="product_reference" style="display: none;">
									<label for="product_reference">Reference: </label>
									<span class="editable"></span>
								</p>

								<!-- quantity wanted -->
								<p id="quantity_wanted_p">
									<label>Quantity:</label>
									<input type="text" name="qty" id="quantity_wanted" class="text" value="1" size="2" maxlength="3"  />
								</p>

								<!-- minimal quantity wanted -->
								<p id="minimal_quantity_wanted_p" style="display: none;">
									This product is not sold individually. You must select at least <b id="minimal_quantity_label">1</b> quantity for this product.
								</p>

								<!-- availability -->
								<p id="availability_statut" style="display: none;">
									<span id="availability_label">Availability:</span>
									<span id="availability_value"> </span>
								</p>

								<!-- number of item in stock -->
								<p id="pQuantityAvailable">
									<span id="quantityAvailable">114</span>
									<span  style="display: none;" id="quantityAvailableTxt">item in stock</span>
									<span  id="quantityAvailableTxtMultiple">items in stock</span>
								</p>

								<!-- Out of stock hook -->
								<p id="oosHook" style="display: none;">

								</p>

								<p class="warning_inline" id="last_quantities" style="display: none" >
									Warning: Last items in stock!
								</p>
							</div>
							<!-- end attributes -->

							<p class="buttons_bottom_block">
								<a href="#" id="wishlist_button" onclick="WishlistCart('wishlist_block_list', 'add', '5', $('#idCombination').val(), document.getElementById('quantity_wanted').value); return false;">&raquo; __ADD_TO_MY_WISHLIST__</a>
							</p>

						</form>

					</div>
				</div>
			</div>
			[COUNT Boutique/Produit/[!Prod::Id!]/Donnee/Type!=Image|NbDo]
			[IF [!NbDo!]]
			<!-- description and features -->
			<div id="more_info_block" class="clear row-fluid">
				<div>
					<ul id="more_info_tabs" class="idTabs idTabsShort clearfix">
						[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Caracteristique+Type=Documentation+Type=Descriptif|CAR|0|1|Ordre|ASC]
							<li>
								<a id="more_info_tab_more_info" href="#idTab1">__DESCRIPTIF__</a>
							</li>
						[/STORPROC]
						[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Fichier+Type=Lien|CAR|0|1|Ordre|ASC]
							<li>
								<a id="more_info_tab_more_info" href="#idTab2">__FICHIERS__</a>
							</li>
						[/STORPROC]
						[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Video|CAR|0|1|Ordre|ASC]
							<li>
								<a id="more_info_tab_more_info" href="#idTab3">__MEDIAS__</a>
							</li>
						[/STORPROC]
					</ul>
					<div id="more_info_sheets" class="sheets align_justify">
						<!-- full description -->
						[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Caracteristique+Type=Documentation+Type=Descriptif|CAR|0|1|Ordre|ASC]
							<div id="idTab1" class="rte">
								[LIMIT 0|100]
								[!CAR::Valeur!]
								[/LIMIT]
							</div>
						[/STORPROC]
						[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Fichier+Type=Lien|CAR|0|1|Ordre|ASC]
							<div id="idTab2" class="rte">
								[LIMIT 0|100]
								<a href="/[!CAR::Fichier!]">[!CAR::Valeur!]</a>
								[/LIMIT]
							</div>
						[/STORPROC]
						[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Video|CAR|0|1|Ordre|ASC]
							<div id="idTab3" class="rte">
								[LIMIT 0|100]
								[!CAR::Fichier!]
								[/LIMIT]
							</div>
						[/STORPROC]
						<!-- Customizable products -->

					</div>
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
							<div class="row-fluid">
							[LIMIT 0|3]
								<div class="p-item span4 product_block ajax_block_product [IF [!Pos!]=1]first_item p-item[/IF]  [IF [!Pos!]>1&&[!Pos!]<[!NbResult!]]alternate_item[/IF]  [IF [!Pos!]=[!NbResult!]]last_item p-item[/IF]   ">
									<div class="product-container">
										<a href="/[!Prod::getUrl()!]" title="[!Prod::Nom!]" class="product_image"><img src="/[!Prod::Image!]" alt="[!Prod::Nom!]" />
											<span class="new">New</span></a>
										<h5 class="s_title_block"><a href="/[!Prod::getUrl()!]" title="[!Prod::Nom!]">[!Prod::Nom!]</a></h5>

										<div>

											<p class="price_container">
												<span class="price">[!Prod::Tarif!]</span>
											</p>
											<a class="exclusive ajax_add_to_cart_button" rel="ajax_id_product_1" href="http://demo4leotheme.com/prestashop/leo_beauty_store/index.php?controller=cart?qty=1&amp;id_product=1&amp;token=c72340620966cb9ae8dcccbd3dd03a3b&amp;add" title="__ADD_TO_CART__">__ADD_TO_CART__</a>
										</div>

										<div class="content-bottom">
											<div class="product_desc">
												<a href="/[!Prod::getUrl()!]" title="More">[!Prod::Description!]</a>
											</div>
											<a href="#" id="wishlist_button1"  title="Add to wishlist" class="btn-add-wishlist btn" onclick="LeoWishlistCart('wishlist_block_list', 'add', '1', $('#idCombination').val(), 1 ); return false;"><i class="icon-heart icon-white">&nbsp;</i></a>
											<a class="lnk_more btn" href="/[!Prod::getUrl()!]" title="View"><i class="icon-file">&nbsp;</i></a>
										</div>
									</div>
								</div>
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
