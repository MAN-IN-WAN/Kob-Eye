// Affichage des catégories à l'accueil
<div class="[!NOMDIV!]"> 
	<div class="CategoriesAccueil">
		[!Cpt:=0!]
		[STORPROC Boutique/Magasin/[!MAGASIN!]/Categorie/Actif=1|Cat]
			[STORPROC Boutique/Categorie/[!Cat::Id!]/Categorie/*/Categorie/ALaUne=1&Actif=1|Cato|||OrdreUne|ASC]
				[IF [!Cpt!]=3][!Cpt:=0!][/IF]
				[!Cpt+=1!]
				<div class="BlocCateg[IF [!Cpt!]=2]Pair[/IF]">
					<div class="CategorieUne" >
						<div class="ImageCategorieUne">
							<a  class="LienCategorieTitreUne"  href="/[!Cato::getUrl()!]" ><img src="/[IF [!Cato::Icone!]!=][!Cato::Icone!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].mini.316x220.jpg" width="316" height="220" alt="[!Cato::Nom!]" title="[!Cato::Nom!]" [IF[!Cato::IconeHover!]!=] onmouseover="this.src='/[!Cato::IconeHover!]';" onmouseout="this.src='/[!Cato::Icone!]';"[/IF] /></a>
							<a  class="LienCategorieTitreUneText"  href="/[!Cato::getUrl()!]" >[!Cato::Nom!]</a>
						</div>
						
					</div>
				</div>
			[/STORPROC]
		[/STORPROC]
	</div>
</div>