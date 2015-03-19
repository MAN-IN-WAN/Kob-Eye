[INFO [!Query!]|I]
[IF [!Niveau!]=][!Niveau:=1!][/IF]
[STORPROC [!I::Historique!]|H|[!Niveau!]|1][/STORPROC]
[!DISPLAY:=1!]
[IF [!MENU!]=]
	[IF [!Module::Actuel::Nom!]!=Boutique][!DISPLAY:=0!][/IF]
	[!MENU:=[!Systeme::CurrentMenu::Titre!]!]
	[STORPROC [!Systeme::CurrentMenu::Alias!]|LIE][/STORPROC]
	[IF [!TITRE!]=][!TITRE:=[!LIE::Nom!]!][/IF]
[ELSE]
	[STORPROC Systeme/Menu/[!MENU!]|Menu|0|1][/STORPROC]
[/IF]

[IF [!DISPLAY!]]
	<div class="EntoureComposant">
		<div class="Navigation">
			<div class="EnteteNavigation">
				[!TITRE!]
			</div>
			<div class="ContenuComposantNavigation">		
				[STORPROC Boutique/[!Menu::Alias!]/Categorie/Actif=1|Cato|0|20|Ordre|ASC]
					<ul>
						[LIMIT 0|20]
								<li>
									[IF [!Cato::Url!]=[!H::Value!]]
										<a href="/[!MENU!]/Categorie/[!Cato::Url!]">[!Cato::Nom!]</a>
										// [COMPONENT Boutique/Navigation/SNavigation?Url=/[!MENU!]/[!Cato::Url!]&CatId=[!Cato::Id!]&Niveau=2]				
									[ELSE]
										<a href="/[!MENU!]/Categorie/[!Cato::Url!]">[!Cato::Nom!]</a>
									[/IF]
								</li>
						[/LIMIT]
					</ul>
				[/STORPROC]
			</div>
		</div>
	</div>	

[/IF]