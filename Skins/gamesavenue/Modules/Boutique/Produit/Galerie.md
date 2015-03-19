// faire le storproc qui va bien
[MODULE Systeme/Structure/CouleurUnivers]
[INFO [!Query!]|I]
//CONSTRUCTION REQUETE
[!REQUETE:=Boutique!]
[!PRODUIT_LINK:=[!Systeme::CurrentMenu::Url!]!]
//GESTION DES CATEGORIES
[STORPROC [!I::Historique!]|H|0|10]
	[IF [!H::DataSource!]=Categorie]
		[!REQUETE+=/[!H::DataSource!]/[!H::Value!]!]
		[!PRODUIT_LINK+=/[!H::Value!]!]
	[/IF]
	[IF [!H::DataSource!]=Genre][!GENRES:::=[!H::Value!]!][/IF]
[/STORPROC]
[!REQUETE+=/Produit!]
//GESTION DES GENRES
[COUNT [!GENRES!]|NbG]
[IF [!NbG!]>0]
	[!REQUETE+=/(!!]
	[!B:=0!]
	[STORPROC [!GENRES!]|G]
		[IF [!B!]][!REQUETE+=+!][ELSE][!B:=1!][/IF]
		[STORPROC Boutique/Genre/[!G!]|Genre][/STORPROC]
		[!REQUETE+=GenreId=[!Genre::Id!]!]
	[/STORPROC]
	[IF [!NbG!]=1]
		//Ajout des enfants dans le cas d'une selection de premier niveau
		[STORPROC Boutique/Genre/[!G!]/Genre|Ge|0|100]
			[IF [!B!]][!REQUETE+=+!][ELSE][!B:=1!][/IF]
			[!REQUETE+=GenreId=[!Ge::Id!]!]
		[/STORPROC]
	[/IF]
	[!REQUETE+=!)!]
[/IF]
//GESTION DES MOTS CLEFS
[STORPROC [!Query!]|P|0|1]
	<b class="coinFinGrisbordertop">
		<b class="coinFinGris1">&nbsp;</b>
		<b class="coinFinGris2">&nbsp;</b>
		<b class="coinFinGris3">&nbsp;</b>
		<b class="coinFinGris4">&nbsp;</b>
	</b>
	<div  class="Bloc1Annonce" > 
		<div class="coinFinGriscontent">
			<span class="blocProduitPagesTitre blocambiance_color" >[!P::Description!]</span>
		</div> // FIN coinFinGriscontent
	</div> //Bloc 1 Annonce
	<b class="coinFinGrisborderbottom">
		<b class="coinFinGris4">&nbsp;</b>
		<b class="coinFinGris3">&nbsp;</b>
		<b class="coinFinGris2">&nbsp;</b>
		<b class="coinFinGris1">&nbsp;</b>
	</b>
[/STORPROC] 
[MODULE Systeme/Diapo]
