[STORPROC [!Query!]|P|0|1]
	[IF [!P::Display!]!=1]
		[HEADER 404][/HEADER]
	[/IF]
[/STORPROC]
[!lelienS:=!][!lelienP:=!]
[!NomProdP:=!][!NomProdS:=!]
[STORPROC Products/Categorie/Produit/[!P::Id!]|CatP|0|1][/STORPROC]

[STORPROC Products/Categorie/[!CatP::Id!]/Produit/Ordre>[!P::Ordre!]&Display=1|ProdS|0|1|Ordre|ASC]
	[!lelienS:=[!Systeme::CurrentMenu::Url!]/[!CatP::Url!]/Produit/[!ProdS::Url!]!]
	[!NomProdS:=[!ProdS::Nom!]!]
	[NORESULT]
		[STORPROC Products/Categorie/[!CatP::Id!]/Produit/Display=1|ProdS|0|1|Ordre|ASC]
			[!lelienS:=[!Systeme::CurrentMenu::Url!]/[!CatP::Url!]/Produit/[!ProdS::Url!]!]
			[!NomProdS:=[!ProdS::Nom!]!]
		[/STORPROC]
	[/NORESULT]
[/STORPROC]
[STORPROC Products/Categorie/[!CatP::Id!]/Produit/Ordre<[!P::Ordre!]&Display=1|ProdP|0|1|Ordre|DESC]
	[!lelienP:=[!Systeme::CurrentMenu::Url!]/[!CatP::Url!]/Produit/[!ProdP::Url!]!]
	[!NomProdP:=[!ProdP::Nom!]!]
	[NORESULT]
		[STORPROC Products/Categorie/[!CatP::Id!]/Produit/Display=1|ProdP|0|1|Ordre|DESC]
			[!lelienP:=[!Systeme::CurrentMenu::Url!]/[!CatP::Url!]/Produit/[!ProdP::Url!]!]
			[!NomProdP:=[!ProdP::Nom!]!]
		[/STORPROC]
	[/NORESULT]
[/STORPROC]

<div class="titre-product gris-clair">
	<div class="container title-product nopadding-right nopadding-left">
		<div class="row">
			<div class="col-lg-10 col-xs-6">
				<h1 class="title_prod">[!P::Nom!]<span class="title">&nbsp;[!P::SousTitre!]</span></h1>
			</div>
			<div class="col-lg-2 col-xs-6">
				<div class="nav-product">
					<div class="nav-product-btn">
						<a class="left" href="/[!lelienP!]" title="[!NomProdP!]"  onmouseover='$("#Nom-P").css("display","block");' onmouseout='$("#Nom-P").css("display","none");' >
							<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-left.png" class="img-responsive" alt="Fone"/>
						</a>
					</div>
					<div class="nav-product-btn">
						<a class="right" href="/[!lelienS!]" title="[!NomProdS!]"  onmouseover='$("#Nom-S").css("display","block");' onmouseout='$("#Nom-S").css("display","none");' >
							<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-right.png" class="img-responsive" alt="Fone"/>
						</a>
					</div>
				</div>
				
			</div>
		</div>
		<div class="row">
			<div class="col-lg-10 col-xs-8">
				[IF [!P::Chapo!]!=]<div class="caract">[!P::Chapo!]</div>[/IF]
			</div>
			<div class="col-lg-2 col-xs-4" >
				<div class="Nom-Navigation" id="Nom-P"  style="display:none">[!ProdP::Nom!]</div>
				<div class="Nom-Navigation" id="Nom-S"  style="display:none" >[!ProdS::Nom!]</div>
			</div>

		</div>
	
	</div>
</div>
<div class="featured">
	<div class="container nopadding-right nopadding-left">
		<div class="row">
			<div id="big-photo" class=" col-lg-7 colborder big-photo">
			[STORPROC [!Query!]/Illustration|Pill|0|1|Ordre|ASC]
				<a class="switch-3d-button" [IF [!Pill::3D!]=]style="visibility:hidden;"[/IF]></a>
				<div class="type3d" style="display:none;">[!Pill::getIframe()!]</div>
				<span data-src="/[!Pill::Image!]" class="PhotoZoom">
					<img src="/[!Pill::Image!]" class="img-responsive" alt="[!P::Nom!]" data-type="[!Pill::Type!]"/>
				</span>
			[/STORPROC]
			</div>
			<div class="col-lg-5 colborder ">
			[COUNT [!Query!]/Illustration|NbPill]
			[IF [!NbPill!]>1]
				[STORPROC [!Query!]/Illustration|Pill|||Ordre|ASC]
				<div class="photo-thumbnails">
					[LIMIT 0|20]
					<div class="col-xs-4">
						<div class="thumbnail">
							<img src="/[!Pill::Image!].mini.120x120.jpg" data-src="/[!Pill::Image!]" class="img-responsive" alt="[!P::Nom!]" data-type="[IF [!Pill::3D!]]3d[/IF]"/>
							<div style="display:none;" class="type3d">
								[!Pill::getIframe()!]
							</div>
						</div>
					</div>
					[/LIMIT]
				</div>
				[/STORPROC]
			[/IF]
				<div class="info-produit">
					[!P::Description!]
				</div>
				[IF [!P::ImageVideo!]]
				<div class="video-produit">
					<img src="/[!P::ImageVideo!].limit.450x450.jpg" data-src="/[!Pill::Image!]" class="img-responsive" alt="[!P::Nom!]" style="width:100%;" id="imgPhoto"/>
				</div>
				[/IF]
				<script type="text/javascript">
					$(function () {
						/** PRODUCT VIDEO **/
						$('#imgPhoto').click(function () {
							$(document).scrollTo($('.product-video'),800);
							return false;
						})
						/** GALLERIE **/
						function initPhotoZoom() {
							$('span.PhotoZoom').each(function (index,item){
								$(item).zoom({url: $(item).attr('data-src')});
							});
						}
						initPhotoZoom();
						$('.photo-thumbnails .thumbnail, .logo-thumbnails .logothumbnail').click(function(event) {
							$('.photo-thumbnails .thumbnail, .logo-thumbnails .logothumbnail').removeClass('current');
							$(this).addClass('current');
							var img = $(this).find('img');
							$('#big-photo .type3d').html($(this).find('.type3d').html());
							if ($(img).attr('data-type')=="3d"){
								//activation du bouton 3d
								console.log("avec 3d");
								$('.switch-3d-button').css('visibility','visible');
								var data = $(img).attr('data-src');
							}else{
								console.log("sans 3d");
								$('.switch-3d-button').css('visibility','hidden');
							}
							if ($('.switch-3d-button').hasClass('vert')) {
								//on ajoute la classe vert au bouton 3d
								$('.switch-3d-button').removeClass('vert');
								//desactivation 3d
								$('#big-photo .type3d').css('display', 'none');
								$('#big-photo .type3d iframe').html();
								//$('#big-photo .type3d').empty();
								//image
								$('span.PhotoZoom').css('display', 'block');
								$('#big-photo img, #bog-photo img ').attr('src', path);
							}
							var path = $(img).attr('data-src');
							//image
							$('span.PhotoZoom').css('display', 'block');
							$('#big-photo img, #bog-photo img ').attr('src', path);
							event.preventDefault();
						});
						
						$('.switch-3d-button').click(function (e) {
							var img = $(e.target).parent('div').find('img');
							var path = $(img).attr('data-src');
							if ($('span.PhotoZoom').css('display')=='block') {
								//activation 3D
								//on cache les images
								$('span.PhotoZoom').css('display', 'none');
								//affichage 3d
								$('#big-photo .type3d').css('display','block');
								$('#big-photo .type3d iframe').attr('src',$('#big-photo .type3d iframe').attr('data-src'));
								$('#big-photo .type3d').html($(this).find('.type3d').html());
								e.preventDefault();
								//en enleve la classe vert du bouton 3d
								$('.switch-3d-button').addClass('vert');
							}else{
								//on ajoute la classe vert au bouton 3d
								$('.switch-3d-button').removeClass('vert');
								//desactivation 3d
								$('#big-photo .type3d').css('display', 'none');
								$('#big-photo .type3d iframe').attr('data-src',$('#big-photo .type3d iframe').attr('src'));
								$('#big-photo .type3d iframe').attr('src','');
								$('#big-photo .type3d iframe').html();
								//$('#big-photo .type3d').empty();
								//image
								$('span.PhotoZoom').css('display', 'block');
								$('#big-photo img, #bog-photo img ').attr('src', path);
							}
						});
						
						//modification du comportement des iframes
/*						$('.type3d iframe').each(function (index,item){
							var sr = $(item).attr('src');
							$(item).html();
							$(item).attr('src','');
							$(item).attr('data-src',sr);
						});*/
						
					});
				</script>
	
				[STORPROC [!Query!]/Range|Rg|||Ordre|ASC]
				<div class="range-use">
					<h4>Range of Use</h4>
					[LIMIT 0|100]
					<div class="progress">
						<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="[!Rg::Note!]" aria-valuemin="0" aria-valuemax="100" style="width: [!Rg::Note!]%">
							<span class="sr-only-com">[!Rg::Nom!]</span>
						</div>
					</div>
					[/LIMIT]
				</div>
				[/STORPROC]
				
				
			</div>
		</div>
		
	</div>
	////////////////////////
	//WIND RANGE / SIZE
	////////////////////////

	[STORPROC [!Query!]/Sizes|Si|||Ordre|ASC]
	<div class="container nopadding-right nopadding-left">
		<div class="[IF [!CatP::SizeType!]=Size]col-lg-offset-7 col-lg-5 [ELSE]col-lg-12[/IF]">
//		<div class="col-lg-12">
			<div class="size slider">
				<a class="scroll-left" href="#nogo">
					<img class="img-responsive" alt="Fone" src="/Skins/FoneKites2014/img/previous.png">
				</a>
				<div class="slider-content">
					<ul class="slider-inner">
						<li class="active">SIZE[IF [!CatP::SizeType!]=WindRange]<br>WIND RANGE[/IF]</li>
						[LIMIT 0|100]
							<li > [!Si::Contenu!]</li>
						[/LIMIT]
					</ul>
				</div>
				<a class="scroll-right" href="#nogo">
					<img class="img-responsive" alt="Fone" src="/Skins/FoneKites2014/img/next.png">
				</a>
			</div>
		</div>
	</div>
	[/STORPROC]
</div>

<div class="container nopadding-right nopadding-left">
	<div class="row">
		<div class="pull-right" style="padding-top:0px; padding-bottom:15px; padding-right:15px; ">
				[MODULE Systeme/Social/Likes?Lurl=[!Lien!]]
		</div>
	</div>
</div>

////////////////////////
//PUNCH TEXT
////////////////////////
[STORPROC [!Query!]/PunchText|Pt|0|1|tmsCreate|ASC]
<div class="gris-fonce">
	<div class="container nopadding-right nopadding-left">
		[LIMIT 0|100]
			<h1>[!Pt::Texte!]</h1>
		[/LIMIT]
	</div>
</div>
[/STORPROC]

////////////////////////
//TECHNOLOGIES
////////////////////////
[STORPROC [!Query!]/Technologie|Tech|||Ordre|ASC]
	<div class="gris ">
		<div class="container nopadding-right nopadding-left">
			<h1>__TECHNOLOGIES_CONSTRUCTION__</h1>
			<div class="Technologies slider">
				<a class="scroll-left" href="#nogo">
					<img class="img-responsive" alt="Fone" src="/Skins/FoneKites2014/img/previous.png">
				</a>
				<div class="marques slider-content">
					<div class="mark slider-inner">
						[LIMIT 0|100]
						<div class="TechnoIcone [IF [!Pos!]=1]current[/IF]">
							<a href="#PanneauTech[!Tech::Id!]" class="technoMini" data-src="/[!Tech::Image!]"><img src="/[!Tech::Icone!]" alt="[!Tech::Titre!]"  /></a> 
						</div>
						[/LIMIT]
					</div>
				</div>
				<a class="scroll-right" href="#nogo">
					<img class="img-responsive" alt="Fone" src="/Skins/FoneKites2014/img/next.png">
				</a>
				<div class="details-technique blocfone">
					<div class="details-const">
						[LIMIT 0|100]
 						<div id="PanneauTech[!Tech::Id!]" class="[IF [!Pos!]=1]current[ELSE]hidden[/IF]" >
							[SWITCH [!Tech::Modele!]|=]
								[CASE Image]
									<div class="[!Couleur!] block-post">
									    <div class="nopadding-right nopadding-left">
										<img src="/[!Tech::Image!].limit.1170x2000.jpg" class="img-responsive" alt="[!Tech::Titre!]" />
									    </div>
									</div>
								[/CASE]
								[CASE Texte]
									<div class="[!Couleur!] block-post">
									    <div class="nopadding-right nopadding-left">
										<p>[!Tech::Texte!]</p>
									    </div>
									</div>
								[/CASE]
								[CASE TexteEtImage]
									<div class="[!Couleur!] block-post">
									    <div class="nopadding-right nopadding-left">
										<div class="row">
											<div class="col-md-6">
												<img src="/[!Tech::Image!].limit.600x2000.jpg" class="img-responsive" alt="[!Tech::Titre!]" />
											</div>
											<div class="col-md-6">
												<p>[!Tech::Texte!]</p>
											</div>
										</div>
									    </div>
									</div>
								[/CASE]
							[/SWITCH]
							<!-- Block -->
							[STORPROC Products/Technologie/[!Tech::Id!]/BlockTechnologie|Bl|0|100|Ordre|ASC]
							    //COULEUR
//							    [IF [!Utils::isPair([!Pos!])!]]
//								[!Couleur:=gris!]
//							    [ELSE]
//								[!Couleur:=gris-clair!]
//							    [/IF]
							    
							    [SWITCH [!Bl::TextePosition!]|=]
								[CASE TexteDroite]
								<div class="[!Couleur!] block-post">
								    <div class="nopadding-right nopadding-left">
									<div class="row">
									    [SWITCH [!Bl::TypeMedia!]|=]
										[CASE 0]
										//image
										<div class="col-lg-6 col-xs-12">
											<img src="/[!Bl::Image!]" class="img-responsive"/>
										</div>
										[/CASE]
										[CASE 1]
										//iframe
										<div class="col-lg-6 col-xs-12">
											[!Bl::Iframe!]
										</div>
										[/CASE]
									    [/SWITCH]
									    <div class="[IF [!Bl::TypeMedia!]=2]col-lg-12[ELSE]col-lg-6[/IF] col-xs-12">
										    [!Bl::Texte!]
									    </div>
									</div>
								    </div>
								</div>
								[/CASE]
								[CASE TexteGauche]
								<div class="[!Couleur!] block-post">
								    <div class="nopadding-right nopadding-left">
									<div class="row">
									    <div class="[IF [!Bl::TypeMedia!]=2]col-lg-12[ELSE]col-lg-6[/IF] col-xs-12">
										    [!Bl::Texte!]
									    </div>
									    [SWITCH [!Bl::TypeMedia!]|=]
										[CASE 0]
										//image
										<div class="col-lg-6 col-xs-12">
											<img src="/[!Bl::Image!]" class="img-responsive"/>
										</div>
										[/CASE]
										[CASE 1]
										//iframe
										<div class="col-lg-6 col-xs-12">
											[!Bl::Iframe!]
										</div>
										[/CASE]
									    [/SWITCH]
									</div>
								    </div>
								</div>
								[/CASE]
								[CASE TexteDessous]
								<div class="[!Couleur!] block-post">
								    <div class="nopadding-right nopadding-left">
									<div class="row">
									    [SWITCH [!Bl::TypeMedia!]|=]
										[CASE 0]
										//image
										<div class="col-lg-12 col-xs-12">
											<img src="/[!Bl::Image!]" class="img-responsive"/>
										</div>
										[/CASE]
										[CASE 1]
										//iframe
										<div class="col-lg-12 col-xs-12">
											[!Bl::Iframe!]
										</div>
										[/CASE]
									    [/SWITCH]
									    <div class="col-lg-12 col-xs-12">
										    [!Bl::Texte!]
									    </div>
									</div>
								    </div>
								</div>
								[/CASE]
								[CASE DoubleTexte]
								<div class="[!Couleur!] block-post">
								    <div class="nopadding-right nopadding-left">
									<div class="row">
									    <div class="col-lg-6 col-xs-12">
										    [!Bl::Texte!]
									    </div>
									    <div class="col-lg-6 col-xs-12">
										    [!Bl::Texte2!]
									    </div>
									</div>
								    </div>
								</div>
								[/CASE]
								[CASE DoubleMedia]
								<div class="[!Couleur!] block-post">
								    <div class="nopadding-right nopadding-left">
									<div class="row">
									    [SWITCH [!Bl::TypeMedia!]|=]
										[CASE 0]
										//image
										<div class="col-lg-6 col-xs-12">
										    [!Bl::Texte!]
											<img src="/[!Bl::Image!]" class="img-responsive"/>
										</div>
										[/CASE]
										[CASE 1]
										//iframe
										<div class="col-lg-6 col-xs-12">
										    [!Bl::Texte!]
											[!Bl::Iframe!]
										</div>
										[/CASE]
									    [/SWITCH]
									    [SWITCH [!Bl::TypeMedia2!]|=]
										[CASE 0]
										//image
										<div class="col-lg-6 col-xs-12">
										    [!Bl::Texte2!]
											<img src="/[!Bl::Image2!]" class="img-responsive"/>
										</div>
										[/CASE]
										[CASE 1]
										//iframe
										<div class="col-lg-6 col-xs-12">
										    [!Bl::Texte2!]
											[!Bl::Iframe2!]
										</div>
										[/CASE]
									    [/SWITCH]
									</div>
								    </div>
								</div>
								[/CASE]
								[DEFAULT]
								    <h1>BLOCK [!Bl::TextePosition!]</h1>
								[/DEFAULT]
							    [/SWITCH]
							[/STORPROC]

						</div>
						[/LIMIT]
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(function (){
			/** TECHNO **/
			$('.marques .technoMini').on('click',function (event) {
				$('.marques .TechnoIcone').removeClass('current');
				$(this).parent().addClass('current');
				$('.details-technique.blocfone .current').removeClass('current').addClass('hidden');
				$($(this).attr('href')).addClass('current').removeClass('hidden');
				event.preventDefault();
			});
			
			//slider techno
			$('.slider .scroll-left').click(function (event){
				//recuperation du slider
				var slider = $(this).parent('.slider');
				var leftPos = $(slider).children('.slider-content').scrollLeft();
				$(slider).children('.slider-content').animate({scrollLeft: leftPos-250}, 300);
				event.preventDefault();
			});
			$('.slider .scroll-right').click(function (event){
				//recuperation du slider
				var slider = $(this).parent('.slider');
				var leftPos = $(slider).children('.slider-content').scrollLeft();
				$(slider).children('.slider-content').animate({scrollLeft: leftPos+250}, 300);
				event.preventDefault();
			});
			
			function initSlider() {
				//dimension du slider
                                console.log('init slider')
				$('.slider').each(function (index,item){
					var length = $(item).find('.slider-content .slider-inner > *').length;
					var width = $(item).find('.slider-inner > *').outerWidth();
					var sliderinner = $(item).find('.slider-inner');
					$(sliderinner).css('width',(length*width + (parseInt($(sliderinner).css('padding-left'))*2)+ (parseInt($(sliderinner).css('margin-left'))*2))+'px');
					if ($(item).find('.slider-inner').width()<=$(item).find('.slider-content').width()) {
						$(item).find('.scroll-right').css('display','none');
						$(item).find('.scroll-left').css('display','none');
					}else{
						$(item).find('.scroll-right').css('display','block');
						$(item).find('.scroll-left').css('display','block');
					}
				});
			}
			initSlider();
			$(window).resize(function (event){
				initSlider();
			});
		});
	</script>
[/STORPROC]


////////////////////////
//RELATED GEARS
////////////////////////
[STORPROC Products/Produit/[!P::Id!]/Produit|PR|0|10]
	<div class="gris-related">
		<div class="container nopadding-right nopadding-left">
			<h1>__RELATED_GEARS__</h1>
			<div class="" id="fone">
				<div class="fone-item item-normal element all"></div>
			[ORDER Id|RANDOM]
				[STORPROC Products/Categorie/Produit/[!PR::Id!]|CatPR][NORESULT][!CatPR:=!][/NORESULT][/STORPROC]
				<div class="fone-item item-[IF [!CatPR::Largeur!]=large]large[ELSE]normal[/IF] element [!CatPR::Url!] all">
					<div class="produits [IF [!CatPR::Hauteur!]!=large]height-mini[/IF]">
						<div class="produits-inner">
							<a href="/[!Systeme::CurrentMenu::Url!]/[!CatPR::Url!]/Produit/[!PR::Url!]">
								<img class="img-responsive" src="/[!PR::ProduitGrandFormat!][IF [!CatPR::Hauteur!]=large].mini.[IF [!CatPR::Largeur!]=large]592[ELSE]290[/IF]x590.jpg[ELSE].mini.[IF [!CatPR::Largeur!]=large]590[ELSE]290[/IF]x255.jpg[/IF]" alt="[!PR::Nom!]"/>
							</a>
							<div class="[!CatPR::Couleur!]">
								<h3><a href="/[!Systeme::CurrentMenu::Url!]/[!CatPR::Url!]/Produit/[!PR::Url!]">[!PR::Nom!]</a></h3>
								<h2><a href="/[!Systeme::CurrentMenu::Url!]/[!CatPR::Url!]/Produit/[!PR::Url!]">[!PR::SousTitre!]</a></h2>
							</div>
						</div>
					</div>
				</div>
			[/ORDER]
			</div>
		</div>
	</div>
[/STORPROC]
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#fone').isotope({
			layoutMode : 'masonry'
			// options
			[IF [!NbNiv!]=1]
			 	,filter: '.[!Cat::Url!]'
			[ELSE]
			 	,filter: '.all'
			[/IF]
			 
		});
		// trigger Isotope after images have loaded
		$('#fone').imagesLoaded( function(){
		    	$('#fone').isotope({
				layoutMode : 'masonry'
	  			// options
				[IF [!NbNiv!]=1]
				 	,filter: '.[!Cat::Url!]'
				[ELSE]
					,filter: '.all'
				[/IF]
			});
		});
		$(window).smartresize(function(){
		    	$('#fone').isotope({
				layoutMode : 'masonry'
	  			// options
				[IF [!NbNiv!]=1]
				 	,filter: '.[!Cat::Url!]'
				[ELSE]
					,filter: '.all'
				[/IF]
			});
		});

		/*$('.filters a.filter').click(function(){
			$('.filters a.filter.filteractive').removeClass('filteractive');
			$('.filters li.active').removeClass('active');
			var selector = $(this).attr('data-filter');
			$('#fone').isotope({ filter: selector });
			$('a[data-filter="'+selector+'"]').addClass('filteractive');
			return false;
		});*/
	});
</script>

////////////////////////
//VIDEO
////////////////////////
[IF [!P::Iframe!]]
	<div class="product-video">
		<div class="container nopadding-right nopadding-left">
			<h1>__PRODUCT_VIDEO__</h1>
			<div class="video-large produits">
				[!P::Iframe!]
			</div>
		</div>
	</div>
[/IF]

