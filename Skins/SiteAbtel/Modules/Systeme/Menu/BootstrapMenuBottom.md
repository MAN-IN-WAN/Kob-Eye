<nav role="navigation" id="bottomNavigation">
	<ul>
		[STORPROC [!Systeme::Menus!]/Affiche=1&MenuBas=1|M|0|20]
			<li class="[IF [!Systeme::CurrentMenu::Url!]=[!M::Url!]] active [/IF]">
				[IF [!M::Url!]~http]
					<a href="[!M::Url!]" target="_blank" [IF [!M::ClassCss!]]class="[!M::ClassCss!]"[/IF]>[!M::Titre!]</a>
				[ELSE]
					<a href="/[!M::Url!]" [IF [!M::ClassCss!]]class="[!M::ClassCss!][/IF]" >[!M::Titre!]</a>
				[/IF]
			</li>
		[/STORPROC]
	</ul>
</nav>