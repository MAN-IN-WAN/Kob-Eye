[!MenuDemande:=!]
[INFO [!Lien!]|I]
[STORPROC [!I::Historique!]|H|1|1]
	[!MenuDemande:=[!H::Value!]!]
[/STORPROC]
// Recup couleur des titre en fonction de l'univers
// Recup couleur des titre en fonction de l'univers
[!ColorTitre:=#ff0000!]
[IF [!MenuDemande!]!=]
	[STORPROC Boutique/Categorie/Url=[!MenuDemande!]|MSel|0|1|Ordre|ASC]
		[STORPROC Boutique/Univers/Categorie/[!MSel::Id!]|USel|0|1][/STORPROC]
		[!ColorTitre:=[!USel::TexteColor!]!]
	[/STORPROC]
[/IF]				
<ul class="fildariane">
//	<a href="/Accueil" title="Page d'accueil du site">Accueil</a> 
	[INFO Systeme/Menu/[!Lien!]|I]
	[!LastMenu:=0!]
	[STORPROC [!I::Historique!]|This]
		[!Men+=/[!This::Value!]!]
		[IF [!Systeme::CurrentMenu::Url!]=[!This::Value!]][!LastMenu:=1!][/IF]
		//Si le menu en cours est égal à la valeur en cours, c est que c est le dernier menu 
		[IF [!LastMenu!]]
		//Si c est le dernier menu 
			[IF [!Alias!]=]
			//et que l Alias est deja cree
				//On va cherche l alias pour completer la requete
				[STORPROC Systeme/Menu/[!This::Value!]|M|0|1|Ordre|ASC][/STORPROC]
				[!Alias:=[!M::Alias!]!]
				<li class="fildariane"><a href="[!Men!]" title="[!This::Value!]" style="text-decoration:none;[IF [!Pos!]=[!NbResult!]]color:[!ColorTitre!];font-weight:bold;"[/IF]"> > [!M::Titre!]</a></li>
			[ELSE]
			//sinon, on le cree et on l incremente de la valeur en cours
				[!Alias+=/[!This::Value!]!]
				[INFO [!Alias!]|Al]
				[IF [!Al::ObjectType!]=[!Al::TypeChild!]]
					[STORPROC [!Alias!]|A|0|1]
						<li class="fildariane"><a href="[!Men!]" title="[!A::getDescription!]" style="text-decoration:none;[IF [!Pos!]=[!NbResult!]]color:[!ColorTitre!];"[/IF]"> > [!A::getFirstSearchOrder!]</a></li>
					[/STORPROC]
				[ELSE]
					//<a href="[!Men!]" title="[!Al::TypeChild!]"> > [!Al::TypeChild!]</a>
				[/IF]
				//puis on fait une requete pour aller chercher son nom
			[/IF]
		[ELSE]
			//s il s agit d un menu
			[STORPROC Systeme/Menu/[!This::Value!]|M|0|1|Ordre|ASC][/STORPROC]
			<li class="fildariane"><a href="[!Men!]" title="[!This::Value!]" class="text-decoration:none;[IF [!Lien!]=[!This::Value!]]color:[!ColorTitre!];font-weight:bold;[/IF]"> > [!M::Titre!]</a></li>
			//on fait une requete pour connaitre son nom et on l affiche
		[/IF]
		//[IF [!Pos!]!=[!NbResult!]] >[/IF]
		//si la position n est pas la dernière, on affiche un >
	[/STORPROC]
</ul>