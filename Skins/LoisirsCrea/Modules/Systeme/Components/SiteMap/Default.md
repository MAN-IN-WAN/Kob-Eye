<div id="Sitemap">
	[IF [!Systeme::CurrentMenu::Alias!]=]
		<h1>Plan du site</h1>
	[/IF]
	//// Niveau 1
	[STORPROC [!Systeme::Menus!]/Affiche=1|M1]
		<ul class="SitemapLvl1">
			[LIMIT 0|100]
				<li class="SitemapLvl1">
					<a class="SitemapLvl1" href="/[!M1::Url!]">[!M1::Titre!]</a>
					//// Niveau 2
					[!Req:=[!M1::Alias!]!]
					[IF [!M1::Alias!]~Redaction][!Req+=/Categorie!][/IF]
					[IF [!M1::URL!]!=]
						// pas pour accueil !
						//// Enfants ou non ?
						[INFO [!Req!]|I1]
						[IF [!I1::TypeSearch!]=Child]
							//// Ordre Affichage
							[STORPROC [!I1::Historique!]|IM1][/STORPROC]
							[OBJ [!IM1::Module!]|[!IM1::DataSource!]|Test]
							[STORPROC [!Test::searchOrder()!]|SO|0|1]
								[!Tri:=[!SO::Nom!]!]
								[NORESULT][!Tri:=Id!][/NORESULT]
							[/STORPROC]
							//// Affichage
							[STORPROC [!Req!]|M2|||[!Tri!]|ASC]
								<ul class="SitemapLvl2">
									[LIMIT 0|100]
										<li class="SitemapLvl2">
											- <a class="SitemapLvl2" href="/[!M1::Url!]/[IF [!M2::Url!]!=][!M2::Url!][ELSE][!M2::Id!][/IF]">[!M2::getFirstSearchOrder()!]</a>
										</li>
									[/LIMIT]
								</ul>
							[/STORPROC]
						[/IF]
					[/IF]
				</li>
			[/LIMIT]
		</ul>
	[/STORPROC]

</div>