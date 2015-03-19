[OBJ Boutique|Magasin|Mag]
[!Mg:=[!Mag::getCurrentMagasin()!]!]

<div class="BlocUneCategories">
	[STORPROC Boutique/Magasin/[!Mg::Id!]/Categorie/ALaUne=1&Actif=1|Cato|||OrdreUne|ASC]
		<div class="ColonneCateg" >
			<h2>[!Cato::Nom!]</h2>
			<div class="ImageCategorieUne">
				[IF [!Cato::Image!]!=]<a href="/[!Cato::getUrl()!]"><img src="/[!Cato::Image!].mini.155x162.jpg" width="155" height="162" alt="[!Cato::Nom!]" title="[!Cato::Nom!]" /></a>[/IF]
			</div>
			<a href="/[!Cato::getUrl()!]" class="ReadMore">Voir [!Cato::Nom!]</a>
		</div>
	[/STORPROC]
	[STORPROC Redaction/Categorie/_Accueil|Cat|0|1]
		<p style="font-size:14px;margin:20px 10px 0 10px;">[!Cat::Description!]</p>
	[/STORPROC]


</div>