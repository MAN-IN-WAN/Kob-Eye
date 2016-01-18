// Devise en cours
[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]

//PARAMETRES

[IF [!Chemin!]=][!Chemin:=[!Query!]!][/IF]
[INFO [!Chemin!]|I]

[IF [!I::ObjectType!]=Categorie]
	[!REQ:=[!Chemin!]/*/Produit/Actif=1&Tarif>0!]
[ELSE]
	[IF [!Type!]!=search]
		[!REQ:=[!Chemin!]/Produit/Actif=1&Tarif>0!]
	[ELSE]
        [!REQ:=[!Chemin!]&Actif=1&Tarif>0!]
        [!Chemin:=Boutique/Categorie!]
	[/IF]
[/IF]
[IF [!Page!]=][!Page:=1!][/IF]
[COUNT [!REQ!]|Nb]
[!NbParPage:=18!]
[!NbNumParPage:=3!]
[!NbPage:=[!Math::Floor([!Nb:/[!NbParPage!]!])!]!]
[IF [!NbPage!]!=[!Nb:/[!NbParPage!]!]][!NbPage+=1!][/IF]

[STORPROC [!Chemin!]|Cat|0|1]
<div class="contenttop row-fluid block">
    [IF [!Type!]!=search]
        <h1 class="title_block"> [IF [!Cat::NomLong!]][!Cat::NomLong!][ELSE][!Cat::Nom!][/IF] <span class="resumecat category-product-count"> / __THERE_IS__ [!Nb!] __PRODUCTS__. </span></h1>

        [IF [!C::Image!]||[!C::Description!]]
        <div class="content_scene_cat">
            <!-- Category image -->
            [IF [!C::Image!]]
            <div class="align_center">
                <img src="/[!C::Image!]" alt="[!C::Nom!]" title="[!C::Nom!]" id="categoryImage" />
            </div>
            [/IF]
            [IF [!C::Description!]]
            <div class="cat_desc">
                <p id="category_description_short">
                    [SUBSTR 200][!C::Description!][/SUBSTR]
                </p>
                <p id="category_description_full" style="display:none">
                    [!C::Description!]
                </p>
                <a href="#" onclick="$('#category_description_short').hide(); $('#category_description_full').show(); $(this).hide(); return false;" class="lnk_more">__MORE__</a>
            </div>
            [/IF]
        </div>
        [/IF]
    [ELSE]
        //Recherche
        <h1 class="title_block"> Recherche [!search!] <span class="resumecat category-product-count"> / __THERE_IS__ [!Nb!] __PRODUCTS__. </span>
        </h1>
    [/IF]
	<div class="products-list">
		<div class="content_sortPagiBar">
			<div class="row-fluid sortPagiBar">
				<div class="span3 hidden-phone productsview">
					<div class="inner">
						<span>__VIEW_AS__:&nbsp;&nbsp;&nbsp;</span>
						<div class="btn-group" id="productsview">
							<a href="#" rel="view-grid"><i class="icon-th active" ></i></a>
							<a href="#"  rel="view-list"><i class="icon-th-list"></i></a>
						</div>
					</div>
				</div>
				<div class="span6 hidden-phone">
					<div class="inner">

						<script type="text/javascript">
							//<![CDATA[
							$(document).ready(function() {
								$('.selectProductSort').change(function() {
									var requestSortProducts = '?';
									var splitData = $(this).val().split(':');
									document.location.href = requestSortProducts + ((requestSortProducts.indexOf('?') < 0) ? '?' : '&') + 'orderby=' + splitData[0] + '&orderway=' + splitData[1];
								});
							});
							//]]>
						</script>

						<form id="productsSortForm" action="?">
							<p class="select">
								<label for="selectPrductSort">__SORT_BY__</label>
								<select id="selectPrductSort" class="selectProductSort">
									<option value="position:asc" selected="selected">--</option>
									<option value="price:asc" >__PRICE_LOWER_FIRST__</option>
									<option value="price:desc" >__PRICE_HIGHEST_FIRST__</option>
									<option value="name:asc" >__PRODUCT_NAME_A_TO_Z__</option>
									<option value="name:desc" >__PRODUCT_NAME_Z_TO_A__</option>
									<option value="quantity:desc" >__IN_STOCK_FIRST__</option>
								</select>
							</p>
						</form>
						<!-- /Sort products -->

					</div>
				</div>

				<div class="span3">
					<div class="inner">
					<!--
						#TODO compare

						<script type="text/javascript">
							// <![CDATA[
							var min_item = '__PLEASE_SELECT_AT_LEAST_ONE_PRODUCT__';
							var max_item = "__YOU_CANNOT_ADD_MORE_THAN_5_PRODUCTS__";
							//]]>
						</script>

						<form method="post" action="http://demo4leotheme.com/prestashop/leo_beauty_store/index.php?controller=products-comparison" onsubmit="true">
							<p>
								<input type="submit" id="bt_compare" class="button" value="__COMPARE__" />
								<input type="hidden" name="compare_product_list" class="compare_product_list" value="" />
							</p>
						</form>
					-->
					</div>
				</div>
			</div>
		</div>

		<!-- Products list -->
		<div id="product_list" class="products_block view-grid">
			<div class="rows-fluid">
				<div class="row-fluid">
					[STORPROC [!REQ!]|Prod|[![!Page:-1!]:*[!NbParPage!]!]|[!NbParPage!]|Nom|ASC]
					[!LePrix:=[!Prod::getTarif!]!]
					[!Promo:=[!Prod::GetPromo!]!]
					<!-- Product item -->
					<div class="p-item span4 product_block ajax_block_product [IF [!Pos!]=1]first_item[/IF][IF [!Pos!]=[!NbResult!]]last_item[/IF] [IF [!Utils::isPair([!Pos!])!]] alternate_item[ELSE] item[/IF]  ">
						<div class="list-products">
							<div class="product-container clearfix">
								<div class="center_block">
									<a href="[!Prod::getUrl()!]" class="product_img_link" title="iPod Nano"> <img src="/[IF [!Prod::Image!]!=][!Prod::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].mini.180x200.jpg" alt=""  />
										<!--<span class="new">__NEW__</span> </a>-->
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

									<a class="btn btn-protector" href="[!Prod::getUrl()!]" title="__ADD_TO_CART__">__ADD_TO_CART__</a>

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
				<div class="row-fluid">
				[/IF]
					[/STORPROC]

				</div>
			</div>
		</div>
		<!-- /Products list -->

		<div class="content_sortPagiBar">
			<div class="row-fluid sortPagiBar">
				<div class="span9">
					<div class="inner">

						<div  id="pagination" class="pagination"> <!-- Start Paging --> 
							<ul>
								//<li><button class="active">Page 1 sur [!NbPage!] </button></li> 
								[IF [!Page!]>1]
									<li><a href="/[!Lien!]?search=[!search!]" class=""><span>&laquo;</span></a></li>
									<li><a href="[IF [!Page!]=2]/&search=[!search!][!Lien!][ELSE]?Page=[!Page:-1!]&search=[!search!][/IF]" class="">&lsaquo;</a>
									[IF [!Page!]>[!Math::Round([!NbNumParPage:/2!])!]]
										<li><a href="/[!Lien!]?search=[!search!]" class=""><span>1</span></a></li>
										<li><a href="#" class=""><span>...</span></a></li> 
									[/IF]
								[/IF]
								[!start:=1!]
								[IF [!Page!]>[!start:+[!NbNumParPage:/[!NbParPage!]!]!]][!start:=[!Math::Round([!Page:-[!NbNumParPage:/2!]!])!]!][/IF]
								[STORPROC [!NbPage:+1!]|P|[!start!]|[!NbNumParPage!]]
								<li class=" [IF [!P!]=[!Page!]]active[/IF]"><a href="[IF [!P!]!=1]?Page=[!P!][ELSE]/[!Lien!][/IF]&search=[!search!]" class="">[!P!]</a></li>
								[/STORPROC]
								[IF [!Page!]<[!NbPage!]]
									[IF [!Page:+[!NbNumParPage:/2!]!]<[!NbPage!]]
										<li><a href="#" class=""><span>...</span></a></li> 
										<li><a href="?Page=[!NbPage!]&search=[!search!]" class="">[!NbPage!]</a></li>
									[/IF]
									<li><a href="?Page=[!Page:+1!]&search=[!search!]" class=""><span>&rsaquo;</span></a></li>
									<li><a href="?Page=[!NbPage!]&search=[!search!]" class="">&raquo;</a></li>
								[/IF] 
							</ul>
						</div>	<!-- End Paging -->

					</div>
				</div>
				<div class="span3">
					<div class="inner">
						<!--
							#TODO Compare
						<script type="text/javascript">
							// <![CDATA[
							var min_item = '__PLEASE_SELECT_AT_LEAST_ONE_PRODUCT__';
							var max_item = "__YOU_CANNOT_ADD_MORE_THAN_5_PRODUCTS__";
							//]]>
						</script>

						<form method="post" action="http://demo4leotheme.com/prestashop/leo_beauty_store/index.php?controller=products-comparison" onsubmit="true">
							<p>
								<input type="submit" id="bt_compare" class="button" value="__COMPARE__" />
								<input type="hidden" name="compare_product_list" class="compare_product_list" value="" />
							</p>
						</form>
						-->
					</div>
				</div>
			</div>
		</div>
	</div>
	[STORPROC [!Query!]/Categorie|SubCat|0|100]
	<!-- Subcategories -->
	<!--<div id="subcategories">
		<h3>__SUBCATEGORIES__</h3>
		<div class="inline_list">
			<div class="row-fluid">
			[LIMIT 0|100]
				<div class="span3">
					<div class="category-container block">
						//<a href="/[!SubCat::getUrl()!]" title="[!SubCat::Nom!]" class="img title_block"> <img src="/[!SubCat::Image!].mini.142x162.jpg" alt=""/> </a>
						<a href="/[!SubCat::getUrl()!]" class="cat_name title_block">[IF [!SubCat::NomLong!]!=][!SubCat::NomLong!][ELSE][!SubCat::Nom!][/IF]</a>
					</div>
				</div>
			[/LIMIT]
			</div>
		</div>
		<br class="clear"/>
	</div>-->
	[/STORPROC]

</div>
<!-- end div block_home -->
[/STORPROC]
