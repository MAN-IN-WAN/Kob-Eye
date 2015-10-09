[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H|[!Niveau!]|1][/STORPROC]
[STORPROC Boutique/Categorie/[!CatId!]/Categorie/Publier=1|Cato|0|20|Ordre|ASC]
	<ul>
		[LIMIT 0|20]
			<li>
				<a href="[!Url!]/[!Cato::Url!]" [IF [!H::Value!]=[!Cato::Url!]]class="Current"[/IF]>[!Cato::Nom!]</a>
				[IF [!H::Value!]=[!Cato::Url!]]
					[COMPONENT Boutique/Bootstrap.Navigation/SNavigation?Url=[!MENU!]/[!Cato::Url!]&Histo=[!I::Historique!]&Niveau:+1!]]				
				[/IF]
			</li>
		[/LIMIT]
	</ul>
[/STORPROC]