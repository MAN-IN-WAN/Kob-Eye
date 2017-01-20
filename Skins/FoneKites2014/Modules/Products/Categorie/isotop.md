[COUNT [!Query!]|NbCat]
[IF [!NbCat!]>1]
	[!Req:=[!Query!]/*!]
[ELSE]
	[!Req:=[!Query!]!]
[/IF]
[!Req:=Products!]

<ul id="filters">
	<li><a href="#" data-filter="*">show all</a></li>
	[STORPROC Products/Categorie|CatP]
	   <li><a href="#filter" data-filter="[!CatP::Url!]"  data-option-key="filter">[!CatP::Nom!]</a></li>
	[/STORPROC]
</ul>

<div class="featured">
	<div class="container" >
		<h1>Products</h1>
 		[STORPROC [!Req!]/Produit|P]
			[STORPROC Products/Categorie/Produit/[!P::Id!]|CatP][/STORPROC]
			<div class="col-lg-3 col-sm-3 col-xs-12">
				<div class="produits">
					<div class="element transition [!CatP::Url!]">
						<a href="/[!Lien!]/Produit/[!P::Url!]">
							<img class="img-responsive" src="/[!P::ProduitGrandFormat!][IF [!CatP::Hauteur!]=large].mini.[IF [!CatP::Largeur!]=large]590[ELSE]290[/IF]x590.jpg[ELSE].mini.[IF [!CatP::Largeur!]=large]590[ELSE]290[/IF]x250.jpg[/IF]" alt="[!P::Nom!]"/>
						</a>
						<div class="[!CatP::Couleur!]">
							<h3><a href="/[!Lien!]/Produit/[!P::Url!]">[!P::Nom!]</a></h3>
						</div>
					</div>
				</div>
			</div>
		[/STORPROC]
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function (){
		$('#container').isotope({ filter: '*' });

		$('#container').isotope({
			animationOptions: {
				duration: 750,
				easing: 'linear',
				queue: false
			}
		});

		$('#filters a').click(function(){
 			var selector = $(this).attr('data-filter');
  			$('#container').isotope({ filter: selector });
			return false;
		});
	});
</script>
