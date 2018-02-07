[INFO [!Query!]|I]
[STORPROC [!I::Historique!]|H|[!Niveau!]|1][/STORPROC]
[STORPROC Catalogue/Categorie/[!CatId!]/Categorie/Publier=1|Cato]
	<ul class="Level[!Niveau!]">
		[LIMIT 0|20]
			<li class="[IF [!Pos!]=1]First[/IF] [IF [!Pos!]=[!NbResult!]]Last[/IF]">
				<a href="[!Url!]/[!Cato::Url!]" [IF [!H::Value!]=[!Cato::Url!]]class="CurrentArbo [IF [!Url!]/[!Cato::Url!]=/[!Lien!]]Current[/IF]"[/IF]>
					- [!Cato::Titre!] 
				</a>
				[IF [!H::Value!]=[!Cato::Url!]]
					// [COMPONENT Catalogue/Navigation/SNavigation?Url=[!Url!]/[!Cato::Url!]&CatId=[!Cato::Id!]&Niveau=[!Niveau:+1!]]
				[/IF]
			</li>
		[/LIMIT]
	</ul>
[/STORPROC]