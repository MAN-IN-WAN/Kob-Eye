//[INFO [!Lien!]|I]
//Recherche du menu racine
//[COUNT [!I::Historique!]|NbNiv]

[COUNT [!Query!]|NbNiv]
[STORPROC [!Query!]|Cat][/STORPROC]

[!Req:=Products!]


<div class="last-news">
	<div class="container nopadding-right nopadding-left">
		<div id="fone">
			<div class="fone-item item-normal element all"></div>
	 		[STORPROC [!Req!]/Produit/Display=1|P]
				[STORPROC Products/Categorie/Produit/[!P::Id!]|CatP][NORESULT][!CatP:=!][/NORESULT][/STORPROC]
				<div class="fone-item item-[IF [!CatP::Largeur!]=large]large[ELSE]normal[/IF] element [!CatP::Url!] all">
					<div class="produits [IF [!CatP::Hauteur!]!=large]height-mini[/IF]">
						<div class="produits-inner">
							<a href="/[!Systeme::CurrentMenu::Url!]/[!CatP::Url!]/Produit/[!P::Url!]">
								<img class="img-responsive" src="/[!P::ProduitGrandFormat!][IF [!CatP::Hauteur!]=large].mini.[IF [!CatP::Largeur!]=large]592[ELSE]290[/IF]x590.jpg[ELSE].mini.[IF [!CatP::Largeur!]=large]590[ELSE]290[/IF]x255.jpg[/IF]" alt="[!P::Nom!]"/>
							</a>
							<div class="[!CatP::Couleur!]">
								<h3><a href="/[!Systeme::CurrentMenu::Url!]/[!CatP::Url!]/Produit/[!P::Url!]">[!P::Nom!]</a></h3>
								<h2><a href="/[!Systeme::CurrentMenu::Url!]/[!CatP::Url!]/Produit/[!P::Url!]">[!P::SousTitre!]</a></h2>
							</div>
						</div>
					</div>
				</div>
			[/STORPROC]
		</div>
	</div>
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
		$(window).on("debouncedresize", function( event ) {
		    // Your event handler code goes here.
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
		/*$(window).smartresize(function(){
		    	$('#fone').isotope({
				layoutMode : 'masonry'
	  			// options
				[IF [!NbNiv!]=1]
				 	,filter: '.[!Cat::Url!]'
				[ELSE]
					,filter: '.all'
				[/IF]
			});
		});*/

		$('.filters a.filter').click(function(){
			$('.filters a.filter.filteractive').removeClass('filteractive');
			$('.filters li.active').removeClass('active');
			var selector = $(this).attr('data-filter');
			$('#fone').isotope({ filter: selector });
			$('a[data-filter="'+selector+'"]').addClass('filteractive');
			return false;
		});
	});
</script>
