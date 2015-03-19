<div id="Ariane">
//	<a href="/[!Systeme::PageDefaut!]" title="Page d'accueil du site">[!Systeme::PageDefaut!]</a> > 
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
				<a href="[!Men!]" title="[!This::Value!]" class="[IF [!Pos!]=[!NbResult!]]ActifAr[/IF]">[!M::Titre!]</a>
			[ELSE]
			//sinon, on le cree et on l incremente de la valeur en cours
				[!Alias+=/[!This::Value!]!]
				[INFO [!Alias!]|Al]
				[IF [!Al::ObjectType!]=[!Al::TypeChild!]]
					[STORPROC [!Alias!]|A|0|1]
						<a href="[!Men!]" title="[!A::getDescription!]" class="[IF [!Pos!]=[!NbResult!]]ActifAr[/IF]">[!A::getFirstSearchOrder!]</a>
					[/STORPROC]
				[ELSE]
					<a href="[!Men!]" title="[!Al::TypeChild!]">[!Al::TypeChild!]</a>
				[/IF]
				//puis on fait une requete pour aller chercher son nom
			[/IF]
		[ELSE]
			//s il s agit d un menu
			[STORPROC Systeme/Menu/[!This::Value!]|M|0|1|Ordre|ASC][/STORPROC]
			<a href="[!Men!]" title="[!This::Value!]" class="[IF [!Lien!]=[!This::Value!]]ActifAr[/IF]">[!M::Titre!]</a>
			//on fait une requete pour connaitre son nom et on l affiche
		[/IF]
		[IF [!Pos!]!=[!NbResult!]] >[/IF]
		//si la position n est pas la dernière, on affiche un >
	[/STORPROC]
</div>


