[INFO [!Query!]|I]

<ul id="LeftNav" class="col-md-2 bloc">
	<li id="moduleName">
		<a href="/MiseEnPage" title="Retour à la racine du module" id="ModTitle">
			<h1><span class="glyphicon glyphicon-home"></span> Mise En Page</h1>
		</a>
	</li>
	<li [IF [!Lien!]~=Categorie]class="selected"[/IF]>
		<a href="/MiseEnPage/Categorie"><span class="glyphicon glyphicon-th-list"></span>Catégories</a>
	</li>
	<li [IF [!Lien!]~=Article]class="selected"[/IF]>
		<a href="/MiseEnPage/Article"><span class="glyphicon glyphicon-align-left"></span>Articles</a>
	</li>
	<li>
		<a href="/"><span class="glyphicon glyphicon-share-alt"></span>Retour à l'accueil</a>
	</li>
</ul>


<div class="col-md-10">
	<div id="ModContent"  class="bloc">	
		[MODULE MiseEnPage/Nav]
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
</div>



