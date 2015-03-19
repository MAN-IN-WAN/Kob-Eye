[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H|[!Niveau!]|1][/STORPROC]
[STORPROC Boutique/Categorie/[!CatId!]/Categorie/Actif=1|CatSn|0|20|Ordre|ASC]
	<ul>
		[LIMIT 0|20]
			<li>
				<a href="[!Url!]/[!CatSn::Url!]" [IF [!H::Value!]=[!CatSn::Url!]]class="Current"[/IF]>[!CatSn::Nom!]</a>
				[IF [!H::Value!]=[!CatSn::Url!]]
					[COMPONENT Boutique/Navigation/SNavigation?Url=[!Url!]/[!CatSn::Url!]&CatId=[!CatSn::Id!]&Histo=[!I::Historique!]&Niveau=[!Niveau:+1!]]				
				[/IF]
			</li>
		[/LIMIT]
	</ul>
[/STORPROC]