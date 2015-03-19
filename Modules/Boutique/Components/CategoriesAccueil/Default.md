// Affichage des catégories à l'accueil
[OBJ Boutique|Magasin|Mag]
[!Mg:=[!Mag::getCurrentMagasin()!]!]

[STORPROC Boutique/Magasin/[!Mg::Id!]|Mag|0|1][/STORPROC]
<div class="BlocUneCategories">
	<div class="BlocTop"></div>
	<div class="BlocLine">
	
		<div class="BandeauTitreBlocUneCategorie">
			<h1 class="[!STYLETITRE!]">[IF [!TITRE!]!=][!TITRE!][ELSE][!Mag::Nom!][/IF]</h1>
		</div>
		<div class="toutesAccueil">
			// calcul pour savoir si on met le border-bottom ou pas
			[COUNT Boutique/Magasin/[!Mg::Id!]/Categorie/ALaUne=1&&Actif=1|Limit]
			[!Limit-=1!]
			[!Limit/=2!]
			[!Limit:=[!Math::Floor([!Limit!])!]!]
			[!Limit*=2!]
			[STORPROC Boutique/Magasin/[!Mag::Id!]/Categorie/ALaUne=1&&Actif=1|Cato|||OrdreUne|ASC]
				[!Nb+=1!]
				<div class="BlocCateg[IF [!Nb!]=2][!Nb:=0!] BlocDroite [ELSE] BlocGauche [/IF][IF [!Pos!]>[!Limit!]] LigneLast [/IF]">
					<div class="CategorieUne" >
						<h2><a  class="LienCategorieUne" href="/[!Cato::getUrl()!]" >voir <br />le détail</a>
						<a  class="LienCategorieTitreUne"  href="/[!Cato::getUrl()!]" >[!Cato::Nom!]</a></h2>
						<div class="CategorieContenuUne">
							<div class="ImageCategorieUne">
								<a  class="LienCategorieTitreUne"  href="/[!Cato::getUrl()!]" ><img src="/[IF [!Cato::Image!]!=][!Cato::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].mini.224x191.jpg" width="224" height="191" alt="[!Cato::Nom!]" title="[!Cato::Nom!]" /></a>
							</div>
							<div class="DescCategorieAccueil">
								[!Cato::Description!]
							</div>
						</div>
						
					</div>
				</div>
			[/STORPROC]
		</div>
	</div>

	<div class="BlocBottom">
	</div>

</div>