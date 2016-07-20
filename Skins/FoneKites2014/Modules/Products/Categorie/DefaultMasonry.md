[COUNT [!Query!]|NbCat]
[IF [!NbCat!]>1]
	[!Req:=[!Query!]/*!]
[ELSE]
	[!Req:=[!Query!]!]
[/IF]
[!Req:=Products!]


<div class="container"><h1>Products</h1>
	<div id="fone">
		
 		[STORPROC [!Req!]/Produit|P]
			[STORPROC Products/Categorie/Produit/[!P::Id!]|CatP][/STORPROC]
			<div class="item-[IF [!CatP::Largeur!]=large]large[ELSE]normal[/IF] fone-item">
				<div class="produits ">
					<a href="/[!Lien!]/Produit/[!P::Url!]">
						<img class="img-responsive" src="/[!P::ProduitGrandFormat!][IF [!CatP::Hauteur!]=large].mini.[IF [!CatP::Largeur!]=large]590[ELSE]290[/IF]x590.jpg[ELSE].mini.[IF [!CatP::Largeur!]=large]590[ELSE]290[/IF]x250.jpg[/IF]" alt="[!P::Nom!]"/>
					</a>
					<div class="[!CatP::Couleur!]">
						<h3><a href="/[!Lien!]/Produit/[!P::Url!]">[!P::Nom!]</a></h3>
					</div>
				</div>
			</div>
		[/STORPROC]
	</div>
</div>

