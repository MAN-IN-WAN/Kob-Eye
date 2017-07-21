[INFO [!Query!]|I]

<a href="/MiseEnPage" title="Retour à la racine du module" id="ModTitle">
	<h1><span class="glyphicon glyphicon-share-alt"> </span> Mise En Page</h1>
</a>
<div id="ModNav">
	[MODULE MiseEnPage/Nav]
</div>

<ul id="LeftNav" class="col-md-2">
	<li>
		<a href="/"><span class="glyphicon glyphicon-home"></span>  Accueil</a>
	</li>
	<li [IF [!Lien!]~=Categorie]class="selected"[/IF]>
		<a href="/MiseEnPage/Categorie"><span class="glyphicon glyphicon-th-list"></span>  Catégories</a>
	</li>
	<li [IF [!Lien!]~=Article]class="selected"[/IF]>
		<a href="/MiseEnPage/Article"><span class="glyphicon glyphicon-align-left"></span>  Articles</a>
	</li>
</ul>
<div id="ModContent"  class="col-md-10 col-md-offset-2">
	[IF [!I::ObjectType!]=Categorie]
	<div id="MEPCat" class="objectContainer">
		[MODULE MiseEnPage/DisplayCats]
	</div>
	[/IF]
	[IF [!I::ObjectType!]=Article]
	<div id="MEPArt" class="objectContainer">
		[MODULE MiseEnPage/DisplayArts]
	</div>
	[/IF]
	[IF [!I::TypeSearch!]=Interface]
	<div id="MEPDesc" class="objectContainer">
		[MODULE MiseEnPage/Base]
	</div>
	[/IF]
</div>



