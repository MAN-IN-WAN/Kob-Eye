[INFO [!Query!]|I]
[IF [!Niveau!]=][!Niveau:=1!][/IF]
[STORPROC [!I::Historique!]|H|[!Niveau!]|1][/STORPROC]
[!DISPLAY:=1!]
[IF [!MENU!]=]
	[IF [!Module::Actuel::Nom!]!=Boutique][!DISPLAY:=0!][/IF]
	[!MENU:=[!Systeme::CurrentMenu::Url!]!]
	[STORPROC [!Systeme::CurrentMenu::Alias!]|LIE][/STORPROC]
	[IF [!TITRE!]=][!TITRE:=[!LIE::Nom!]!][/IF]
[/IF]
[STORPROC Systeme/Menu/[!MENU!]|Men|0|1][/STORPROC]
[STORPROC [!Men::Alias!]|Cat|0|1][/STORPROC]
[IF [!DISPLAY!]]
	<div class="[!NOMDIV!]">
		<div class="EntoureComposant">
			<div class="Navigation">
				<div class="EnteteNavigation">
					<div class="[!Men::Url!]">[IF [!Cat::IconeMini!]!=]<img src="/[!Cat::IconeMini!]" alt="[!TITRE!]" title="[!TITRE!]" />[/IF][!TITRE!]</div>
				</div>
				<div class="ContenuComposantNavigation">
					[STORPROC [!Men::Alias!]/Categorie/Actif=1|Cato|0|20|Ordre|ASC]
						<ul>
							[LIMIT 0|20]
								<li>
									[IF [!Cato::Url!]=[!H::Value!]]
										<a href="/[!MENU!]/Categorie/[!Cato::Url!]" class="current">[!Cato::Nom!]</a>
										[COMPONENT Boutique/Navigation/SNavigation?Url=/[!MENU!]/[!Cato::Url!]&CatId=[!Cato::Id!]&Niveau=2]
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
	</div>	
[/IF]