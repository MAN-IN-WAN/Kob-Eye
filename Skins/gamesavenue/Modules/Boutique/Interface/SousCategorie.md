//AFFICHAGE DES SOUS CATEGORIE 
//INPUT
//O ==> OBJET CATEGORIE PARENT
//U ==> LIEN A INCREMENTER
[STORPROC [!O::getUrl!]/Categorie|MSCat|0|100|Ordre|ASC]
	<hr>
	<div class="titreColonneGrise"><a class="titreColonneGrise" href="[!U!]/[!MSCat::Url!]">[!MSCat::Nom!]</a></div>
	[MODULE Boutique/Interface/SousCategorie?O=[!MSCat!]&U=[!U!]/[!MSCat::Url!]]
	[NORESULT]
		[MODULE Boutique/Interface/Genre?O=[!O!]&U=[!U!]/Genre]
	[/NORESULT]
[/STORPROC]
