// Affichage de la liste des catégories de la catégorie en cours	
[IF [!Lien!]!=]
	// bien garder le test sur le lien car sinon on a ce contenu à l'accueil ce qui n'est pas voulu
	[STORPROC [!Query!]|Mag][/STORPROC]
	
	[COUNT [!Query!]/Categorie/Actif=1|NbCat]
	[IF [!NbCat!]]
		<div class="BlocCategories">
			<div class="BandeauTitreBlocCategorie">
				<h1 class="TitreBlocCategorie">[!Mag::Nom!]</h1>
			</div>
			<div class="toutesCategories">
				// calcul pour savoir si on met le border-bottom ou pas
				[STORPROC [!Query!]/Categorie/Actif=1|Cato|||Ordre|ASC]
					<div class="BlocCateg">
						<div class="Categorie " >
							<div class="CategorieTitreBlock " >
								<h2><a  class="LienCategorieDetail" href="/[!Cato::getUrl()!]" >voir <br />le détail</a>
								<a  class="LienCategorieTitre"  href="/[!Cato::getUrl()!]" >[IF [!Cato::NomLong!]!=][!Cato::NomLong!][ELSE][!Cato::Nom!][/IF]</a></h2>

							</div>
							<div class="CategorieContenu">
								<div class="ImageCategorieRight">
									<a  class="LienCategorieTitre"  href="/[!Cato::getUrl()!]" ><img src="/[IF [!Cato::Image!]!=][!Cato::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].limit.175x166.jpg" width="175" height="166" alt="[!Cato::Nom!]" title="[!Cato::Nom!]" /></a>
								</div>
								<div class="DescCategorieCat">
									[SUBSTR 320|[...]][!Cato::Description!][/SUBSTR]
									
								</div>
							</div>
							
						</div>
					</div>
				[/STORPROC]
			</div>
		</div>
	[/IF]
[/IF]
//<a  class="LienBtnCategorie" href="/[!Cato::getUrl()!]" >Voirledetail</a>