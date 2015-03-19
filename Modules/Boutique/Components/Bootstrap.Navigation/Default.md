[INFO [!Query!]|I]
[IF [!Niveau!]=][!Niveau:=1!][/IF]
[STORPROC [!I::Historique!]|H|[!Niveau!]|1][/STORPROC]
[!DISPLAY:=1!]
[IF [!Module::Actuel::Nom!]!=Boutique][!DISPLAY:=0!][/IF]
[!Menu:=[!Systeme::CurrentMenu!]!]
[!MENU:=[!Systeme::CurrentMenu::Url!]!]

[IF [!DISPLAY!]]
<div class="well">
	<h4>[!TITRE!]</h4>
	<div class="block_content">
		<ul class="nav nav-pills nav-stacked">
			[STORPROC [!Menu::Alias!]/Categorie/Actif=1|Cato|0|20|Ordre|ASC]
				<li>
					[IF [!Cato::Url!]=[!H::Value!]]
						<a href="/[!MENU!]/[!Cato::Url!]" [IF [!Lien!]~[!Cato::Url!]]class="active"[/IF]>[!Cato::Nom!]</a>
						 [COMPONENT Boutique/Bootstrap.Navigation/SNavigation?Url=/[!MENU!]/[!Cato::Url!]&CatId=[!Cato::Id!]&Niveau=2]				
					[ELSE]
						<a href="/[!MENU!]/Categorie/[!Cato::Url!]" [IF [!Lien!]~[!Cato::Url!]]class="active"[/IF]>[!Cato::Nom!]</a>
					[/IF]
				</li>
			[/STORPROC]
		</ul>
	</div>
</div>
[/IF]