// Catégorie courante...
[INFO [!Lien!]|I]
//Recherche du menu racine
[STORPROC [!I::Historique!]|LaRacine|0|1][/STORPROC]
[!Lr:=[!Systeme::searchMenu([!LaRacine::Value!])!]!]
[INFO [!Lr::Alias!]|In]
[!Niveau:=1!]
[STORPROC [!I::Historique!]|H|1|1][/STORPROC]

[!Base:=[!Lr::Alias!]!]
[!Menu:=[!Lr::Url!]!]

<div class="BlocNavigation" >
	[STORPROC [!Base!]|CatParent|0|1][/STORPROC]
	<div class="EntoureNavigation">
		<div class="TitreNavigation">[!CatParent::Nom!]</div>
	</div>
	[STORPROC [!Base!]/Categorie/Publier=1|Cat|||Ordre|ASC]
	
		[IF [!Cat::Url!]=[!H::Value!]]
			// celui sélectionné
			<a href="/[!Menu!]/[!Cat::Url!]" class="CurrentArbo"  >- [!Cat::Nom!]</a>
			
		[ELSE]
			<a href="/[!Menu!]/[!Cat::Url!]"  >- [!Cat::Nom!]</a>
		[/IF]
		[COMPONENT Catalogue/Navigation/SNavigation?CatId=[!Cat::Id!]&Url=/[!Menu!]/[!Cat::Url!]&Niveau=[!Niveau:+1!]]
	[/STORPROC]

</div>




	
