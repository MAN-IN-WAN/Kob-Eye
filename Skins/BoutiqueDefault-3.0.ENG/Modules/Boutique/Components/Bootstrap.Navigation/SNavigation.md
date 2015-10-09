[INFO [!Lien!]|I]
[STORPROC [!I::Historique!]|H|[!Niveau!]|1][/STORPROC]
[STORPROC Boutique/Categorie/[!CatId!]/Categorie/Actif=1|Cato|0|20|Ordre|ASC]
	<ul>
		[LIMIT 0|20]
			<li>
				<a href="[!Url!]/[!Cato::Url!]" [IF [!H::Value!]=[!Cato::Url!]]class="selected"[/IF]>[!Cato::Nom!]</a>
				[IF [!H::Value!]=[!Cato::Url!]]
					[COMPONENT Boutique/Bootstrap.Navigation/SNavigation?Url=[!Url!]/[!Cato::Url!]&CatId=[!Cato::Id!]&Niveau=[!Niveau:+1!]]				
				[/IF]
			</li>
		[/LIMIT]
	</ul>
[/STORPROC]