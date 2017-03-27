[OBJ Systeme|Site|Sit]
[!CurSite:=[!Sit::getCurrentSite()!]!]
[!EntiteSite:=[!CurSite::getOneChild(Entite)!]!]

<nav role="navigation" id="topNavigation">
	<ul class="navbar-top">
		[STORPROC [!Systeme::Menus!]/Affiche=1&MenuHaut=1|M|0|10|Ordre|ASC]
			<li >
			[IF [!M::Url!]~http]
				<a href="[!M::Url!]" target="_blank" class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF] [!M::Url!]"  >[IF [!M::Icone!]!=]<img src="/[!M::Icone!]" alt="[!M::Titre!]" title="[!M::Titre!]" />[/IF][!M::Titre!] [!M::SousTitre!]</a>
			[ELSE]
				<a href="/[!M::Url!]" class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF] [!M::Url!] " [IF [!Pos!]=[!NbResult!]]style="background-color:[!EntiteSite::CodeCouleur!];"[/IF] >
					[IF [!M::Icone!]!=]<img src="/[!M::Icone!]" alt="[!M::Titre!]" title="[!M::Titre!]" />[/IF]
					[IF [!Pos!]=[!NbResult!]]
						[!M::SousTitre!]
					[ELSE]
						[!M::Titre!]
					[/IF]
				</a>
			[/IF]
			</li>
		[/STORPROC]
	</ul>
</nav>
